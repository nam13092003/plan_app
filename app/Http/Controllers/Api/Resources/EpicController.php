<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\Epic;
use Illuminate\Http\Request;

class EpicController extends Controller
{
    public function index(Request $request)
    {
        $query = Epic::with(['project', 'sprint', 'tickets', 'parent']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $epics = $query->orderBy('starts_at')->paginate($perPage);

        return response()->json($epics);
    }

    public function show(Epic $epic)
    {
        $epic->load(['project', 'sprint', 'tickets', 'parent']);
        return response()->json($epic);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'parent_id' => 'nullable|exists:epics,id',
        ]);

        $epic = Epic::create($validated);
        $epic->load(['project', 'sprint', 'tickets', 'parent']);

        return response()->json($epic, 201);
    }

    public function update(Request $request, Epic $epic)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'parent_id' => 'nullable|exists:epics,id',
        ]);

        $epic->update($validated);
        $epic->load(['project', 'sprint', 'tickets', 'parent']);

        return response()->json($epic);
    }

    public function destroy(Epic $epic)
    {
        $epic->delete();
        return response()->json(['message' => 'Epic deleted successfully']);
    }
}


