<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json(['users' => $users]);
    }

    public function show(User $user)
    {
        return response()->json(['user' => $user]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string',
                'profilePicture' => 'nullable|string',
                'gender' => 'nullable|integer',
                'age' => 'nullable|integer',
                'height' => 'nullable|double',
                'weight' => 'nullable|integer',
                'weightType' => 'nullable|integer',
                'physicalActivityLevel' => 'nullable|array',
                'goals' => 'nullable|array',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'phone' => $request->phone,
                'profilePicture' => $request->profilePicture,
                'gender' => $request->gender,
                'age' => $request->age,
                'height' => $request->height,
                'weight' => $request->weight,
                'weightType' => $request->weight,
                'physicalActivityLevel' => $request->physicalActivityLevel,
                'goals' => $request->goals,
                'password' => bcrypt($request->password),
            ]);

            return response()->json(['user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string',
                'profilePicture' => 'nullable|string',
                'gender' => 'nullable|integer',
                'age' => 'nullable|integer',
                'height' => 'nullable|double',
                'weight' => 'nullable|integer',
                'weightType' => 'nullable|integer',
                'physicalActivityLevel' => 'nullable|array',
                'goals' => 'nullable|array',
                'password' => 'required|string|min:8',
            ]);

            $user->update([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'phone' => $request->phone,
                'profilePicture' => $request->profilePicture,
                'gender' => $request->gender,
                'age' => $request->age,
                'height' => $request->height,
                'weight' => $request->weight,
                'weightType' => $request->weight,
                'physicalActivityLevel' => $request->physicalActivityLevel,
                'goals' => $request->goals,
                'password' => $request->has('password') ? bcrypt($request->password) : $user->password,
            ]);

            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
