<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        return response()->json(['roles' => $roles]);
    }

    public function show(Role $role)
    {
        return response()->json(['role' => $role]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:roles,name',
                'description' => 'nullable|string',
            ]);

            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json(['role' => $role], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Role $role)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $role->id,
                'description' => 'nullable|string',
            ]);
    
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
    
            return response()->json(['role' => $role]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
