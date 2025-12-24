# Giải Thích Các File Migration

Tài liệu này giải thích mục đích và chức năng của từng file migration trong hệ thống quản lý dự án.

## Mục Lục

1. [Migration Cơ Bản & Xác Thực](#migration-cơ-bản--xác-thực)
2. [Migration Quản Lý Dự Án](#migration-quản-lý-dự-án)
3. [Migration Quản Lý Ticket](#migration-quản-lý-ticket)
4. [Migration Quản Lý Thời Gian](#migration-quản-lý-thời-gian)
5. [Migration Quản Lý Sprint & Epic](#migration-quản-lý-sprint--epic)
6. [Migration Hệ Thống & Cấu Hình](#migration-hệ-thống--cấu-hình)
7. [Migration Cập Nhật & Bổ Sung](#migration-cập-nhật--bổ-sung)

---

## Migration Cơ Bản & Xác Thực

### 0001_create_users_table.php
**Mục đích:** Tạo bảng `users` để lưu trữ thông tin người dùng.

**Các trường:**
- `id`: ID duy nhất
- `name`: Tên người dùng
- `email`: Email (unique)
- `email_verified_at`: Thời gian xác thực email
- `password`: Mật khẩu đã mã hóa
- `remember_token`: Token để nhớ đăng nhập
- `timestamps`: Thời gian tạo và cập nhật

### 0002_create_password_resets_table.php
**Mục đích:** Tạo bảng `password_resets` để lưu token reset mật khẩu.

**Chức năng:** Hỗ trợ tính năng quên mật khẩu, lưu token và email để xác thực khi reset.

### 0003_create_failed_jobs_table.php
**Mục đích:** Tạo bảng `failed_jobs` để lưu các job thất bại trong queue.

**Chức năng:** Theo dõi và debug các công việc xử lý nền bị lỗi.

### 0004_create_personal_access_tokens_table.php
**Mục đích:** Tạo bảng `personal_access_tokens` cho Laravel Sanctum.

**Chức năng:** Quản lý token API để xác thực người dùng qua API.

### 0005_add_two_factor_columns_to_table.php
**Mục đích:** Thêm các cột xác thực hai yếu tố (2FA) vào bảng users.

**Chức năng:** Hỗ trợ bảo mật đăng nhập bằng 2FA.

### 0006_create_permission_tables.php
**Mục đích:** Tạo các bảng quản lý quyền (permissions) sử dụng Spatie Permission.

**Các bảng được tạo:**
- `permissions`: Danh sách quyền
- `roles`: Vai trò người dùng
- `model_has_permissions`: Quyền trực tiếp của model
- `model_has_roles`: Vai trò của model
- `role_has_permissions`: Quyền của từng vai trò

**Chức năng:** Hệ thống phân quyền chi tiết cho người dùng và vai trò.

---

## Migration Quản Lý Dự Án

### 0007_create_project_statuses_table.php
**Mục đích:** Tạo bảng `project_statuses` để quản lý trạng thái dự án.

**Các trường:**
- `name`: Tên trạng thái
- `color`: Màu hiển thị (mặc định #cecece)
- `is_default`: Trạng thái mặc định
- `soft_deletes`: Xóa mềm

**Ví dụ trạng thái:** Planning, In Progress, Completed, On Hold, Cancelled

### 0008_create_projects_table.php
**Mục đích:** Tạo bảng `projects` - bảng chính lưu thông tin dự án.

**Các trường:**
- `name`: Tên dự án
- `description`: Mô tả dự án
- `owner_id`: ID người sở hữu (FK → users)
- `status_id`: ID trạng thái (FK → project_statuses)
- `soft_deletes`: Xóa mềm
- `timestamps`: Thời gian tạo và cập nhật

### 0009_create_project_users_table.php
**Mục đích:** Tạo bảng `project_users` - bảng trung gian quản lý thành viên dự án.

**Các trường:**
- `user_id`: ID người dùng (FK → users)
- `project_id`: ID dự án (FK → projects)
- `role`: Vai trò trong dự án (ví dụ: member, manager, viewer)

**Chức năng:** Quan hệ many-to-many giữa users và projects, lưu vai trò của từng thành viên.

### 0010_create_media_table.php
**Mục đích:** Tạo bảng `media` để quản lý file đính kèm.

**Chức năng:** Lưu trữ thông tin file, hình ảnh được upload trong hệ thống (sử dụng Spatie Media Library).

### 0011_create_project_favorites_table.php
**Mục đích:** Tạo bảng `project_favorites` để lưu dự án yêu thích của người dùng.

**Chức năng:** Cho phép người dùng đánh dấu dự án yêu thích để truy cập nhanh.

---

## Migration Quản Lý Ticket

### 0012_create_ticket_statuses_table.php
**Mục đích:** Tạo bảng `ticket_statuses` để quản lý trạng thái ticket.

**Các trường:**
- `name`: Tên trạng thái
- `color`: Màu hiển thị
- `is_default`: Trạng thái mặc định
- `soft_deletes`: Xóa mềm

**Ví dụ trạng thái:** Open, In Progress, Resolved, Closed, Reopened

### 0013_create_tickets_table.php
**Mục đích:** Tạo bảng `tickets` - bảng chính lưu thông tin ticket/công việc.

**Các trường:**
- `name`: Tên ticket
- `content`: Nội dung mô tả
- `owner_id`: ID người tạo (FK → users)
- `responsible_id`: ID người phụ trách (FK → users, nullable)
- `status_id`: ID trạng thái (FK → ticket_statuses)
- `project_id`: ID dự án (FK → projects)
- `soft_deletes`: Xóa mềm
- `timestamps`: Thời gian tạo và cập nhật

### 0014_add_tickets_prefix_to_projects.php
**Mục đích:** Thêm cột `tickets_prefix` vào bảng `projects`.

**Chức năng:** Tiền tố để tạo mã ticket tự động (ví dụ: PROJ-001, DEV-002).

### 0015_add_code_to_tickets.php
**Mục đích:** Thêm cột `code` vào bảng `tickets`.

**Chức năng:** Mã ticket duy nhất được tạo tự động dựa trên prefix của dự án.

### 0016_create_ticket_types_table.php
**Mục đích:** Tạo bảng `ticket_types` để phân loại ticket.

**Các trường:**
- `name`: Tên loại (ví dụ: Bug, Feature, Task, Story)
- `icon`: Icon hiển thị
- `color`: Màu hiển thị
- `is_default`: Loại mặc định
- `soft_deletes`: Xóa mềm

### 0017_add_type_to_ticket.php
**Mục đích:** Thêm cột `type_id` vào bảng `tickets`.

**Chức năng:** Liên kết ticket với loại ticket (FK → ticket_types).

### 0018_add_order_to_tickets.php
**Mục đích:** Thêm cột `order` vào bảng `tickets`.

**Chức năng:** Sắp xếp thứ tự hiển thị ticket trong danh sách.

### 0019_add_order_to_ticket_statuses.php
**Mục đích:** Thêm cột `order` vào bảng `ticket_statuses`.

**Chức năng:** Sắp xếp thứ tự hiển thị trạng thái trong workflow.

### 0020_create_ticket_activities_table.php
**Mục đích:** Tạo bảng `ticket_activities` để lưu lịch sử hoạt động của ticket.

**Chức năng:** Theo dõi các thay đổi, bình luận, cập nhật trạng thái của ticket.

### 0021_create_ticket_priorities_table.php
**Mục đích:** Tạo bảng `ticket_priorities` để quản lý mức độ ưu tiên.

**Các trường:**
- `name`: Tên mức độ (ví dụ: Low, Medium, High, Critical)
- `color`: Màu hiển thị
- `is_default`: Mức độ mặc định
- `soft_deletes`: Xóa mềm

### 0022_add_priority_to_tickets.php
**Mục đích:** Thêm cột `priority_id` vào bảng `tickets`.

**Chức năng:** Liên kết ticket với mức độ ưu tiên (FK → ticket_priorities).

### 0023_add_status_type_to_project.php
**Mục đích:** Thêm cột `status_type` vào bảng `projects`.

**Chức năng:** Phân loại trạng thái dự án (ví dụ: active, archived, completed).

### 0024_add_project_to_ticket_statuses.php
**Mục đích:** Thêm cột `project_id` vào bảng `ticket_statuses`.

**Chức năng:** Cho phép mỗi dự án có bộ trạng thái ticket riêng.

### 0025_create_ticket_comments_table.php
**Mục đích:** Tạo bảng `ticket_comments` để lưu bình luận trên ticket.

**Chức năng:** Người dùng có thể bình luận, thảo luận về ticket.

### 0026_create_ticket_subscribers_table.php
**Mục đích:** Tạo bảng `ticket_subscribers` để quản lý người đăng ký nhận thông báo.

**Chức năng:** Người dùng có thể đăng ký nhận thông báo khi ticket có thay đổi.

### 0029_create_ticket_relations_table.php
**Mục đích:** Tạo bảng `ticket_relations` để quản lý quan hệ giữa các ticket.

**Chức năng:** Liên kết ticket với nhau (ví dụ: blocks, relates to, duplicates).

---

## Migration Quản Lý Thời Gian

### 0035_create_ticket_hours_table.php
**Mục đích:** Tạo bảng `ticket_hours` để ghi nhận thời gian làm việc trên ticket.

**Các trường:**
- `ticket_id`: ID ticket (FK → tickets)
- `user_id`: ID người dùng (FK → users)
- `value`: Số giờ làm việc (float)
- `timestamps`: Thời gian ghi nhận

**Chức năng:** Theo dõi thời gian thực tế làm việc, tính toán chi phí, báo cáo.

### 0036_add_estimation_to_tickets.php
**Mục đích:** Thêm cột `estimation` vào bảng `tickets`.

**Chức năng:** Ước tính thời gian dự kiến hoàn thành ticket (giờ).

### 0042_add_comment_to_ticket_hours.php
**Mục đích:** Thêm cột `comment` vào bảng `ticket_hours`.

**Chức năng:** Ghi chú về công việc đã làm trong khoảng thời gian đó.

### 0044_create_activities_table.php
**Mục đích:** Tạo bảng `activities` để quản lý hoạt động/dự án con.

**Chức năng:** Phân chia công việc thành các hoạt động nhỏ hơn.

### 0045_add_activity_to_ticket_hours_table.php
**Mục đích:** Thêm cột `activity_id` vào bảng `ticket_hours`.

**Chức năng:** Liên kết thời gian làm việc với hoạt động cụ thể (FK → activities).

---

## Migration Quản Lý Sprint & Epic

### 0039_create_epics_table.php
**Mục đích:** Tạo bảng `epics` để quản lý epic (chức năng lớn).

**Các trường:**
- `project_id`: ID dự án (FK → projects)
- `name`: Tên epic
- `starts_at`: Ngày bắt đầu
- `ends_at`: Ngày kết thúc
- `soft_deletes`: Xóa mềm
- `timestamps`: Thời gian tạo và cập nhật

**Chức năng:** Nhóm các ticket liên quan thành epic, quản lý theo timeline.

### 0040_add_epic_to_ticket.php
**Mục đích:** Thêm cột `epic_id` vào bảng `tickets`.

**Chức năng:** Liên kết ticket với epic (FK → epics).

### 0041_add_parent_to_epics.php
**Mục đích:** Thêm cột `parent_id` vào bảng `epics`.

**Chức năng:** Tạo cấu trúc phân cấp epic (epic con - epic cha).

### 0048_add_type_to_projects.php
**Mục đích:** Thêm cột `type` vào bảng `projects`.

**Chức năng:** Phân loại dự án (ví dụ: Scrum, Kanban, Waterfall).

### 0049_create_sprints_table.php
**Mục đích:** Tạo bảng `sprints` để quản lý sprint (chu kỳ phát triển).

**Các trường:**
- `name`: Tên sprint
- `starts_at`: Ngày bắt đầu
- `ends_at`: Ngày kết thúc
- `description`: Mô tả sprint
- `project_id`: ID dự án (FK → projects)
- `soft_deletes`: Xóa mềm
- `timestamps`: Thời gian tạo và cập nhật

**Chức năng:** Quản lý sprint trong phương pháp Agile/Scrum.

### 0050_add_sprint_to_tickets.php
**Mục đích:** Thêm cột `sprint_id` vào bảng `tickets`.

**Chức năng:** Gán ticket vào sprint cụ thể (FK → sprints).

### 0051_add_epic_to_sprints.php
**Mục đích:** Thêm cột `epic_id` vào bảng `sprints`.

**Chức năng:** Liên kết sprint với epic (FK → epics).

### 0052_add_started_ended_at_to_sprints.php
**Mục đích:** Cập nhật các cột `started_at` và `ended_at` trong bảng `sprints`.

**Chức năng:** Thay đổi từ `date` sang `timestamp` để lưu cả thời gian, không chỉ ngày.

---

## Migration Hệ Thống & Cấu Hình

### 0027_create_notifications_table.php
**Mục đích:** Tạo bảng `notifications` để lưu thông báo.

**Chức năng:** Hệ thống thông báo trong ứng dụng (Laravel Notifications).

### 0028_create_jobs_table.php
**Mục đích:** Tạo bảng `jobs` để quản lý queue jobs.

**Chức năng:** Lưu các công việc xử lý nền đang chờ xử lý.

### 0030_create_settings_table.php
**Mục đích:** Tạo bảng `settings` để lưu cấu hình hệ thống.

**Chức năng:** Lưu các thiết lập toàn cục của ứng dụng.

### 0031_create_socialite_users_table.php
**Mục đích:** Tạo bảng `socialite_users` để quản lý đăng nhập qua mạng xã hội.

**Chức năng:** Hỗ trợ đăng nhập bằng Google, GitHub, Facebook, etc. (Laravel Socialite).

### 0038_create_pending_user_emails_table.php
**Mục đích:** Tạo bảng `pending_user_emails` để quản lý email đang chờ xác thực.

**Chức năng:** Lưu email mới khi người dùng thay đổi email, chờ xác thực.

---

## Migration Cập Nhật & Bổ Sung

### 0032_make_user_password_nullable.php
**Mục đích:** Cho phép cột `password` trong bảng `users` có thể null.

**Chức năng:** Hỗ trợ đăng nhập qua OAuth/Socialite không cần mật khẩu.

### 0033_remove_unique_from_users.php
**Mục đích:** Xóa ràng buộc unique từ cột `email` trong bảng `users`.

**Chức năng:** Cho phép nhiều tài khoản có cùng email (có thể từ các provider khác nhau).

### 0034_add_soft_deletes_to_users.php
**Mục đích:** Thêm soft deletes vào bảng `users`.

**Chức năng:** Xóa mềm người dùng thay vì xóa vĩnh viễn.

### 0037_add_creation_token_to_users.php
**Mục đích:** Thêm cột `creation_token` vào bảng `users`.

**Chức năng:** Token để xác thực khi tạo tài khoản mới qua email.

### 0043_add_attachments_to_tickets.php
**Mục đích:** Thêm cột `attachments` vào bảng `tickets`.

**Chức năng:** Lưu danh sách file đính kèm (có thể là JSON hoặc text).

### 0046_remove_unique_constraint_from_users.php
**Mục đích:** Xóa ràng buộc unique khác từ bảng `users`.

**Chức năng:** Tiếp tục hỗ trợ đăng nhập qua nhiều provider.

### 0047_drop_attachments.php
**Mục đích:** Xóa cột `attachments` khỏi bảng `tickets`.

**Chức năng:** Chuyển sang sử dụng bảng `media` thay vì lưu trực tiếp trong ticket.

### 0053_update_users_for_oidc.php
**Mục đích:** Cập nhật bảng `users` để hỗ trợ OIDC (OpenID Connect).

**Chức năng:** Thêm các trường cần thiết cho xác thực OIDC.

### 0054_add_unique_ticket_prefix_to_projects_table.php
**Mục đích:** Thêm ràng buộc unique cho cột `tickets_prefix` trong bảng `projects`.

**Chức năng:** Đảm bảo mỗi dự án có prefix ticket duy nhất.

---

## Tổng Kết

Hệ thống migration được tổ chức theo các nhóm chức năng chính:

1. **Xác thực & Phân quyền:** Quản lý người dùng, đăng nhập, quyền truy cập
2. **Quản lý Dự án:** Tạo và quản lý dự án, thành viên, trạng thái
3. **Quản lý Ticket:** Tạo và theo dõi công việc, bình luận, quan hệ
4. **Quản lý Thời gian:** Ghi nhận và báo cáo thời gian làm việc
5. **Agile/Scrum:** Sprint, Epic, quản lý theo phương pháp Agile
6. **Hệ thống:** Thông báo, cấu hình, tích hợp bên thứ ba

Tất cả các migration đều được đánh số thứ tự từ 0001 đến 0054 để đảm bảo thứ tự thực thi đúng.

