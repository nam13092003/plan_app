# Hướng dẫn Deploy trên Server

## Tóm tắt các thay đổi

### 1. Dockerfile
- ✅ Đổi từ PHP 8.4 sang PHP 8.2 (phù hợp với server)
- ✅ Sử dụng multi-stage build để build assets với Node.js
- ✅ Sử dụng PHP-FPM thay vì `artisan serve` cho production
- ✅ Cài đặt OPcache để tối ưu performance
- ✅ Copy files thay vì mount volumes (phù hợp production)
- ✅ Tự động chạy migrations và optimize khi khởi động

### 2. docker-compose.yml
- ✅ Thêm Nginx service làm reverse proxy
- ✅ Sử dụng environment variables thay vì hardcode
- ✅ Thêm health checks cho tất cả services
- ✅ Tạo network riêng cho các containers
- ✅ Cấu hình volumes cho persistent data

### 3. Files mới được tạo
- ✅ `docker/nginx.conf` - Cấu hình Nginx
- ✅ `docker/php-fpm.conf` - Cấu hình PHP-FPM
- ✅ `docker/opcache.ini` - Cấu hình OPcache
- ✅ `docker/docker-entrypoint.sh` - Script khởi động
- ✅ `.dockerignore` - Tối ưu build context
- ✅ `docker/README.md` - Hướng dẫn chi tiết

## Các bước deploy

### 1. Chuẩn bị trên server

```bash
# Cài đặt Docker và Docker Compose (nếu chưa có)
sudo apt update
sudo apt install docker.io docker-compose -y

# Clone project hoặc upload files lên server
cd /path/to/project
```

### 2. Cấu hình environment

Tạo file `.env` với các biến môi trường:

```env
APP_NAME="Project Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com
APP_KEY=base64:your-generated-key

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=laravel
DB_PASSWORD=your_secure_password

# Các biến khác...
```

**Lưu ý**: 
- `APP_KEY` cần được generate: `php artisan key:generate`
- `DB_PASSWORD` nên là password mạnh
- `APP_URL` là domain hoặc IP của server

### 3. Build và chạy

```bash
# Build images
docker-compose build

# Start services
docker-compose up -d

# Xem logs để kiểm tra
docker-compose logs -f
```

### 4. Setup database (lần đầu)

```bash
# Chạy migrations
docker-compose exec app php artisan migrate --force

# Seed database (nếu cần)
docker-compose exec app php artisan db:seed --force
```

### 5. Tối ưu cho production

```bash
# Cache config, routes, views
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Optimize autoloader
docker-compose exec app composer dump-autoload --optimize
```

## Cấu trúc Services

1. **db** (MySQL 8.0)
   - Port: 3306 (có thể thay đổi)
   - Volume: `db_data` (persistent)

2. **app** (Laravel + PHP 8.2-FPM)
   - Port: 9000 (internal, PHP-FPM)
   - Depends on: db

3. **nginx** (Web Server)
   - Port: 80 (HTTP), 443 (HTTPS)
   - Reverse proxy đến app:9000

## Environment Variables trong docker-compose.yml

Có thể override các biến sau trong `.env` hoặc trực tiếp trong `docker-compose.yml`:

- `APP_PORT` - Port HTTP (mặc định: 80)
- `APP_HTTPS_PORT` - Port HTTPS (mặc định: 443)
- `DB_PORT` - Port MySQL (mặc định: 3306)
- `DB_DATABASE` - Tên database
- `DB_USERNAME` - Username database
- `DB_PASSWORD` - Password database
- `DB_ROOT_PASSWORD` - Root password MySQL
- `APP_ENV` - Environment (production/local)
- `APP_DEBUG` - Debug mode (true/false)
- `APP_URL` - URL của ứng dụng

## Troubleshooting

### Kiểm tra logs
```bash
docker-compose logs app
docker-compose logs nginx
docker-compose logs db
```

### Vào container để debug
```bash
docker-compose exec app bash
docker-compose exec nginx sh
```

### Restart services
```bash
docker-compose restart
# Hoặc restart từng service
docker-compose restart app
docker-compose restart nginx
```

### Rebuild từ đầu
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Kiểm tra health
```bash
# Health check endpoint
curl http://localhost/health

# Kiểm tra database connection
docker-compose exec app php artisan tinker
# Trong tinker: DB::connection()->getPdo();
```

## Lưu ý quan trọng

1. **Security**: 
   - Đổi tất cả passwords mặc định
   - Không expose MySQL port ra ngoài nếu không cần
   - Sử dụng HTTPS trong production

2. **Performance**:
   - OPcache đã được bật và cấu hình
   - Assets đã được build và optimize
   - Config, routes, views nên được cache

3. **Backup**:
   - Backup database thường xuyên
   - Backup volume `db_data`

4. **Updates**:
   - Khi update code, rebuild images
   - Chạy migrations nếu có thay đổi database

## Next Steps

- [ ] Cấu hình SSL/HTTPS với Let's Encrypt
- [ ] Setup backup tự động cho database
- [ ] Cấu hình monitoring và logging
- [ ] Tối ưu Nginx (rate limiting, caching, etc.)
- [ ] Setup CI/CD pipeline

