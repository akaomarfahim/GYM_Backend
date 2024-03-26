<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
class GoalController extends Controller
{
    /**
     * Get all fitness goals
     *
     * @OA\Get(
     *     path="/api/goals",
     *     tags={"Goals"},
     *     summary="Get all fitness goals",
     *     description="Returns a list of all fitness goals.",
     *     operationId="getGoals",
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
     *
     */
    public function index()
    {
        $goals = Goal::pluck('name');

        return response()->json(['goals' => $goals]);
    }
}