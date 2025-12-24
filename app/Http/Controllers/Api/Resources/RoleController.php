<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $roles = $query->orderBy('name')->paginate($perPage);

        return response()->json($roles);
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return response()->json($role);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        // Sync permissions if provided
        if (isset($validated['permission_ids'])) {
            $role->syncPermissions($validated['permission_ids']);
        }

        $role->load('permissions');

        return response()->json($role, 201);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:roles,name,' . $role->id,
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if (isset($validated['name'])) {
            $role->update(['name' => $validated['name']]);
        }

        // Sync permissions if provided
        if (isset($validated['permission_ids'])) {
            $role->syncPermissions($validated['permission_ids']);
        }

        $role->load('permissions');

        return response()->json($role);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}


