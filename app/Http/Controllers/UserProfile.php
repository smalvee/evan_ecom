<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Hash;

class UserProfile extends Controller
{
    use ProvidesStorefrontData;

    public function index()
    {
        return view('front.account.new_profile', $this->storefrontData([
            'user' => Auth::user(),
        ]));
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
