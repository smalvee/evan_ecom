<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\NewProduct;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class ImageController extends Controller
{
    public function get_product_list(Request $request)
    {
        $query = ProductVariant::latest('id')->with('product');

        if ($request->filled('keyword')) {
            $keyword = $request->get('keyword');

            $query->where(function ($q) use ($keyword) {
                $q->where('sku', 'like', "%{$keyword}%")->orWhereHas('product', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            });
        }

        // paginate AFTER filters
        $products = $query->paginate(10);

        return view('admin.product_image_manager.product_list', compact('products'));
    }

    public function edit($id)
    {
        $product_info = ProductVariant::where('id', $id)->first();

        return view('admin.product_image_manager.edit', compact('product_info'));
    }

    public function store(Request $request)
    {
        $rules = [
            'variant_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            // Save Galary pics
            if (!empty($request->gallery_image_ids)) {
                foreach ($request->gallery_image_ids as $temp_image_id) {
                    $tempImageInfo = TempImage::find($temp_image_id);
                    if (!$tempImageInfo) {
                        continue;
                    }

                    $ext = pathinfo($tempImageInfo->name, PATHINFO_EXTENSION);

                    $productImage = new ProductImage();
                    $productImage->product_id = $request->variant_id;
                    $productImage->is_thumb = 0; // 👈 gallery
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $request->variant_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    $sourcePath = public_path('temp/' . $tempImageInfo->name);

                    Image::read($sourcePath)
                        ->scale(width: 1400)
                        ->save(public_path('uploads/products/large/' . $imageName));

                    Image::read($sourcePath)
                        ->cover(300, 300)
                        ->save(public_path('uploads/products/small/' . $imageName));
                }
            }

            // Save thumbnail pics
            if (!empty($request->thumb_image_id)) {
                $thumb_image = ProductImage::where('product_id', $request->variant_id)->where('is_thumb', 1)->first();

                if (!empty($thumb_image)) {
                    // Delete file from public/temp
                    $filePath = public_path('uploads/products/thumb/' . $thumb_image->image);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }

                $tempImageInfo = TempImage::find($request->thumb_image_id);
                if ($tempImageInfo) {
                    $ext = pathinfo($tempImageInfo->name, PATHINFO_EXTENSION);

                    // Optional: delete previous thumb
                    ProductImage::where('product_id', $request->variant_id)->where('is_thumb', 1)->delete();

                    $productImage = new ProductImage();
                    $productImage->product_id = $request->variant_id;
                    $productImage->is_thumb = 1; // 👈 THUMB
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $request->variant_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    $sourcePath = public_path('temp/' . $tempImageInfo->name);

                    Image::read($sourcePath)
                        ->scale(width: 1400)
                        ->save(public_path('uploads/products/thumb/' . $imageName));
                }
            }

            $request->session()->flash('success', 'Product Image Added successfully');

            \Illuminate\Support\Facades\Cache::forget('front_search_products');

            return response()->json([
                'status' => true,
                'message' => 'Product Image added successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function distroy($id)
    {
        $galery_image = ProductImage::find($id);

        if (!$galery_image) {
            return response()->json(['success' => false, 'message' => 'Image not found']);
        }

        // Delete file from public/temp
        $filePath = public_path('uploads/products/small/' . $galery_image->image);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete file from public/temp/thumd
        $filePath = public_path('uploads/products/large/' . $galery_image->image);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete record from DB
        $galery_image->delete();

        \Illuminate\Support\Facades\Cache::forget('front_search_products');

        return response()->json([
            'status' => true,
            'message' => 'Product Image Deleted successfully',
        ]);
    }
}
