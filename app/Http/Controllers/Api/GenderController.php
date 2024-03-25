<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Http\Request;

class GenderController extends Controller
{
    public function index()
    {
        $genders = Gender::all();

        return response()->json(['genders' => $genders]);
    }

    public function show(Gender $gender)
    {
        return response()->json(['gender' => $gender]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:genders,name',
            ]);

            $gender = Gender::create([
                'name' => $request->name,
            ]);

            return response()->json(['gender' => $gender], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Gender $gender)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:genders,name,' . $gender->id,
            ]);

            $gender->update([
                'name' => $request->name,
            ]);

            return response()->json(['gender' => $gender]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Gender $gender)
    {
        $gender->delete();

        return response()->json(['message' => 'Gender deleted successfully']);
    }
}