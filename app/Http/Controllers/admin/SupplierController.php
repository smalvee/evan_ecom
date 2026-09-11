<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index()
    {
         $suppliers = Supplier::all();
        return view('admin.suppliers.supplierList' , compact('suppliers'));
    }

    public function create()
    {

       

        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:suppliers',
            'phone' => 'required|unique:suppliers',
            'address' => 'required',
        ]);

        if ($validator->passes()) {
            $supplier = new Supplier();
            $supplier->name = $request->name;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->address = $request->address;
            $supplier->save();

            $request->session()->flash('success', 'Supplier Created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Supplier Created succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($supplier_id)
    {
        $supplier_info = Supplier::where('id', $supplier_id)->first();
        return view('admin.suppliers.edit', compact('supplier_info'));
    }

    public function update($id, Request $request)
    {

        $supplier_info = Supplier::find($id);

         $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:suppliers,email,' . $supplier_info->id . ',id',
            'phone' => 'required|unique:suppliers,phone,' . $supplier_info->id . ',id',
            'address' => 'required',
        ]);

       
        if ($validator->passes()) {
            $supplier_info->name = $request->name;
            $supplier_info->email = $request->email;
            $supplier_info->phone = $request->phone;
            $supplier_info->address = $request->address;
            $supplier_info->save();

            $request->session()->flash('success', 'Supplier updated succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Supplier updated succesfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }


    }
}
