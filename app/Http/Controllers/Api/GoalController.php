<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::pluck('name');

        return response()->json(['goals' => $goals]);
    }
}