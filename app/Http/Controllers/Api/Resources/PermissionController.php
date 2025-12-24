<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $permissions = $query->orderBy('name')->paginate($perPage);

        return response()->json($permissions);
    }

    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create($validated);

        return response()->json($permission, 201);
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update($validated);

        return response()->json($permission);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }
}


