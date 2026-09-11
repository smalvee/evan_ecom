<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('keyword')) {
            $keyword = $request->get('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $users = $query->paginate(10);

        return view('admin.user.list', compact('users'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'role' => 'required',
            'password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($request->password == $request->confirm_password) {
            if ($validator->passes()) {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->status = $request->status;
                $user->role = $request->role;
                $user->password = Hash::make($request->password);
                $user->save();

                $request->session()->flash('success', 'User added succesfully');

                return response()->json([
                    'status' => true,
                    'message' => 'User added succesfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Both Password not mached',
            ]);
        }
    }

    public function edit($user_id)
    {
        $user = User::find($user_id);
        if (empty($user)) {
            return redirect()->route('users.index');
        }

        return view('admin.user.edit', compact('user'));
    }

    public function update($user_id, Request $request)
    {
        $user = User::find($user_id);
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
            'role' => 'required',
            'password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->passes()) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->role = $request->role;
            $user->password = Hash::make($request->password);
            $user->save();

            $request->session()->flash('success', 'User updated succesfully');

            return response()->json([
                'status' => true,
                'message' => 'User updated succesfully',
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
        $user = User::find($id);
        if (empty($user)) {
            $request->session()->flash('error', 'User not Found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not found',
            ]);
        }

        $user->delete();

        $request->session()->flash('success', 'User deleted succefully');

        return response()->json([
            'status' => true,
            'message' => 'User deleted succefully',
        ]);
    }
}
