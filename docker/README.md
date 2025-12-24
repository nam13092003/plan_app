# Hướng dẫn Deploy với Docker

## Yêu cầu
- Docker >= 20.10
- Docker Compose >= 2.0

## Cấu hình

### 1. Tạo file `.env` từ `.env.example` (nếu chưa có)

```bash
cp .env.example .env
```

### 2. Cấu hình các biến môi trường trong `.env`

```env
APP_NAME="Project Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=laravel
DB_PASSWORD=your_secure_password

# Hoặc có thể override trong docker-compose.yml
```

### 3. Build và chạy containers

```bash
# Build images
docker-compose build

# Start services
docker-compose up -d

# Xem logs
docker-compose logs -f

# Chạy migrations (lần đầu)
docker-compose exec app php artisan migrate --force

# Seed database (nếu cần)
docker-compose exec app php artisan db:seed --force
```

### 4. Tối ưu cho production

Sau khi deploy, chạy các lệnh tối ưu:

```bash
# Cache config, routes, views
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Optimize autoloader
docker-compose exec app composer dump-autoload --optimize
```

## Cấu trúc

- `Dockerfile`: Multi-stage build cho PHP-FPM application
- `docker-compose.yml`: Orchestration cho các services
- `docker/nginx.conf`: Cấu hình Nginx
- `docker/php-fpm.conf`: Cấu hình PHP-FPM
- `docker/opcache.ini`: Cấu hình OPcache
- `docker/docker-entrypoint.sh`: Script khởi động container

## Services

1. **db**: MySQL 8.0 database
2. **app**: Laravel application với PHP 8.2-FPM
3. **nginx**: Nginx web server

## Ports

- `80`: HTTP (có thể thay đổi bằng biến `APP_PORT`)
- `443`: HTTPS (có thể thay đổi bằng biến `APP_HTTPS_PORT`)
- `3306`: MySQL (có thể thay đổi bằng biến `DB_PORT`)

## Volumes

- `db_data`: Persistent storage cho MySQL
- `./storage`: Laravel storage (logs, cache, files)
- `./bootstrap/cache`: Laravel bootstrap cache

## Troubleshooting

### Xem logs
```bash
docker-compose logs app
docker-compose logs nginx
docker-compose logs db
```

### Vào container
```bash
docker-compose exec app bash
docker-compose exec nginx sh
```

### Restart services
```bash
docker-compose restart
```

### Rebuild containers
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

