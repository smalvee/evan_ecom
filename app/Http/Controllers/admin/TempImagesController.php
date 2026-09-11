<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;

class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;

        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();

            // unique filename with microtime
            $newName = str_replace('.', '', microtime(true)) . '.' . $ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path() . '/temp', $newName);

            // Generate thumb image (optional — do not fail the upload if image driver is unavailable)
            try {
                $sourcePath = public_path() . '/temp/' . $newName;
                $destPath = public_path() . '/temp/thumb/' . $newName;
                Image::read($sourcePath)->save($destPath);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Thumbnail generation failed: ' . $e->getMessage());
            }

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'ImagePath' => asset('/temp/' . $newName),
                'message' => 'Image uploaded successfully',
            ]);
        }
    }

    public function delete($id)
    {
        $temp_image = TempImage::find($id);

        if (!$temp_image) {
            return response()->json(['success' => false, 'message' => 'Image not found']);
        }

        // Delete file from public/temp
        $filePath = public_path('temp/' . $temp_image->name);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

          // Delete file from public/temp/thumd
        $filePath = public_path('temp/thumb/' . $temp_image->name);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete record from DB
        $temp_image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }
}
