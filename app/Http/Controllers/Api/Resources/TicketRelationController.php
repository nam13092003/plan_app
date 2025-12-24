<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\TicketRelation;
use Illuminate\Http\Request;

class TicketRelationController extends Controller
{
    public function index(Request $request, $ticketId)
    {
        $query = TicketRelation::with(['relation'])
            ->where('ticket_id', $ticketId);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $relations = $query->orderBy('sort')->get();

        return response()->json($relations);
    }

    public function show($ticketId, TicketRelation $relation)
    {
        // Verify relation belongs to ticket
        if ($relation->ticket_id != $ticketId) {
            return response()->json(['message' => 'Relation not found'], 404);
        }

        $relation->load(['ticket', 'relation']);
        return response()->json($relation);
    }

    public function store(Request $request, $ticketId)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(config('system.tickets.relations.list', []))),
            'relation_id' => 'required|exists:tickets,id|different:' . $ticketId,
            'sort' => 'nullable|integer',
        ]);

        $validated['ticket_id'] = $ticketId;

        // Set sort if not provided
        if (!isset($validated['sort'])) {
            $maxSort = TicketRelation::where('ticket_id', $ticketId)->max('sort') ?? 0;
            $validated['sort'] = $maxSort + 1;
        }

        $relation = TicketRelation::create($validated);
        $relation->load(['ticket', 'relation']);

        return response()->json($relation, 201);
    }

    public function update(Request $request, $ticketId, TicketRelation $relation)
    {
        // Verify relation belongs to ticket
        if ($relation->ticket_id != $ticketId) {
            return response()->json(['message' => 'Relation not found'], 404);
        }

        $validated = $request->validate([
            'type' => 'sometimes|required|string|in:' . implode(',', array_keys(config('system.tickets.relations.list', []))),
            'relation_id' => 'sometimes|required|exists:tickets,id|different:' . $ticketId,
            'sort' => 'nullable|integer',
        ]);

        $relation->update($validated);
        $relation->load(['ticket', 'relation']);

        return response()->json($relation);
    }

    public function destroy($ticketId, TicketRelation $relation)
    {
        // Verify relation belongs to ticket
        if ($relation->ticket_id != $ticketId) {
            return response()->json(['message' => 'Relation not found'], 404);
        }

        $relation->delete();
        return response()->json(['message' => 'Relation deleted successfully']);
    }
}


