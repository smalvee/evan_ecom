<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    public function index()
    {
        $add_infos = Advertisement::firstOrNew();
        return view('admin.advertisement.addvertise', compact('add_infos'));
    }

    public function update(Request $request, $slot)
    {
        $slot = (int) $slot;

        if ($slot < 1 || $slot > 7) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid advertisement slot.',
            ]);
        }

        $nameKey = 'name_0' . $slot;
        $urlKey = 'url_0' . $slot;
        $imageKey = 'image_0' . $slot;

        $validator = Validator::make($request->all(), [
            $nameKey => 'required|string|max:255',
            $urlKey => 'required|url',
            'image_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $advertise = Advertisement::first();
        $existingImage = $advertise ? ($advertise->{$imageKey} ?? null) : null;

        if ($request->filled('image_id')) {
            $tempImage = TempImage::find($request->image_id);

            if (!$tempImage) {
                return response()->json([
                    'status' => false,
                    'errors' => ['image_id' => ['Invalid image. Please upload the image again.']],
                ]);
            }

            $extArray = explode('.', $tempImage->name);
            $ext = last($extArray);

            $newImageName = 'add_0' . $slot . '_' . now()->format('Ymd_His_u') . '.' . $ext;

            $sourcePath = public_path('temp/' . $tempImage->name);
            $destPath = public_path('uploads/add/' . $newImageName);

            if (!File::exists($sourcePath)) {
                return response()->json([
                    'status' => false,
                    'errors' => ['image_id' => ['Image file not found. Please upload the image again.']],
                ]);
            }

            File::copy($sourcePath, $destPath);
            $image = $newImageName;
        } else {
            if (empty($existingImage)) {
                return response()->json([
                    'status' => false,
                    'errors' => ['image_id' => ['Image is required.']],
                ]);
            }

            $image = $existingImage;
        }

        Advertisement::updateOrCreate(
            ['id' => 1],
            [
                $nameKey => $request->input($nameKey),
                $urlKey => $request->input($urlKey),
                $imageKey => $image,
            ],
        );

        $request->session()->flash('success', 'Advertisement updated successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Advertisement updated successfully.',
            'name' => $request->input($nameKey),
            'image' => asset('uploads/add/' . $image),
        ]);
    }
}
