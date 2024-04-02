<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\EmailVerificationOTP;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     required={"firstName", "lastName", "email", "password"},
 *     @OA\Property(property="firstName", type="string"),
 *     @OA\Property(property="lastName", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="password", type="string", format="password", minLength=8),
 *     @OA\Property(property="age", type="integer"),
 *     @OA\Property(property="height", type="number", format="float"),
 *     @OA\Property(property="weight", type="integer"),
 *     @OA\Property(property="physicalActivityLevel", type="integer"),
 *     @OA\Property(property="goals", type="array", @OA\Items(type="integer"), nullable=true),
 *     @OA\Property(property="registrationType", type="string"),
 *     @OA\Property(property="userType", type="string"),
 * )
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Registers a new user.",
     *     operationId="register",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'age' => 'nullable|integer',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|integer',
            'physicalActivityLevel' => 'nullable|integer',
            'goals' => 'nullable|array',
            'registrationType' => 'nullable|string',
            'userType' => 'nullable|string',
            'password' => 'nullable|string|min:8',
        ]);

        $validator->setCustomMessages([
            'email.unique' => 'Email already taken.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Generate OTP
        $otp = rand(10000, 99999);

        $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'physicalActivityLevel' => $request->physicalActivityLevel,
            'goals' => $request->goals,
            'registrationType' => $request->registrationType,
            'userType' => $request->userType,
            'otp' => $otp,
            'password' => Hash::make($request->password),
        ]);

        // Send OTP via email
        $user->notify(new EmailVerificationOTP($otp));

        return response()->json(['message' => 'OTP sent to your email.'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/register-with-social",
     *     tags={"Non Password Authentication"},
     *     summary="Register a new user without password",
     *     description="Registers a new user without requiring a password.",
     *     operationId="registerWithoutPass",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User details",
     *         @OA\JsonContent(
     *             required={"firstName", "lastName", "email"},
     *             @OA\Property(property="firstName", type="string"),
     *             @OA\Property(property="lastName", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string", nullable=true),
     *             @OA\Property(property="age", type="integer", nullable=true),
     *             @OA\Property(property="height", type="number", format="float", nullable=true),
     *             @OA\Property(property="weight", type="integer", nullable=true),
     *             @OA\Property(property="physicalActivityLevel", type="integer", nullable=true),
     *             @OA\Property(property="goals", type="array",  @OA\Items(type="integer"), nullable=true),
     *             @OA\Property(property="registrationType", type="string", nullable=true),
     *             @OA\Property(property="userType", type="string", nullable=true),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, nullable=true),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="Bearer", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function registerWithoutPass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'age' => 'nullable|integer',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|integer',
            'physicalActivityLevel' => 'nullable|integer',
            'goals' => 'nullable|array',
            'registrationType' => 'nullable|string',
            'userType' => 'nullable|string',
            'password' => 'nullable|string|min:8',
        ]);

        $validator->setCustomMessages([
            'email.unique' => 'Email already taken.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'physicalActivityLevel' => $request->physicalActivityLevel,
            'goals' => $request->goals,
            'registrationType' => $request->registrationType,
            'userType' => $request->userType,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['Bearer' => $token, 'message' => 'Welcome aboard!'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login-with-social",
     *     tags={"Non Password Authentication"},
     *     summary="Login without Password",
     *     description="Login the user without requiring a password.",
     *     operationId="loginWithoutPass",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="Bearer", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
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

        return response()->json(['Bearer' => $token, 'message' => 'Welcome back'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users/verify",
     *     tags={"User OTP Verify"},
     *     summary="Verify OTP and Register",
     *     description="Verify OTP and register the user.",
     *     operationId="verifyOtpAndRegister",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email and OTP",
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="otp", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="Bearer", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
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
        $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        // Check if the user is already verified
        if (!$user->verified) {
            // Mark the user as verified and update email verification timestamp
            $user->update([
                'verified' => true,
                'emailVerifiedAt' => Carbon::now()
            ]);
        }

        // Log in the user and generate token
        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['Bearer' => $token, 'message' => 'Registration successful.'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/users/password/create",
     *     tags={"Authentication"},
     *     summary="Verify Password",
     *     description="Verify and update the user's password.",
     *     operationId="verifyPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email and new password",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Registration successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation Error")
     *         )
     *     )
     * )
     */
    public function verifyPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Registration successful'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login a user",
     *     description="Logs in a user.",
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="Bearer", type="string", description="Access token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=402,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=424,
     *         description="User not verified"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid OTP"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
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
            $user->update(['otp' => $otp]);
            $user->notify(new EmailVerificationOTP($otp));
            return response()->json(['message' => 'User not verified. OTP sent to your email.'], 422);
        }

        if ($request->has('otp')) {
            // $user = User::where('email', $request->email)->first();

            // Verify OTP
            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$user) {
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
                $user->update(['otp' => $otp]);
                $user->notify(new EmailVerificationOTP($otp));
                auth()->logout();
                return response()->json(['message' => 'User not verified. OTP sent to your email.'], 422);
            }
        }

        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json(['Bearer' => $token], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/password/reset",
     *     tags={"Reset Password"},
     *     summary="Reset user password",
     *     description="Resets the user password.",
     *     operationId="resetPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
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

        $user->update(['otp' => $otp]);

        // Send OTP via email
        $user->notify(new EmailVerificationOTP($otp));

        return response()->json(['message' => 'OTP sent to your email.'], 200);
    }

    // /**
    //  * @OA\Post(
    //  *     path="/api/password/verify-otp",
    //  *     tags={"Reset Password"},
    //  *     summary="Verify password reset OTP",
    //  *     description="Verifies the password reset OTP.",
    //  *     operationId="verifyPasswordResetOTP",
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         description="User email and OTP",
    //  *         @OA\JsonContent(
    //  *             required={"email", "otp"},
    //  *             @OA\Property(property="email", type="string", format="email"),
    //  *             @OA\Property(property="otp", type="string")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="OTP verified successfully",
    //  *         @OA\JsonContent(
    //  *             type="object",
    //  *             @OA\Property(property="message", type="string")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=422,
    //  *         description="Validation Error or Invalid OTP"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=500,
    //  *         description="Server Error"
    //  *     )
    //  * )
    //  */
    // public function verifyPasswordResetOTP(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'otp' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()->first()], 422);
    //     }

    //     // Verify OTP
    //     $user = User::where('email', $request->email)
    //         ->where('otp', $request->otp)
    //         ->first();

    //     if (!$user) {
    //         return response()->json(['message' => 'Invalid OTP.'], 422);
    //     }

    //     // Return success response if OTP is verified
    //     return response()->json(['message' => 'OTP verified successfully.'], 200);
    // }

    /**
     * @OA\Post(
     *     path="/api/password/update",
     *     tags={"Reset Password"},
     *     summary="Update user password",
     *     description="Updates the user's password.",
     *     operationId="updatePassword",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email, new password, and password confirmation",
     *         @OA\JsonContent(
     *             required={"email", "password", "confirmPassword"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", minLength=8),
     *             @OA\Property(property="confirmPassword", type="string", minLength=8)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/resend-otp",
     *     tags={"Authentication"},
     *     summary="Resend OTP",
     *     description="Resends the OTP to the user's email.",
     *     operationId="resendOtp",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="New OTP sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
    public function resendOtp(Request $request)
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

        // Generate new OTP
        $otp = rand(10000, 99999);

        // Save new OTP
        $user->update(['otp' => $otp]);

        // Send new OTP via email
        $user->notify(new EmailVerificationOTP($otp));

        return response()->json(['message' => 'New OTP sent to your email.'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout",
     *     description="Logs out the authenticated user and revokes access tokens.",
     *     operationId="logout",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}