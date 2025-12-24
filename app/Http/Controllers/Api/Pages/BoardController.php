<?php

namespace App\Http\Controllers\Api\Pages;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    /**
     * Get projects accessible for board selection
     */
    public function getProjects(Request $request)
    {
        $query = Project::where(function ($query) {
            $query->where('owner_id', auth()->id())
                ->orWhereHas('users', function ($query) {
                    $query->where('users.id', auth()->id());
                });
        });

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $projects = $query->select('id', 'name', 'type', 'cover')
            ->orderBy('name')
            ->get();

        return response()->json($projects);
    }

    /**
     * Get Kanban/Scrum board statuses
     */
    public function getStatuses(Project $project)
    {
        // Check access
        if ($project->owner_id != auth()->id() && 
            !$project->users->where('id', auth()->id())->count()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = TicketStatus::query();
        
        if ($project->status_type === 'custom') {
            $query->where('project_id', $project->id);
        } else {
            $query->whereNull('project_id');
        }

        $statuses = $query->orderBy('order')->get();

        // Get ticket counts for all relevant statuses in a single query
        $ticketCounts = Ticket::select('status_id', DB::raw('count(*) as count'))
            ->where('project_id', $project->id)
            ->active()
            ->whereIn('status_id', $statuses->pluck('id'))
            ->groupBy('status_id')
            ->pluck('count', 'status_id'); // Pluck counts into an associative array

        $mappedStatuses = $statuses->map(function ($status) use ($ticketCounts) {
            return [
                'id' => $status->id,
                'title' => $status->name,
                'color' => $status->color,
                'size' => $ticketCounts->get($status->id, 0), // Get count, default to 0 if not found
                'order' => $status->order,
            ];
        });

        return TicketStatus::where('project_id', $project->id)
        ->orWhereNull('project_id')
        ->withCount(['tickets' => function ($query) use ($project) {
            $query->where('project_id', $project->id);
        }])
        ->orderBy('order')
        ->get()
        ->map(function ($status) {
            return [
                'id' => $status->id,
                'title' => $status->name, // Đổi 'name' thành 'title' để khớp TaskCard.vue
                'color' => $status->color,
                'count' => $status->tickets_count, // Lấy từ withCount
            ];
        });
    }

    /**
     * Get Kanban board tickets
     */
   public function getKanbanTickets(Request $request, Project $project) {
    // 1. Sửa lỗi cú pháp: auth()->id thành auth()->id()
    if ($project->owner_id != auth()->id() && 
        !$project->users->where('id', auth()->id())->count()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // 2. Kiểm tra loại dự án (Scrum/Kanban)
    if ($project->type === 'scrum') {
        return response()->json([
            'message' => 'This project uses Scrum. Use /api/projects/{project}/scrum/tickets endpoint.',
            'errors' => ['type' => ['Project type is scrum, not kanban']]
        ], 422);
    }

    // 3. Eager Loading các quan hệ cần thiết để tránh lỗi N+1
    $query = Ticket::with(['project', 'owner', 'responsible', 'status', 'type', 'priority', 'epic', 'relations'])
        ->where('project_id', $project->id);

    // 4. Áp dụng các bộ lọc (Filters)
    if ($request->has('users') && is_array($request->users) && count($request->users) > 0) {
        $query->where(function ($q) use ($request) {
            $q->whereIn('owner_id', $request->users)
              ->orWhereIn('responsible_id', $request->users);
        });
    }

    if ($request->has('types') && is_array($request->types) && count($request->types) > 0) {
        $query->whereIn('type_id', $request->types);
    }

    if ($request->has('priorities') && is_array($request->priorities) && count($request->priorities) > 0) {
        $query->whereIn('priority_id', $request->priorities);
    }

    if ($request->boolean('includeNotAffectedTickets')) {
        $query->whereNull('responsible_id');
    }

    // 5. Mapping dữ liệu an toàn (Sử dụng toán tử null-safe ?->)
    $tickets = $query->orderBy('order')->get()
        ->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'code' => $ticket->code,
                'title' => $ticket->name,
                'responsible' => $ticket->responsible ? [
                    'id' => $ticket->responsible->id,
                    'name' => $ticket->responsible->name,
                    'avatar' => $ticket->responsible->avatar,
                ] : null,
                'status' => $ticket->status, // Trả về cả object status
                'priority' => $ticket->priority,
                'type' => $ticket->type,
                'estimation' => $ticket->estimation, // Thêm estimation
                'epic' => $ticket->epic, // Thêm epic
            ];
        });

    return response()->json($tickets);
}

    /**
     * Get Scrum board sprint info
     */
    public function getScrumSprint(Project $project)
    {
        // Check access
        if ($project->owner_id != auth()->id() && 
            !$project->users->where('id', auth()->id)->count()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check project type
        if ($project->type !== 'scrum') {
            return response()->json([
                'message' => 'This project uses Kanban. Use /api/projects/{project}/kanban/tickets endpoint.',
                'errors' => ['type' => ['Project type is kanban, not scrum']]
            ], 422);
        }

        $currentSprint = $project->currentSprint;
        $nextSprint = $project->nextSprint;

        if (!$currentSprint) {
            return response()->json([
                'message' => 'No active sprint found',
                'current_sprint' => null,
                'next_sprint' => $nextSprint ? [
                    'id' => $nextSprint->id,
                    'name' => $nextSprint->name,
                    'starts_at' => $nextSprint->starts_at,
                    'ends_at' => $nextSprint->ends_at,
                ] : null,
            ]);
        }

        return response()->json([
            'current_sprint' => [
                'id' => $currentSprint->id,
                'name' => $currentSprint->name,
                'starts_at' => $currentSprint->starts_at,
                'ends_at' => $currentSprint->ends_at,
                'started_at' => $currentSprint->started_at,
                'remaining' => $currentSprint->remaining,
            ],
            'next_sprint' => $nextSprint ? [
                'id' => $nextSprint->id,
                'name' => $nextSprint->name,
                'starts_at' => $nextSprint->starts_at,
                'ends_at' => $nextSprint->ends_at,
            ] : null,
        ]);
    }

    /**
     * Get Scrum board tickets
     */
    public function getScrumTickets(Request $request, Project $project)
    {
        // Check access
        if ($project->owner_id != auth()->id() && 
            !$project->users->where('id', auth()->id)->count()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check project type
        if ($project->type !== 'scrum') {
            return response()->json([
                'message' => 'This project uses Kanban. Use /api/projects/{project}/kanban/tickets endpoint.',
                'errors' => ['type' => ['Project type is kanban, not scrum']]
            ], 422);
        }

        $currentSprint = $project->currentSprint;
        if (!$currentSprint) {
            return response()->json([
                'message' => 'No active sprint found',
                'tickets' => []
            ]);
        }

        $query = Ticket::with(['project', 'owner', 'responsible', 'status', 'type', 'priority', 'epic', 'relations'])
            ->where('project_id', $project->id)
            ->where('sprint_id', $currentSprint->id);

        // Apply same filters as Kanban
        if ($request->has('users') && is_array($request->users) && count($request->users) > 0) {
            $query->where(function ($q) use ($request) {
                $q->whereIn('owner_id', $request->users)
                  ->orWhereIn('responsible_id', $request->users);
            });
        }

        if ($request->has('types') && is_array($request->types) && count($request->types) > 0) {
            $query->whereIn('type_id', $request->types);
        }

        if ($request->has('priorities') && is_array($request->priorities) && count($request->priorities) > 0) {
            $query->whereIn('priority_id', $request->priorities);
        }

        if ($request->boolean('includeNotAffectedTickets')) {
            $query->whereNull('responsible_id');
        }

        $tickets = $query->orderBy('order')->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'code' => $ticket->code,
                    'title' => $ticket->name,
                    'owner' => $ticket->owner,
                    'type' => $ticket->type,
                    'responsible' => $ticket->responsible,
                    'project' => $ticket->project,
                    'status' => $ticket->status?->id,
                    'priority' => $ticket->priority,
                    'epic' => $ticket->epic,
                    'relations' => $ticket->relations,
                    'totalLoggedHours' => $ticket->totalLoggedSeconds ? $ticket->totalLoggedHours : null,
                ];
            });

        return response()->json($tickets);
    }

    /**
     * Move ticket (update status and order)
     */
    public function moveTicket(Request $request, Ticket $ticket)
    {
        // Check access
        $project = $ticket->project;
        if ($project->owner_id != auth()->id() && 
            !$project->users->where('id', auth()->id)->count()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status_id' => 'required|exists:ticket_statuses,id',
            'order' => 'nullable|integer|min:0',
        ]);

        $ticket->status_id = $validated['status_id'];
        if (isset($validated['order'])) {
            $ticket->order = $validated['order'];
        }
        $ticket->save();

        $ticket->load(['project', 'owner', 'responsible', 'status', 'type', 'priority', 'epic', 'relations']);

        return response()->json([
            'message' => 'Ticket moved successfully',
            'ticket' => [
                'id' => $ticket->id,
                'code' => $ticket->code,
                'title' => $ticket->name,
                'owner' => $ticket->owner,
                'type' => $ticket->type,
                'responsible' => $ticket->responsible,
                'project' => $ticket->project,
                'status' => $ticket->status?->id,
                'priority' => $ticket->priority,
                'epic' => $ticket->epic,
                'relations' => $ticket->relations,
                'totalLoggedHours' => $ticket->totalLoggedSeconds ? $ticket->totalLoggedHours : null,
            ]
        ]);
    }
}



