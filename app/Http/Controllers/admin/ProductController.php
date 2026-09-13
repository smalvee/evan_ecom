<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\NewProduct;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\VariationValues;
use Illuminate\Http\Request;

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
    }
}
