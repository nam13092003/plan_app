<?php

use App\Http\Controllers\Api\Pages\BoardController;
use App\Http\Controllers\Api\Pages\DashboardController;
use App\Http\Controllers\Api\Pages\RoadMapController;
use App\Http\Controllers\Api\Pages\TimesheetDashboardController;
use App\Http\Controllers\Api\Resources\ActivityController;
use App\Http\Controllers\Api\Resources\EpicController;
use App\Http\Controllers\Api\Resources\PermissionController;
use App\Http\Controllers\Api\Resources\ProjectController;
use App\Http\Controllers\Api\Resources\ProjectStatusController;
use App\Http\Controllers\Api\Resources\RoleController;
use App\Http\Controllers\Api\Resources\SprintController;
use App\Http\Controllers\Api\Resources\TicketCommentController;
use App\Http\Controllers\Api\Resources\TicketController;
use App\Http\Controllers\Api\Resources\TicketPriorityController;
use App\Http\Controllers\Api\Resources\TicketRelationController;
use App\Http\Controllers\Api\Resources\TicketStatusController;
use App\Http\Controllers\Api\Resources\TicketTypeController;
use App\Http\Controllers\Api\Resources\TimesheetController;
use App\Http\Controllers\Api\Resources\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login'])->middleware('web');
Route::post('/register', [AuthController::class, 'register'])->middleware('web');

// Protected API routes
Route::middleware(['web', 'auth'])->group(function () {
    // Authenticated session helpers
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/favorite-projects', [DashboardController::class, 'favoriteProjects']);
    Route::get('/dashboard/latest-activities', [DashboardController::class, 'latestActivities']);
    Route::get('/dashboard/latest-comments', [DashboardController::class, 'latestComments']);
    Route::get('/dashboard/latest-projects', [DashboardController::class, 'latestProjects']);
    Route::get('/dashboard/latest-tickets', [DashboardController::class, 'latestTickets']);
    Route::get('/dashboard/tickets-by-priority', [DashboardController::class, 'ticketsByPriority']);
    Route::get('/dashboard/tickets-by-type', [DashboardController::class, 'ticketsByType']);
    Route::get('/dashboard/ticket-time-logged', [DashboardController::class, 'ticketTimeLogged']);
    Route::get('/dashboard/user-time-logged', [DashboardController::class, 'userTimeLogged']);
    Route::get('/dashboard/my-tasks-today', [DashboardController::class, 'myTasksToday']);
    Route::get('/projects/active', [ProjectController::class, 'active']);
    Route::get('/tickets/open', [TicketController::class, 'open']);
    Route::get('/tickets/in-progress', [TicketController::class, 'inProgress']);

    // Core resources
    Route::apiResource('projects', ProjectController::class);
    Route::delete('/projects/{project}/cover', [ProjectController::class, 'deleteCover']);
    Route::post('/projects/{project}/favorite', [ProjectController::class, 'toggleFavorite']);
    Route::get('/projects/{project}/export-hours', [ProjectController::class, 'exportHours']);
    Route::get('/projects/{project}/users', [ProjectController::class, 'getUsers']);
    Route::post('/projects/{project}/users', [ProjectController::class, 'attachUser']);
    Route::put('/projects/{project}/users/{user}', [ProjectController::class, 'updateUserRole']);
    Route::delete('/projects/{project}/users/{user}', [ProjectController::class, 'detachUser']);
    Route::get('/projects/{project}/sprints', [ProjectController::class, 'getSprints']);
    Route::get('/projects/{project}/statuses', [ProjectController::class, 'getStatuses']);

    Route::apiResource('tickets', TicketController::class);
    Route::get('/tickets/{ticket}/subscribers', [TicketController::class, 'getSubscribers']);
    Route::post('/tickets/{ticket}/subscribers/{user}', [TicketController::class, 'subscribe']);
    Route::delete('/tickets/{ticket}/subscribers/{user}', [TicketController::class, 'unsubscribe']);
    Route::get('/tickets/{ticket}/hours', [TicketController::class, 'getHours']);
    Route::post('/tickets/{ticket}/hours', [TicketController::class, 'logHours']);
    Route::get('/tickets/{ticket}/export-hours', [TicketController::class, 'exportHours']);
    
    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'updateMe']);

    Route::apiResource('users', UserController::class);

    // Referential resources
    Route::apiResource('activities', ActivityController::class);
    Route::apiResource('project-statuses', ProjectStatusController::class);
    Route::apiResource('ticket-statuses', TicketStatusController::class);
    Route::apiResource('ticket-types', TicketTypeController::class);
    Route::apiResource('ticket-priorities', TicketPriorityController::class);
    
    // Permission & Role management
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);
    
    // Timesheet
    Route::apiResource('timesheets', TimesheetController::class);
    
    // Sprint management
    Route::apiResource('sprints', SprintController::class);
    Route::post('/sprints/{sprint}/start', [SprintController::class, 'start']);
    Route::post('/sprints/{sprint}/stop', [SprintController::class, 'stop']);
    Route::post('/sprints/{sprint}/tickets', [SprintController::class, 'associateTickets']);
    
    // Epic management
    Route::apiResource('epics', EpicController::class);
    
    // Ticket nested resources
    Route::get('/tickets/{ticket}/comments', [TicketCommentController::class, 'index']);
    Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store']);
    Route::get('/tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'show']);
    Route::put('/tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'update']);
    Route::delete('/tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'destroy']);
    
    Route::get('/tickets/{ticket}/relations', [TicketRelationController::class, 'index']);
    Route::post('/tickets/{ticket}/relations', [TicketRelationController::class, 'store']);
    Route::get('/tickets/{ticket}/relations/{relation}', [TicketRelationController::class, 'show']);
    Route::put('/tickets/{ticket}/relations/{relation}', [TicketRelationController::class, 'update']);
    Route::delete('/tickets/{ticket}/relations/{relation}', [TicketRelationController::class, 'destroy']);

    // Board/Kanban/Scrum
    Route::get('/board/projects', [BoardController::class, 'getProjects']);
    Route::get('/projects/{project}/kanban/statuses', [BoardController::class, 'getStatuses']);
    Route::get('/projects/{project}/kanban/tickets', [BoardController::class, 'getKanbanTickets']);
    Route::get('/projects/{project}/scrum/sprint', [BoardController::class, 'getScrumSprint']);
    Route::get('/projects/{project}/scrum/tickets', [BoardController::class, 'getScrumTickets']);
    Route::put('/tickets/{ticket}/move', [BoardController::class, 'moveTicket']);

    // RoadMap
    Route::get('/projects/{project}/roadmap', [RoadMapController::class, 'getRoadmap']);
    Route::get('/projects/{project}/roadmap/dates', [RoadMapController::class, 'getRoadmapDates']);

    // Timesheet Dashboard
    Route::get('/timesheet/monthly-report', [TimesheetDashboardController::class, 'monthlyReport']);
    Route::get('/timesheet/weekly-report', [TimesheetDashboardController::class, 'weeklyReport']);
    Route::get('/timesheet/activities-report', [TimesheetDashboardController::class, 'activitiesReport']);

    // Backward compatibility aliases
    Route::get('/referential/activities', [ActivityController::class, 'index']);
    Route::get('/referential/project-statuses', [ProjectStatusController::class, 'index']);
    Route::get('/referential/ticket-statuses', [TicketStatusController::class, 'index']);
    Route::get('/referential/ticket-types', [TicketTypeController::class, 'index']);
    Route::get('/referential/ticket-priorities', [TicketPriorityController::class, 'index']);
});
