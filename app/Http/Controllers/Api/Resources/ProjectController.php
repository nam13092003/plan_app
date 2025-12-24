<?php

namespace App\Http\Controllers\Api\Resources;

use App\Exports\ProjectHoursExport;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFavorite;
use App\Models\ProjectStatus;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        $query = Project::with(['owner', 'status', 'users'])
            ->withCount(['tickets']);

        // Filter by user access: only show projects where user is owner or member
        // Admin users can see all projects
        if (!$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                    ->orWhereHas('users', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    });
            });
        }

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Handle pagination
        $perPage = $request->get('per_page', 15);
        if ($perPage == -1) {
            $projects = $query->get();
        } else {
            $projects = $query->paginate($perPage);
        }

        return response()->json($projects);
    }

    public function show(Project $project)
        {
            $project->load([
                'owner', 
                'status', 
                'users',
                'tickets.status', 
                'tickets.priority', 
                'tickets.responsible',
                'statuses', 
                'sprints', 
                'epics'
            ]);

            return response()->json($project);
        }


    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'status_id' => 'nullable|exists:project_statuses,id',
            'ticket_prefix' => 'required|string|max:3|unique:projects,ticket_prefix',
            'type' => 'required|in:kanban,scrum',
            'status_type' => 'required|in:default,custom',
            'cover' => 'nullable|image|max:2048',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Set default values như Filament
        if (!isset($validated['owner_id'])) {
            $validated['owner_id'] = auth()->id();
        }
        
        if (!isset($validated['status_id'])) {
            $defaultStatus = ProjectStatus::where('is_default', true)->first();
            if ($defaultStatus) {
                $validated['status_id'] = $defaultStatus->id;
            } else {
                return response()->json([
                    'message' => 'No default project status found. Please create a default status first.',
                    'errors' => [
                        'status_id' => ['No default project status found.']
                    ]
                ], 422);
            }
        }

        $project = Project::create($validated);

        // Ensure owner is also added as project member with manager role
        if ($project->owner_id) {
            $project->users()->syncWithoutDetaching([
                $project->owner_id => ['role' => config('system.projects.affectations.roles.can_manage', 'administrator')]
            ]);
        }

        // Handle cover image upload
        if ($request->hasFile('cover')) {
            $project->addMediaFromRequest('cover')
                ->toMediaCollection('cover');
        }

        // Attach users if provided
        if ($request->has('user_ids') && is_array($request->user_ids)) {
            $project->users()->attach($request->user_ids);
        }

        $project->load(['owner', 'status', 'users']);

        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'sometimes|required|exists:users,id',
            'status_id' => 'sometimes|required|exists:project_statuses,id',
            'ticket_prefix' => [
                'sometimes',
                'required',
                'string',
                'max:3',
                Rule::unique('projects', 'ticket_prefix')->ignore($project->id),
            ],
            'type' => 'sometimes|required|in:kanban,scrum',
            'status_type' => 'sometimes|required|in:default,custom',
            'cover' => 'nullable|image|max:2048',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Validate ticket_prefix không thể thay đổi nếu đã có tickets
        if (isset($validated['ticket_prefix']) && $project->tickets()->count() > 0) {
            if ($validated['ticket_prefix'] !== $project->ticket_prefix) {
                return response()->json([
                    'message' => 'Ticket prefix cannot be changed when project has tickets.',
                    'errors' => [
                        'ticket_prefix' => ['Ticket prefix cannot be changed when project has tickets.']
                    ]
                ], 422);
            }
        }

        // Validate status_type không thể thay đổi nếu đã có tickets
        if (isset($validated['status_type']) && $project->tickets()->count() > 0) {
            if ($validated['status_type'] !== $project->status_type) {
                return response()->json([
                    'message' => 'Status type cannot be changed when project has tickets.',
                    'errors' => [
                        'status_type' => ['Status type cannot be changed when project has tickets.']
                    ]
                ], 422);
            }
        }

        $project->update($validated);

        // If owner changed, ensure new owner is attached as member with manager role
        if ($project->owner_id) {
            $project->users()->syncWithoutDetaching([
                $project->owner_id => ['role' => config('system.projects.affectations.roles.can_manage', 'administrator')]
            ]);
        }

        // Handle cover image upload/replacement
        if ($request->hasFile('cover')) {
            // Xóa cover cũ nếu có
            $project->clearMediaCollection('cover');
            // Thêm cover mới
            $project->addMediaFromRequest('cover')
                ->toMediaCollection('cover');
        }

        // Sync users if provided
        if ($request->has('user_ids') && is_array($request->user_ids)) {
            $project->users()->sync($request->user_ids);
        }

        $project->load(['owner', 'status', 'users']);

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }

    /**
     * Delete cover image
     */
    public function deleteCover(Project $project)
    {
        $project->clearMediaCollection('cover');
        $project->load(['owner', 'status', 'users']);
        return response()->json($project);
    }

    /**
     * Toggle favorite project
     */
    public function toggleFavorite(Project $project)
    {
        $projectFavorite = ProjectFavorite::where('project_id', $project->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($projectFavorite) {
            $projectFavorite->delete();
            $isFavorite = false;
        } else {
            ProjectFavorite::create([
                'project_id' => $project->id,
                'user_id' => auth()->id()
            ]);
            $isFavorite = true;
        }

        return response()->json([
            'message' => 'Project favorite updated',
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Export project hours as CSV
     */
    public function exportHours(Project $project)
    {
        $export = new ProjectHoursExport($project);
        $filename = 'time_' . Str::slug($project->name) . '.csv';

        return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv'
        ]);
    }

    /**
     * Get project users with roles
     */
    public function getUsers(Project $project)
    {
        $users = $project->users()->withPivot('role')->get();
        return response()->json($users);
    }

    /**
     * Attach user to project with role
     */
    public function attachUser(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:' . implode(',', array_keys(config('system.projects.affectations.roles.list', []))),
        ]);

        // Check if user is already attached
        if ($project->users()->where('users.id', $validated['user_id'])->exists()) {
            return response()->json([
                'message' => 'User is already attached to this project.',
                'errors' => ['user_id' => ['User is already attached to this project.']]
            ], 422);
        }

        $project->users()->attach($validated['user_id'], ['role' => $validated['role']]);
        $user = User::find($validated['user_id']);

        return response()->json([
            'message' => 'User attached successfully',
            'user' => $user,
            'role' => $validated['role']
        ], 201);
    }

    /**
     * Update user role in project
     */
    public function updateUserRole(Request $request, Project $project, User $user)
    {
        // Check if user is attached to project
        if (!$project->users()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is not attached to this project.',
                'errors' => ['user_id' => ['User is not attached to this project.']]
            ], 404);
        }

        $validated = $request->validate([
            'role' => 'required|string|in:' . implode(',', array_keys(config('system.projects.affectations.roles.list', []))),
        ]);

        $project->users()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user,
            'role' => $validated['role']
        ]);
    }

    /**
     * Detach user from project
     */
    public function detachUser(Project $project, User $user)
    {
        // Check if user is attached to project
        if (!$project->users()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'User is not attached to this project.',
                'errors' => ['user_id' => ['User is not attached to this project.']]
            ], 404);
        }

        $project->users()->detach($user->id);

        return response()->json([
            'message' => 'User detached successfully'
        ]);
    }

    /**
     * Get project sprints
     */
    // ProjectController.php
    public function getSprints(Project $project)
    {
        // Load tickets relationship để có thể hiển thị trong UI
        $sprints = $project->sprints()
            ->with(['epic', 'tickets.status', 'tickets.priority', 'tickets.type']) // Load tickets với relations
            ->withCount('tickets') // Thêm cái này để có sprint.tickets_count
            ->orderBy('starts_at', 'asc') // Sắp xếp theo ngày bắt đầu để dễ nhìn
            ->get();

        return response()->json($sprints);
    }

    /**
     * Get project custom statuses (only if status_type = custom)
     */
    public function getStatuses(Project $project)
    {
        if ($project->status_type !== 'custom') {
            return response()->json([
                'message' => 'Project does not use custom statuses.',
                'errors' => ['status_type' => ['Project does not use custom statuses.']]
            ], 422);
        }

        $statuses = TicketStatus::where('project_id', $project->id)
            ->orderBy('order')
            ->get();

        return response()->json($statuses);
    }

    // GET /api/projects/active
    public function active(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        $query = Project::with(['owner', 'status'])
            ->withCount('tickets')
            ->whereHas('status', fn ($q) => $q->where('name', 'Active'));

        if (!$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                ->orWhereHas('users', fn ($u) => $u->whereKey($user->id));
            });
        }

        return $query->paginate(12);
    }
}

