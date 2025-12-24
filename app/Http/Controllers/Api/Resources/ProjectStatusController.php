<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectStatus::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', 'desc');

        if ($request->boolean('paginate', false)) {
            return response()->json($query->paginate($request->get('per_page', 15)));
        }

        return response()->json($query->get());
    }

    public function show(ProjectStatus $projectStatus)
    {
        return response()->json($projectStatus);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        $projectStatus = ProjectStatus::create([
            'name' => $data['name'],
            'color' => $data['color'],
            'is_default' => $data['is_default'] ?? false,
        ]);

        $this->syncDefaultStatus($projectStatus);

        return response()->json($projectStatus, Response::HTTP_CREATED);
    }

    public function update(Request $request, ProjectStatus $projectStatus)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|required|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        $projectStatus->update($data);

        $this->syncDefaultStatus($projectStatus);

        return response()->json($projectStatus);
    }

    public function destroy(ProjectStatus $projectStatus)
    {
        $projectStatus->delete();

        return response()->json([
            'message' => 'Project status deleted successfully',
        ]);
    }

    private function syncDefaultStatus(ProjectStatus $projectStatus): void
    {
        if ($projectStatus->is_default) {
            ProjectStatus::where('id', '<>', $projectStatus->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}

