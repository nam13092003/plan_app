# Kế hoạch Thiết kế & Xây dựng Web Project Management
## Laravel Backend + Vue.js Frontend

**Công nghệ:** Laravel (Backend API) + Vue.js (Frontend SPA)  
**Mục tiêu:** Xây dựng hệ thống quản lý dự án toàn diện với khả năng realtime, phân quyền, thống kê trực quan, và kiến trúc frontend/backend tách biệt.

---

## 📊 GANTT CHART 

```
THÁNG 1: FOUNDATION & CORE FEATURES
═══════════════════════════════════════════════════════════════════
Week 1  │████████│ Setup & Authentication
Week 2  │████████████│ Projects Module (Backend + Frontend)
Week 3  │████████████│ Tickets Module - Part 1
Week 4  │████████████│ Tickets Module - Part 2 + Comments

THÁNG 2: ADVANCED FEATURES
═══════════════════════════════════════════════════════════════════
Week 5  │████████████│ Sprint & Kanban Board
Week 6  │████████████│ Drag-drop + Board Interactions
Week 7  │████████│ Dashboard & Analytics
Week 8  │████████│ Epic & Roadmap Features

THÁNG 3: POLISH & DEPLOYMENT
═══════════════════════════════════════════════════════════════════
Week 9  │████████│ Notifications & Real-time Updates
Week 10 │████████│ File Upload & Time Tracking
Week 11 │████████████│ Testing & Bug Fixes
Week 12 │████████████│ Deployment & Documentation
```

---

## 🎯 I. TỔNG QUAN KIẾN TRÚC

### 1.1 Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────┐
│                    CLIENT LAYER                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Vue.js     │  │ Filament     │  │  Mobile App  │  │
│  │   (Users)    │  │ (Admin)      │  │  (Future)    │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↓ HTTP/HTTPS
┌─────────────────────────────────────────────────────────┐
│                    API GATEWAY                          │
│    Laravel Sanctum + CORS + Rate Limiting               │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│              BACKEND (Laravel 11)                       │
│  Controllers → Services → Models → Database             │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                   DATA LAYER                            │
│     MySQL + Redis Cache + File Storage                  │
└─────────────────────────────────────────────────────────┘
```

### 1.2 Chiến lược Hybrid

**Quyết định:** Filament (Admin) + Vue.js (Users) song song

**Lợi ích:**
- ✅ Tận dụng Filament admin panel có sẵn
- ✅ Vue.js cho trải nghiệm user tốt hơn
- ✅ Dễ mở rộng mobile app sau này
- ✅ Phân quyền rõ ràng: Admin vs Users

---

## 📁 II. CẤU TRÚC THƯ MỤC CHI TIẾT

### 2.1 Backend Structure (Laravel)

```
project-management/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php           # Login, Register, Logout
│   │   │       ├── ProfileController.php        # User profile
│   │   │       ├── DashboardController.php      # Dashboard stats
│   │   │       ├── ProjectController.php        # CRUD Projects
│   │   │       ├── ProjectMemberController.php  # Manage members
│   │   │       ├── TicketController.php         # CRUD Tickets
│   │   │       ├── TicketCommentController.php  # Comments
│   │   │       ├── TicketAttachmentController.php # File uploads
│   │   │       ├── TicketHourController.php     # Time tracking
│   │   │       ├── SprintController.php         # CRUD Sprints
│   │   │       ├── SprintBoardController.php    # Kanban data
│   │   │       ├── EpicController.php           # CRUD Epics
│   │   │       ├── RoadmapController.php        # Roadmap timeline
│   │   │       ├── NotificationController.php   # Notifications
│   │   │       ├── SearchController.php         # Global search
│   │   │       └── ReferenceController.php      # Statuses, Priorities
│   │   │
│   │   ├── Middleware/
│   │   │   ├── CheckProjectAccess.php           # Verify project access
│   │   │   └── CheckTicketPermission.php        # Verify ticket permission
│   │   │
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── ProjectRequest.php
│   │   │   ├── TicketRequest.php
│   │   │   ├── SprintRequest.php
│   │   │   └── EpicRequest.php
│   │   │
│   │   └── Resources/
│   │       ├── UserResource.php
│   │       ├── ProjectResource.php
│   │       ├── ProjectCollection.php
│   │       ├── TicketResource.php
│   │       ├── TicketCollection.php
│   │       ├── CommentResource.php
│   │       ├── SprintResource.php
│   │       ├── EpicResource.php
│   │       └── ActivityResource.php
│   │
│   ├── Services/                                 # Business Logic
│   │   ├── TicketService.php                    # Ticket operations
│   │   ├── NotificationService.php              # Send notifications
│   │   ├── SprintService.php                    # Sprint logic
│   │   └── FileService.php                      # File uploads
│   │
│   ├── Events/
│   │   ├── TicketCreated.php
│   │   ├── TicketAssigned.php
│   │   ├── TicketStatusChanged.php
│   │   └── TicketCommented.php
│   │
│   ├── Listeners/
│   │   ├── SendTicketNotification.php
│   │   └── LogTicketActivity.php
│   │
│   ├── Policies/
│   │   ├── ProjectPolicy.php
│   │   ├── TicketPolicy.php
│   │   └── SprintPolicy.php
│   │
│   ├── Models/                                   # (Giữ nguyên models hiện có)
│   └── Filament/                                 # (Giữ nguyên Filament)
│
├── routes/
│   ├── api.php                                   # API routes
│   ├── web.php                                   # Filament routes
│   └── channels.php                              # Broadcasting channels
│
├── database/
│   ├── migrations/                               # (Giữ nguyên)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── ProjectSeeder.php
│       ├── TicketSeeder.php
│       └── ReferenceDataSeeder.php              # Statuses, Priorities
│
├── tests/
│   ├── Feature/
│   │   └── Api/
│   │       ├── AuthTest.php
│   │       ├── ProjectTest.php
│   │       ├── TicketTest.php
│   │       └── SprintTest.php
│   └── Unit/
│       └── Services/
│           └── TicketServiceTest.php
│
└── storage/
    ├── app/
    │   └── public/
    │       └── attachments/                      # Uploaded files
    └── logs/
