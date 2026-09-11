<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NewProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $numberOfTotalOrder = Order::count();
        $numberOfTotalCustomer = User::count();

        $numberOfTotalCategories = Category::count();
        $numberOfTotalProduct = NewProduct::count();

        $totalQuantity = Product::sum('qty');

        $totalPendingOrder = Order::where('status', 'pending')->count();
        $totalDeliveredOrder = Order::where('status', 'shipped')->count();
        $totalConfirmeddOrder = Order::where('status', 'confirm')->count();


        $quantityLess = Product::where('qty', '<=', 10)->count();

        $categories = Category::get();



        // dd($quantityLess);

        $data['numberOfTotalOrder'] = $numberOfTotalOrder;
        $data['numberOfTotalCustomer'] = $numberOfTotalCustomer;
        $data['numberOfTotalCategories'] = $numberOfTotalCategories;
        $data['numberOfTotalProduct'] = $numberOfTotalProduct;
        $data['totalQuantity'] = $totalQuantity;
        $data['totalPendingOrder'] = $totalPendingOrder;
        $data['totalDeliveredOrder'] = $totalDeliveredOrder;
        $data['quantityLess'] = $quantityLess;
        $data['totalConfirmeddOrder'] = $totalConfirmeddOrder;
        $data['categories'] = $categories;

        return view('admin.new_dashboard', $data);
        //  $admin = Auth::guard('admin')->user();
        // echo 'Welcome '.$admin->name.' <a href= "'.route('admin.logout').'">Logout</a>';
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}

 