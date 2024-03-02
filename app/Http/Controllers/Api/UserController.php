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
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string',
                'profile_picture' => 'nullable|string',
                'weight' => 'nullable|string',
                'height' => 'nullable|string',
                'physical_activity_level' => 'nullable|array',
                'goal' => 'nullable|array',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'profile_picture' => $request->profile_picture,
                'weight' => $request->weight,
                'height' => $request->height,
                'physical_activity_level' => $request->physical_activity_level,
                'goal' => $request->goal,
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
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string',
                'profile_picture' => 'nullable|string',
                'weight' => 'nullable|string',
                'height' => 'nullable|string',
                'physical_activity_level' => 'nullable|array',
                'goal' => 'nullable|array',
                'password' => 'nullable|string|min:8',
            ]);

            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'profile_picture' => $request->profile_picture,
                'weight' => $request->weight,
                'height' => $request->height,
                'physical_activity_level' => $request->physical_activity_level,
                'goal' => $request->goal,
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
