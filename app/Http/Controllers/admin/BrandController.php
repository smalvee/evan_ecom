<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::latest();

        if (!empty($request->get('keyword'))) {
            $categories = $brands->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $brands = $brands->paginate(10);

        return view('admin.brands.new_list', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.new_create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands',
        ]);

        if ($validator->passes()) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;

            $brand->save();

            $request->session()->flash('success', 'Brand created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand created Successfully',
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
        $brands = Brand::find($id);
        if (empty($brands)) {
            return redirect()->route('brands.index');
        }

        return view('admin.brands.new_edit', compact('brands'));
    }

    public function update($id, Request $request)
    {
        $brands = Brand::find($id);
        if (empty($brands)) {
            return redirect()->route('brands.index');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $brands->id . ',id',
        ]);

        if ($validator->passes()) {
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;

            $brands->save();

            $request->session()->flash('success', 'Brand updated succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand updated Successfully',
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
        $brands = Brand::find($id);
        if (empty($brands)) {
            return redirect()->route('brands.index');
        }

        $brands->delete();
        $request->session()->flash('success', 'Brand Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Brand Deleted Successfully',
        ]);
    }
}
