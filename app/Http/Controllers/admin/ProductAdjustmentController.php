<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAdjustment;
use App\Models\ProductVariant;
use App\Services\ProductAdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductAdjustmentController extends Controller
{
    public function __construct(protected ProductAdjustmentService $service)
    {
    }

    public function index()
    {
        $adjustments = ProductAdjustment::with(['variant', 'product', 'creator'])
            ->latest('id')
            ->paginate(15);

        return view('admin.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $variants = ProductVariant::with('product')->get();

        return view('admin.adjustments.create', compact('variants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|integer|exists:product_variants,id',
            'adjustment_type' => 'required|in:' . implode(',', array_keys(ProductAdjustment::types())),
            'quantity' => 'nullable|integer|min:1',
            'actual_stock' => 'nullable|integer|min:0',
            'reason' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
        ]);

        $adjustment = $this->service->adjust($data, Auth::guard('admin')->id());

        return redirect()
            ->route('admin.adjustments.show', $adjustment->id)
            ->with('success', 'Stock adjusted successfully.');
    }

    public function show($id)
    {
        $adjustment = ProductAdjustment::with(['variant', 'product', 'creator'])->findOrFail($id);

        return view('admin.adjustments.show', compact('adjustment'));
    }
}
