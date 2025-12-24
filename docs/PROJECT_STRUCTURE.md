# Cấu trúc Dự án - Project Management System

## 📋 Tổng quan

Dự án này là một hệ thống quản lý dự án (Project Management System) được xây dựng với:
- **Backend**: Laravel 10.x + Filament (Admin Panel)
- **Frontend**: Vue.js 3 (SPA) + Filament (Admin UI)
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum + Session-based

---

## 🗂️ Cấu trúc Thư mục Chính

### 📁 `/app` - Application Core (Quan trọng nhất)

Thư mục chứa toàn bộ logic nghiệp vụ của ứng dụng.

#### `/app/Http/Controllers` - Controllers (Rất quan trọng)

**Vai trò**: Xử lý HTTP requests và trả về responses.

##### `/app/Http/Controllers/Api/Resources/` - API Resource Controllers
**Vai trò**: Xử lý CRUD operations cho các resources qua API RESTful.

**Controllers quan trọng**:
- `ProjectController.php` - Quản lý Projects (CRUD, cover image, favorite, users, sprints, statuses)
- `TicketController.php` - Quản lý Tickets (CRUD, subscribers, hours logging, export)
- `UserController.php` - Quản lý Users (CRUD, roles sync)
- `SprintController.php` - Quản lý Sprints (CRUD, start/stop, associate tickets)
- `EpicController.php` - Quản lý Epics cho RoadMap
- `TicketCommentController.php` - Quản lý Comments của Tickets (nested resource)
- `TicketRelationController.php` - Quản lý Relations giữa Tickets (nested resource)
- `TimesheetController.php` - Quản lý Timesheet entries (TicketHour)
- `PermissionController.php` - Quản lý Permissions
- `RoleController.php` - Quản lý Roles và sync permissions
- `ActivityController.php` - Quản lý Activities
- `ProjectStatusController.php` - Quản lý Project Statuses
- `TicketStatusController.php` - Quản lý Ticket Statuses
- `TicketTypeController.php` - Quản lý Ticket Types
- `TicketPriorityController.php` - Quản lý Ticket Priorities

##### `/app/Http/Controllers/Api/Pages/` - API Page Controllers
**Vai trò**: Xử lý các endpoints cho các trang đặc biệt (tương ứng với Filament Pages).

**Controllers quan trọng**:
- `DashboardController.php` - **Rất quan trọng**: Cung cấp data cho Dashboard widgets
  - `stats()` - Thống kê tổng quan
  - `favoriteProjects()` - Widget favorite projects
  - `latestActivities()` - Widget latest activities
  - `latestComments()` - Widget latest comments
  - `latestProjects()` - Widget latest projects
  - `latestTickets()` - Widget latest tickets
  - `ticketsByPriority()` - Chart data tickets by priority
  - `ticketsByType()` - Chart data tickets by type
  - `ticketTimeLogged()` - Chart data time logged by tickets
  - `userTimeLogged()` - Chart data time logged by users
- `BoardController.php` - **Quan trọng**: Cung cấp data cho Kanban/Scrum boards
  - `getProjects()` - Danh sách projects cho board selection
  - `getStatuses()` - Statuses với ticket counts
  - `getKanbanTickets()` - Tickets cho Kanban board
  - `getScrumSprint()` - Sprint info cho Scrum board
  - `getScrumTickets()` - Tickets cho Scrum board
  - `moveTicket()` - Di chuyển ticket (drag & drop)
- `RoadMapController.php` - Cung cấp data cho RoadMap view
  - `getRoadmap()` - Epic data với tickets
  - `getRoadmapDates()` - Date range cho Gantt chart
- `TimesheetDashboardController.php` - Cung cấp data cho Timesheet Dashboard
  - `monthlyReport()` - Báo cáo theo tháng
  - `weeklyReport()` - Báo cáo theo tuần
  - `activitiesReport()` - Báo cáo theo activities

##### `/app/Http/Controllers/Auth/` - Authentication Controllers
**Vai trò**: Xử lý authentication và authorization.

- `AuthController.php` - **Quan trọng**: Login, logout, session management
- `OidcAuthController.php` - OIDC authentication

##### `/app/Http/Controllers/RoadMap/` - RoadMap Controllers
- `DataController.php` - Xử lý data cho RoadMap (có thể được thay thế bởi RoadMapController)

#### `/app/Models` - Eloquent Models (Rất quan trọng)

