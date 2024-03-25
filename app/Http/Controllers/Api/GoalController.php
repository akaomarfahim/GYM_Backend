<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::all();

        return response()->json(['goals' => $goals]);
    }

    public function show(Goal $goal)
    {
        return response()->json(['goal' => $goal]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:goals,name',
            ]);

            $goal = Goal::create([
                'name' => $request->name,
            ]);

            return response()->json(['goal' => $goal], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Goal $goal)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:goals,name,' . $goal->id,
            ]);

            $goal->update([
                'name' => $request->name,
            ]);

            return response()->json(['goal' => $goal]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Goal $goal)
    {
        $goal->delete();

        return response()->json(['message' => 'Goal deleted successfully']);
    }
}