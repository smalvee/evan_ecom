<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class AdvertisementController extends Controller
{
    public function index()
    {
        $add_infos = Advertisement::first();
        return view('admin.advertisement.addvertise', compact('add_infos'));
    }

    public function store_01(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_01' => 'required',
            'url_01' => 'required',
        ]);

        if ($validator->passes()) {
            $add01 = Advertisement::updateOrCreate(
                ['id' => 1],
                [
                    'name_01' => $request->name_01,
                    'url_01' => $request->url_01,
                ],
            );

            $add01->save();

            // save imager here

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                // // Delete file from public/temp/thumd
                // $filePath = public_path('/uploads/add/' . $tempImage->name);
                // if (File::exists($filePath)) {
                //     File::delete($filePath);
                // }

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = 'add_01_' . now()->format('Ymd_His_u') . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/add/' . $newImageName;
                File::copy($spath, $dpath);

                $add01->image_01 = $newImageName;
                $add01->save();
            }

            $request->session()->flash('success', 'Add created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Advertisement added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function store_02(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_02' => 'required',
            'url_02' => 'required',

        ]);

        if ($validator->passes()) {
            $add01 = Advertisement::updateOrCreate(
                ['id' => 1],
                [
                    'name_02' => $request->name_02,
                    'url_02' => $request->url_02,
                ],
            );

            $add01->save();

            // save imager here

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                // // Delete file from public/temp/thumd
                // $filePath = public_path('/uploads/add/' . $tempImage->name);
                // if (File::exists($filePath)) {
                //     File::delete($filePath);
                // }

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = 'add_02_' . now()->format('Ymd_His_u') . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/add/' . $newImageName;
                File::copy($spath, $dpath);

                $add01->image_02 = $newImageName;
                $add01->save();
            }

            $request->session()->flash('success', 'Add created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Advertisement added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function store_03(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_03' => 'required',
            'url_03' => 'required',

        ]);

        if ($validator->passes()) {
            $add01 = Advertisement::updateOrCreate(
                ['id' => 1],
                [
                    'name_03' => $request->name_03,
                    'url_03' => $request->url_03,
                ],
            );

            $add01->save();

            // save imager here

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                // // Delete file from public/temp/thumd
                // $filePath = public_path('/uploads/add/' . $tempImage->name);
                // if (File::exists($filePath)) {
                //     File::delete($filePath);
                // }

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = 'add_03_' . now()->format('Ymd_His_u') . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/add/' . $newImageName;
                File::copy($spath, $dpath);

                $add01->image_03 = $newImageName;
                $add01->save();
            }

            $request->session()->flash('success', 'Add created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Advertisement added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function store_04(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_04' => 'required',
            'url_04' => 'required',

        ]);

        if ($validator->passes()) {
            $add01 = Advertisement::updateOrCreate(
                ['id' => 1],
                [
                    'name_04' => $request->name_04,
                    'url_04' => $request->url_04,

                ],
            );

            $add01->save();

            // save imager here

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                // // Delete file from public/temp/thumd
                // $filePath = public_path('/uploads/add/' . $tempImage->name);
                // if (File::exists($filePath)) {
                //     File::delete($filePath);
                // }

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = 'add_04_' . now()->format('Ymd_His_u') . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/add/' . $newImageName;
                File::copy($spath, $dpath);

                $add01->image_04 = $newImageName;
                $add01->save();
            }

            $request->session()->flash('success', 'Add created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Advertisement added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function store_05(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_05' => 'required',
            'url_05' => 'required',

        ]);

        if ($validator->passes()) {
            $add01 = Advertisement::updateOrCreate(
                ['id' => 1],
                [
                    'name_05' => $request->name_05,
                    'url_05' => $request->url_05,

                ],
            );

            $add01->save();

            // save imager here

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                // // Delete file from public/temp/thumd
                // $filePath = public_path('/uploads/add/' . $tempImage->name);
                // if (File::exists($filePath)) {
                //     File::delete($filePath);
                // }

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = 'add_05_' . now()->format('Ymd_His_u') . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/add/' . $newImageName;
                File::copy($spath, $dpath);

                $add01->image_05 = $newImageName;
                $add01->save();
            }

            $request->session()->flash('success', 'Add created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Advertisement added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function store_06(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_06' => 'required',
            'url_06' => 'required',

        ]);

        if ($validator->passes()) {
            $add01 = Advertisement::updateOrCreate(
                ['id' => 1],
                [
                    'name_06' => $request->name_06,
                    'url_06' => $request->url_06,
                ],
            );

            $add01->save();

            // save imager here

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                // // Delete file from public/temp/thumd
                // $filePath = public_path('/uploads/add/' . $tempImage->name);
                // if (File::exists($filePath)) {
                //     File::delete($filePath);
                // }

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = 'add_06_' . now()->format('Ymd_His_u') . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/add/' . $newImageName;
                File::copy($spath, $dpath);

                $add01->image_06 = $newImageName;
                $add01->save();
            }

            $request->session()->flash('success', 'Add created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Advertisement added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

        public function store_07(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_07' => 'required',
            'url_07' => 'required',

        ]);

        if ($validator->passes()) {
            $add01 = Advertisement::updateOrCreate(
                ['id' => 1],
                [
                    'name_07' => $request->name_06,
                    'url_07' => $request->url_07,
                ],
            );

            $add01->save();

            // save imager here

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);

                // // Delete file from public/temp/thumd
                // $filePath = public_path('/uploads/add/' . $tempImage->name);
                // if (File::exists($filePath)) {
                //     File::delete($filePath);
                // }

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = 'add_07_' . now()->format('Ymd_His_u') . '.' . $ext;
                $spath = public_path() . '/temp/' . $tempImage->name;
                $dpath = public_path() . '/uploads/add/' . $newImageName;
                File::copy($spath, $dpath);

                $add01->image_07 = $newImageName;
                $add01->save();
            }

            $request->session()->flash('success', 'Add created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Advertisement added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
}