```

### 2.2 Frontend Structure (Vue.js)

```
frontend-vue/
│
├── public/
│   ├── favicon.ico
│   └── index.html
│
├── src/
│   ├── main.js                                   # Entry point
│   ├── App.vue                                   # Root component
│   │
│   ├── router/
│   │   ├── index.js                              # Main router
│   │   └── guards/
│   │       └── auth.guard.js                     # Auth check
│   │
│   ├── stores/                                   # Pinia State Management
│   │   ├── auth.js                               # Auth state
│   │   ├── user.js                               # Current user
│   │   ├── projects.js                           # Projects state
│   │   ├── tickets.js                            # Tickets state
│   │   ├── sprints.js                            # Sprints state
│   │   ├── notifications.js                      # Notifications
│   │   └── ui.js                                 # UI state (sidebar, theme)
│   │
│   ├── api/                                      # API Layer
│   │   ├── axios.js                              # Axios instance
│   │   ├── auth.api.js                           # Auth endpoints
│   │   ├── projects.api.js                       # Project endpoints
│   │   ├── tickets.api.js                        # Ticket endpoints
│   │   ├── sprints.api.js                        # Sprint endpoints
│   │   ├── epics.api.js                          # Epic endpoints
│   │   ├── notifications.api.js                  # Notification endpoints
│   │   └── search.api.js                         # Search endpoint
│   │
│   ├── views/                                    # Page Components
│   │   ├── auth/
│   │   │   ├── LoginView.vue
│   │   │   ├── RegisterView.vue
│   │   │   └── ForgotPasswordView.vue
│   │   │
│   │   ├── dashboard/
│   │   │   └── DashboardView.vue                 # Main dashboard
│   │   │
│   │   ├── projects/
│   │   │   ├── ProjectListView.vue               # All projects
│   │   │   ├── ProjectDetailView.vue             # Project overview
│   │   │   ├── ProjectSettingsView.vue           # Settings
│   │   │   └── ProjectMembersView.vue            # Members
│   │   │
│   │   ├── tickets/
│   │   │   ├── TicketBoardView.vue               # Kanban board
│   │   │   ├── TicketListView.vue                # Table view
│   │   │   └── TicketDetailView.vue              # Ticket detail
│   │   │
│   │   ├── sprints/
│   │   │   ├── SprintListView.vue
│   │   │   ├── SprintBoardView.vue               # Sprint kanban
│   │   │   └── SprintReportView.vue              # Burndown chart
│   │   │
│   │   ├── roadmap/
│   │   │   └── RoadmapView.vue                   # Visual roadmap
│   │   │
│   │   ├── profile/
│   │   │   └── ProfileView.vue
│   │   │
│   │   └── errors/
│   │       ├── NotFoundView.vue                  # 404
│   │       └── UnauthorizedView.vue              # 403
│   │
│   ├── components/                               # Reusable Components
│   │   ├── layout/
│   │   │   ├── AppLayout.vue                     # Main layout
│   │   │   ├── Sidebar.vue                       # Navigation
│   │   │   ├── Navbar.vue                        # Top bar
│   │   │   └── Breadcrumb.vue
│   │   │
│   │   ├── ui/                                   # UI Components
│   │   │   ├── Button.vue
│   │   │   ├── Input.vue
│   │   │   ├── Select.vue
│   │   │   ├── Modal.vue
│   │   │   ├── Card.vue
│   │   │   ├── Table.vue
│   │   │   ├── Pagination.vue
│   │   │   ├── Loading.vue
│   │   │   └── EmptyState.vue
│   │   │
│   │   ├── project/
│   │   │   ├── ProjectCard.vue
│   │   │   ├── ProjectStats.vue
│   │   │   ├── ProjectMemberList.vue
│   │   │   └── ProjectForm.vue
│   │   │
│   │   ├── ticket/
│   │   │   ├── TicketCard.vue                    # For kanban
│   │   │   ├── TicketRow.vue                     # For table
│   │   │   ├── TicketForm.vue
│   │   │   ├── TicketStatusBadge.vue
│   │   │   ├── TicketPriorityBadge.vue
│   │   │   ├── TicketComments.vue
│   │   │   ├── TicketActivity.vue
│   │   │   ├── TicketAttachments.vue
│   │   │   └── TicketTimeLog.vue
│   │   │
│   │   ├── sprint/
│   │   │   ├── SprintCard.vue
│   │   │   ├── SprintBoard.vue                   # Kanban component
│   │   │   ├── BurndownChart.vue
│   │   │   └── SprintProgress.vue
│   │   │
│   │   ├── epic/
│   │   │   └── EpicTimeline.vue
│   │   │
│   │   ├── user/
│   │   │   ├── UserAvatar.vue
│   │   │   └── UserSelect.vue
│   │   │
│   │   ├── charts/
│   │   │   ├── LineChart.vue
│   │   │   ├── BarChart.vue
│   │   │   └── PieChart.vue
│   │   │
│   │   └── notifications/
│   │       ├── NotificationBell.vue
│   │       └── NotificationDropdown.vue
│   │
│   ├── composables/                              # Composition API
│   │   ├── useAuth.js
│   │   ├── useProjects.js
│   │   ├── useTickets.js
│   │   ├── useSprints.js
│   │   ├── useNotifications.js
│   │   ├── useWebSocket.js                       # Real-time
│   │   └── useDragDrop.js
│   │
│   ├── utils/
│   │   ├── validators.js
│   │   ├── formatters.js                         # Date, number format
│   │   ├── constants.js
│   │   └── permissions.js
│   │
│   ├── plugins/
│   │   ├── axios.js
│   │   ├── toast.js                              # Toast notifications
│   │   └── charts.js                             # Chart.js
│   │
│   └── assets/
│       ├── styles/
│       │   ├── main.css
│       │   └── tailwind.css
│       └── images/
│
├── .env.development
├── .env.production
├── vite.config.js
├── tailwind.config.js
└── package.json
```
