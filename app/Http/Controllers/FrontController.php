<?php

namespace App\Http\Controllers;

use App\Models\BannerPhoto;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        // Build the query but do not execute it yet
        $products = Product::latest('id')->where('status', 1)->where('qty', '>=', 1)->with('product_image')->get();

        $categories = Category::latest('id')->get();
        $cartContent = Cart::content();
        $banner = BannerPhoto::where('status', 1)->first();

        $data = [];
        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        $data['banner'] = $banner;
        // dd($cartContent);
        return view('front.home', $data);
    }

    public function Product_details($slug)
    {
        $product = Product::where('slug', $slug)->with('product_image')->first();
        $categories = Category::latest('id')->get();
        $cartContent = Cart::content();

        // dd($product->title);
        $products = Product::latest('id')->where('status', 1)->where('qty', '>=', 1)->with('product_image')->get();

        // $categories = Category::latest('id')->get();

        $extra_products = Product::where('category_id', $product->category_id)->get();

        // dd($extra_products);

        // if (!empty($request->get('keyword'))) {
        //     $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        // }

        // $products = $query->paginate(20);

        $data = [];
        $data['product'] = $product;
        $data['extra_products'] = $extra_products;
        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;

        return view('front.product_details', $data);
    }

    public function shop_page($category_id)
    {
        $categories = Category::latest('id')->get();
        $selectedCategory = Category::where('id', $category_id)->first();
        $products = Product::where('category_id', $category_id)->where('status', 1)->with('product_image')->get();
        $cartContent = Cart::content();
        $brandList = Brand::get();

        // dd($selectedCategory);

        // if (!empty($request->get('keyword'))) {
        //     $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        // }

        // $products = $query->paginate(20);

        $data = [];
        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['selectedCategory'] = $selectedCategory;
        $data['cartContent'] = $cartContent;
        $data['brandList'] = $brandList;

        return view('front.shop', $data);
    }

    public function shop_page_offerZone($is_offered)
    {
        $categories = Category::latest('id')->get();
        $brandList = Brand::get();
        // $selectedCategory = Category::where('id', $category_id)->first();
        $products = Product::where('is_offered', $is_offered)->where('status', 1)->with('product_image')->get();
        $cartContent = Cart::content();

        // dd($selectedCategory);

        // if (!empty($request->get('keyword'))) {
        //     $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        // }

        // $products = $query->paginate(20);

        $data = [];
        $data['products'] = $products;
        $data['categories'] = $categories;
        // $data['selectedCategory'] = $selectedCategory;
        $data['cartContent'] = $cartContent;
        $data['brandList'] = $brandList;

        return view('front.shop', $data);
    }

    public function trackOrderPage()
    {
        $products = Product::latest('id')->where('status', 1)->where('qty', '>=', 1)->with('product_image')->get();

        $categories = Category::latest('id')->get();
        $cartContent = Cart::content();

        $data = [];
        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;

        return view('front.trac_order', $data);
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric',
        ]);

        $order = Order::where('order_id', $request->order_id)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'order_status' => $order->status,
            'name' => $order->name,
            'phone' => $order->phone,
        ]);
    }
}
