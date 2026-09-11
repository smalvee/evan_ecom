<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Gloudemans\Shoppingcart\Facades\Cart;

class UserProfile extends Controller
{
    public function index()
    {
        $user = Auth::user();
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
        return view('front.account.profile', $data);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        $user = User::find($userId);
        if (empty($user)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'User not found',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id . ',id',
            'phone' => 'required|unique:users,phone,' . $user->id . ',id',
        ]);

        if ($validator->passes()) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role = 1;
            $user->save();

            $request->session()->flash('success', 'User profile updated succesfully');

            return response()->json([
                'status' => true,
                'message' => 'User profile updated succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'User not found',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required', // add confirmed rule if using confirmPassword field
            'confirmPassword' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // Check if old password matches
        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Old password does not match',
            ]);
        }

        // Update password
        $user->password = Hash::make($request->newPassword);
        $user->save();
            $request->session()->flash('success', 'User Password updated succesfully');


        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully',
        ]);
        
    }
}
