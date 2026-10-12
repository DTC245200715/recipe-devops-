# Bếp Ngon - Recipe Sharing Website

Website chia sẻ công thức nấu ăn được triển khai theo kiến trúc Docker Compose, gồm PHP, MySQL, Nginx, phpMyAdmin và các dịch vụ giám sát/logging theo yêu cầu của đề tài.

## 1. Kiến trúc hệ thống

- Backend: PHP 8.2 FPM, PDO + prepared statements
- Reverse proxy: Nginx (HTTP/HTTPS redirect, TLS tự ký, security headers)
- Database: MySQL 8.0 + dữ liệu mẫu trong `db/init.sql`
- DBA tool: phpMyAdmin
- Monitoring: Prometheus + Grafana + Node Exporter + cAdvisor + mysqld_exporter + nginx_exporter
- Logging: Loki + Promtail
- Hardening: container non-root, network isolation, security headers, quyền hạn tối thiểu
- UI/UX: Sử dụng thống nhất bộ phông chữ **Poppins** của Google Fonts và bộ icon FontAwesome để mang lại giao diện hiện đại, đồng bộ trên toàn bộ website.

## 2. Cấu trúc thư mục

```text
recipe-devops/
├── docker-compose.yml
├── .env
├── .env.example
├── README.md
├── app/
│   ├── Dockerfile
│   ├── public/
│   │   ├── index.php
│   │   ├── admin.php
│   │   ├── edit.php
│   │   ├── recipe.php
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── health.php
│   │   └── css/
│   │       └── style.css
│   └── src/
│       └── db.php
├── db/
│   └── init.sql
├── nginx/
│   ├── certs/
│   └── conf.d/
│       └── default.conf
├── grafana/
│   ├── dashboards/
│   │   └── recipe-overview.json
│   └── provisioning/
│       ├── dashboards/
│       │   └── dashboard.yml
│       └── datasources/
│           └── datasource.yml
├── loki/
│   └── local-config.yaml
├── mysql-exporter/
│   └── .my.cnf
├── prometheus/
│   └── prometheus.yml
├── promtail/
│   └── config.yml
└── .gitignore
```

## 3. Cách chạy dự án

1. Tạo file `.env` nếu chưa có:

```bash
cp .env.example .env
```

2. Khởi động toàn bộ stack:

```bash
docker compose up -d --build
```

3. Chờ 30-60 giây cho MySQL và các dịch vụ monitoring hoàn tất khởi tạo.

4. Kiểm tra trạng thái:

```bash
docker compose ps
```

## 4. Thông tin truy cập

- Website: https://localhost
- Website (HTTP redirect): http://localhost
- Admin: https://localhost/admin.php
  - Username: `thang`
  - Password: `thang123`
- phpMyAdmin: http://localhost:8080
  - Host: `db`
  - User: `thang` hoặc `recipe_user`
  - Password: `thang-dtc245200715` hoặc lưu trong `.env`
- Prometheus: http://localhost:9090
- Grafana: http://localhost:3000
  - User: `thang`
  - Password: `thang-dtc245200715`
- Loki: http://localhost:3100
- Healthcheck: https://localhost/health.php

## 5. Hardening và bảo mật

- Container PHP chạy với user non-root (`appuser`)
- Backend network là `internal`, chỉ các service cần thiết mới truy cập vào DB và app
- Nginx bật HTTPS với chứng chỉ self-signed và các security headers:
  - X-Frame-Options
  - X-Content-Type-Options
  - Referrer-Policy
  - Content-Security-Policy
  - Permissions-Policy
- Tắt `server_tokens` và giới hạn quyền trong cấu hình Nginx
- Dùng password mạnh cho DB, Grafana và MySQL exporter

## 6. Monitoring & Logging

### Prometheus / Grafana

- Prometheus scrape các mục tiêu sau:
  - cAdvisor
  - Node Exporter
  - MySQL Exporter
  - Nginx Exporter
  - chính Prometheus self-monitoring
- Dashboard mặc định: `Recipe system overview`

### Loki / Promtail

Promtail theo dõi log container thông qua Docker socket, sau đó đẩy lên Loki.

Ví dụ truy vấn LogQL cơ bản:

```logql
{container="recipe_web"} |= "User login"
```

```logql
{container=~"recipe_.*"} |= "ERROR"
```

```logql
{container="recipe_nginx"} |= "401" or "403"
```

## 7. Log và kiểm tra nhanh

```bash
docker compose logs -f web

docker compose logs -f nginx

docker compose logs -f promtail
```

## 8. Gỡ cài đặt

```bash
docker compose down -v
```

Lưu ý: câu lệnh trên sẽ xóa cả volume database và dữ liệu giám sát.

