<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Variation;
use App\Models\VariationValues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VariationController extends Controller
{
    public function index()
    {
        $variations = Variation::with('values')->get();
        return view('admin.variations.variations', compact('variations'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'variations' => 'required|string|max:255',
                'values' => 'required|array|min:1',
                'values.*' => 'required|string|max:255|distinct',
            ],
            [
                'values.*.required' => 'This field is required.',
                'values.*.distinct' => 'Duplicate values are not allowed.',
            ],
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            $variation = Variation::create([
                'variations' => $request->variations,
            ]);

            foreach ($request->values as $value) {
                VariationValues::create([
                    'variation_id' => $variation->id,
                    'value' => $value,
                ]);
            }

            return response()->json([
                'status' => true,
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'variations' => 'required|string|max:255',
            'values' => 'required|array|min:1',
            'values.*' => 'required|string|max:255|distinct',
        ]);

        $variation = Variation::findOrFail($id);

        // Update variation name
        $variation->update([
            'variations' => $request->variations,
        ]);

        // Delete old values
        $variation->values()->delete();

        // Insert new values
        $variation->values()->createMany(array_map(fn($val) => ['value' => $val], $request->values));

        return back()->with('success', 'Variation updated.');
    }

    public function destroy($id)
    {
        $variation = Variation::findOrFail($id);

        // Delete all values automatically (because of cascade)
        $variation->delete();

        return back()->with('success', 'Variation deleted successfully.');
    }

    public function getValues(Request $request)
    {
        // dd($request->variation_id);
        if (!empty($request->variation_id)) {
            $variation_values = VariationValues::where('variation_id', $request->variation_id)->orderBy('value', 'ASC')->get();

            return response()->json([
                'status' => true,
                'variation_values' => $variation_values,
            ]);
        } else {
            return response()->json([
                'status' => true,
                'variation_values' => ['no'],
            ]);
        }
    }
}