**Vai trò**: Định nghĩa database models và relationships.

**Models quan trọng**:
- `User.php` - **Rất quan trọng**: User model với roles, permissions, favorite projects
- `Project.php` - **Rất quan trọng**: Project model với relationships (owner, status, users, tickets, sprints, epics)
- `Ticket.php` - **Rất quan trọng**: Ticket model với relationships (owner, responsible, project, status, type, priority, comments, hours, subscribers)
- `Sprint.php` - Sprint model cho Scrum projects
- `Epic.php` - Epic model cho RoadMap
- `TicketHour.php` - Timesheet entries
- `TicketComment.php` - Comments trên tickets
- `TicketRelation.php` - Relations giữa tickets
- `TicketSubscriber.php` - Users subscribed to tickets
- `ProjectFavorite.php` - Favorite projects của users
- `ProjectUser.php` - Pivot table cho project-user relationships với role
- `TicketActivity.php` - Activity log cho tickets
- `Activity.php` - Activity types
- `ProjectStatus.php` - Project statuses
- `TicketStatus.php` - Ticket statuses
- `TicketType.php` - Ticket types
- `TicketPriority.php` - Ticket priorities
- `Permission.php` - Permissions (Spatie)
- `Role.php` - Roles (Spatie)

#### `/app/Filament` - Filament Admin Panel (Quan trọng)

**Vai trò**: Cung cấp admin interface cho quản trị viên.

##### `/app/Filament/Resources/` - Filament Resources
**Vai trò**: Định nghĩa CRUD interfaces cho các resources trong admin panel.

**Resources quan trọng**:
- `ProjectResource.php` - Quản lý Projects trong admin
- `TicketResource.php` - Quản lý Tickets trong admin
- `UserResource.php` - Quản lý Users trong admin
- `SprintResource.php` - Quản lý Sprints
- `EpicResource.php` - Quản lý Epics
- `TimesheetResource.php` - Quản lý Timesheet
- `PermissionResource.php` - Quản lý Permissions
- `RoleResource.php` - Quản lý Roles
- Các referential resources: `ActivityResource`, `ProjectStatusResource`, `TicketStatusResource`, `TicketTypeResource`, `TicketPriorityResource`

**Relation Managers** (trong `ProjectResource/RelationManagers/`):
- `SprintsRelationManager.php` - Quản lý sprints của project
- `UsersRelationManager.php` - Quản lý users của project
- `StatusesRelationManager.php` - Quản lý custom statuses của project

##### `/app/Filament/Pages/` - Filament Pages
**Vai trò**: Các trang đặc biệt trong admin panel.

- `Dashboard.php` - Dashboard page với widgets
- `Kanban.php` - Kanban board page
- `Scrum.php` - Scrum board page
- `Board.php` - Board selection page
- `RoadMap.php` - RoadMap page
- `TimesheetDashboard.php` - Timesheet dashboard page
- `TimesheetExport.php` - Timesheet export page
- `JiraImport.php` - Jira import page
- `ManageGeneralSettings.php` - General settings page

##### `/app/Filament/Widgets/` - Filament Widgets
**Vai trò**: Dashboard widgets hiển thị thống kê và charts.

- `FavoriteProjects.php` - Favorite projects widget
- `LatestActivities.php` - Latest activities widget
- `LatestComments.php` - Latest comments widget
- `LatestProjects.php` - Latest projects widget
- `LatestTickets.php` - Latest tickets widget
- `TicketsByPriority.php` - Chart tickets by priority
- `TicketsByType.php` - Chart tickets by type
- `TicketTimeLogged.php` - Chart time logged by tickets
- `UserTimeLogged.php` - Chart time logged by users
- `Timesheet/MonthlyReport.php` - Monthly timesheet report
- `Timesheet/WeeklyReport.php` - Weekly timesheet report
- `Timesheet/ActivitiesReport.php` - Activities timesheet report

#### `/app/Policies` - Authorization Policies (Quan trọng)

**Vai trò**: Định nghĩa authorization rules cho các resources.

**Policies quan trọng**:
- `ProjectPolicy.php` - Authorization cho Projects
- `TicketPolicy.php` - Authorization cho Tickets
- `UserPolicy.php` - Authorization cho Users
- Các policies khác cho resources tương ứng

#### `/app/Providers` - Service Providers (Quan trọng)

**Vai trò**: Đăng ký services, bindings, và cấu hình.

