# QUY TRÌNH DEPLOY TRÊN SERVER

## 📋 Mục lục
1. [Chuẩn bị Server](#1-chuẩn-bị-server)
2. [Upload Code lên Server](#2-upload-code-lên-server)
3. [Cấu hình Environment](#3-cấu-hình-environment)
4. [Build và Chạy Docker](#4-build-và-chạy-docker)
5. [Setup Database](#5-setup-database)
6. [Kiểm tra và Tối ưu](#6-kiểm-tra-và-tối-ưu)
7. [Troubleshooting](#7-troubleshooting)

---

## 1. Chuẩn bị Server

### 1.1. Kiểm tra hệ thống

```bash
# Kiểm tra OS
cat /etc/os-release

# Kiểm tra Docker (cần >= 20.10)
docker --version

# Kiểm tra Docker Compose (cần >= 2.0)
docker-compose --version
```

### 1.2. Cài đặt Docker (nếu chưa có)

```bash
# Update package list
sudo apt update

# Cài đặt dependencies
sudo apt install -y apt-transport-https ca-certificates curl gnupg lsb-release

# Thêm Docker repository
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Cài đặt Docker
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Khởi động Docker
sudo systemctl start docker
sudo systemctl enable docker

# Thêm user vào docker group (để không cần sudo)
sudo usermod -aG docker $USER
# Logout và login lại để áp dụng

# Kiểm tra cài đặt
docker run hello-world
```

### 1.3. Tạo thư mục project

```bash
# Tạo thư mục cho project
sudo mkdir -p /var/www/project-management
sudo chown $USER:$USER /var/www/project-management
cd /var/www/project-management
```

---

## 2. Upload Code lên Server

### 2.1. Sử dụng Git (Khuyến nghị)

```bash
# Clone repository
cd /var/www/project-management
git clone <repository-url> .

# Hoặc nếu đã có code, pull latest
git pull origin main
```

### 2.2. Sử dụng SCP/SFTP

```bash
# Từ máy local
scp -r /path/to/project/* user@server:/var/www/project-management/

# Hoặc dùng FileZilla, WinSCP, etc.
```

### 2.3. Kiểm tra files

```bash
cd /var/www/project-management

# Kiểm tra các file quan trọng
ls -la Dockerfile
ls -la docker-compose.yml
ls -la docker/
```

---

## 3. Cấu hình Environment

### 3.1. Tạo file .env

```bash
# Tạo file .env từ template (nếu có)
cp .env.example .env

# Hoặc tạo mới
nano .env
```

### 3.2. Cấu hình các biến môi trường

Mở file `.env` và cấu hình:

```env
# Application
APP_NAME="Project Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com
# Hoặc nếu dùng IP: http://35.202.29.6

# Application Key (sẽ generate ở bước sau)
APP_KEY=

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=laravel
DB_PASSWORD=your_secure_password_here

# Database Root Password (cho MySQL container)
DB_ROOT_PASSWORD=your_root_password_here

# Ports (optional, có thể để mặc định)
APP_PORT=80
APP_HTTPS_PORT=443
DB_PORT=3306

# Mail Configuration (nếu cần)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Other settings
LOG_CHANNEL=stack
LOG_LEVEL=error
```

**Lưu ý quan trọng:**
- `DB_HOST=db` - Tên service trong docker-compose, không đổi
- `DB_PASSWORD` và `DB_ROOT_PASSWORD` nên là password mạnh
- `APP_URL` phải đúng với domain/IP của server
- `APP_KEY` sẽ được generate ở bước sau

### 3.3. Set quyền cho .env

```bash
chmod 600 .env
```

---

## 4. Build và Chạy Docker

### 4.1. Build Docker images

```bash
cd /var/www/project-management

# Build images (lần đầu sẽ mất vài phút)
docker-compose build

# Nếu muốn rebuild từ đầu
docker-compose build --no-cache
```

### 4.2. Khởi động services

```bash
# Start tất cả services
docker-compose up -d

# Xem logs để kiểm tra
docker-compose logs -f
```

**Giải thích:**
- `-d`: Chạy ở background (detached mode)
- `-f`: Follow logs (xem logs real-time)

### 4.3. Kiểm tra containers đang chạy

```bash
# Xem danh sách containers
docker-compose ps

# Kết quả mong đợi:
# NAME            IMAGE          STATUS
# mysql-db        mysql:8.0      Up
# laravel-app     ...            Up
# nginx-server    nginx:alpine   Up
```

---

## 5. Setup Database

### 5.1. Generate Application Key

```bash
# Generate APP_KEY
docker-compose exec app php artisan key:generate

# Kiểm tra key đã được tạo
docker-compose exec app php artisan tinker
# Trong tinker: config('app.key')
# Exit: exit
```

### 5.2. Chạy Migrations

```bash
# Chạy migrations
docker-compose exec app php artisan migrate --force

# Xem kết quả
docker-compose exec app php artisan migrate:status
```

### 5.3. Seed Database (Tùy chọn)

```bash
# Seed dữ liệu mặc định
docker-compose exec app php artisan db:seed --force

# Hoặc seed từng seeder cụ thể
docker-compose exec app php artisan db:seed --class=DefaultUserSeeder --force
```

### 5.4. Tạo Storage Link

```bash
# Tạo symbolic link cho storage
docker-compose exec app php artisan storage:link
```

---

## 6. Kiểm tra và Tối ưu

### 6.1. Kiểm tra ứng dụng

```bash
# Kiểm tra health endpoint
curl http://localhost/health

# Kiểm tra từ browser
# Mở: http://your-server-ip hoặc http://your-domain.com
```

### 6.2. Tối ưu cho Production

```bash
# Cache config
docker-compose exec app php artisan config:cache

# Cache routes
docker-compose exec app php artisan route:cache

# Cache views
docker-compose exec app php artisan view:cache

# Optimize autoloader
docker-compose exec app composer dump-autoload --optimize
```

### 6.3. Kiểm tra permissions

```bash
# Kiểm tra permissions của storage và cache
docker-compose exec app ls -la storage
docker-compose exec app ls -la bootstrap/cache

# Nếu cần, set lại permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### 6.4. Kiểm tra logs

```bash
# Xem logs của app
docker-compose logs app

# Xem logs của nginx
docker-compose logs nginx

# Xem logs của database
docker-compose logs db

# Xem logs real-time
docker-compose logs -f app
```

---

## 7. Troubleshooting

### 7.1. Container không start

```bash
# Xem logs chi tiết
docker-compose logs container-name

# Kiểm tra status
docker-compose ps

# Restart container
docker-compose restart container-name

# Rebuild và restart
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### 7.2. Lỗi kết nối database

```bash
# Kiểm tra database container
docker-compose exec db mysql -u root -p
# Nhập password từ DB_ROOT_PASSWORD

# Kiểm tra từ app container
docker-compose exec app php artisan tinker
# Trong tinker: DB::connection()->getPdo();

# Kiểm tra biến môi trường
docker-compose exec app env | grep DB_
```

### 7.3. Lỗi permissions

```bash
# Fix permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache
```

### 7.4. Lỗi 502 Bad Gateway

```bash
# Kiểm tra PHP-FPM
docker-compose exec app php-fpm -t

# Kiểm tra Nginx config
docker-compose exec nginx nginx -t

# Restart services
docker-compose restart app nginx
```

### 7.5. Clear cache khi có lỗi

```bash
# Clear tất cả cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Sau đó cache lại
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### 7.6. Vào container để debug

```bash
# Vào app container
docker-compose exec app bash

# Vào nginx container
docker-compose exec nginx sh

# Vào database container
docker-compose exec db bash
```

---

## 8. Các lệnh thường dùng

### 8.1. Quản lý containers

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose stop

# Stop và xóa containers
docker-compose down

# Restart services
docker-compose restart

# Xem logs
docker-compose logs -f [service-name]
```

### 8.2. Chạy Artisan commands

```bash
# Cú pháp chung
docker-compose exec app php artisan [command]

# Ví dụ
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app php artisan queue:work
```

### 8.3. Backup database

```bash
# Backup
docker-compose exec db mysqldump -u root -p project_management > backup.sql

# Restore
docker-compose exec -T db mysql -u root -p project_management < backup.sql
```

---

## 9. Cập nhật Code

### 9.1. Pull code mới

```bash
cd /var/www/project-management

# Pull code mới
git pull origin main

# Hoặc nếu không dùng git, upload code mới
```

### 9.2. Rebuild và restart

```bash
# Rebuild images (nếu có thay đổi Dockerfile)
docker-compose build

# Restart services
docker-compose restart

# Hoặc down và up lại
docker-compose down
docker-compose up -d
```

### 9.3. Chạy migrations mới

```bash
# Chạy migrations mới
docker-compose exec app php artisan migrate --force
```

---

## 10. Bảo mật

### 10.1. Firewall

```bash
# Chỉ mở port 80, 443
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 10.2. Đổi passwords mặc định

- Đổi `DB_PASSWORD` và `DB_ROOT_PASSWORD` trong `.env`
- Sử dụng password mạnh (ít nhất 16 ký tự)

### 10.3. SSL/HTTPS (Tùy chọn)

Cấu hình SSL với Let's Encrypt:

```bash
# Cài đặt Certbot
sudo apt install certbot python3-certbot-nginx

# Tạo certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal
sudo certbot renew --dry-run
```

---

## 11. Monitoring

### 11.1. Kiểm tra resources

```bash
# Xem usage của containers
docker stats

# Xem disk usage
df -h
docker system df
```

### 11.2. Logs rotation

```bash
# Cấu hình log rotation trong docker-compose.yml hoặc
# Sử dụng logrotate cho logs trên host
```

---

## ✅ Checklist Deploy

- [ ] Docker và Docker Compose đã cài đặt
- [ ] Code đã được upload lên server
- [ ] File `.env` đã được cấu hình đúng
- [ ] Docker images đã được build
- [ ] Containers đang chạy (docker-compose ps)
- [ ] APP_KEY đã được generate
- [ ] Migrations đã chạy
- [ ] Database đã được seed (nếu cần)
- [ ] Storage link đã được tạo
- [ ] Config, routes, views đã được cache
- [ ] Ứng dụng có thể truy cập được
- [ ] Logs không có lỗi nghiêm trọng
- [ ] Permissions đã được set đúng

---

## 📞 Hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. Logs: `docker-compose logs -f`
2. Container status: `docker-compose ps`
3. Health check: `curl http://localhost/health`
4. Database connection: `docker-compose exec app php artisan tinker`

---

**Lưu ý cuối:** 
- Luôn backup database trước khi deploy
- Test trên staging trước khi deploy production
- Giữ file `.env` bảo mật, không commit vào git

