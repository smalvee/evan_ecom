<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UniteController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('admin.units.unit', compact('units'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            's_name' => 'required|unique:units',
        ]);

        if ($validator->passes()) {
            $units = new Unit();
            $units->name = $request->name;
            $units->s_name = $request->s_name;
            $units->save();

            // $request->session()->flash('success', 'Brand created succesfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand created Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::find($id);

        if (empty($unit)) {
            return redirect()->route('units.index');
        }
        // Validate request
        $validator = Validator::make($request->all(), [
            'edit_name' => 'required',
            'edit_s_name' => 'required',
        ]);

        if ($validator->passes()) {
            $unit->name = $request->edit_name;
            $unit->s_name = $request->edit_s_name;
            $unit->save();

            return redirect()->back()->with('success', 'Unit updated successfully!');
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function distroy($id, Request $request)
    {
        $unit = Unit::find($id);
        if (empty($unit)) {
            return redirect()->route('units.index');
        }

        $unit->delete();
        return redirect()->back()->with('success', 'Unit Deleted successfully!');
    }
}
