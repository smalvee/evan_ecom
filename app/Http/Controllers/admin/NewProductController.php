<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\NewProduct;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\VariationValues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NewProductController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'slug' => 'required|string|unique:new_products,slug',
            'product_sku' => 'required|string|unique:new_products,sku',
            'units_id' => 'required',
            'brand_id' => 'required',
            'category' => 'required',
            'sub_category' => 'required',
            'product_type' => 'required',

            // Only required if product_type == 1
            'variation_sku' => 'required_if:product_type,1|array',
            'variation_sku.*' => 'required_if:product_type,1|string',
            'variation_values' => 'required_if:product_type,1|array',
            'variation_values.*' => 'required_if:product_type,1|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

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
            'free_delivery' => $request->boolean('free_delivery'),
        ]);

        // Single product default variant
        if ($request->product_type == 0) {
            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $request->product_sku,
                'is_variant' => 0,
            ]);
        }

        // Handle variable product variations
        if ($request->filled('variation_sku') && $request->filled('variation_values')) {
            foreach ($request->variation_sku as $index => $variationSku) {
                $variationJson = json_decode(urldecode($request->variation_values[$index]), true);

                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variationSku,
                    'is_variant' => 1,
                    'variation_values' => $variationJson ? json_encode($variationJson) : null,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Product added successfully',
        ]);
    }

    public function edit($id)
    {
        $product = NewProduct::find($id);

        //  $product = NewProduct::select('id', $id)->first();

        //  dd($product);

        if (empty($product)) {
            return redirect()->route('products.index')->with('error', 'Product Not Found');
        }

        // product Image fetch
        // $productImages = ProductImage::where('product_id', $product->id)->get();

        $subCategories = SubCategory::where('category_id', $product->cat_id)->get();
        $product_variation = ProductVariant::where('product_id', $product->id)->get();
        // dd($product_variation);
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

        // $data['productImages'] = $productImages;
        return view('admin.products.new_edit', $data);
    }

    public function update($id, Request $request)
    {
        $product = NewProduct::find($id);
        $rules = [
            'name' => 'required|string',
            'slug' => 'required|string|unique:new_products,slug,' . $product->id . ',id',
            'product_sku' => 'required|string|unique:new_products,sku,' . $product->id . ',id',
            'units_id' => 'required',
            'brand_id' => 'required',
            'category' => 'required',
            'sub_category' => 'required',
            'product_type' => 'required',

            // Only required if product_type == 1
            'variation_sku' => 'required_if:product_type,1|array', 
            'variation_sku.*' => 'required_if:product_type,1|string',
            'variation_values' => 'required_if:product_type,1',
            'variation_values.*' => 'required_if:product_type,1',
        ];

    

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $product = NewProduct::updateOrCreate(
            ['id' => $id],
            [
                'name' => $request->name,
                'slug' => $request->slug,
                'sku' => $request->product_sku,
                'unit_id' => $request->units_id,
                'brand_id' => $request->brand_id,
                'cat_id' => $request->category,
                'sub_cat_id' => $request->sub_category,
                'description' => $request->description,
                'type' => $request->product_type,
                'status' => $request->status,
                'hot_products' => $request->hot_products,
                'free_delivery' => $request->boolean('free_delivery'),
            ],
        );

        // Single product default variant
        if ($request->product_type == 0) {
            ProductVariant::updateOrCreate(
                ['product_id' => $id],
                [
                    'sku' => $request->product_sku,
                    'is_variant' => 0,
                ],
            );
        }

        // Handle variable product variations
        if ($request->filled('variation_sku') && $request->filled('variation_values')) {
            foreach ($request->variation_sku as $index => $variationSku) {
                $variationJson = json_decode(urldecode($request->variation_values[$index]), true);

                ProductVariant::firstOrCreate(
                    ['sku' => $variationSku], // check uniqueness on sku
                    [
                        'product_id' => $product->id,
                        'is_variant' => 1,
                        'variation_values' => $variationJson ? json_encode($variationJson) : null,
                    ],
                );
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Product added successfully',
        ]);
    }

    public function destroy_variant($id, Request $request)
    {
        $variant = ProductVariant::find($id);

        $variant->delete();
        return redirect()->back()->with('success', 'Unit Deleted successfully!');
    }

    public function distroy($id, Request $request)
    {
        // Find the product
        $new_product = NewProduct::findOrFail($id);

        // Delete all variants for this product
        ProductVariant::where('product_id', $id)->delete();

        // Delete the product itself
        $new_product->delete();

        // return redirect()->back()->with('success', 'Product and its variants deleted successfully!');

        return response()->json([
            'status' => true,
            'message' => 'Product added successfully',
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
