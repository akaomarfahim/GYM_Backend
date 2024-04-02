<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"User Profile"},
     *     summary="Get authenticated user details",
     *     description="Returns the details of the authenticated user.",
     *     operationId="getUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     )
     * )
     */
    public function show()
    {
        $user = auth()->user();
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/profile/update",
     *     tags={"User Profile"},
     *     summary="Update user details",
     *     description="Updates the details of the authenticated user.",
     *     operationId="updateUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User details to update",
     *         @OA\JsonContent(
     *             required={"firstName", "lastName", "email", "phone", "profilePicture", "gender", "age", "height", "weight", "weightType", "physicalActivityLevel", "goals", "newPassword", "confirmPassword"},
     *             @OA\Property(property="firstName", type="string"),
     *             @OA\Property(property="lastName", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="profilePicture", type="string"),
     *             @OA\Property(property="gender", type="integer"),
     *             @OA\Property(property="age", type="integer"),
     *             @OA\Property(property="height", type="number", format="double"),
     *             @OA\Property(property="weight", type="integer"),
     *             @OA\Property(property="weightType", type="integer"),
     *             @OA\Property(property="physicalActivityLevel", type="integer"),
     *             @OA\Property(property="goals", type="array", @OA\Items(type="integer"), nullable=true),
     *             @OA\Property(property="newPassword", type="string", minLength=8),
     *             @OA\Property(property="confirmPassword", type="string", minLength=8),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="firstName", type="string"),
     *                 @OA\Property(property="lastName", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="profilePicture", type="string"),
     *                 @OA\Property(property="gender", type="integer"),
     *                 @OA\Property(property="age", type="integer"),
     *                 @OA\Property(property="height", type="number", format="double"),
     *                 @OA\Property(property="weight", type="integer"),
     *                 @OA\Property(property="weightType", type="integer"),
     *                 @OA\Property(property="physicalActivityLevel", type="integer"),
     *                 @OA\Property(property="goals", type="array", @OA\Items(type="integer"), nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
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
                'physicalActivityLevel' => 'nullable|integer',
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

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/upload-profile-picture",
     *     tags={"User Profile"},
     *     summary="Upload profile picture",
     *     description="Uploads a profile picture for the authenticated user.",
     *     operationId="uploadProfilePicture",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Profile picture to upload",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"profilePicture"},
     *                 @OA\Property(
     *                     property="profilePicture",
     *                     description="Profile picture file",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile picture uploaded successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="profilePicture", type="string", description="URL of the uploaded profile picture")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profilePicture' => 'required|image|max:2048',
        ]);

        // Handle the profile picture upload
        if ($request->hasFile('profilePicture')) {
            $image = $request->file('profilePicture');
            $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('img/profile_pictures'), $fileName);

            // Assuming 'profile_pictures' is a directory within your public directory
            $profilePictureUrl = url('img/profile_pictures/' . $fileName);

            // Update the user's record with the profile picture URL
            $user = User::find(auth()->id());
            $user->profilePicture = $profilePictureUrl;
            $user->save();

            return response()->json(['profilePicture' => $profilePictureUrl]);
        }

        return response()->json(['error' => 'Profile picture upload failed.'], 500);
    }

}