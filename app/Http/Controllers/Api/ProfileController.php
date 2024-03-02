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
        return response()->json(['user' => $user]);
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string',
                'profile_picture' => 'nullable|string',
                'weight' => 'nullable|string',
                'height' => 'nullable|string',
                'physical_activity_level' => 'nullable|array',
                'goal' => 'nullable|array',
                'new_password' => 'nullable|min:8|different:old_password',
                'confirm_password' => 'nullable|same:new_password',
            ]);

            $data = $request->except(['old_password', 'new_password', 'confirm_password']);

            if ($request->filled('old_password') && $request->filled('new_password')) {
                if (!\Hash::check($request->input('old_password'), $user->password)) {
                    return response()->json(['error' => 'The old password is incorrect.'], 422);
                }

                $data['password'] = bcrypt($request->input('new_password'));
            }

            $user->update($data);

            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
