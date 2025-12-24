<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Models\TicketHour;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketHour::with(['user', 'ticket', 'activity']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by ticket
        if ($request->has('ticket_id')) {
            $query->where('ticket_id', $request->ticket_id);
        }

        // Filter by activity
        if ($request->has('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('comment', 'like', '%' . $request->search . '%')
                  ->orWhereHas('ticket', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $timesheets = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($timesheets);
    }

    public function show(TicketHour $timesheet)
    {
        $timesheet->load(['user', 'ticket', 'activity']);
        return response()->json($timesheet);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'value' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
            'activity_id' => 'nullable|exists:activities,id',
        ]);

        // Set user_id to current user if not provided
        if (!isset($validated['user_id'])) {
            $validated['user_id'] = auth()->id();
        } else {
            // Only allow setting other user if current user has permission
            $validated['user_id'] = $request->user_id;
        }

        $timesheet = TicketHour::create($validated);
        $timesheet->load(['user', 'ticket', 'activity']);

        return response()->json($timesheet, 201);
    }

    public function update(Request $request, TicketHour $timesheet)
    {
        $validated = $request->validate([
            'value' => 'sometimes|required|numeric|min:0',
            'comment' => 'nullable|string',
            'activity_id' => 'nullable|exists:activities,id',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        $timesheet->update($validated);
        $timesheet->load(['user', 'ticket', 'activity']);

        return response()->json($timesheet);
    }

    public function destroy(TicketHour $timesheet)
    {
        $timesheet->delete();
        return response()->json(['message' => 'Timesheet entry deleted successfully']);
    }
}


