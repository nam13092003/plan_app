<?php

namespace App\Http\Controllers\Api\Resources;

use App\Models\Project;
use App\Exports\TicketHoursExport;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketHour;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['owner', 'responsible', 'status', 'project', 'type', 'priority']);

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter by priority
        if ($request->has('priority_id')) {
            $query->where('priority_id', $request->priority_id);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination with support for per_page = -1 (return all)
        $perPage = $request->get('per_page', 15);
        if ((int) $perPage === -1) {
            $tickets = $query->orderBy('order')->get();
            return response()->json($tickets);
        }

        $tickets = $query->orderBy('order')->paginate($perPage);
        return response()->json($tickets);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load([
            'owner', 'responsible', 'status', 'project', 
            'type', 'priority', 'comments', 'activities', 
            'hours', 'subscribers', 'epic', 'sprint'
        ]);
        return response()->json($ticket);
    }

  public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string', // Đã thêm: Nhận nội dung từ frontend
            'project_id' => 'required|exists:projects,id',
            'owner_id' => 'nullable|exists:users,id',
            'responsible_id' => 'nullable|exists:users,id',
            'status_id' => 'nullable|exists:ticket_statuses,id',
            'type_id' => 'nullable|exists:ticket_types,id',
            'priority_id' => 'nullable|exists:ticket_priorities,id',
            'estimation' => 'nullable|numeric|min:0',
            'sprint_id' => 'nullable|exists:sprints,id',
            'epic_id' => 'nullable|exists:epics,id',
        ]);

        // Đảm bảo content không bị null khi lưu vào DB
        if (!isset($validated['content'])) {
            $validated['content'] = '';
        }

        // Tự động gán owner_id là người dùng hiện tại nếu chưa có
        if (!isset($validated['owner_id'])) {
            $validated['owner_id'] = auth()->id();
        }

        // Gán status mặc định nếu chưa chọn
        if (!isset($validated['status_id'])) {
            $defaultStatus = TicketStatus::where('is_default', true)
                ->where(function ($query) use ($validated) {
                    $query->where('project_id', $validated['project_id'])
                        ->orWhereNull('project_id');
                })->first();
            
            if ($defaultStatus) {
                $validated['status_id'] = $defaultStatus->id;
            }
        }

        // Tạo mã ticket tự động (Code)
        $project = Project::find($validated['project_id']);
        $validated['code'] = $project->ticket_prefix . '-' . (Ticket::where('project_id', $project->id)->count() + 1);
        $validated['order'] = Ticket::where('project_id', $project->id)->max('order') + 1;

        $ticket = Ticket::create($validated);

        return response()->json($ticket, 201);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'project_id' => 'sometimes|required|exists:projects,id',
            'owner_id' => 'sometimes|required|exists:users,id',
            'responsible_id' => 'nullable|exists:users,id',
            'status_id' => 'sometimes|required|exists:ticket_statuses,id',
            'type_id' => 'nullable|exists:ticket_types,id',
            'priority_id' => 'nullable|exists:ticket_priorities,id',
            'estimation' => 'nullable|numeric',
            'epic_id' => 'nullable|exists:epics,id',
            'sprint_id' => 'nullable|exists:sprints,id',
            'order' => 'nullable|integer',
        ]);

        $ticket->update($validated);
        $ticket->load(['owner', 'responsible', 'status', 'project', 'type', 'priority']);

        return response()->json($ticket);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted successfully']);
    }

    /**
     * Get ticket subscribers
     */
    public function getSubscribers(Ticket $ticket)
    {
        $subscribers = $ticket->subscribers;
        return response()->json($subscribers);
    }

    /**
     * Subscribe user to ticket
     */
    public function subscribe(Ticket $ticket, User $user)
    {
        if ($ticket->subscribers()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is already subscribed to this ticket.',
                'errors' => ['user_id' => ['User is already subscribed to this ticket.']]
            ], 422);
        }

        $ticket->subscribers()->attach($user->id);

        return response()->json([
            'message' => 'User subscribed successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Unsubscribe user from ticket
     */
    public function unsubscribe(Ticket $ticket, User $user)
    {
        if (!$ticket->subscribers()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is not subscribed to this ticket.',
                'errors' => ['user_id' => ['User is not subscribed to this ticket.']]
            ], 404);
        }

        $ticket->subscribers()->detach($user->id);

        return response()->json([
            'message' => 'User unsubscribed successfully'
        ]);
    }

    /**
     * Get ticket hours
     */
    public function getHours(Ticket $ticket)
    {
        $hours = $ticket->hours()->with(['user', 'activity'])->orderBy('created_at', 'desc')->get();
        return response()->json($hours);
    }

    /**
     * Log hours for ticket
     */
    public function logHours(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'value' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
            'activity_id' => 'nullable|exists:activities,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $validated['ticket_id'] = $ticket->id;
        if (!isset($validated['user_id'])) {
            $validated['user_id'] = auth()->id();
        }

        $hour = TicketHour::create($validated);
        $hour->load(['user', 'activity']);

        return response()->json($hour, 201);
    }

    /**
     * Export ticket hours as CSV
     */
    public function exportHours(Ticket $ticket)
    {
        $export = new TicketHoursExport($ticket);
        $filename = 'time_' . str_replace('-', '_', $ticket->code) . '.csv';

        return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv'
        ]);
    }

    public function open(Request $request)
    {
        $user = $request->user();

        $tickets = Ticket::with([
                'project:id,name',
                'status:id,name,color',
                'priority:id,name,color'
            ])
            ->where('responsible_id', $user->id)  
            ->where('status_id', 1)              
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $tickets
        ]);
    }

    public function inProgress(Request $request)
    {
        $user = $request->user();

        $tickets = Ticket::with([
                'project:id,name',
                'status:id,name,color',
                'priority:id,name,color'
            ])
            ->where('responsible_id', $user->id)  
            ->where('status_id', 2)              
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $tickets
        ]);
    }
}