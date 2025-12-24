# 🐳 Hướng dẫn Deploy Laravel + MySQL + PHP 8.4 + Nginx với Docker

## 📋 Yêu cầu

- Docker >= 20.10
- Docker Compose >= 2.0
- Git (để clone code)

## 📁 Cấu trúc thư mục

```
laravel-docker/
├── docker/
│   ├── nginx/
│   │   └── default.conf          # Cấu hình Nginx
│   └── php/
│       └── Dockerfile            # Dockerfile cho PHP 8.4
├── docker-compose.yml            # Orchestration file
├── .env                          # Environment variables (tạo từ .env.example)
└── src/                          # Code Laravel
    ├── app/
    ├── config/
    ├── database/
    ├── public/
    └── ...
```

## 🚀 Các bước deploy

### Bước 1: Chuẩn bị code Laravel

Nếu bạn chưa có code Laravel trong thư mục `src/`, hãy di chuyển code vào đó:

```bash
# Nếu code Laravel đang ở thư mục gốc
mkdir -p src
# Di chuyển các file Laravel vào src/ (trừ docker-compose.yml, .env, docker/)
# Hoặc nếu code đã ở src/ rồi thì bỏ qua bước này
```

### Bước 2: Cấu hình Environment

Tạo file `.env` từ template:

```bash
cp .env.example .env
```

Chỉnh sửa file `.env` với các thông tin của bạn:

```env
# Application
APP_NAME="Laravel App"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_root_password

# Ports
APP_PORT=80
```

**Lưu ý quan trọng:**
- `DB_HOST=db` - Tên service trong docker-compose, **KHÔNG ĐỔI**
- `DB_PASSWORD` và `DB_ROOT_PASSWORD` nên là password mạnh
- `APP_URL` phải đúng với domain/IP của server

### Bước 3: Build và khởi động containers

```bash
# Build images
docker-compose build

# Khởi động tất cả services
docker-compose up -d

# Xem logs để kiểm tra
docker-compose logs -f
```

### Bước 4: Cài đặt dependencies và setup Laravel

```bash
# Vào container PHP
docker-compose exec app bash

# Hoặc chạy lệnh trực tiếp từ ngoài:
# Cài đặt Composer dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Tạo symbolic link cho storage
docker-compose exec app php artisan storage:link

# Chạy migrations
docker-compose exec app php artisan migrate --force

# Seed database (nếu cần)
docker-compose exec app php artisan db:seed --force
```

### Bước 5: Tối ưu cho Production

```bash
# Cache config, routes, views
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Optimize autoloader
docker-compose exec app composer dump-autoload --optimize
```

### Bước 6: Kiểm tra

Mở trình duyệt và truy cập:
- `http://localhost` (hoặc domain/IP của bạn)
- `http://localhost/health` - Health check endpoint

## 🔧 Các lệnh hữu ích

### Quản lý containers

```bash
# Xem trạng thái containers
docker-compose ps

# Xem logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db

# Restart services
docker-compose restart

# Stop services
docker-compose stop

# Start services
docker-compose start

# Stop và xóa containers
docker-compose down

# Stop và xóa containers + volumes (xóa cả database)
docker-compose down -v
```

### Vào container

```bash
# Vào container PHP
docker-compose exec app bash

# Vào container MySQL
docker-compose exec db bash

# Vào container Nginx
docker-compose exec nginx sh
```

### Database

```bash
# Backup database
docker-compose exec db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} > backup.sql

# Restore database
docker-compose exec -T db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < backup.sql

# Kết nối MySQL từ ngoài
mysql -h localhost -P 3306 -u laravel -p
```

### Laravel Artisan

```bash
# Chạy bất kỳ lệnh artisan nào
docker-compose exec app php artisan [command]

# Ví dụ:
docker-compose exec app php artisan tinker
docker-compose exec app php artisan queue:work
docker-compose exec app php artisan cache:clear
```

## 🏗️ Cấu trúc Services

### 1. **db** - MySQL 8.0
- Port: `3306` (có thể thay đổi trong `.env`)
- Volume: `db_data` (persistent storage)
- Health check: Tự động kiểm tra sức khỏe

### 2. **app** - PHP 8.4 FPM
- Base image: `php:8.4-fpm`
- Port: `9000` (internal, không expose ra ngoài)
- Extensions: pdo_mysql, mbstring, exif, pcntl, bcmath, gd, zip, opcache, intl
- Volume: Mount thư mục `src/` vào `/var/www/html`

### 3. **nginx** - Nginx Web Server
- Image: `nginx:alpine`
- Port: `80` (có thể thay đổi trong `.env`)
- Config: `docker/nginx/default.conf`
- Volume: Mount thư mục `src/` để serve static files

## 🔒 Bảo mật

### Production Checklist

- [ ] Đổi `APP_DEBUG=false` trong `.env`
- [ ] Đặt `APP_ENV=production`
- [ ] Sử dụng password mạnh cho database
- [ ] Cấu hình HTTPS với SSL certificate
- [ ] Cập nhật `APP_URL` với domain thực tế
- [ ] Chạy `php artisan config:cache` sau khi thay đổi `.env`
- [ ] Kiểm tra file permissions cho `storage/` và `bootstrap/cache/`

### Cấu hình HTTPS (Nginx)

Để cấu hình HTTPS, bạn cần:
1. Có SSL certificate
2. Cập nhật `docker/nginx/default.conf` để thêm server block cho port 443
3. Cập nhật `docker-compose.yml` để expose port 443

## 🐛 Troubleshooting

### Lỗi: "Connection refused" khi kết nối database

**Nguyên nhân:** Database chưa sẵn sàng hoặc cấu hình sai.

**Giải pháp:**
```bash
# Kiểm tra database đã chạy chưa
docker-compose ps db

# Kiểm tra logs
docker-compose logs db

# Đảm bảo DB_HOST=db trong .env
```

### Lỗi: "Permission denied" trong storage/

**Giải pháp:**
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Lỗi: "502 Bad Gateway"

**Nguyên nhân:** PHP-FPM chưa sẵn sàng hoặc cấu hình Nginx sai.

**Giải pháp:**
```bash
# Kiểm tra PHP-FPM
docker-compose logs app

# Kiểm tra Nginx config
docker-compose exec nginx nginx -t

# Restart services
docker-compose restart
```

### Lỗi: "Class not found" hoặc autoload issues

**Giải pháp:**
```bash
docker-compose exec app composer dump-autoload
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

## 📚 Tài liệu tham khảo

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Nginx Documentation](https://nginx.org/en/docs/)

## 📝 Notes

- Code Laravel phải nằm trong thư mục `src/`
- File `.env` không nên commit vào Git (đã có trong `.gitignore`)
- Database data được lưu trong Docker volume `db_data`, sẽ không mất khi xóa container
- Để xóa hoàn toàn (kể cả database), chạy: `docker-compose down -v`

## 🆘 Hỗ trợ

Nếu gặp vấn đề, hãy kiểm tra:
1. Logs: `docker-compose logs -f`
2. Trạng thái containers: `docker-compose ps`
3. Cấu hình `.env` có đúng không
4. Permissions của thư mục `storage/` và `bootstrap/cache/`

