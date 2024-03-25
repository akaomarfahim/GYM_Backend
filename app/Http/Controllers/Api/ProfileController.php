<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return response()->json($user);
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'firstName' => 'nullable|string|max:255',
                'lastName' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string',
                'profilePicture' => 'nullable|string',
                'gender' => 'nullable|integer',
                'age' => 'nullable|integer',
                'height' => 'nullable|double',
                'weight' => 'nullable|integer',
                'weightType' => 'nullable|integer',
                'physicalActivityLevel' => 'nullable|array',
                'goals' => 'nullable|array',
                'newPassword' => 'nullable|min:8|different:oldPassword',
                'confirmPassword' => 'nullable|same:new_password',
            ]);

            $data = $request->except(['old_password', 'newPassword', 'confirmPassword']);

            if ($request->filled('oldPassword') && $request->filled('newPassword')) {
                if (!\Hash::check($request->input('oldPassword'), $user->password)) {
                    return response()->json(['error' => 'The old password is incorrect.'], 422);
                }

                $data['password'] = bcrypt($request->input('newPassword'));
            }

            $user->update($data);

            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}