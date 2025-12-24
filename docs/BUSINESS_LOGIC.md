# Module Quản lý Dự án (Project Management)
1.1 Quản lý Vòng đời Dự án

Khởi tạo và cấu hình dự án: Cho phép tạo mới dự án với các thuộc tính cơ bản (tên, mã dự án, mô tả, ngày bắt đầu/kết thúc dự kiến)
Theo dõi trạng thái dự án: Hệ thống hỗ trợ quản lý dự án qua các giai đoạn trong vòng đời:

Planning (Lên kế hoạch)
In Progress (Đang thực hiện)
On Hold (Tạm dừng)
Completed (Hoàn thành)
Archived (Lưu trữ)



1.2 Quản lý Tài nguyên Dự án

Phân quyền theo vai trò: Gán thành viên vào dự án với các vai trò cụ thể, mỗi vai trò có phạm vi quyền hạn khác nhau:

Project Owner: Toàn quyền quản lý dự án
Project Manager: Quản lý tiến độ, phân công công việc
Team Member: Thực hiện và cập nhật công việc được gán
Viewer: Chỉ xem, không chỉnh sửa


Bookmarking: Tính năng đánh dấu dự án yêu thích (Favorite Projects) để truy cập nhanh từ Dashboard

2. Module Quản lý Công việc (Work Item Management)
2.1 Phân loại và Tạo Work Item (Ticket)
Hệ thống hỗ trợ 3 loại work item chính:

Bug: Lỗi cần khắc phục
Feature: Tính năng mới cần phát triển
Task: Nhiệm vụ thông thường

2.2 Thuộc tính Work Item
Mỗi work item được quản lý với các thuộc tính:

Trạng thái (Status): To Do → In Progress → Done → Closed
Độ ưu tiên (Priority): Low, Medium, High, Critical/Urgent
Người được gán (Assignee): Thành viên chịu trách nhiệm chính
Ước lượng thời gian (Estimate): Thời gian dự kiến hoàn thành
Ngày đáo hạn (Due Date): Deadline của công việc

2.3 Quản lý Quan hệ giữa các Work Item
Hệ thống hỗ trợ thiết lập mối liên kết giữa các work item:

Blocks/Blocked by: Work item A chặn/bị chặn bởi work item B
Relates to: Liên quan đến
Duplicates/Duplicated by: Bản sao/trùng lặp
Parent/Child: Quan hệ cha-con trong phân rã công việc

2.4 Cộng tác và Tương tác

Hệ thống bình luận (Comments): Thread thảo luận theo thời gian thực trên từng work item
Đính kèm tài liệu (Attachments): Upload file, hình ảnh liên quan
Watchers: Đăng ký theo dõi để nhận thông báo tự động khi có thay đổi
Mentions: Tag (@mention) thành viên để thu hút sự chú ý

3. Module Agile Project Management
3.1 Kanban Board

Giao diện trực quan: Hiển thị work items dưới dạng thẻ (cards) trên các cột trạng thái
Drag & Drop: Kéo-thả thẻ giữa các cột để cập nhật trạng thái công việc
WIP Limit: Giới hạn số lượng work item trên mỗi cột để tối ưu luồng công việc
Swim lanes: Phân nhóm công việc theo người thực hiện hoặc độ ưu tiên

3.2 Scrum Framework

Sprint Planning: Tạo và lên kế hoạch cho các Sprint (chu kỳ phát triển 1-4 tuần)
Sprint Backlog: Danh sách work items được commit trong Sprint
Sprint Tracking: Theo dõi tiến độ Sprint qua:

Burndown Chart: Biểu đồ công việc còn lại theo thời gian
Velocity: Tốc độ hoàn thành công việc của team
Sprint Progress: Phần trăm hoàn thành



3.3 Product Roadmap

Epic Management: Quản lý các Epic (nhóm tính năng lớn, thường kéo dài nhiều Sprint)
Timeline View: Hiển thị lộ trình phát triển sản phẩm theo thời gian
Release Planning: Lên kế hoạch các phiên bản phát hành (Release/Version) 4. Module Quản lý Thời gian (Time Tracking)
4.1 Ghi nhận Giờ công

Time Logging: Ghi nhận thời gian thực tế làm việc cho từng work item
Phân loại hoạt động: Gắn nhãn thời gian theo loại công việc (Development, Testing, Meeting, Documentation)
Work Log Description: Mô tả chi tiết công việc đã thực hiện

4.2 Báo cáo Thời gian
Hệ thống cung cấp các báo cáo:

Timesheet Report: Báo cáo tổng hợp giờ công theo người dùng, theo tuần/tháng
Project Time Summary: Tổng thời gian đã sử dụng vs ước lượng của dự án
Activity Breakdown: Phân tích thời gian theo loại hoạt động
Utilization Report: Tỷ lệ sử dụng nguồn lực (billable vs non-billable hours)

5. Module Quản lý Người dùng và Bảo mật
5.1 Hệ thống Phân quyền (RBAC - Role-Based Access Control)

Định nghĩa vai trò: Tạo và cấu hình các vai trò với bộ quyền hạn cụ thể
Phân quyền chi tiết: Quyền hạn được kiểm soát ở mức độ hành động (Create, Read, Update, Delete) trên từng đối tượng nghiệp vụ
Kế thừa quyền: Quyền hạn có thể được kế thừa từ cấp hệ thống xuống cấp dự án

5.2 Xác thực và Bảo mật

Multi-factor Authentication: Xác thực hai yếu tố (2FA) qua OTP/Authenticator App
Single Sign-On (SSO): Đăng nhập tích hợp qua OAuth2 (Google, GitHub, Microsoft)
Session Management: Quản lý phiên đăng nhập và timeout tự động
Audit Log: Ghi nhận lịch sử hoạt động quan trọng để truy vết

6. Module Dashboard và Business Intelligence
6.1 Dashboard Tổng quan
Hiển thị các thông tin quan trọng tập trung:

Quick Access: Danh sách dự án yêu thích và dự án gần đây
My Work: Công việc được gán cho người dùng hiện tại
Recent Activities: Luồng hoạt động mới nhất của dự án
Notifications Center: Thông báo về công việc, bình luận, thay đổi

6.2 Báo cáo và Phân tích

Biểu đồ trực quan hóa dữ liệu:

Pie Chart: Phân bổ work items theo trạng thái, độ ưu tiên, loại
Bar Chart: So sánh thời gian làm việc, số lượng công việc hoàn thành
Line Chart: Xu hướng tạo/đóng work items theo thời gian


Custom Reports: Tạo báo cáo tùy chỉnh với bộ lọc linh hoạt
Export Function: Xuất báo cáo ra Excel, PDF, CSV

6.3 Key Performance Indicators (KPIs)

Project Health Metrics: Tỷ lệ hoàn thành đúng hạn, số lượng work items quá hạn
Team Productivity: Số công việc hoàn thành trung bình, thời gian xử lý trung bình
Quality Metrics: Tỷ lệ bug/feature, bug reopening rate