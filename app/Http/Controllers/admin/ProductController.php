<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\NewProduct;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\VariationValues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = NewProduct::latest('id')->with('product_variation');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query
                ->leftJoin('categories', 'categories.id', '=', 'new_products.cat_id')
                ->leftJoin('sub_categories', 'sub_categories.id', '=', 'new_products.sub_cat_id')
                ->where(function ($q) use ($keyword) {
                    $q->where('new_products.name', 'like', "%{$keyword}%")
                        ->orWhere('new_products.sku', 'like', "%{$keyword}%")
                        ->orWhere('categories.name', 'like', "%{$keyword}%")
                        ->orWhere('sub_categories.name', 'like', "%{$keyword}%");
                })
                ->select('new_products.*');
        }

        // paginate AFTER filters
        $products = $query->paginate(20)->appends([
            'keyword' => $request->keyword,
        ]);

        return view('admin.products.new_list', compact('products'));
    }

    public function create()
    {
        $variations = Variation::all();
        $variation_value = VariationValues::all();
        $units = Unit::all();
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data = [];
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['variations'] = $variations;
        $data['variation_value'] = $variation_value;
        $data['units'] = $units;

        return view('admin.products.new_create', $data);
        // return view('admin.products.create', $data);
    }

    public function store(Request $request)
    {
        // dd($request->image_array);
        // exit();

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if ($request->track_qty === 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->is_offered = $request->is_offered;

            $product->save();

            // Save Galary pics
            if (!empty($request->image_array)) {
                foreach ($request->image_array as $temp_image_id) {
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.', $tempImageInfo->name);
                    $ext = last($extArray);

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    // Generate Product Thumbnail

                    // large Image

                    $sourcePath = public_path() . '/temp/' . $tempImageInfo->name;
                    $destPath = public_path() . '/uploads/products/large/' . $imageName;

                    $image = Image::read($sourcePath)
                        ->scale(width: 1400) // height is calculated automatically
                        ->save($destPath);

                    // small Image
                    $destPath = public_path() . '/uploads/products/small/' . $imageName;
                    $image = Image::read($sourcePath)->cover(300, 300)->save($destPath);
                }
            }

            $request->session()->flash('success', 'Product added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product added successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $product = Product::find($id);

        if (empty($product)) {
            return redirect()->route('products.index')->with('error', 'Product Not Found');
        }

        // product Image fetch
        $productImages = ProductImage::where('product_id', $product->id)->get();

        $subCategories = SubCategory::where('category_id', $product->category_id)->get();
        // dd($subCategories);

        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data = [];
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $data['productImages'] = $productImages;

        return view('admin.products.edit', $data);
    }

    public function update($id, Request $request)
    {
        $product = Product::find($id);

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,' . $product->id . ',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,' . $product->id . ',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if ($request->track_qty === 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->is_offered = $request->is_offered;

            $product->save();

            // Save Galary pics

            $request->session()->flash('success', 'Product updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function distroy($id, Request $request)
    {
        $product = Product::find($id);

        if (empty($product)) {
            return response()->json([
                'status' => false,
                'notfound' => true,
            ]);
        }

        $productImages = ProductImage::where('product_id', $id)->get();

        if (!empty($productImages)) {
            foreach ($productImages as $productImage) {
                File::delete(public_path('uploads/products/large/' . $productImage->image));
                File::delete(public_path('uploads/products/small/' . $productImage->image));
            }

            ProductImage::where('product_id', $id)->delete();
        }
        $product->delete();

        $request->session()->flash('success', 'Product deleted successfully');

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}
