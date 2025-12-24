# Quy trình làm việc GitHub (Phiên bản Đơn giản hóa)
(Laravel + Vue.js)

Đây là tài liệu quy định luồng làm việc (workflow) **đơn giản hóa** cho dự án. Mục tiêu là để 4 thành viên phối hợp nhịp nhàng, chỉ sử dụng các tính năng có sẵn của GitHub.

Mô hình này được gọi là **GitHub Flow**: rất nhanh, gọn, và mọi thứ đều xoay quanh nhánh `main`.

---

## 1. Thiết lập ban đầu (Làm 1 lần)

Người Lead sẽ thực hiện các bước này.

### 1.1. Cấu hình Repository

Chúng ta vẫn sử dụng **2 repository riêng biệt**:
1.  `project-management-api` (Cho Laravel Backend)
2.  `project-management-web` (Cho Vue.js Frontend)

**Với mỗi repository:**
1.  Tạo repository trên GitHub, chọn **Private**.
2.  Check "Add a README file" và "Add .gitignore" (Chọn template `Laravel` hoặc `Vue` tương ứng).
3.  Vào **Settings** > **Collaborators** > Mời 3 thành viên còn lại vào.

### 1.2. Cấu hình `.gitignore` (Bắt buộc)

File này đảm bảo bạn không "commit" file rác, file thư viện (`vendor`, `node_modules`) hoặc file nhạy cảm (`.env`) lên GitHub.

**Repo `project-management-api` (Laravel):**

/vendor/
/node_modules/
.env
.env.testing
/storage/app/public/
/public/storage
.DS_Store

**Repo `project-management-web` (Vue.js):**

/node_modules/
/dist/
.env.local
.env.development.local
.env.production.local
.env.test.local
.DS_Store

### 1.3. File môi trường `.env.example`

Vì `.env` bị bỏ qua, mọi người cần file mẫu để chạy code:

1.  Tạo file `.env.example` trong cả 2 repo.
2.  Copy tên các biến từ `.env` vào `.env.example` (xóa hết giá trị nhạy cảm).
3.  Commit file `.env.example` này lên.
4.  Khi thành viên khác clone về, họ chỉ cần copy file này thành `.env` và tự điền thông tin của mình.

---

## 2. Mô hình phân nhánh (Siêu đơn giản)

Chúng ta **CHỈ** dùng 2 loại nhánh:

1.  **`main` (Nhánh chính):**
   - Đây là nhánh **duy nhất** tồn tại vĩnh viễn.  
   - Đại diện cho code **ổn định nhất**, sẵn sàng để chạy (deploy).  
   - **QUY TẮC VÀNG:** **TUYỆT ĐỐI CẤM** push code trực tiếp lên `main`.  
     Mọi code đưa lên `main` **BẮT BUỘC** phải qua Pull Request và được ít nhất 1 người khác review.

2.  **`feature/<ten-tinh-nang>` (Nhánh tính năng):**
   - Đây là nhánh tạm thời để bạn code một tính năng mới.  
   - **Tạo từ:** `main`.  
   - **Merge về:** `main`.  
   - **Ví dụ:** `feature/auth-controller`, `feature/login-view`.  
   - Nhánh này sẽ được **xóa ngay lập tức** sau khi merge vào `main`.

### Cài đặt bảo vệ nhánh `main` (Bắt buộc)

Người Lead vào **Settings > Branches > Add branch protection rule**:
- Branch name pattern: `main`
- Check: **Require a pull request before merging**
- Check: **Require approvals** (Chọn `1`)

---

## 3. Quản lý công việc (100% bằng GitHub)

Chúng ta chỉ dùng **GitHub Issues** (để tạo task) và **GitHub Projects** (để xem task đó dưới dạng bảng).

> **Lưu ý:** "GitHub Projects" là một tính năng **có sẵn** của GitHub, nó hiển thị trực quan các "Issues", giống như Trello/Jira nhưng tích hợp 100%.

### 3.1. Tạo Nhiệm vụ (GitHub Issues)

- Mọi task phải được tạo thành **Issue**.
- Vào tab **Issues > New Issue**.
- **Title:** `[API] Tạo CRUD cho ProjectController`
- **Assignees:** Người thực hiện.
- **Labels:** `backend`, `frontend`, `bug`, `feature`, ...

### 3.2. Trực quan hóa bằng Bảng (GitHub Projects)

1. Vào tab **Projects > New project > Board**.  
2. Tạo 4 cột: `To Do`, `In Progress`, `Needs Review`, `Done`.  
3. Vào mục "Automation":  
   - Khi Issue tạo → thêm vào `To Do`.  
   - Khi mở Pull Request → sang `Needs Review`.  
   - Khi PR merge → sang `Done`.

---

## 4. Luồng làm việc hàng ngày (Vòng đời của một tính năng)

Giả sử bạn được giao **Issue #12: [API] Tạo ProjectController**.

### Bước 1: Bắt đầu nhiệm vụ
- Gán Issue cho mình.
- Kéo sang `In Progress` nếu có bảng Projects.

### Bước 2: Lấy code mới nhất

git checkout main
git pull origin main

### Bước 3: Tạo nhánh mới

git checkout -b feature/12-project-controller

### Bước 4: Code và Commit

git status
git add .
git commit -m "Feat(API): Thêm ProjectController hàm store và validation"

**Quy tắc viết Commit Message:**
- `Feat:` thêm tính năng mới.
- `Fix:` sửa lỗi.
- `Refactor:` tối ưu code.
- `Chore:` việc phụ.
- `Docs:` viết/sửa tài liệu.

### Bước 5: Đẩy code lên GitHub

