<?php

namespace App\Http\Controllers\Api\Pages;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketComment;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats()
    {
        $stats = [
            'projects' => Project::count(),
            'tickets' => Ticket::count(),
            'active_tickets' => 0, // Temporarily disabled to prevent crash
            'users' => User::count(),
            'latest_projects' => Project::with(['owner', 'status'])
                ->latest()
                ->limit(5)
                ->get(),
            'latest_tickets' => Ticket::with(['owner', 'project', 'status'])
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get favorite projects widget data
     */
    public function favoriteProjects()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([]);
        }

        $favoriteProjects = $user->favoriteProjects()
            ->with(['owner', 'status'])
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'cover' => $project->cover,
                    'tickets_count' => $project->tickets()->count(),
                    'contributors_count' => $project->contributors->count(),
                    'owner' => $project->owner,
                    'status' => $project->status,
                ];
            });

        return response()->json($favoriteProjects);
    }

    /**
     * Get latest activities widget data
     */
    public function latestActivities()
    {
        $activities = TicketActivity::with(['ticket.project', 'ticket.status', 'user', 'oldStatus', 'newStatus'])
            ->whereHas('ticket', function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhere('responsible_id', auth()->id())
                    ->orWhereHas('project', function ($query) {
                        $query->where('owner_id', auth()->id())
                            ->orWhereHas('users', function ($query) {
                                $query->where('users.id', auth()->id());
                            });
                    });
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'ticket' => [
                        'id' => $activity->ticket->id,
                        'code' => $activity->ticket->code,
                        'name' => $activity->ticket->name,
                        'project' => [
                            'id' => $activity->ticket->project->id,
                            'name' => $activity->ticket->project->name,
                        ],
                    ],
                    'old_status' => [
                        'id' => $activity->oldStatus->id,
                        'name' => $activity->oldStatus->name,
                        'color' => $activity->oldStatus->color,
                    ],
                    'new_status' => [
                        'id' => $activity->newStatus->id,
                        'name' => $activity->newStatus->name,
                        'color' => $activity->newStatus->color,
                    ],
                    'user' => [
                        'id' => $activity->user->id,
                        'name' => $activity->user->name,
                        'email' => $activity->user->email,
                    ],
                    'created_at' => $activity->created_at,
                ];
            });

        return response()->json($activities);
    }

    /**
     * Get latest comments widget data
     */
    public function latestComments()
    {
        $comments = TicketComment::with(['ticket.project', 'user'])
            ->whereHas('ticket', function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhere('responsible_id', auth()->id())
                    ->orWhereHas('project', function ($query) {
                        $query->where('owner_id', auth()->id())
                            ->orWhereHas('users', function ($query) {
                                $query->where('users.id', auth()->id());
                            });
                    });
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'ticket' => [
                        'id' => $comment->ticket->id,
                        'code' => $comment->ticket->code,
                        'name' => $comment->ticket->name,
                        'project' => [
                            'id' => $comment->ticket->project->id,
                            'name' => $comment->ticket->project->name,
                        ],
                    ],
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'email' => $comment->user->email,
                    ],
                    'created_at' => $comment->created_at,
                ];
            });

        return response()->json($comments);
    }

    /**
     * Get latest projects widget data
     */
    public function latestProjects()
    {
        $projects = Project::with(['owner', 'status'])
            ->where(function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhereHas('users', function ($query) {
                        $query->where('users.id', auth()->id());
                    });
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'cover' => $project->cover,
                    'owner' => [
                        'id' => $project->owner->id,
                        'name' => $project->owner->name,
                    ],
                    'status' => [
                        'id' => $project->status->id,
                        'name' => $project->status->name,
                        'color' => $project->status->color,
                    ],
                    'created_at' => $project->created_at,
                ];
            });

        return response()->json($projects);
    }

    /**
     * Get latest tickets widget data
     */
    public function latestTickets()
    {
        $tickets = Ticket::with(['project', 'status', 'type', 'priority', 'responsible'])
            ->where(function ($query) {
                $query->where('owner_id', auth()->id())
                    ->orWhere('responsible_id', auth()->id())
                    ->orWhereHas('project', function ($query) {
                        $query->where('owner_id', auth()->id())
                            ->orWhereHas('users', function ($query) {
                                $query->where('users.id', auth()->id());
                            });
                    });
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'code' => $ticket->code,
                    'name' => $ticket->name,
                    'project' => [
                        'id' => $ticket->project->id,
                        'name' => $ticket->project->name,
                    ],
                    'status' => [
                        'id' => $ticket->status->id,
                        'name' => $ticket->status->name,
                        'color' => $ticket->status->color,
                    ],
                    'type' => $ticket->type ? [
                        'id' => $ticket->type->id,
                        'name' => $ticket->type->name,
                    ] : null,
                    'priority' => $ticket->priority ? [
                        'id' => $ticket->priority->id,
                        'name' => $ticket->priority->name,
                        'color' => $ticket->priority->color,
                    ] : null,
                    'responsible' => $ticket->responsible ? [
                        'id' => $ticket->responsible->id,
                        'name' => $ticket->responsible->name,
                    ] : null,
                    'created_at' => $ticket->created_at,
                ];
            });

        return response()->json($tickets);
    }

    /**
     * Get tickets by priority chart data
     */
    public function ticketsByPriority()
    {
        $data = TicketPriority::withCount('tickets')->get();
        
        return response()->json([
            'datasets' => [
                [
                    'label' => __('Tickets by priorities'),
                    'data' => $data->pluck('tickets_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, .6)',
                        'rgba(54, 162, 235, .6)',
                        'rgba(255, 205, 86, .6)'
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, .8)',
                        'rgba(54, 162, 235, .8)',
                        'rgba(255, 205, 86, .8)'
                    ],
                    'hoverOffset' => 4
                ]
            ],
            'labels' => $data->pluck('name')->toArray(),
        ]);
    }

    /**
     * Get tickets by type chart data
     */
    public function ticketsByType()
    {
        $data = TicketType::withCount('tickets')->get();
        
        return response()->json([
            'datasets' => [
                [
                    'label' => __('Tickets by types'),
                    'data' => $data->pluck('tickets_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, .6)',
                        'rgba(54, 162, 235, .6)',
                        'rgba(255, 205, 86, .6)'
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, .8)',
                        'rgba(54, 162, 235, .8)',
                        'rgba(255, 205, 86, .8)'
                    ],
                    'hoverOffset' => 4
                ]
            ],
            'labels' => $data->pluck('name')->toArray(),
        ]);
    }

    /**
     * Get ticket time logged chart data
     */
    public function ticketTimeLogged()
    {
        $tickets = Ticket::with('hours')
            ->has('hours')
            ->limit(10)
            ->get();

        return response()->json([
            'datasets' => [
                [
                    'label' => __('Total time logged (hours)'),
                    'data' => $tickets->map(function ($ticket) {
                        return $ticket->totalLoggedInHours ?? 0;
                    })->toArray(),
                    'backgroundColor' => [
                        'rgba(54, 162, 235, .6)'
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, .8)'
                    ],
                ],
            ],
            'labels' => $tickets->pluck('code')->toArray(),
        ]);
    }

    /**
     * Get user time logged chart data
     */
    public function userTimeLogged()
    {
        $users = User::query()
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(ticket_hours.value) as total_hours')
            )
            ->join('ticket_hours', 'ticket_hours.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_hours') 
            ->limit(10)
            ->get();

        return response()->json([
            'datasets' => [
                [
                    'label' => __('Total time logged (hours)'),
                    'data' => $users->pluck('total_hours')->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, .6)',
                    'borderColor' => 'rgba(54, 162, 235, .8)',
                ],
            ],
            'labels' => $users->pluck('name')->toArray(),
        ]);
    }

public function myTasksToday()
{
    $user = auth()->user();
    if (!$user) {
        return response()->json([]);
    }

    $today = Carbon::today();
    $threeDaysLater = Carbon::today()->addDays(3);

    $tasks = Ticket::with(['project', 'status', 'priority'])
        ->where('responsible_id', $user->id)
        ->whereDate('due_date', '>=', $today)
        ->whereDate('due_date', '<=', $threeDaysLater)
        ->whereHas('status', function ($q) {
            $q->where('is_closed', false);
        })
        ->orderByDesc('priority_id')
        ->limit(10)
        ->get()
        ->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'title' => $ticket->name,
                'project' => [
                    'id' => $ticket->project?->id,
                    'name' => $ticket->project?->name,
                ],
                'status' => $ticket->status?->name,
                'priority' => $ticket->priority?->name,
                'due_date' => optional($ticket->due_date)?->toDateString(),
                'is_overdue' => $ticket->due_date < Carbon::today(),
                'is_due_today' => $ticket->due_date == Carbon::today(),
            ];
        });

    return response()->json($tasks);
}
}



