<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\NewProduct;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\VariationValues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NewProductController extends Controller
{
    /* =====================================================================
       CREATE
       ===================================================================== */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->productRules(null));
        $this->addVariantErrors($validator, $request, null);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        try {
            DB::transaction(function () use ($request) {
                $product = NewProduct::create([
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'sku' => $request->product_sku,
                    'unit_id' => $request->units_id,
                    'brand_id' => $request->brand_id,
                    'cat_id' => $request->category,
                    'sub_cat_id' => $request->sub_category,
                    'description' => $request->description,
                    'type' => $request->product_type,
                    'status' => $request->input('status', 1),
                    'hot_products' => $request->input('hot_products', 0),
                    'free_delivery' => $request->boolean('free_delivery'),
                ]);

                if ($this->isSingle($request)) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $request->product_sku,
                        'is_variant' => 0,
                        'allow_pre_order' => $request->boolean('allow_pre_order'),
                    ]);
                } else {
                    foreach ($this->normalizeVariants($request) as $variant) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $variant['sku'],
                            'is_variant' => 1,
                            'variation_values' => $variant['variation_values'] ? json_encode($variant['variation_values']) : null,
                            'allow_pre_order' => $variant['allow_pre_order'],
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Unable to save the product. No changes were applied. Please try again.',
            ]);
        }

        Cache::forget('front_search_products');

        return response()->json([
            'status' => true,
            'message' => 'Product added successfully',
        ]);
    }

    /* =====================================================================
       EDIT
       ===================================================================== */

    public function edit($id)
    {
        $product = NewProduct::find($id);

        if (empty($product)) {
            return redirect()->route('products.index')->with('error', 'Product Not Found');
        }

        $subCategories = SubCategory::where('category_id', $product->cat_id)->get();
        $product_variation = ProductVariant::where('product_id', $product->id)->get();
        $units = Unit::get();
        $variations = Variation::all();

        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();

        $data = [];
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $data['units'] = $units;
        $data['variations'] = $variations;
        $data['product_variation'] = $product_variation;
        $data['variationData'] = $this->variationData();
        $data['variantsForJs'] = $this->variantsForJs($product_variation);

        return view('admin.products.new_edit', $data);
    }

    /* =====================================================================
       UPDATE
       ===================================================================== */

    public function update($id, Request $request)
    {
        $product = NewProduct::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ]);
        }

        $validator = Validator::make($request->all(), $this->productRules($product->id));
        $this->addVariantErrors($validator, $request, $product->id);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        try {
            DB::transaction(function () use ($product, $request) {
                $product->update([
                    'name' => $request->name,
                    'slug' => $request->slug,
                    'sku' => $request->product_sku,
                    'unit_id' => $request->units_id,
                    'brand_id' => $request->brand_id,
                    'cat_id' => $request->category,
                    'sub_cat_id' => $request->sub_category,
                    'description' => $request->description,
                    'type' => $request->product_type,
                    'status' => $request->input('status', $product->status),
                    'hot_products' => $request->input('hot_products', $product->hot_products),
                    'free_delivery' => $request->boolean('free_delivery'),
                ]);

                if ($this->isSingle($request)) {
                    $this->syncSingleVariant($product, $request);
                } else {
                    $this->syncVariableVariants($product, $request);
                }
            });
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Unable to save the product. No changes were applied. Please try again.',
            ]);
        }

        Cache::forget('front_search_products');

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
        ]);
    }

    /* =====================================================================
       VARIANT SYNC
       ===================================================================== */

    /**
     * Single product: exactly one is_variant=0 variant, sku = product_sku.
     * Every other variant is treated as removed — but only if it is safe to
     * delete (no stock / no dependent records).
     */
    protected function syncSingleVariant(NewProduct $product, Request $request): void
    {
        $default = ProductVariant::where('product_id', $product->id)
            ->where('is_variant', 0)
            ->lockForUpdate()
            ->first();

        $others = ProductVariant::where('product_id', $product->id)
            ->when($default, fn ($q) => $q->where('id', '!=', $default->id))
            ->get();

        foreach ($others as $variant) {
            $reason = $this->variantDeleteBlockReason($variant);

            if ($reason) {
                throw ValidationException::withMessages([
                    'product_type' => 'Cannot switch to Single Product because variant "' . $variant->sku . '" ' . $reason . '.',
                ]);
            }
        }

        foreach ($others as $variant) {
            $variant->delete();
        }

        if (!$default) {
            $default = new ProductVariant(['product_id' => $product->id]);
        }

        $default->product_id = $product->id;
        $default->sku = $request->product_sku;
        $default->is_variant = 0;
        $default->variation_values = null;
        $default->allow_pre_order = $request->boolean('allow_pre_order');
        $default->save();
    }

    /**
     * Variable product: update by variant id (supports SKU rename), create new
     * variants, and reconcile removed ones (delete only when safe).
     */
    protected function syncVariableVariants(NewProduct $product, Request $request): void
    {
        $keptIds = [];

        foreach ($this->normalizeVariants($request) as $index => $data) {
            if (!empty($data['id'])) {
                $variant = ProductVariant::where('id', $data['id'])
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    throw ValidationException::withMessages([
                        'variation_sku.' . $index => 'Invalid variant for this product.',
                    ]);
                }

                $variant->sku = $data['sku'];
                $variant->is_variant = 1;
                $variant->variation_values = $data['variation_values'] ? json_encode($data['variation_values']) : null;
                $variant->allow_pre_order = $data['allow_pre_order'];
                $variant->save();

                $keptIds[] = $variant->id;
            } else {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $data['sku'],
                    'is_variant' => 1,
                    'variation_values' => $data['variation_values'] ? json_encode($data['variation_values']) : null,
                    'allow_pre_order' => $data['allow_pre_order'],
                ]);

                $keptIds[] = $variant->id;
            }
        }

        $removed = ProductVariant::where('product_id', $product->id)
            ->whereNotIn('id', $keptIds)
            ->get();

        foreach ($removed as $variant) {
            $reason = $this->variantDeleteBlockReason($variant);

            if ($reason) {
                throw ValidationException::withMessages([
                    'variants' => 'Cannot remove variant "' . $variant->sku . '" because it ' . $reason . '.',
                ]);
            }
        }

        foreach ($removed as $variant) {
            $variant->delete();
        }
    }

    /* =====================================================================
       VALIDATION
       ===================================================================== */

    protected function productRules(?int $id = null): array
    {
        $slugUnique = 'unique:new_products,slug' . ($id ? ',' . $id . ',id' : '');
        $skuUnique = 'unique:new_products,sku' . ($id ? ',' . $id . ',id' : '');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|' . $slugUnique,
            'product_sku' => 'required|string|max:255|' . $skuUnique,
            'units_id' => 'required|exists:units,id',
            'brand_id' => 'required|exists:brands,id',
            'category' => 'required|exists:categories,id',
            'sub_category' => 'required|exists:sub_categories,id',
            'product_type' => 'required|in:0,1',
            'description' => 'nullable|string',
            'free_delivery' => 'nullable|boolean',
            'status' => 'nullable|in:0,1',
            'hot_products' => 'nullable|in:0,1',

            'variation_sku' => 'required_if:product_type,1|array',
            'variation_sku.*' => 'required_if:product_type,1|string|max:255',
            'variation_values' => 'required_if:product_type,1|array',
            'variation_values.*' => 'required_if:product_type,1|string',

            'variant_id' => 'nullable|array',
            'variant_id.*' => 'nullable|integer',
        ];
    }

    /**
     * Variant-level rules that need cross-row / database context. Registered as
     * "after" callbacks so they run as part of validation.
     */
    protected function addVariantErrors($validator, Request $request, ?int $productId): void
    {
        $validator->after(function ($validator) use ($request, $productId) {
            if ($this->isSingle($request)) {
                // The default variant's SKU equals the product SKU and must be
                // unique among product_variants too.
                $query = ProductVariant::where('sku', $request->product_sku);

                if ($productId) {
                    $query->whereNotIn('id', ProductVariant::where('product_id', $productId)->pluck('id')->all());
                }

                if ($query->exists()) {
                    $validator->errors()->add('product_sku', 'This SKU is already used by another variant.');
                }

                return;
            }

            $skus = (array) $request->variation_sku;
            $ids = (array) $request->variant_id;

            if (empty($skus)) {
                return;
            }

            // Duplicates inside the submission.
            $counts = array_count_values(array_map('strval', $skus));

            foreach ($skus as $index => $sku) {
                if (($counts[(string) $sku] ?? 0) > 1) {
                    $validator->errors()->add('variation_sku.' . $index, 'This SKU is already used by another variant.');
                    continue;
                }

                // Uniqueness against the database (ignoring the row being edited).
                $query = ProductVariant::where('sku', $sku);
                $variantId = $ids[$index] ?? null;

                if ($variantId) {
                    $query->where('id', '!=', $variantId);
                }

                if ($query->exists()) {
                    $validator->errors()->add('variation_sku.' . $index, 'This SKU is already used by another variant.');
                }
            }

            // Ownership of submitted variant ids.
            foreach ($ids as $index => $variantId) {
                if (!$variantId) {
                    continue;
                }

                $belongs = $productId
                    && ProductVariant::where('id', $variantId)->where('product_id', $productId)->exists();

                if (!$belongs) {
                    $validator->errors()->add('variation_sku.' . $index, 'Invalid variant for this product.');
                }
            }
        });
    }

    /* =====================================================================
       HELPERS
       ===================================================================== */

    protected function isSingle(Request $request): bool
    {
        return (string) $request->product_type === '0';
    }

    /**
     * Normalise submitted variants into a predictable structure.
     */
    protected function normalizeVariants(Request $request): array
    {
        $skus = (array) $request->variation_sku;
        $ids = (array) $request->variant_id;
        $values = (array) $request->variation_values;
        $preOrders = (array) $request->allow_pre_order;

        $out = [];

        foreach ($skus as $index => $sku) {
            $out[] = [
                'id' => $ids[$index] ?? null,
                'sku' => $sku,
                'variation_values' => $this->decodeVariationValues($values[$index] ?? null),
                'allow_pre_order' => !empty($preOrders[$index]),
            ];
        }

        return $out;
    }

    protected function decodeVariationValues($encoded): ?array
    {
        if (empty($encoded) || !is_string($encoded)) {
            return null;
        }

        $decoded = json_decode(urldecode($encoded), true);

        if (!is_array($decoded)) {
            return null;
        }

        return array_values(array_map(function ($item) {
            return [
                'variation_id' => (int) ($item['variation_id'] ?? 0),
                'value_id' => (int) ($item['value_id'] ?? 0),
            ];
        }, $decoded));
    }

    /**
     * Reason a variant must not be deleted, or null when deletion is safe.
     */
    protected function variantDeleteBlockReason(ProductVariant $variant): ?string
    {
        if ((int) $variant->qty > 0) {
            return 'has stock on hand';
        }

        if (DB::table('order_items')->where('product_id', $variant->id)->exists()) {
            return 'is referenced by existing orders';
        }

        if (DB::table('purchase_items')->where('variant_id', $variant->id)->exists()) {
            return 'has purchase history';
        }

        if (DB::table('purchase_return_items')->where('variant_id', $variant->id)->exists()) {
            return 'has purchase-return history';
        }

        if (DB::table('product_images')->where('product_id', $variant->id)->exists()) {
            return 'has product images attached';
        }

        return null;
    }

    protected function variationData(): array
    {
        return Variation::all()->map(function ($variation) {
            $values = VariationValues::where('variation_id', $variation->id)->orderBy('value')->get();

            return [
                'id' => $variation->id,
                'name' => $variation->variations,
                'values' => $values->map(fn ($value) => [
                    'id' => $value->id,
                    'value' => $value->value,
                ])->values()->all(),
            ];
        })->values()->all();
    }

    protected function variantsForJs($productVariation): array
    {
        return $productVariation->map(function ($variant) {
            $values = json_decode($variant->variation_values, true);
            $values = is_array($values) ? $values : [];

            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'values' => array_values(array_map(fn ($item) => [
                    'variation_id' => (int) ($item['variation_id'] ?? 0),
                    'value_id' => (int) ($item['value_id'] ?? 0),
                ], $values)),
                'allow_pre_order' => (bool) $variant->allow_pre_order,
                'is_variant' => (bool) $variant->is_variant,
                'qty' => $variant->stock(),
            ];
        })->values()->all();
    }

    /* =====================================================================
       VARIANT DELETE (legacy endpoint kept for compatibility)
       ===================================================================== */

    public function destroy_variant($id, Request $request)
    {
        $variant = ProductVariant::find($id);

        if (!$variant) {
            return $this->variantDeleteResponse($request, false, 'Variant not found.');
        }

        $reason = $this->variantDeleteBlockReason($variant);

        if ($reason) {
            return $this->variantDeleteResponse($request, false, 'Cannot delete this variant because it ' . $reason . '.');
        }

        $variant->delete();
        Cache::forget('front_search_products');

        return $this->variantDeleteResponse($request, true, 'Variant deleted successfully.');
    }

    protected function variantDeleteResponse(Request $request, bool $status, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => $status,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with($status ? 'success' : 'error', $message);
    }

    public function distroy($id, Request $request)
    {
        $new_product = NewProduct::findOrFail($id);

        ProductVariant::where('product_id', $id)->delete();
        $new_product->delete();

        Cache::forget('front_search_products');

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        $products = NewProduct::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($products);
    }
}
