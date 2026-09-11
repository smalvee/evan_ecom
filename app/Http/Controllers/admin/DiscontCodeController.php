<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscontCodeController extends Controller
{
    public function index()
    {
        $coupons = DiscountCoupon::paginate(10);

        $data['coupons'] = $coupons;

        return view('admin.coupon.new_list', $data);
    }

    public function create()
    {
        return view('admin.coupon.new_create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $coupon = new DiscountCoupon();
            $coupon->code = $request->code;
            $coupon->name = $request->name;
            $coupon->description = $request->description;
            $coupon->max_uses = $request->max_uses;
            $coupon->max_uses_user = $request->max_uses_user;
            $coupon->type = $request->type;
            $coupon->discount_amount = $request->discount_amount;
            $coupon->min_amount = $request->min_amount;
            $coupon->status = $request->status;
            $coupon->starts_at = $request->starts_at;
            $coupon->expires_at = $request->expires_at;
            $coupon->save();

            $request->session()->flash('success', 'Coupon Charge added succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Coupon added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit() {}

    public function update() {}

    public function distroy() {}
}
