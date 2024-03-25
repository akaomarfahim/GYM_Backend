<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserType;
use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    public function index()
    {
        $userTypes = UserType::all();
        return response()->json(['user_types' => $userTypes]);
    }

    public function show(UserType $userType)
    {
        return response()->json(['user_type' => $userType]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:user_types,name',
        ]);

        $userType = UserType::create([
            'name' => $request->name,
        ]);

        return response()->json(['user_type' => $userType], 201);
    }

    public function update(Request $request, UserType $userType)
    {
        $request->validate([
            'name' => 'required|string|unique:user_types,name,' . $userType->id,
        ]);

        $userType->update([
            'name' => $request->name,
        ]);

        return response()->json(['user_type' => $userType]);
    }

    public function destroy(UserType $userType)
    {
        $userType->delete();
        return response()->json(['message' => 'User type deleted successfully']);
    }
}
