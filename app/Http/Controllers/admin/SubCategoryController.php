<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subcategories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')->latest('sub_categories.id')->leftJoin('categories', 'categories.id', 'sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $subcategories = $subcategories->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
            $subcategories = $subcategories->orwhere('categories.name', 'like', '%' . $request->get('keyword') . '%');
        }

        $subcategories = $subcategories->paginate(10);

        return view('admin.sub_category.new_list', compact('subcategories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();

        return view('admin.sub_category.new_create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $sub_category = new SubCategory();
            $sub_category->category_id = $request->category;
            $sub_category->name = $request->name;
            $sub_category->slug = $request->slug;
            $sub_category->status = $request->status;
            $sub_category->save();

            $request->session()->flash('success', 'Sub Category created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category created Successfully',
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
        $subcategories = SubCategory::find($id);
        if (empty($subcategories)) {
            $request->session()->flash('error', 'Record Not Found');
            return redirect()->route('sub-categories.index');
        }

        $categories = Category::orderBy('name', 'ASC')->get();

        $data['categories'] = $categories;
        $data['subcategories'] = $subcategories;

        return view('admin.sub_category.new_edit', $data);
    }

    public function update($id, Request $request)
    {
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            $request->session()->flash('error', 'Record Not Found');
            // return redirect()->route('sub-categories.index');
            return response([
                'status' => false,
                'notFound' => true,
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,' . $subCategory->id . ',id',
            'category' => 'required',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $subCategory->category_id = $request->category;
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->save();

            $request->session()->flash('success', 'Sub Category updated succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category updated Successfully',
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
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            $request->session()->flash('error', 'Record Not Found');
            // return redirect()->route('sub-categories.index');
            return response([
                'status' => false,
                'notFound' => true,
            ]);
        }

        $subCategory->delete();
        $request->session()->flash('success', 'Sub Category Deleted succesfully');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category Deleted Successfully',
        ]);
    }
}
