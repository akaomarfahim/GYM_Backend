<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnitOfMeasurement;

class UnitOfMeasurementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/units-of-measurement",
     *     tags={"Units of Measurement"},
     *     summary="Get all units of measurement",
     *     description="Returns a list of all units of measurement.",
     *     operationId="getUnitsOfMeasurement",
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
        $units = UnitOfMeasurement::pluck('name');
        
        return response()->json(['units' => $units]);
    }
}