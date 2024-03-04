<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\EmailVerificationOTP;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Generate OTP
        $otp = rand(10000, 99999);

        // Save the OTP in the user record or any temporary storage
        // You may use a separate table or cache to store OTPs temporarily
        // For simplicity, storing in the session for now
        $request->session()->put('email_verification_otp', $otp);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Send OTP via email
        $user->notify(new EmailVerificationOTP($otp));

        return response()->json(['message' => 'OTP sent to your email.'], 201);
    }

    public function verifyOtpAndRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Verify OTP
        $otpFromUser = $request->input('otp');
        $storedOtp = $request->session()->get('email_verification_otp');

        if ($otpFromUser != $storedOtp) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        // Clear the stored OTP
        $request->session()->forget('email_verification_otp');

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Mark the user as verified (you may have a 'verified' and 'email_verified_at' column in the users table)
        $user->update([
            'verified' => true,
            'email_verified_at' => Carbon::now()
        ]);

        // Log in the user and generate token
        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'message' => 'Registration successful.'], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'otp' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if ($request->has('otp')) {
            $user = User::where('email', $request->email)->first();

            // Verify OTP
            $otpFromUser = $request->input('otp');
            $storedOtp = $request->session()->get('email_verification_otp');

            if ($otpFromUser != $storedOtp) {
                return response()->json(['message' => 'Invalid OTP.'], 422);
            }

            // Clear the stored OTP
            $request->session()->forget('email_verification_otp');

            if (!$user->verified) {
                return response()->json(['message' => 'User not verified.'], 422);
            }

            // Mark the user as verified
            $user->update(['email_verified_at' => Carbon::now()]);
        } else {
            if (!Auth::attempt($request->only('email', 'password'))) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            if (!auth()->user()->verified) {
                auth()->logout();

                return response()->json(['message' => 'User not verified.'], 422);
            }
        }

        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
