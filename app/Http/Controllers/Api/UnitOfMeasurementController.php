<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnitOfMeasurement;

class UnitOfMeasurementController extends Controller
{
    public function index()
    {
        $units = UnitOfMeasurement::pluck('name');

        return response()->json(['units' => $units]);
    }
}