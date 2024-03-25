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
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'age' => 'nullable|integer',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|integer',
            'physicalActivityLevel' => 'nullable|integer',
            'goals' => 'nullable|array',
            'registrationType' => 'nullable|string',
            'userType' => 'nullable|string',
            'password' => 'nullable|string|min:8',
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
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'physicalActivityLevel' => $request->physicalActivityLevel,
            'goals' => $request->goals,
            'registrationType' => $request->registrationType,
            'userType' => $request->userType,
            'password' => Hash::make($request->password),
        ]);

        // Send OTP via email
        $user->notify(new EmailVerificationOTP($otp));

        return response()->json(['message' => 'OTP sent to your email.'], 201);
    }

    public function registerWithoutPass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $user = User::create([
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'User registered successfully.'], 201);
    }

    public function loginWithoutPass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid email or user not verified.'], 422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['Bearer' => $token], 200);
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

        // Mark the user as verified (you may have a 'verified' and 'emailVerifiedAt' column in the users table)
        $user->update([
            'verified' => true,
            'emailVerifiedAt' => Carbon::now()
        ]);

        // Log in the user and generate token
        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['Bearer' => $token, 'message' => 'Registration successful.'], 201);
    }

    public function verifyPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();
        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Registration successful.'], 201);
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

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!$user->verified && !$request->has('otp')) {
            // Resend OTP via email
            $otp = rand(10000, 99999);
            $request->session()->put('email_verification_otp', $otp);
            $user->notify(new EmailVerificationOTP($otp));
            return response()->json(['message' => 'User not verified. OTP sent to your email.'], 422);
        }

        if ($request->has('otp')) {
            // $user = User::where('email', $request->email)->first();

            // Verify OTP
            $otpFromUser = $request->input('otp');
            $storedOtp = $request->session()->get('email_verification_otp');

            if ($otpFromUser != $storedOtp) {
                return response()->json(['message' => 'Invalid OTP.'], 422);
            }

            if (!$user->verified) {
                return response()->json(['message' => 'User not verified.'], 422);
            }

            // Mark the user as verified
            $user->update(['emailVerifiedAt' => Carbon::now()]);
        } else {
            // if (!Auth::attempt($request->only('email', 'password'))) {
            //     throw ValidationException::withMessages([
            //         'email' => ['The provided credentials are incorrect.'],
            //     ]);
            // }

            // Attempt authentication with provided email and password
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Incorrect credentials.'], 401);
            }

            if (!$user->verified) {
                // Resend OTP via email
                $otp = rand(10000, 99999);
                $request->session()->put('email_verification_otp', $otp);
                $user->notify(new EmailVerificationOTP($otp));
                auth()->logout();
                return response()->json(['message' => 'User not verified. OTP sent to your email.'], 422);
            }
        }

        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json(['Bearer' => $token], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Generate OTP
        $otp = rand(10000, 99999);

        // Save the OTP in the user record or any temporary storage
        // You may use a separate table or cache to store OTPs temporarily
        // For simplicity, storing in the session for now
        $request->session()->put('password_reset_otp', $otp);

        // Send OTP via email
        $user->notify(new EmailVerificationOTP($otp));

        return response()->json(['message' => 'OTP sent to your email.'], 200);
    }

    public function verifyPasswordResetOTP(Request $request)
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
        $storedOtp = $request->session()->get('password_reset_otp');

        if ($otpFromUser != $storedOtp) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        // Clear the stored OTP
        $request->session()->forget('password_reset_otp');

        // Return success response if OTP is verified
        return response()->json(['message' => 'OTP verified successfully.'], 200);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'confirmPassword' => 'required|string|same:password',
        ], [
            'confirmPassword.same' => 'The password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password updated successfully.'], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}