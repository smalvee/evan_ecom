<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create()
    {
        $shippingCharge = ShippingCharge::all();

        $data['shippingCharge'] = $shippingCharge;

        return view('admin.shipping.create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'district' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->passes()) {
            $shipping = new ShippingCharge();
            $shipping->district = $request->district;
            $shipping->location = $request->district;
            $shipping->amount = $request->amount;
            $shipping->save();

            $request->session()->flash('success', 'Shipping Charge added succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Shipping Charge added succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($id)
    {
        $shippingCharge = ShippingCharge::find($id);
        $data['shippingCharge'] = $shippingCharge;

        return view('admin.shipping.edit', $data);
    }

    public function update($id, Request $request)
    {
        $shippingCharge = ShippingCharge::find($id);
        if (empty($shippingCharge)) {
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Shipping method not found',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'district' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->passes()) {
            $shippingCharge->district = $request->district;
            $shippingCharge->location = $request->district;
            $shippingCharge->amount = $request->amount;
            $shippingCharge->save();

            $request->session()->flash('success', 'Shipping Charge Updated succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Shipping Charge Updated succesfully',
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
        $ShippingCharge = ShippingCharge::find($id);
        if (empty($ShippingCharge)) {
            $request->session()->flash('error', 'Category not Found');
            return response()->json([
                'status' => true,
                'message' => 'ShippingCharge Not found',
            ]);
        }


        $ShippingCharge->delete();

        $request->session()->flash('success', 'ShippingCharge deleted succefully');

        return response()->json([
            'status' => true,
            'message' => 'ShippingCharge deleted succefully',
        ]);
    }
}