git push -u origin feature/12-project-controller

### Bước 6: Tạo Pull Request (PR)

- Lên GitHub → "Compare & pull request"
- Base: `main`, Compare: `feature/...`
- **Title:** `Feat(API): Hoàn thành ProjectController CRUD`
- **Description:** Gõ `Closes #12`
- Tag Reviewer: 1-2 người
- Issue tự động nhảy sang `Needs Review`

---

## 5. Quy trình Code Review & Merge

### Reviewer:
- Kiểm tra logic, bug, style.
- Comment góp ý.
- Approve hoặc Request Changes.

### Người tạo PR:
- Sửa code theo góp ý.
- Commit lại với `Fix(Review): ...`
- Push → PR tự cập nhật.

### Merge:
- Khi PR được Approve → chọn **"Squash and merge"**.
- Xóa nhánh `feature` sau khi merge.

### Sau khi merge (dọn dẹp):

git checkout main
git pull origin main
git branch -d feature/12-project-controller

---

## 6. Xử lý Xung đột (Merge Conflict)

git checkout main
git pull origin main
git checkout feature/12-project-controller
git merge main

Nếu có conflict:
- Mở file có `<<<<<<<`, `=======`, `>>>>>>>`
- Sửa hợp lý
- Sau khi xong:

git add .
git commit -m "Merge: Hợp nhất main vào feature và giải quyết conflict"
git push

---

## 7. (Tùy chọn) Tự động hóa với GitHub Actions

Tạo thư mục `.github/workflows/` trong mỗi repo.

### 7.1. Cho Backend (Laravel)
File: `.github/workflows/laravel.yml`

name: Laravel CI
on:
  push:
    branches: [ "main" ]
  pull_request:
    branches: [ "main" ]

jobs:
  laravel-tests:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.example', '.env');"
    - name: Install Dependencies
      run: composer install --prefer-dist --no-progress
    - name: Generate key
      run: php artisan key:generate
    - name: Directory Permissions
      run: chmod -R 777 storage bootstrap/cache
    - name: Create Database
      run: |
        mkdir -p database
        touch database/database.sqlite
    - name: Run Migrations & Tests (PHPUnit/Pest)
      env:
        DB_CONNECTION: sqlite
        DB_DATABASE: database/database.sqlite
      run: php artisan test

### 7.2. Cho Frontend (Vue.js)
File: `.github/workflows/vue.yml`

name: Vue CI
on:
  push:
    branches: [ "main" ]
  pull_request:
    branches: [ "main" ]

jobs:
  build-and-test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - name: Use Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '18.x'
        cache: 'npm'
    - name: Install Dependencies
      run: npm install
    - name: Run Linter (ESLint)
      run: npm run lint
    - name: Run Tests (Vitest/Jest)
      run: npm run test:unit

---
## 8 Cách commit 

🧭 I. Cấu trúc repository
Repo: se2025-16.3

Nhánh chính: main (ổn định, để release cuối cùng)

Nhánh phát triển: test01 (nhánh nhóm làm việc)
Cấu trúc thư mục:


/frontend   ← Vue.js
/backend    ← Laravel
/docs       ← tài liệu báo cáo, ERD, UML, hướng dẫn
👥 II. Vai trò nhóm (4 người ví dụ)
Thành viênVai tròNhánh phụ tráchAFrontend lead (UI, Gantt, Vue Router, API call)feature/frontend-*BBackend lead (Laravel API, Model, Controller)feature/backend-*CDatabase + Auth + API integrationfeature/integration-*DTest + Fix bug + Deploy + Documentfix/*, docs/*
🔀 III. Quy trình làm việc chuẩn (GitHub Flow)
1️⃣ Bắt đầu từ nhánh phát triển

git checkout test01
git pull origin test01
2️⃣ Tạo nhánh tính năng riêng

git checkout -b feature/frontend-login
3️⃣ Làm việc & commit rõ ràng
Ví dụ:


git add .
git commit -m "feat(frontend-login): tạo form đăng nhập và gọi API login"
4️⃣ Đẩy lên GitHub

git push origin feature/frontend-login
5️⃣ Tạo Pull Request
Lên GitHub → Pull requests → New pull request
Chọn:
Base: test01
Compare: feature/frontend-login
Ghi mô tả ngắn gọn: “Hoàn thành giao diện và API login phía frontend.”
Assign cho Leader review & merge.
6️⃣ Merge xong xóa nhánh cũ
Sau khi merge PR, leader hoặc dev chạy:


git branch -d feature/frontend-login
🧩 IV. Quy ước commit message (rất quan trọng)
Loại commitCú phápVí dụ✨ Thêm tính năngfeat(scope): mô tảfeat(auth): thêm API đăng nhập🐞 Sửa lỗifix(scope): mô tảfix(ui): căn chỉnh nút Save🧹 Dọn dẹp / cải tiếnrefactor(scope): mô tảrefactor(api): gom code axios🧪 Kiểm thửtest(scope): mô tảtest(api): thêm test đăng nhập📝 Tài liệudocs(scope): mô tảdocs(readme): cập nhật hướng dẫn setup

## 🎯 Tổng kết

- **Chỉ 1 nhánh chính (`main`)** → sạch sẽ, dễ kiểm soát.  
- **Mỗi tính năng = 1 nhánh feature riêng** → tách biệt, dễ review.  
- **Mọi PR đều cần review** → đảm bảo chất lượng code.  
- **GitHub Projects + Issues** → quản lý trực quan, không cần tool ngoài.  
- **GitHub Actions** → tự động kiểm thử, đảm bảo ổn định trước khi merge.