**Providers quan trọng**:
- `AppServiceProvider.php` - **Rất quan trọng**: Đăng ký services chính
- `AuthServiceProvider.php` - **Quan trọng**: Đăng ký policies
- `RouteServiceProvider.php` - **Quan trọng**: Cấu hình routes
- `EventServiceProvider.php` - Đăng ký events và listeners
- `BroadcastServiceProvider.php` - Broadcasting configuration

#### `/app/Http/Middleware` - HTTP Middleware (Quan trọng)

**Vai trò**: Xử lý requests trước khi đến controllers.

**Middleware quan trọng**:
- `Authenticate.php` - **Rất quan trọng**: Kiểm tra authentication
- `VerifyCsrfToken.php` - **Quan trọng**: CSRF protection
- `LocaleMiddleware.php` - Xử lý đa ngôn ngữ
- `EncryptCookies.php` - Mã hóa cookies
- `TrustProxies.php` - Trust proxy headers

#### `/app/Http/Livewire` - Livewire Components

**Vai trò**: Server-side rendered components với real-time updates.

- `Profile.php` - User profile component
- `RoadMap/EpicForm.php` - Epic form component
- `RoadMap/IssueForm.php` - Issue form component
- `Ticket/Attachments.php` - Ticket attachments component
- `Timesheet/TimeLogged.php` - Timesheet component
- `ValidateAccount.php` - Account validation component

#### `/app/Exports` - Excel Exports (Quan trọng)

**Vai trò**: Export data ra Excel/CSV.

- `ProjectHoursExport.php` - Export project hours
- `TicketHoursExport.php` - Export ticket hours
- `TimesheetExport.php` - Export timesheet

#### `/app/Jobs` - Background Jobs

**Vai trò**: Xử lý các tác vụ chạy nền.

- `ImportJiraTicketsJob.php` - Import tickets từ Jira

#### `/app/Notifications` - Notifications

**Vai trò**: Gửi notifications (email, database, etc.).

- `TicketCreated.php` - Notification khi ticket được tạo
- `TicketCommented.php` - Notification khi có comment
- `TicketStatusUpdated.php` - Notification khi status thay đổi
- `UserCreatedNotification.php` - Notification khi user được tạo

#### `/app/Helpers` - Helper Classes

**Vai trò**: Utility functions và helpers.

- `JiraHelper.php` - Helper cho Jira integration
- `KanbanScrumHelper.php` - Helper cho Kanban/Scrum boards

#### `/app/Settings` - Application Settings

**Vai trò**: Quản lý application settings.

- `GeneralSettings.php` - General settings (site name, logo, registration, etc.)

---

### 📁 `/routes` - Routes (Rất quan trọng)

**Vai trò**: Định nghĩa tất cả routes của ứng dụng.

#### `routes/api.php` - **Rất quan trọng**
**Vai trò**: Định nghĩa tất cả API endpoints cho Vue.js frontend.

**Cấu trúc routes**:
- Dashboard routes: `/api/dashboard/*`
- Resource routes: `/api/projects`, `/api/tickets`, `/api/users`, etc.
- Nested routes: `/api/tickets/{ticket}/comments`, `/api/tickets/{ticket}/relations`
- Board routes: `/api/board/*`, `/api/projects/{project}/kanban/*`, `/api/projects/{project}/scrum/*`
- RoadMap routes: `/api/projects/{project}/roadmap/*`
- Timesheet routes: `/api/timesheet/*`

#### `routes/web.php` - **Quan trọng**
**Vai trò**: Định nghĩa web routes (Filament admin panel, authentication).

#### `routes/channels.php`
**Vai trò**: Định nghĩa broadcasting channels.

#### `routes/console.php`
**Vai trọng**: Định nghĩa console commands.

---

### 📁 `/config` - Configuration Files (Rất quan trọng)

**Vai trò**: Chứa tất cả configuration files.

**Files quan trọng**:
- `app.php` - **Rất quan trọng**: Application configuration (name, timezone, locale, etc.)
- `database.php` - **Rất quan trọng**: Database configuration
- `auth.php` - **Quan trọng**: Authentication configuration
- `filesystems.php` - **Quan trọng**: File storage configuration
- `mail.php` - Mail configuration
- `session.php` - Session configuration
- `cache.php` - Cache configuration
- `queue.php` - Queue configuration
- `permission.php` - Spatie Permission configuration
- `filament.php` - Filament configuration
- `system.php` - **Quan trọng**: Custom system configuration (project roles, ticket relations, etc.)

