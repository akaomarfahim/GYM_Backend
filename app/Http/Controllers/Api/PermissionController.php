<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        return response()->json(['permissions' => $permissions]);
    }

    public function show(Permission $permission)
    {
        return response()->json(['permission' => $permission]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:permissions,name',
                'description' => 'nullable|string',
            ]);

            $permission = Permission::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json(['permission' => $permission], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Permission $permission)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:permissions,name,' . $permission->id,
                'description' => 'nullable|string',
            ]);

            $permission->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json(['permission' => $permission]);
        } catch  (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
