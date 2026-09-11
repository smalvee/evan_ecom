<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BannerPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\TempImage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = BannerPhoto::get();

        // dd($banner);

        $data['banners'] = $banners;

        return view('admin.banners.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_id' => 'required|integer',
        ]);

        // find temp image
        $tempImageInfo = TempImage::find($request->image_id);
        if (!$tempImageInfo) {
            return response()->json([
                'status' => false,
                'errors' => ['image_id' => 'Invalid image. Please try again.'],
            ]);
        }

        // extract extension
        $extArray = explode('.', $tempImageInfo->name);
        $ext = last($extArray);

        // create banner record
        $banner = new BannerPhoto();
        $banner->image = 'NULL';
        $banner->save();

        // generate final name
        $imageName = 'banner-' . $banner->id . '-' . time() . '.' . $ext;
        $banner->image = $imageName;
        $banner->status = 1;
        $banner->save();

        // define paths
        $sourcePath = public_path('temp/' . $tempImageInfo->name);
        $destPath = public_path('uploads/banners/' . $imageName);

        // make sure destination folder exists
        if (!File::exists(public_path('uploads/banners'))) {
            File::makeDirectory(public_path('uploads/banners'), 0777, true);
        }

        // ✅ move original file (no resize)
        File::move($sourcePath, $destPath);

        // delete temp record
        $tempImageInfo->delete();

        return response()->json([
            'status' => true,
            'message' => 'Banner added successfully',
            'data' => [
                [
                    'id' => $banner->id,
                    'image_url' => asset('uploads/banners/' . $banner->image),
                    'created_at' => $banner->created_at->format('Y-m-d H:i:s'),
                ],
            ],
        ]);
    }

    // app/Http/Controllers/BannerController.php
    public function toggleStatus(Request $request)
    {
        $id = $request->input('id');

        // Deactivate all banners first
        BannerPhoto::query()->update(['status' => 0]);

        // Activate the selected banner if it exists
        $banner = BannerPhoto::find($id);
        if ($banner) {
            $banner->status = 1;
            $banner->save();
        }

        return response()->json(['success' => true]);
    }

    // app/Http/Controllers/BannerController.php
    public function destroy($id, Request $request)
    {
        $banner = BannerPhoto::find($id);
        if (empty($banner)) {
            $request->session()->flash('error', 'Banner not Found');
            return response()->json([
                'status' => true,
                'message' => 'Banner Not found',
            ]);
        }

        // Delete Image
        File::delete(public_path() . '/uploads/banners/' . $banner->image);

        $banner->delete();

        $request->session()->flash('success', 'Banner deleted succefully');

        return response()->json([
            'status' => true,
            'message' => 'Banner deleted succefully',
        ]);
    }
}