---

### 📁 `/database` - Database (Rất quan trọng)

**Vai trò**: Chứa migrations, seeders, và database schema.

#### `/database/migrations` - **Rất quan trọng**
**Vai trò**: Định nghĩa database schema và thay đổi cấu trúc database.

**Migrations quan trọng**:
- `*_create_users_table.php` - Users table
- `*_create_projects_table.php` - Projects table
- `*_create_tickets_table.php` - Tickets table
- `*_create_sprints_table.php` - Sprints table
- `*_create_epics_table.php` - Epics table
- `*_create_ticket_hours_table.php` - Timesheet entries
- `*_create_ticket_comments_table.php` - Ticket comments
- `*_create_ticket_relations_table.php` - Ticket relations
- `*_create_project_favorites_table.php` - Favorite projects
- `*_create_project_user_table.php` - Project-user pivot
- Các migrations cho referential data (statuses, types, priorities, activities)

#### `/database/seeders` - **Quan trọng**
**Vai trò**: Seed dữ liệu mặc định vào database.

**Seeders quan trọng**:
- `DatabaseSeeder.php` - **Rất quan trọng**: Main seeder, gọi tất cả seeders khác
- `DefaultUserSeeder.php` - Tạo default admin user
- `PermissionsSeeder.php` - Seed permissions
- `ActivitySeeder.php` - Seed activities
- `TicketStatusSeeder.php` - Seed default ticket statuses
- `TicketTypeSeeder.php` - Seed default ticket types
- `TicketPrioritySeeder.php` - Seed default ticket priorities

#### `/database/factories`
**Vai trò**: Model factories cho testing.

#### `/database/settings`
**Vai trò**: Settings migrations.

---

### 📁 `/resources` - Frontend Resources (Quan trọng)

**Vai trò**: Chứa frontend code (Vue.js và Blade templates).

#### `/resources/js` - Vue.js Frontend (Rất quan trọng)

**Vai trò**: Vue.js SPA frontend code.

**Cấu trúc**:
- `app.js` - **Rất quan trọng**: Entry point của Vue app
- `App.vue` - Root Vue component
- `bootstrap.js` - Bootstrap code (axios, CSRF token)
- `filament.js` - Filament-specific JavaScript

##### `/resources/js/router` - Vue Router
- `index.js` - **Quan trọng**: Route definitions cho Vue SPA

##### `/resources/js/stores` - Pinia Stores
- `index.js` - **Quan trọng**: State management stores

##### `/resources/js/services` - API Services
- `api.js` - **Quan trọng**: Axios instance và API helper functions

##### `/resources/js/components` - Vue Components
- `Layout.vue` - **Quan trọng**: Main layout component
- `Sidebar.vue` - Sidebar navigation
- `Card.vue` - Card component
- `Modal.vue` - Modal component
- `DataTable.vue` - Data table component
- `FormInput.vue` - Form input component
- `FormSelect.vue` - Form select component

##### `/resources/js/views` - Vue Pages
- `Dashboard.vue` - **Quan trọng**: Dashboard page
- `Login.vue` - **Quan trọng**: Login page
- `Projects.vue` - Projects list page
- `Tickets.vue` - Tickets list page
- `Users.vue` - Users list page

#### `/resources/css` - Stylesheets
- `app.css` - Main stylesheet
- `filament.scss` - Filament styles
- `kanban.scss` - Kanban board styles
- `dialogs.scss` - Dialog styles
- `loading.io.scss` - Loading animation styles

#### `/resources/views` - Blade Templates

**Vai trò**: Blade templates cho Filament admin panel và web views.

**Quan trọng**:
- `app.blade.php` - Main layout template
- `/resources/views/filament/` - Filament-specific templates
  - `pages/kanban.blade.php` - Kanban board template
  - `pages/scrum.blade.php` - Scrum board template
  - `pages/road-map.blade.php` - RoadMap template
  - `resources/tickets/view.blade.php` - Ticket view template

---

### 📁 `/public` - Public Assets (Quan trọng)

**Vai trò**: Publicly accessible files.

**Quan trọng**:
- `index.php` - **Rất quan trọng**: Entry point của Laravel application
- `/public/build/` - Compiled assets (CSS, JS) từ Vite
- `/public/storage/` - Symlink đến storage/app/public (media files)

---

### 📁 `/storage` - Storage (Quan trọng)

