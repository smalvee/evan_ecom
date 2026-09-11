<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Testing\Fluent\Concerns\Has;
use Gloudemans\Shoppingcart\Facades\Cart;


class AuthController extends Controller
{
    public function dashboard()
    {
        $products = Product::latest('id')->where('status', 1)->where('qty', '>=', 1)->with('product_image')->get();
        $cartContent = Cart::content();
        $categories = Category::latest('id')->get();


        $user = Auth::user();

        $orders = Order::select('orders.*')->where('user_id', $user->id)->get();

        // dd($orders);

        $data['orders'] = $orders;
        $data['products'] = $products;
        $data['user'] = $user;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;

        return view('front.account.new_dashboard', $data);
    }

    public function orderDetails($orderId)
    {
        $products = Product::latest('id')->where('status', 1)->where('qty', '>=', 1)->with('product_image')->get();
        $categories = Category::latest('id')->get();
        $cartContent = Cart::content();



        $order = Order::where('id', $orderId)->first();
        $orderedItems = OrderItem::where('order_id', $order->id)->get();
        $user = Auth::user();

        $data = [];
        $data['order'] = $order;
        $data['user'] = $user;
        $data['orderedItems'] = $orderedItems;
        $data['categories'] = $categories;
        $data['products'] = $products;
        $data['cartContent'] = $cartContent;
        return view('front.account.new_order_details', $data);
    }

    public function login()
    {
        $products = Product::latest('id')->where('status', 1)->where('qty', '>=', 1)->with('product_image')->get();
        $categories = Category::latest('id')->get();
        $cartContent = Cart::content();


        $data['products'] = $products;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;

        return view('front.account.new_login', $data);
    }

    public function register()
    {
        $products = Product::latest('id')->where('status', 1)->where('qty', '>=', 1)->with('product_image')->get();
        $categories = Category::latest('id')->get();
        $cartContent = Cart::content();


        $data['categories'] = $categories;
        $data['products'] = $products;
        $data['cartContent'] = $cartContent;
        return view('front.account.new_register', $data);
    }

    public function processRegister(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[0-9]{10,15}$/|unique:users,phone',
            'password' => 'required|min:5|confirmed',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            $successMessage = 'Account Created Successfully';

            session()->flash('success', $successMessage);

            return response()->json([
                'status' => true,
                'message' => $successMessage,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^[0-9]{10,15}$/', $value)) {
                        $fail('The ' . $attribute . ' must be a valid email or phone number.');
                    }
                },
            ],
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.userLogin')->withErrors($validator)->withInput($request->only('login'));
        }

        $loginInput = $request->input('login');
        $password = $request->input('password');

        // Detect if input is email or phone
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$fieldType => $loginInput, 'password' => $password], $request->get('remember'))) {
            return redirect()->route('account.userDashboard')->withInput($request->only('login'));
        } else {
            session()->flash('error', 'Either Email/Phone or Password is Incorrect');
            return redirect()->route('account.userLogin')->withInput($request->only('login'));
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.userLogin')->with('success', 'Logout succefully');
    }
}
