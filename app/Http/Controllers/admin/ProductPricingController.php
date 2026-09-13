<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ProductVariantPriceHistory;
use App\Services\ProductVariantPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductPricingController extends Controller
{
    protected ProductVariantPricingService $pricing;

    public function __construct(ProductVariantPricingService $pricing)
    {
        $this->pricing = $pricing;
    }

    /**
     * List product variants with their cost, MRP and selling price.
     */
    public function index(Request $request)
    {
        $query = ProductVariant::with('product')->latest('id');

        if ($request->filled('keyword')) {
            $keyword = $request->get('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('sku', 'like', "%{$keyword}%")
                    ->orWhereHas('product', function ($p) use ($keyword) {
                        $p->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->get('product_id'));
        }

        $variants = $query->paginate(20)->appends($request->query());

        return view('admin.pricing.index', compact('variants'));
    }

    /**
     * Update a variant's MRP and selling price (manual admin action).
     */
    public function update(Request $request, $variantId)
    {
        $variant = ProductVariant::findOrFail($variantId);

        $validator = Validator::make($request->all(), [
            'compare_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        try {
            $result = $this->pricing->updatePrices(
                $variant,
                $request->input('compare_price'),
                $request->input('selling_price'),
                (string) $request->input('reason'),
                Auth::guard('admin')->id() ?? Auth::id()
            );
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ]);
        }

        $variant->refresh();

        return response()->json([
            'status' => true,
            'changed' => $result['changed'],
            'message' => $result['changed']
                ? 'Price updated successfully.'
                : 'No change detected — nothing was saved.',
            'compare_price' => number_format((float) $variant->compare_price, 2, '.', ''),
            'selling_price' => number_format((float) $variant->selling_price, 2, '.', ''),
        ]);
    }

    /**
     * JSON price history for a variant.
     */
    public function history($variantId)
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);

        $history = ProductVariantPriceHistory::with('user')
            ->where('product_variant_id', $variant->id)
            ->latest('id')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => optional($row->created_at)->format('d M Y, h:i A'),
                    'old_compare_price' => number_format((float) $row->old_compare_price, 2),
                    'new_compare_price' => number_format((float) $row->new_compare_price, 2),
                    'old_selling_price' => number_format((float) $row->old_selling_price, 2),
                    'new_selling_price' => number_format((float) $row->new_selling_price, 2),
                    'reason' => $row->reason,
                    'changed_by' => $row->user->name ?? 'System',
                ];
            });

        return response()->json([
            'status' => true,
            'product' => $variant->product->name ?? '',
            'sku' => $variant->sku,
            'history' => $history,
        ]);
    }
}