**Vai trò**: Chứa files, logs, cache.

**Quan trọng**:
- `/storage/app/` - Application files (uploads, media)
  - `/storage/app/public/` - Public files (cover images, avatars)
- `/storage/logs/` - **Quan trọng**: Application logs (laravel.log)
- `/storage/framework/` - Framework files (cache, sessions, views)

---

### 📁 `/bootstrap` - Bootstrap Files

**Vai trò**: Bootstrap và cache files.

**Quan trọng**:
- `app.php` - Bootstrap application

---

### 📁 `/tests` - Tests

**Vai trò**: Unit và Feature tests.

---

## 🔑 Files Quan trọng Nhất cho Vận Hành

### 1. Entry Points
- `public/index.php` - **Rất quan trọng**: Entry point của ứng dụng
- `resources/js/app.js` - Entry point của Vue.js frontend
- `artisan` - Laravel CLI tool

### 2. Configuration
- `config/app.php` - Application configuration
- `config/database.php` - Database configuration
- `config/system.php` - Custom system configuration
- `.env` - **Rất quan trọng**: Environment variables (không commit vào git)

### 3. Routes
- `routes/api.php` - **Rất quan trọng**: API routes cho Vue.js frontend
- `routes/web.php` - Web routes cho Filament admin

### 4. Core Controllers
- `app/Http/Controllers/Api/Pages/DashboardController.php` - Dashboard widgets
- `app/Http/Controllers/Api/Resources/ProjectController.php` - Project management
- `app/Http/Controllers/Api/Resources/TicketController.php` - Ticket management
- `app/Http/Controllers/Api/Pages/BoardController.php` - Kanban/Scrum boards

### 5. Core Models
- `app/Models/User.php` - User model
- `app/Models/Project.php` - Project model
- `app/Models/Ticket.php` - Ticket model

### 6. Database
- `database/migrations/` - Database schema
- `database/seeders/DatabaseSeeder.php` - Database seeding

### 7. Frontend
- `resources/js/router/index.js` - Vue Router configuration
- `resources/js/services/api.js` - API service
- `resources/js/stores/index.js` - State management
- `vite.config.js` - Vite configuration

### 8. Build Tools
- `composer.json` - PHP dependencies
- `package.json` - Node.js dependencies
- `vite.config.js` - Vite build configuration

---

## 🔄 Luồng Hoạt động của Ứng dụng

### 1. Request Flow (API)
```
Client (Vue.js) 
  → routes/api.php 
  → Middleware (auth, CSRF) 
  → Controller (Api/Resources/* hoặc Api/Pages/*) 
  → Model 
  → Database 
  → Response (JSON)
```

### 2. Request Flow (Web/Admin)
```
Browser 
  → routes/web.php 
  → Filament Resources/Pages 
  → Controller 
  → Model 
  → Database 
  → Blade View 
  → HTML Response
```

### 3. Authentication Flow
```
Login Request 
  → AuthController 
  → Session/Cookie 
  → Middleware (Authenticate) 
  → Protected Routes
```

---

## 📝 Ghi chú Quan trọng

1. **Dual Frontend**: Ứng dụng có 2 frontend:
   - Vue.js SPA tại `/app/*` (routes/api.php)
   - Filament Admin tại `/admin/*` (routes/web.php)

2. **API Structure**: API được tổ chức giống Filament:
   - `Api/Resources/` - Resource controllers (CRUD)
   - `Api/Pages/` - Page controllers (special pages)

3. **Media Files**: Cover images và avatars được lưu trong `storage/app/public/` và truy cập qua `/storage/`

4. **Localization**: Hỗ trợ đa ngôn ngữ, files trong `/lang/`

5. **Permissions**: Sử dụng Spatie Permission package, permissions được seed trong `PermissionsSeeder`

---

## 🚀 Deployment Checklist

Khi deploy, cần chú ý:
1. Set `.env` file với đúng database, mail, storage configs
2. Run `php artisan migrate` để tạo database schema
3. Run `php artisan db:seed` để seed dữ liệu mặc định
4. Run `php artisan storage:link` để tạo symlink cho media files
5. Run `npm run build` để build Vue.js frontend
6. Set proper permissions cho `storage/` và `bootstrap/cache/`
7. Configure web server (Nginx/Apache) để point đến `public/` directory

---

*Tài liệu này được tạo tự động và cần được cập nhật khi có thay đổi trong cấu trúc dự án.*

