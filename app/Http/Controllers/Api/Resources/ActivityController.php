<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $activities = $query->orderByDesc('created_at')->paginate($request->get('per_page', 15));

        return response()->json($activities);
    }

    public function show(Activity $activity)
    {
        return response()->json($activity);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $activity = Activity::create($data);

        return response()->json($activity, Response::HTTP_CREATED);
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $activity->update($data);

        return response()->json($activity);
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return response()->json([
            'message' => 'Activity deleted successfully',
        ]);
    }
}

