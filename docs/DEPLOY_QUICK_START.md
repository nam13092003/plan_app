# 🚀 QUICK START - DEPLOY TRÊN SERVER

## Bước nhanh (5 phút)

### 1. Chuẩn bị
```bash
# Cài Docker (nếu chưa có)
sudo apt update && sudo apt install -y docker.io docker-compose

# Tạo thư mục
sudo mkdir -p /var/www/project-management
sudo chown $USER:$USER /var/www/project-management
cd /var/www/project-management
```

### 2. Upload code
```bash
# Clone hoặc upload code vào thư mục này
git clone <repo-url> .
# hoặc
scp -r /path/to/code/* user@server:/var/www/project-management/
```

### 3. Cấu hình .env
```bash
# Tạo file .env
nano .env
```

**Nội dung tối thiểu:**
```env
APP_NAME="Project Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-server-ip
APP_KEY=

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=laravel
DB_PASSWORD=your_password
DB_ROOT_PASSWORD=your_root_password
```

### 4. Build và chạy
```bash
# Build
docker-compose build

# Start
docker-compose up -d

# Xem logs
docker-compose logs -f
```

### 5. Setup database
```bash
# Generate key
docker-compose exec app php artisan key:generate

# Migrate
docker-compose exec app php artisan migrate --force

# Seed (nếu cần)
docker-compose exec app php artisan db:seed --force

# Storage link
docker-compose exec app php artisan storage:link
```

### 6. Tối ưu
```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### 7. Kiểm tra
```bash
# Mở browser: http://your-server-ip
curl http://localhost/health
```

---

## Lệnh hữu ích

```bash
# Xem logs
docker-compose logs -f app

# Restart
docker-compose restart

# Vào container
docker-compose exec app bash

# Backup DB
docker-compose exec db mysqldump -u root -p project_management > backup.sql
```

---

## Xem chi tiết

Xem file `QUY_TRINH_DEPLOY.md` để biết hướng dẫn đầy đủ.

