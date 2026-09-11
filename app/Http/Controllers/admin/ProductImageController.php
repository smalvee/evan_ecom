<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class ProductImageController extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        // dd($productImage->product_id);
        $productImage->image = 'NULL';
        $productImage->save();

        $imageName = $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
        $productImage->image = $imageName;
        $productImage->save();

        // large Image
        $destPath = public_path() . '/uploads/products/large/' . $imageName;

        $image = Image::read($sourcePath)
            ->scale(width: 1400) // height is calculated automatically
            ->save($destPath);

        // small Image
        $destPath = public_path() . '/uploads/products/small/' . $imageName;
        $image = Image::read($sourcePath)->cover(300, 300)->save($destPath);

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'ImagePath' => asset('uploads/products/small/' . $productImage->image),
            'message' => 'Image saved successfully',
        ]);
    }

    public function distroy(Request $request)
    {
        $productImage = ProductImage::find($request->id);

        if (empty($productImage)) {
            return response()->json([
            'status' => false,
            'message' => 'Image not found',
        ]);
        }

        File::delete(public_path('uploads/products/large/' . $productImage->image));
        File::delete(public_path('uploads/products/small/' . $productImage->image));
        $productImage->delete();

          return response()->json([
            'status' => true,
            'message' => 'Image Deleted successfully',
        ]);
    }
}
