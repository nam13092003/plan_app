# DANH SÁCH CÔNG VIỆC - PROJECT MANAGEMENT SYSTEM

## PHASE 1: SETUP MÔI TRƯỜNG & CƠ SỞ HẠ TẦNG

### 1.1. Setup Laravel Project

**Task: Tạo Laravel 10.x project mới**
- Mô tả: Khởi tạo project Laravel 10.x làm nền tảng cho hệ thống
- Files: `composer.json`, `artisan`, `bootstrap/app.php`, `public/index.php`
- Command: `composer create-project laravel/laravel project-management`

**Task: Cấu hình .env file**
- Mô tả: Tạo file environment variables với database, app name, timezone
- Files: `.env`, `.env.example`
- Checklist:
  - [ ] Copy từ .env.example
  - [ ] Cấu hình APP_NAME, APP_ENV, APP_DEBUG
  - [ ] Cấu hình database connection (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

**Task: Cài đặt Composer dependencies**
- Mô tả: Cài đặt tất cả PHP packages từ composer.json
- Files: `composer.json`, `composer.lock`, `vendor/`
- Command: `composer install`

**Task: Cấu hình database connection**
- Mô tả: Cấu hình kết nối MySQL/PostgreSQL trong config
- Files: `config/database.php`
- Checklist:
  - [ ] Set default connection là mysql
  - [ ] Cấu hình charset utf8mb4

**Task: Generate application key**
- Mô tả: Tạo encryption key cho sessions và cookies
- Files: `.env` (APP_KEY)
- Command: `php artisan key:generate`

### 1.2. Cài đặt Packages chính

**Task: Cài đặt Filament v2.16**
- Mô tả: Cài đặt Filament admin panel framework (chỉ dùng cho admin backend)
- Files: `config/filament.php`, `app/Providers/Filament/AdminPanelProvider.php`
- Commands:
  - `composer require filament/filament:"^2.16"`
  - `php artisan filament:install --panels`

**Task: Cài đặt Spatie Permission**
- Mô tả: Cài đặt package quản lý permissions và roles
- Files: `config/permission.php`, migrations
- Commands:
  - `composer require spatie/laravel-permission`
  - `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
  - `php artisan migrate`

**Task: Cài đặt Laravel Sanctum**
- Mô tả: Cài đặt authentication package cho Vue.js SPA
- Files: `config/sanctum.php`
- Commands:
  - `composer require laravel/sanctum`
  - `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
  - `php artisan migrate`

**Task: Cài đặt Maatwebsite Excel**
- Mô tả: Cài đặt package export/import Excel cho timesheet và reports
- Files: `config/excel.php`
- Command: `composer require maatwebsite/excel`

**Task: Cài đặt Spatie Media Library Plugin**
- Mô tả: Cài đặt plugin quản lý media files (cover images, avatars)
- Command: `composer require filament/spatie-laravel-media-library-plugin`

**Task: Cài đặt Spatie Settings Plugin**
- Mô tả: Cài đặt plugin quản lý application settings
- Files: `config/settings.php`
- Command: `composer require filament/spatie-laravel-settings-plugin`

**Task: Cài đặt các Filament plugins khác**
- Mô tả: Cài đặt Breezy, Socialite, Avatar, Icon Picker, FontAwesome
- Commands:
  - `composer require jeffgreco13/filament-breezy`
  - `composer require dutchcodingcompany/filament-socialite`
  - `composer require devaslanphp/filament-avatar`
  - `composer require guava/filament-icon-picker`
  - `composer require owenvoke/blade-fontawesome`

### 1.3. Setup Frontend (Vue.js)

**Task: Cài đặt Node.js dependencies**
- Mô tả: Cài đặt tất cả JavaScript packages cho Vue.js frontend
- Files: `package.json`, `package-lock.json`, `node_modules/`
- Command: `npm install`

**Task: Cấu hình Vite**
- Mô tả: Cấu hình Vite build tool cho Vue.js
- Files: `vite.config.js`
- Checklist:
  - [ ] Cấu hình entry point: resources/js/app.js
  - [ ] Cấu hình output: public/build
  - [ ] Thêm Laravel và Vue plugins

**Task: Cài đặt Vue 3 và dependencies**
- Mô tả: Cài đặt Vue 3, Vue Router, Pinia, Axios
- Files: `package.json`
- Commands:
  - `npm install vue@^3.5.24`
  - `npm install vue-router@^4.6.3`
  - `npm install pinia@^2.3.1`
  - `npm install axios@^1.1.2`

**Task: Cài đặt Tailwind CSS và Flowbite**
- Mô tả: Cài đặt CSS framework và UI components
- Files: `tailwind.config.js`, `postcss.config.js`, `resources/css/app.css`
- Commands:
  - `npm install tailwindcss@^3.2.1`
  - `npm install flowbite@^1.5.3`
  - `npx tailwindcss init`

---

## PHASE 2: DATABASE & MODELS

### 2.1. Tạo Migrations

**Task: Migration create_users_table**
- Mô tả: Tạo bảng users với email, password, name, OIDC support, soft deletes
- Files: `database/migrations/2014_10_12_000000_create_users_table.php`
- Fields: id, name, email, password (nullable), email_verified_at, avatar, two_factor_secret, creation_token, deleted_at, timestamps

**Task: Migration create_projects_table**
- Mô tả: Tạo bảng projects với owner, status, type, ticket_prefix
- Files: `database/migrations/2022_11_02_124028_create_projects_table.php`
- Fields: id, name, description, owner_id, status_id, ticket_prefix (unique), status_type, type, deleted_at, timestamps

**Task: Migration create_tickets_table**
- Mô tả: Tạo bảng tickets - bảng quan trọng nhất với nhiều relationships
- Files: `database/migrations/2022_11_02_193242_create_tickets_table.php`
- Fields: id, code (unique), title, description, project_id, owner_id, responsible_id, status_id, type_id, priority_id, epic_id, sprint_id, order, estimation, deleted_at, timestamps

**Task: Migration create_ticket_hours_table**
- Mô tả: Tạo bảng timesheet entries cho tickets
- Files: `database/migrations/2022_11_10_193214_create_ticket_hours_table.php`
- Fields: id, ticket_id, user_id, activity_id, hours, date, comment, timestamps

**Task: Migration create_ticket_comments_table**
- Mô tả: Tạo bảng comments cho tickets
- Files: `database/migrations/2022_11_07_064347_create_ticket_comments_table.php`
- Fields: id, ticket_id, user_id, comment, timestamps

**Task: Migration create_ticket_relations_table**
- Mô tả: Tạo bảng relations giữa các tickets (related_to, blocked_by, duplicate_of)
- Files: `database/migrations/2022_11_08_163244_create_ticket_relations_table.php`
- Fields: id, ticket_id, related_ticket_id, type, timestamps

**Task: Migration create_sprints_table**
- Mô tả: Tạo bảng sprints cho Scrum projects
- Files: `database/migrations/2023_01_15_202225_create_sprints_table.php`
- Fields: id, project_id, epic_id, name, description, started_at, ended_at, timestamps

**Task: Migration create_epics_table**
- Mô tả: Tạo bảng epics cho RoadMap
- Files: `database/migrations/2022_12_15_100852_create_epics_table.php`
- Fields: id, project_id, parent_id, name, description, start_date, end_date, timestamps

**Task: Migration create_pivot_tables**
- Mô tả: Tạo các bảng pivot: project_users, project_favorites, ticket_subscribers
- Files:
  - `database/migrations/2022_11_02_131753_create_project_users_table.php`
  - `database/migrations/2022_11_02_152359_create_project_favorites_table.php`
  - `database/migrations/2022_11_08_084509_create_ticket_subscribers_table.php`

**Task: Migration create_referential_tables**
- Mô tả: Tạo các bảng referential: project_statuses, ticket_statuses, ticket_types, ticket_priorities, activities
- Files:
  - `database/migrations/2022_11_02_124027_create_project_statuses_table.php`
  - `database/migrations/2022_11_02_193241_create_ticket_statuses_table.php`
  - `database/migrations/2022_11_06_164004_create_ticket_types_table.php`
  - `database/migrations/2022_11_06_194000_create_ticket_priorities_table.php`
  - `database/migrations/2023_01_09_113159_create_activities_table.php`

**Task: Migration create_supporting_tables**
- Mô tả: Tạo các bảng hỗ trợ: media, notifications, jobs, settings, socialite_users
- Files: Các migrations tương ứng trong database/migrations/

**Task: Migration add_additional_fields**
- Mô tả: Tạo các migrations thêm fields: order, code, estimation, epic_id, sprint_id, etc.
- Files: Các migrations add_* trong database/migrations/

### 2.2. Tạo Models

**Task: Model User**
- Mô tả: Tạo User model với relationships: roles, permissions, favoriteProjects, projects, tickets, comments, hours
- Files: `app/Models/User.php`
- Traits: HasRoles (Spatie), Notifiable
- Relationships: favoriteProjects, projects, ownedProjects, tickets, comments, hours

**Task: Model Project**
- Mô tả: Tạo Project model với relationships và media handling
- Files: `app/Models/Project.php`
- Traits: HasFactory, SoftDeletes, InteractsWithMedia
- Relationships: owner, status, users, tickets, sprints, epics, favorites, statuses
- Accessor: cover (getFirstMediaUrl)

**Task: Model Ticket**
- Mô tả: Tạo Ticket model với nhiều relationships
- Files: `app/Models/Ticket.php`
- Traits: HasFactory, SoftDeletes
- Relationships: project, owner, responsible, status, type, priority, epic, sprint, comments, hours, subscribers, relations
- Methods: generateCode(), scopes (active, closed)

**Task: Model Sprint**
- Mô tả: Tạo Sprint model cho Scrum
- Files: `app/Models/Sprint.php`
- Relationships: project, epic, tickets
- Methods: start(), stop(), isActive()

**Task: Model Epic**
- Mô tả: Tạo Epic model cho RoadMap
- Files: `app/Models/Epic.php`
- Relationships: project, parent, children, tickets, sprints

**Task: Model TicketHour**
- Mô tả: Tạo TicketHour model cho timesheet
- Files: `app/Models/TicketHour.php`
- Relationships: ticket, user, activity

**Task: Model TicketComment**
- Mô tả: Tạo TicketComment model
- Files: `app/Models/TicketComment.php`
- Relationships: ticket, user

**Task: Model TicketRelation**
- Mô tả: Tạo TicketRelation model
- Files: `app/Models/TicketRelation.php`
- Relationships: ticket, relatedTicket

**Task: Model Referential**
- Mô tả: Tạo các referential models: ProjectStatus, TicketStatus, TicketType, TicketPriority, Activity
- Files: `app/Models/ProjectStatus.php`, `app/Models/TicketStatus.php`, etc.

### 2.3. Tạo Seeders

**Task: Seeder DatabaseSeeder**
- Mô tả: Main seeder gọi tất cả seeders khác
- Files: `database/seeders/DatabaseSeeder.php`
- Calls: DefaultUserSeeder, PermissionsSeeder, TicketTypeSeeder, TicketPrioritySeeder, TicketStatusSeeder, ActivitySeeder

**Task: Seeder DefaultUserSeeder**
- Mô tả: Tạo admin user mặc định (john.doe@helper.app / Passw@rd)
- Files: `database/seeders/DefaultUserSeeder.php`

**Task: Seeder PermissionsSeeder**
- Mô tả: Seed permissions và roles cơ bản
- Files: `database/seeders/PermissionsSeeder.php`
- Permissions: List projects, Create projects, Edit projects, Delete projects, etc.

**Task: Seeder TicketTypeSeeder**
- Mô tả: Seed default ticket types (Bug, Feature, Task, etc.)
- Files: `database/seeders/TicketTypeSeeder.php`

**Task: Seeder TicketPrioritySeeder**
- Mô tả: Seed default ticket priorities (Low, Medium, High, Critical)
- Files: `database/seeders/TicketPrioritySeeder.php`

**Task: Seeder TicketStatusSeeder**
- Mô tả: Seed default ticket statuses (To Do, In Progress, Done, etc.)
- Files: `database/seeders/TicketStatusSeeder.php`

**Task: Seeder ActivitySeeder**
- Mô tả: Seed default activities cho timesheet (Development, Testing, Meeting, etc.)
- Files: `database/seeders/ActivitySeeder.php`

---

## PHASE 3: AUTHENTICATION & AUTHORIZATION

### 3.1. Authentication Setup

**Task: Tạo AuthController**
- Mô tả: Controller xử lý login, logout, session management cho Vue.js frontend
- Files: `app/Http/Controllers/Auth/AuthController.php`
- Methods: login(), logout(), user()
- Routes: POST /api/login, POST /api/logout, GET /api/user

**Task: Tạo OidcAuthController**
- Mô tả: Controller xử lý OIDC authentication
- Files: `app/Http/Controllers/Auth/OidcAuthController.php`

**Task: Cấu hình Authentication**
- Mô tả: Cấu hình auth.php và sanctum.php
- Files: `config/auth.php`, `config/sanctum.php`
- Checklist:
  - [ ] Cấu hình guards (web, api)
  - [ ] Cấu hình providers
  - [ ] Cấu hình Sanctum SPA authentication

**Task: Tạo Middleware Authenticate**
- Mô tả: Middleware kiểm tra authentication (đã có sẵn, chỉ cần cấu hình)
- Files: `app/Http/Middleware/Authenticate.php`, `app/Http/Kernel.php`

**Task: Tạo Middleware VerifyCsrfToken**
- Mô tả: Middleware bảo vệ CSRF (đã có sẵn, chỉ cần cấu hình)
- Files: `app/Http/Middleware/VerifyCsrfToken.php`

### 3.2. Authorization (Policies)

**Task: Policy ProjectPolicy**
- Mô tả: Định nghĩa authorization rules cho Project
- Files: `app/Policies/ProjectPolicy.php`
- Methods: viewAny, view, create, update, delete, attachUser

**Task: Policy TicketPolicy**
- Mô tả: Định nghĩa authorization rules cho Ticket
- Files: `app/Policies/TicketPolicy.php`
- Methods: viewAny, view, create, update, delete, subscribe, logHours

**Task: Policy UserPolicy**
- Mô tả: Định nghĩa authorization rules cho User
- Files: `app/Policies/UserPolicy.php`

**Task: Policy các Resources khác**
- Mô tả: Tạo policies cho Sprint, Epic, Timesheet, Activity, ProjectStatus, TicketStatus, TicketType, TicketPriority
- Files: `app/Policies/*.php`

**Task: Đăng ký Policies**
- Mô tả: Đăng ký tất cả policies trong AuthServiceProvider
- Files: `app/Providers/AuthServiceProvider.php`

### 3.3. Permissions & Roles

**Task: Cấu hình Spatie Permission**
- Mô tả: Cấu hình permission.php
- Files: `config/permission.php`

**Task: Tạo Permissions và Roles**
- Mô tả: Tạo permissions và roles trong PermissionsSeeder
- Files: `database/seeders/PermissionsSeeder.php`
- Roles: admin, manager, developer, customer
- Permissions: List/Create/Edit/Delete cho mỗi resource

---

## PHASE 4: BACKEND API - RESOURCE CONTROLLERS

### 4.1. Core Resource Controllers

**Task: ProjectController - CRUD**
- Mô tả: Controller xử lý CRUD operations cho Project
- Files: `app/Http/Controllers/Api/Resources/ProjectController.php`
- Methods: index(), store(), show(), update(), destroy()
- Routes: Route::apiResource('projects', ProjectController::class)

**Task: ProjectController - uploadCover**
- Mô tả: Method upload cover image cho project
- Files: `app/Http/Controllers/Api/Resources/ProjectController.php`
- Route: POST /api/projects/{project}/cover

**Task: ProjectController - toggleFavorite**
- Mô tả: Method toggle favorite project
- Files: `app/Http/Controllers/Api/Resources/ProjectController.php`
- Route: POST /api/projects/{project}/favorite

**Task: ProjectController - exportHours**
- Mô tả: Method export project hours ra Excel
- Files: `app/Http/Controllers/Api/Resources/ProjectController.php`
- Route: GET /api/projects/{project}/export-hours

**Task: ProjectController - User Management**
- Mô tả: Methods quản lý users trong project: getUsers, attachUser, updateUserRole, detachUser
- Files: `app/Http/Controllers/Api/Resources/ProjectController.php`
- Routes: GET/POST/PUT/DELETE /api/projects/{project}/users

**Task: ProjectController - Sprints và Statuses**
- Mô tả: Methods getSprints, getStatuses
- Files: `app/Http/Controllers/Api/Resources/ProjectController.php`
- Routes: GET /api/projects/{project}/sprints, GET /api/projects/{project}/statuses

**Task: TicketController - CRUD**
- Mô tả: Controller xử lý CRUD operations cho Ticket
- Files: `app/Http/Controllers/Api/Resources/TicketController.php`
- Methods: index() (với filters), store() (auto-generate code), show(), update(), destroy()
- Routes: Route::apiResource('tickets', TicketController::class)

**Task: TicketController - Subscribers**
- Mô tả: Methods quản lý subscribers: getSubscribers, subscribe, unsubscribe
- Files: `app/Http/Controllers/Api/Resources/TicketController.php`
- Routes: GET/POST/DELETE /api/tickets/{ticket}/subscribers

**Task: TicketController - Hours**
- Mô tả: Methods quản lý timesheet: getHours, logHours, exportHours
- Files: `app/Http/Controllers/Api/Resources/TicketController.php`
- Routes: GET/POST /api/tickets/{ticket}/hours, GET /api/tickets/{ticket}/export-hours

**Task: UserController - CRUD**
- Mô tả: Controller xử lý CRUD operations cho User
- Files: `app/Http/Controllers/Api/Resources/UserController.php`
- Methods: index(), store(), show(), update(), destroy(), syncRoles()
- Routes: Route::apiResource('users', UserController::class)

**Task: SprintController - CRUD và Actions**
- Mô tả: Controller quản lý Sprints với start/stop actions
- Files: `app/Http/Controllers/Api/Resources/SprintController.php`
- Methods: CRUD + start(), stop(), associateTickets()
- Routes: Route::apiResource('sprints', SprintController::class) + custom routes

**Task: EpicController - CRUD**
- Mô tả: Controller quản lý Epics
- Files: `app/Http/Controllers/Api/Resources/EpicController.php`
- Routes: Route::apiResource('epics', EpicController::class)

**Task: TimesheetController - CRUD**
- Mô tả: Controller quản lý Timesheet entries
- Files: `app/Http/Controllers/Api/Resources/TimesheetController.php`
- Routes: Route::apiResource('timesheets', TimesheetController::class)

### 4.2. Nested Resource Controllers

**Task: TicketCommentController**
- Mô tả: Controller quản lý comments của tickets (nested resource)
- Files: `app/Http/Controllers/Api/Resources/TicketCommentController.php`
- Methods: index(), store(), update(), destroy()
- Routes: GET/POST/PUT/DELETE /api/tickets/{ticket}/comments

**Task: TicketRelationController**
- Mô tả: Controller quản lý relations giữa tickets (nested resource)
- Files: `app/Http/Controllers/Api/Resources/TicketRelationController.php`
- Methods: index(), store(), destroy()
- Routes: GET/POST/DELETE /api/tickets/{ticket}/relations

### 4.3. Referential Resource Controllers

**Task: ActivityController**
- Mô tả: Controller CRUD cho Activities
- Files: `app/Http/Controllers/Api/Resources/ActivityController.php`
- Routes: Route::apiResource('activities', ActivityController::class)

**Task: ProjectStatusController**
- Mô tả: Controller CRUD cho ProjectStatuses với default logic
- Files: `app/Http/Controllers/Api/Resources/ProjectStatusController.php`
- Routes: Route::apiResource('project-statuses', ProjectStatusController::class)

**Task: TicketStatusController**
- Mô tả: Controller CRUD cho TicketStatuses với order management
- Files: `app/Http/Controllers/Api/Resources/TicketStatusController.php`
- Routes: Route::apiResource('ticket-statuses', TicketStatusController::class)

**Task: TicketTypeController**
- Mô tả: Controller CRUD cho TicketTypes với default logic
- Files: `app/Http/Controllers/Api/Resources/TicketTypeController.php`
- Routes: Route::apiResource('ticket-types', TicketTypeController::class)

**Task: TicketPriorityController**
- Mô tả: Controller CRUD cho TicketPriorities với default logic
- Files: `app/Http/Controllers/Api/Resources/TicketPriorityController.php`
- Routes: Route::apiResource('ticket-priorities', TicketPriorityController::class)

**Task: PermissionController**
- Mô tả: Controller CRUD cho Permissions
- Files: `app/Http/Controllers/Api/Resources/PermissionController.php`
- Routes: Route::apiResource('permissions', PermissionController::class)

**Task: RoleController**
- Mô tả: Controller CRUD cho Roles với sync permissions
- Files: `app/Http/Controllers/Api/Resources/RoleController.php`
- Routes: Route::apiResource('roles', RoleController::class)

---

## PHASE 5: BACKEND API - PAGE CONTROLLERS

### 5.1. Dashboard Controller

**Task: DashboardController - stats**
- Mô tả: Method trả về thống kê tổng quan (projects count, tickets count, active tickets, users)
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/stats

**Task: DashboardController - favoriteProjects**
- Mô tả: Method trả về favorite projects widget data
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/favorite-projects

**Task: DashboardController - latestActivities**
- Mô tả: Method trả về latest activities widget data
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/latest-activities

**Task: DashboardController - latestComments**
- Mô tả: Method trả về latest comments widget data
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/latest-comments

**Task: DashboardController - latestProjects**
- Mô tả: Method trả về latest projects widget data
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/latest-projects

**Task: DashboardController - latestTickets**
- Mô tả: Method trả về latest tickets widget data
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/latest-tickets

**Task: DashboardController - ticketsByPriority**
- Mô tả: Method trả về chart data tickets by priority
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/tickets-by-priority

**Task: DashboardController - ticketsByType**
- Mô tả: Method trả về chart data tickets by type
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/tickets-by-type

**Task: DashboardController - ticketTimeLogged**
- Mô tả: Method trả về chart data time logged by tickets
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/ticket-time-logged

**Task: DashboardController - userTimeLogged**
- Mô tả: Method trả về chart data time logged by users
- Files: `app/Http/Controllers/Api/Pages/DashboardController.php`
- Route: GET /api/dashboard/user-time-logged

### 5.2. Board Controller

**Task: BoardController - getProjects**
- Mô tả: Method trả về danh sách projects cho board selection
- Files: `app/Http/Controllers/Api/Pages/BoardController.php`
- Route: GET /api/board/projects

**Task: BoardController - getStatuses**
- Mô tả: Method trả về statuses với ticket counts
- Files: `app/Http/Controllers/Api/Pages/BoardController.php`
- Route: GET /api/board/statuses

**Task: BoardController - getKanbanTickets**
- Mô tả: Method trả về tickets cho Kanban board, group theo status
- Files: `app/Http/Controllers/Api/Pages/BoardController.php`
- Route: GET /api/projects/{project}/kanban/tickets

**Task: BoardController - getScrumSprint**
- Mô tả: Method trả về sprint info cho Scrum board
- Files: `app/Http/Controllers/Api/Pages/BoardController.php`
- Route: GET /api/projects/{project}/scrum/sprint

**Task: BoardController - getScrumTickets**
- Mô tả: Method trả về tickets cho Scrum board
- Files: `app/Http/Controllers/Api/Pages/BoardController.php`
- Route: GET /api/projects/{project}/scrum/tickets

**Task: BoardController - moveTicket**
- Mô tả: Method xử lý drag & drop ticket, cập nhật status và order
- Files: `app/Http/Controllers/Api/Pages/BoardController.php`
- Route: PUT /api/tickets/{ticket}/move

### 5.3. RoadMap Controller

**Task: RoadMapController - getRoadmap**
- Mô tả: Method trả về epic data với tickets cho RoadMap
- Files: `app/Http/Controllers/Api/Pages/RoadMapController.php`
- Route: GET /api/projects/{project}/roadmap

**Task: RoadMapController - getRoadmapDates**
- Mô tả: Method trả về date range cho Gantt chart
- Files: `app/Http/Controllers/Api/Pages/RoadMapController.php`
- Route: GET /api/projects/{project}/roadmap/dates

### 5.4. Timesheet Dashboard Controller

**Task: TimesheetDashboardController - monthlyReport**
- Mô tả: Method trả về báo cáo timesheet theo tháng
- Files: `app/Http/Controllers/Api/Pages/TimesheetDashboardController.php`
- Route: GET /api/timesheet/monthly-report

**Task: TimesheetDashboardController - weeklyReport**
- Mô tả: Method trả về báo cáo timesheet theo tuần
- Files: `app/Http/Controllers/Api/Pages/TimesheetDashboardController.php`
- Route: GET /api/timesheet/weekly-report

**Task: TimesheetDashboardController - activitiesReport**
- Mô tả: Method trả về báo cáo timesheet theo activities
- Files: `app/Http/Controllers/Api/Pages/TimesheetDashboardController.php`
- Route: GET /api/timesheet/activities-report

---

## PHASE 6: API ROUTES

**Task: Cấu hình API Routes**
- Mô tả: Định nghĩa tất cả API routes trong routes/api.php
- Files: `routes/api.php`
- Checklist:
  - [ ] Public routes (login)
  - [ ] Protected routes group (middleware: web, auth)
  - [ ] Dashboard routes (/api/dashboard/*)
  - [ ] Resource routes (/api/projects, /api/tickets, etc.)
  - [ ] Nested routes (/api/tickets/{ticket}/comments)
  - [ ] Board routes (/api/board/*, /api/projects/{project}/kanban/*)
  - [ ] RoadMap routes (/api/projects/{project}/roadmap/*)
  - [ ] Timesheet routes (/api/timesheet/*)

**Task: Cấu hình Web Routes**
- Mô tả: Cấu hình web routes cho Filament admin panel (backend only)
- Files: `routes/web.php`

---

## PHASE 7: FILAMENT ADMIN PANEL (Backend Only)

### 7.1. Filament Resources

**Task: ProjectResource**
- Mô tả: Filament resource quản lý Projects trong admin panel
- Files: `app/Filament/Resources/ProjectResource.php`
- Features: CRUD, cover upload, favorite, users, sprints, statuses

**Task: TicketResource**
- Mô tả: Filament resource quản lý Tickets trong admin panel
- Files: `app/Filament/Resources/TicketResource.php`
- Features: CRUD, subscribers, hours, export

**Task: UserResource**
- Mô tả: Filament resource quản lý Users trong admin panel
- Files: `app/Filament/Resources/UserResource.php`
- Features: CRUD, roles sync

**Task: Các Resources khác**
- Mô tả: Tạo Filament resources cho Sprint, Epic, Timesheet, Permission, Role, Activity, ProjectStatus, TicketStatus, TicketType, TicketPriority
- Files: `app/Filament/Resources/*.php`

### 7.2. Filament Relation Managers

**Task: SprintsRelationManager**
- Mô tả: Relation manager quản lý sprints của project
- Files: `app/Filament/Resources/ProjectResource/RelationManagers/SprintsRelationManager.php`

**Task: UsersRelationManager**
- Mô tả: Relation manager quản lý users của project
- Files: `app/Filament/Resources/ProjectResource/RelationManagers/UsersRelationManager.php`

**Task: StatusesRelationManager**
- Mô tả: Relation manager quản lý custom statuses của project
- Files: `app/Filament/Resources/ProjectResource/RelationManagers/StatusesRelationManager.php`

### 7.3. Filament Pages

**Task: Filament Dashboard Page**
- Mô tả: Dashboard page với widgets trong admin panel
- Files: `app/Filament/Pages/Dashboard.php`

**Task: Filament Kanban/Scrum/Board Pages**
- Mô tả: Các pages cho Kanban, Scrum, Board selection
- Files: `app/Filament/Pages/Kanban.php`, `app/Filament/Pages/Scrum.php`, `app/Filament/Pages/Board.php`

**Task: Filament RoadMap và Timesheet Pages**
- Mô tả: RoadMap, TimesheetDashboard, TimesheetExport pages
- Files: `app/Filament/Pages/RoadMap.php`, `app/Filament/Pages/TimesheetDashboard.php`, etc.

### 7.4. Filament Widgets

**Task: Dashboard Widgets**
- Mô tả: Tạo các widgets cho Filament dashboard: FavoriteProjects, LatestActivities, LatestComments, LatestProjects, LatestTickets
- Files: `app/Filament/Widgets/*.php`

**Task: Chart Widgets**
- Mô tả: Tạo chart widgets: TicketsByPriority, TicketsByType, TicketTimeLogged, UserTimeLogged
- Files: `app/Filament/Widgets/*.php`

**Task: Timesheet Widgets**
- Mô tả: Tạo timesheet report widgets: MonthlyReport, WeeklyReport, ActivitiesReport
- Files: `app/Filament/Widgets/Timesheet/*.php`

---

## PHASE 8: FRONTEND VUE.JS

### 8.1. Core Setup

**Task: Tạo app.js (entry point)**
- Mô tả: File entry point khởi tạo Vue app, Pinia, Router
- Files: `resources/js/app.js`

**Task: Tạo App.vue (root component)**
- Mô tả: Root Vue component với router-view
- Files: `resources/js/App.vue`

**Task: Tạo bootstrap.js**
- Mô tả: Setup Axios và CSRF token
- Files: `resources/js/bootstrap.js`

### 8.2. Router Setup

**Task: Tạo router/index.js**
- Mô tả: Vue Router configuration với routes và navigation guards
- Files: `resources/js/router/index.js`
- Routes: /login, /dashboard, /projects, /tickets, /users, /board, /roadmap, /timesheet
- Guards: Authentication check, redirect logic

### 8.3. State Management (Pinia)

**Task: Tạo Auth Store**
- Mô tả: Pinia store quản lý authentication state
- Files: `resources/js/stores/auth.js` hoặc `resources/js/stores/index.js`
- State: user, isAuthenticated, loading, error
- Actions: login, logout, fetchUser

**Task: Tạo Projects Store**
- Mô tả: Pinia store quản lý projects state
- Files: `resources/js/stores/projects.js`
- State: projects, currentProject, loading, pagination
- Actions: fetchProjects, fetchProject, createProject, updateProject, deleteProject, toggleFavorite

**Task: Tạo Tickets Store**
- Mô tả: Pinia store quản lý tickets state
- Files: `resources/js/stores/tickets.js`
- State: tickets, currentTicket, loading, pagination
- Actions: fetchTickets, fetchTicket, createTicket, updateTicket, deleteTicket, subscribe, logHours

**Task: Tạo Users Store**
- Mô tả: Pinia store quản lý users state
- Files: `resources/js/stores/users.js`

**Task: Tạo Dashboard Store**
- Mô tả: Pinia store quản lý dashboard data
- Files: `resources/js/stores/dashboard.js`
- Actions: fetchStats, fetchFavoriteProjects, fetchLatestActivities, etc.

**Task: Tạo Board Store**
- Mô tả: Pinia store quản lý Kanban/Scrum board state
- Files: `resources/js/stores/board.js`
- Actions: fetchKanbanTickets, fetchScrumTickets, moveTicket

### 8.4. API Service

**Task: Tạo services/api.js**
- Mô tả: Axios instance và interceptors cho API calls
- Files: `resources/js/services/api.js`
- Features: baseURL, request/response interceptors, error handling

### 8.5. Components

**Task: Tạo Layout.vue**
- Mô tả: Main layout component với header, sidebar, content area
- Files: `resources/js/components/Layout.vue`

**Task: Tạo Sidebar.vue**
- Mô tả: Sidebar navigation component
- Files: `resources/js/components/Sidebar.vue`

**Task: Tạo Card.vue**
- Mô tả: Reusable card component
- Files: `resources/js/components/Card.vue`

**Task: Tạo Modal.vue**
- Mô tả: Reusable modal component
- Files: `resources/js/components/Modal.vue`

**Task: Tạo DataTable.vue**
- Mô tả: Reusable data table component với pagination, sorting, filtering
- Files: `resources/js/components/DataTable.vue`

**Task: Tạo Form Components**
- Mô tả: FormInput, FormSelect components
- Files: `resources/js/components/FormInput.vue`, `resources/js/components/FormSelect.vue`

**Task: Tạo Board Components**
- Mô tả: KanbanBoard, ScrumBoard components
- Files: `resources/js/components/KanbanBoard.vue`, `resources/js/components/ScrumBoard.vue`

**Task: Tạo Chart Component**
- Mô tả: Chart component cho dashboard (Chart.js hoặc ApexCharts)
- Files: `resources/js/components/Chart.vue`

### 8.6. Views/Pages

**Task: Tạo Login.vue**
- Mô tả: Login page với form đăng nhập
- Files: `resources/js/views/Login.vue`

**Task: Tạo Dashboard.vue**
- Mô tả: Dashboard page với widgets và charts
- Files: `resources/js/views/Dashboard.vue`

**Task: Tạo Projects.vue**
- Mô tả: Projects list page với DataTable
- Files: `resources/js/views/Projects.vue`

**Task: Tạo ProjectDetail.vue**
- Mô tả: Project detail page với tabs (info, tickets, users, sprints)
- Files: `resources/js/views/ProjectDetail.vue`

**Task: Tạo Tickets.vue**
- Mô tả: Tickets list page với filters
- Files: `resources/js/views/Tickets.vue`
- Checklist:
  - [x] Cải thiện bảo mật: Thêm kiểm tra quyền cho nút "Create Ticket".

**Task: Tạo TicketDetail.vue**
- Mô tả: Ticket detail page với comments, hours, relations
- Files: `resources/js/views/TicketDetail.vue`

**Task: Tạo Users.vue**
- Mô tả: Users list page
- Files: `resources/js/views/Users.vue`

**Task: Tạo Board Pages**
- Mô tả: BoardSelection, KanbanBoard, ScrumBoard pages
- Files: `resources/js/views/BoardSelection.vue`, `resources/js/views/KanbanBoard.vue`, `resources/js/views/ScrumBoard.vue`

**Task: Tạo RoadMap.vue**
- Mô tả: RoadMap page với Gantt chart
- Files: `resources/js/views/RoadMap.vue`

**Task: Tạo TimesheetDashboard.vue**
- Mô tả: Timesheet dashboard page với reports
- Files: `resources/js/views/TimesheetDashboard.vue`

### 8.7. Styles

**Task: Cấu hình Tailwind CSS**
- Mô tả: Setup Tailwind trong app.css
- Files: `resources/css/app.css`

**Task: Tạo Kanban Styles**
- Mô tả: Custom styles cho Kanban board
- Files: `resources/css/kanban.scss`

**Task: Tạo Dialog Styles**
- Mô tả: Custom styles cho modals/dialogs
- Files: `resources/css/dialogs.scss`

---

## PHASE 9: HELPERS & UTILITIES

**Task: Tạo JiraHelper**
- Mô tả: Helper class cho Jira integration
- Files: `app/Helpers/JiraHelper.php`

**Task: Tạo KanbanScrumHelper**
- Mô tả: Helper class cho Kanban/Scrum boards
- Files: `app/Helpers/KanbanScrumHelper.php`

**Task: Tạo Export Classes**
- Mô tả: Export classes cho Excel: ProjectHoursExport, TicketHoursExport, TimesheetExport
- Files: `app/Exports/*.php`

**Task: Tạo ImportJiraTicketsJob**
- Mô tả: Background job import tickets từ Jira
- Files: `app/Jobs/ImportJiraTicketsJob.php`

**Task: Tạo Notifications**
- Mô tả: Notification classes: TicketCreated, TicketCommented, TicketStatusUpdated, UserCreatedNotification
- Files: `app/Notifications/*.php`

**Task: Tạo Livewire Components**
- Mô tả: Livewire components cho Filament: Profile, EpicForm, IssueForm, Attachments, TimeLogged, ValidateAccount
- Files: `app/Http/Livewire/*.php`

---

## PHASE 10: CONFIGURATION & SETTINGS

**Task: Cấu hình config files**
- Mô tả: Cấu hình app.php, database.php, filesystems.php, mail.php, cache.php, queue.php, filament.php, system.php, permission.php, settings.php
- Files: `config/*.php`

**Task: Tạo GeneralSettings**
- Mô tả: Settings class quản lý general settings
- Files: `app/Settings/GeneralSettings.php`

---

## PHASE 11: STORAGE & MEDIA

**Task: Setup File Storage**
- Mô tả: Cấu hình storage và tạo symlink
- Files: `config/filesystems.php`
- Command: `php artisan storage:link`
- Folders: storage/app/public/projects/, storage/app/public/avatars/, storage/app/public/tickets/

---

## PHASE 12: TESTING & VALIDATION

**Task: Tạo Unit Tests**
- Mô tả: Unit tests cho Models
- Files: `tests/Unit/*.php`

**Task: Tạo Feature Tests**
- Mô tả: Feature tests cho API endpoints, Authentication, Authorization
- Files: `tests/Feature/*.php`

**Task: Tạo Form Requests**
- Mô tả: Form Request classes cho validation: StoreProjectRequest, UpdateProjectRequest, StoreTicketRequest, etc.
- Files: `app/Http/Requests/*.php`

---

## PHASE 13: DEPLOYMENT PREPARATION

**Task: Build Frontend**
- Mô tả: Build Vue.js frontend với Vite
- Command: `npm run build`
- Output: `public/build/`

**Task: Optimize Laravel**
- Mô tả: Cache config, routes, views
- Commands:
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`

**Task: Tạo Documentation**
- Mô tả: Tạo README.md, PROJECT_STRUCTURE.md, API_COMPARISON.md, API_DOCUMENTATION.md
- Files: `README.md`, `PROJECT_STRUCTURE.md`, `API_COMPARISON.md`, `API_DOCUMENTATION.md`

**Task: Tạo .env.example**
- Mô tả: Tạo template .env với tất cả variables
- Files: `.env.example`

---

## PHASE 14: OPTIONAL FEATURES

**Task: Jira Integration**
- Mô tả: Implement Jira import functionality
- Files: `app/Jobs/ImportJiraTicketsJob.php`, `app/Helpers/JiraHelper.php`

**Task: Email Notifications**
- Mô tả: Setup email notifications
- Files: `config/mail.php`, `app/Notifications/*.php`

**Task: Real-time Updates**
- Mô tả: Implement broadcasting cho real-time updates
- Files: `config/broadcasting.php`, `routes/channels.php`

**Task: Search Functionality**
- Mô tả: Implement advanced search
- Files: Controllers, Models (scopes)

**Task: File Attachments**
- Mô tả: Implement file attachments handling
- Files: Controllers, Models, storage setup