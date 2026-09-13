<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Testing\Fluent\Concerns\Has;
use Gloudemans\Shoppingcart\Facades\Cart;


class AuthController extends Controller
{
    public function dashboard()
    {
        $cartContent = Cart::content();
        $categories = Category::latest('id')->get();


        $user = Auth::user();

        $orders = Order::select('orders.*')->with('items')->where('user_id', $user->id)->get();

        // dd($orders);

        $data['orders'] = $orders;
        $data['user'] = $user;
        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;

        return view('front.account.new_dashboard', $data);
    }

    public function orderDetails($orderId)
    {
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
        $data['cartContent'] = $cartContent;
        return view('front.account.new_order_details', $data);
    }

    public function login()
    {
        $categories = Category::latest('id')->get();
        $cartContent = Cart::content();


        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;

        return view('front.account.new_login', $data);
    }

    public function register()
    {
        $categories = Category::latest('id')->get();
        $cartContent = Cart::content();


        $data['categories'] = $categories;
        $data['cartContent'] = $cartContent;
        return view('front.account.new_register', $data);
    }

    public function processRegister(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[0-9]{10,15}$/',
            'password' => 'required|min:5|confirmed',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // A phone may already exist as a guest account created during guest checkout.
        // Only such guest accounts may be claimed; real registered accounts are protected.
        $existing = User::where('phone', $request->phone)->first();

        if ($existing && !Str::startsWith((string) $existing->email, 'guest_')) {
            return response()->json([
                'status' => false,
                'errors' => ['phone' => ['This phone number is already registered.']],
            ]);
        }

        $user = $existing ?: new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role = 1;
        $user->password = Hash::make($request->password);
        $user->save();

        $successMessage = 'Account Created Successfully';

        session()->flash('success', $successMessage);

        return response()->json([
            'status' => true,
            'message' => $successMessage,
        ]);
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
