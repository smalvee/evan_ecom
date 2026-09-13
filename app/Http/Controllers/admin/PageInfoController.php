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

    /**
     * The About Us content is a single (singleton) record. Always update the
     * first existing row — the one the storefront reads via firstOrNew() — or
     * create id=1. (updateOrCreate(['id'=>1]) does not work because `id` is not
     * mass-assignable, which previously created duplicate rows.)
     */
    protected function aboutUsSingleton(): AboutUs
    {
        $about = AboutUs::orderBy('id')->first();

        if (!$about) {
            $about = new AboutUs();
            $about->id = 1;
        }

        return $about;
    }

    //store or update about us page content
    public function store_who_we_are(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $about = $this->aboutUsSingleton();
        $about->who_we_are = $request->input('description', $request->input('who_we_are'));
        $about->save();

        $request->session()->flash('success', 'Who We Are Updated');

        return response()->json([
            'status' => true,
            'message' => 'Who We Are Updated',
        ]);
    }

    public function store_our_mission(Request $request)
    {
        $about = $this->aboutUsSingleton();
        $about->our_mission = $request->input('description', $request->input('our_mission'));
        $about->save();

        $request->session()->flash('success', 'Our Mission Updated');

        return response()->json([
            'status' => true,
            'message' => 'Our Mission Updated',
        ]);
    }

    public function store_our_vission(Request $request)
    {
        $about = $this->aboutUsSingleton();
        $about->our_vision = $request->input('description', $request->input('our_vission'));
        $about->save();

        $request->session()->flash('success', 'Our Vision Updated');

        return response()->json([
            'status' => true,
            'message' => 'Our Vision Updated',
        ]);
    }

    public function store_refund_policy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $about = $this->aboutUsSingleton();
        $about->refund_policy = $request->input('description', $request->input('refund_policy'));
        $about->save();

        $request->session()->flash('success', 'Refund Policy Updated');

        return response()->json([
            'status' => true,
            'message' => 'Refund Policy Updated',
            'data' => $about,
        ]);
    }

    public function store_return_policy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $about = $this->aboutUsSingleton();
        $about->return_policy = $request->input('description', $request->input('return_policy'));
        $about->save();

        $request->session()->flash('success', 'Return Policy Updated');

        return response()->json([
            'status' => true,
            'message' => 'Return Policy Updated',
            'data' => $about,
        ]);
    }
}
