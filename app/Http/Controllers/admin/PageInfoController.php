<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PageInfoController extends Controller
{
    public function ViewAboutUs()
    {
        $cartContent = Cart::content();
        $about_us = AboutUs::firstOrNew();
        $categories = Category::latest('id')->get();

        $user = Auth::user();
        $customerAddress = null; // default value

        if ($user) {
            $customerAddress = CustomerAddress::where('user_id', $user->id)->first();
            // dd($customerAddress); // only for debugging
        }

        $shippingCharge = ShippingCharge::all();

        $data['cartContent'] = $cartContent;
        $data['customerAddress'] = $customerAddress;
        $data['shippingCharge'] = $shippingCharge;
        $data['categories'] = $categories;
        $data['about_us'] = $about_us;

        return view('front.pages.about_us', $data);
    }

    public function ViewReturnPolicy()
    {
        $cartContent = Cart::content();
        $categories = Category::latest('id')->get();
        $about_us = AboutUs::firstOrNew();

        $user = Auth::user();
        $customerAddress = null; // default value

        if ($user) {
            $customerAddress = CustomerAddress::where('user_id', $user->id)->first();
            // dd($customerAddress); // only for debugging
        }

        $shippingCharge = ShippingCharge::all();

        $data['cartContent'] = $cartContent;
        $data['customerAddress'] = $customerAddress;
        $data['shippingCharge'] = $shippingCharge;
        $data['categories'] = $categories;
        $data['about_us'] = $about_us;
        return view('front.pages.return_policy', $data);
    }

    public function ViewRefundPolicy()
    {
        $cartContent = Cart::content();
        $categories = Category::latest('id')->get();

        $about_us = AboutUs::firstOrNew();


        $user = Auth::user();
        $customerAddress = null; // default value

        if ($user) {
            $customerAddress = CustomerAddress::where('user_id', $user->id)->first();
            // dd($customerAddress); // only for debugging
        }

        $shippingCharge = ShippingCharge::all();

        $data['cartContent'] = $cartContent;
        $data['customerAddress'] = $customerAddress;
        $data['shippingCharge'] = $shippingCharge;
        $data['categories'] = $categories;
        $data['about_us'] = $about_us;
        return view('front.pages.refund_policy', $data);
    }

    public function displayaboutus()
    {
        $about_us = AboutUs::firstOrNew();

        // dd($about_us);

        return view('admin.page_info.new_aboutus', compact('about_us'));
    }

      public function displayrefund()
    {
        $about_us = AboutUs::firstOrNew();

        // dd($about_us);

        return view('admin.page_info.new_refund', compact('about_us'));
    }

         public function displayreturn()
    {
        $about_us = AboutUs::firstOrNew();

        // dd($about_us);

        return view('admin.page_info.new_return', compact('about_us'));
    }

    //store or update about us page content
    public function store_who_we_are(Request $request)
    {
        $data = AboutUs::updateOrCreate(
            ['id' => 1], // condition: look for record with id = 1
            ['who_we_are' => $request->who_we_are], // update or insert this value
        );

        if ($data) {
            $request->session()->flash('success', 'Who We Are Updated');

            return response()->json([
                'status' => true,
                'message' => 'Who We Are Updated',
            ]);
        }
    }

    public function store_our_mission(Request $request) {

          $data = AboutUs::updateOrCreate(
            ['id' => 1], // condition: look for record with id = 1
            ['our_mission' => $request->our_mission], // update or insert this value
        );

        if ($data) {
            $request->session()->flash('success', 'Our Mission Updated');

            return response()->json([
                'status' => true,
                'message' => 'Our Mission Updated',
            ]);
        }
    }

    public function store_our_vission(Request $request) {

         $data = AboutUs::updateOrCreate(
            ['id' => 1], // condition: look for record with id = 1
            ['our_vision' => $request->our_vission], // update or insert this value
        );

        if ($data) {
            $request->session()->flash('success', 'Our Vission Updated');

            return response()->json([
                'status' => true,
                'message' => 'Our Vission Updated',
            ]);
        }
    }

      public function store_refund_policy(Request $request) {

         $data = AboutUs::updateOrCreate(
            ['id' => 1], // condition: look for record with id = 1
            ['refund_policy' => $request->refund_policy], // update or insert this value
        );

        if ($data) {
            $request->session()->flash('success', 'Refund Policy Updated');

            return response()->json([
                'status' => true,
                'message' => 'Refund Policy Updated',
                'data' => $data,
            ]);
        }
    }


          public function store_return_policy(Request $request) {

         $data = AboutUs::updateOrCreate(
            ['id' => 1], // condition: look for record with id = 1
            ['return_policy' => $request->return_policy], // update or insert this value
        );

        if ($data) {
            $request->session()->flash('success', 'Refund Policy Updated');

            return response()->json([
                'status' => true,
                'message' => 'Refund Policy Updated',
                'data' => $data,
            ]);
        }
    }
}
