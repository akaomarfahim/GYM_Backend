<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gender;

/**
 * Class GenderController
 * @package App\Http\Controllers\Api
 *
 * @OA\Info(
 *     title="GYM API Documentation",
 *     version="1.0.0",
 *     description="GYM API Documentation for make connections to your application",
 *     @OA\Contact(
 *         email="",
 *         name="Brenbala"
 *     )
 * )
 */

class GenderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/genders",
     *     tags={"Genders"},
     *     summary="Get all genders",
     *     description="Returns a list of all genders.",
     *     operationId="getGenders",
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
        $genders = Gender::pluck('name');

        return response()->json(['genders' => $genders]);
    }
}