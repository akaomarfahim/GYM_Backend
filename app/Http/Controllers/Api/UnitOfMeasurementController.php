<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnitOfMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitOfMeasurementController extends Controller
{
    public function index()
    {
        $units = UnitOfMeasurement::all();

        return response()->json(['units' => $units]);
    }

    public function show(UnitOfMeasurement $unit)
    {
        return response()->json(['unit' => $unit]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:unit_of_measurements,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $unit = UnitOfMeasurement::create([
            'name' => $request->name,
        ]);

        return response()->json(['unit' => $unit], 201);
    }

    public function update(Request $request, UnitOfMeasurement $unit)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:unit_of_measurements,name,' . $unit->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $unit->update([
            'name' => $request->name,
        ]);

        return response()->json(['unit' => $unit]);
    }

    public function destroy(UnitOfMeasurement $unit)
    {
        $unit->delete();

        return response()->json(['message' => 'Unit deleted successfully']);
    }
}