<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\Sprint;
use App\Models\Ticket;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function index(Request $request)
    {
        $query = Sprint::with(['project', 'epic', 'tickets'])
            ->withCount('tickets');

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $sprints = $query->orderBy('id')->paginate($perPage);

        return response()->json($sprints);
    }

    public function show(Sprint $sprint)
    {
        $sprint->load(['project', 'epic', 'tickets']);
        $sprint->loadCount('tickets');
        return response()->json($sprint);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'description' => 'nullable|string',
        ]);

        $sprint = Sprint::create($validated);
        $sprint->load(['project', 'epic', 'tickets']);
        $sprint->loadCount('tickets');

        return response()->json($sprint, 201);
    }

    public function update(Request $request, Sprint $sprint)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'starts_at' => 'sometimes|required|date',
            'ends_at' => 'sometimes|required|date|after_or_equal:starts_at',
            'description' => 'nullable|string',
        ]);

        $sprint->update($validated);
        $sprint->load(['project', 'epic', 'tickets']);
        $sprint->loadCount('tickets');

        return response()->json($sprint);
    }

    public function destroy(Sprint $sprint)
    {
        $sprint->delete();
        return response()->json(['message' => 'Sprint deleted successfully']);
    }

    /**
     * Start sprint
     */
    public function start(Sprint $sprint)
    {
        if ($sprint->started_at) {
            return response()->json([
                'message' => 'Sprint is already started.',
                'errors' => ['sprint' => ['Sprint is already started.']]
            ], 422);
        }

        if ($sprint->ended_at) {
            return response()->json([
                'message' => 'Sprint is already ended.',
                'errors' => ['sprint' => ['Sprint is already ended.']]
            ], 422);
        }

        $now = now();

        // End all other active sprints in the same project
        Sprint::where('project_id', $sprint->project_id)
            ->where('id', '<>', $sprint->id)
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->update(['ended_at' => $now]);

        $sprint->started_at = $now;
        $sprint->save();
        $sprint->load(['project', 'epic', 'tickets']);
        $sprint->loadCount('tickets');

        return response()->json([
            'message' => 'Sprint started successfully',
            'sprint' => $sprint
        ]);
    }

    /**
     * Stop sprint
     */
    public function stop(Sprint $sprint)
    {
        if (!$sprint->started_at) {
            return response()->json([
                'message' => 'Sprint is not started.',
                'errors' => ['sprint' => ['Sprint is not started.']]
            ], 422);
        }

        if ($sprint->ended_at) {
            return response()->json([
                'message' => 'Sprint is already ended.',
                'errors' => ['sprint' => ['Sprint is already ended.']]
            ], 422);
        }

        $sprint->ended_at = now();
        $sprint->save();
        $sprint->load(['project', 'epic', 'tickets']);
        $sprint->loadCount('tickets');

        return response()->json([
            'message' => 'Sprint stopped successfully',
            'sprint' => $sprint
        ]);
    }

    /**
     * Associate tickets with sprint
     */
    public function associateTickets(Request $request, Sprint $sprint)
    {
        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
        ]);

        // Remove tickets from this sprint first
        Ticket::where('sprint_id', $sprint->id)->update(['sprint_id' => null]);

        // Associate new tickets
        Ticket::whereIn('id', $validated['ticket_ids'])->update(['sprint_id' => $sprint->id]);

        $sprint->load(['project', 'epic', 'tickets']);
        $sprint->loadCount('tickets');

        return response()->json([
            'message' => 'Tickets associated with sprint successfully',
            'sprint' => $sprint
        ]);
    }
}


