<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserType;

class UserTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-types",
     *     tags={"User Types"},
     *     summary="Get all user types",
     *     description="Returns a list of all user types.",
     *     operationId="getUserTypes",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
    public function index()
    {
        $userTypes = UserType::pluck('name');
        return response()->json(['user_types' => $userTypes]);
    }
}