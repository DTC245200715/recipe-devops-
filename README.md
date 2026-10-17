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
- UI/UX: Thiết kế hiện đại chuẩn HeroUI với phông chữ **Plus Jakarta Sans**, bộ icon FontAwesome, hệ thống đánh giá sao cộng đồng thời gian thực và Bảng thông số Dinh dưỡng (Nutritional Information) chuẩn khoa học.

## 2. Cấu trúc thư mục

```text
recipe-devops/
├── docker-compose.yml
├── .env
├── .env.example
├── .gitignore
├── .dockerignore
├── README.md
│
├── app/                                # Mã nguồn ứng dụng Web (PHP-FPM)
│   ├── Dockerfile                      # Build image web với user non-root (appuser)
│   ├── .dockerignore                   # Tối ưu hóa build context Docker
│   ├── public/                         # Thư mục web public
│   │   ├── index.php                   # Trang chủ danh sách món ăn, danh mục & đánh giá sao
│   │   ├── recipe.php                  # Trang chi tiết công thức, Bảng Dinh Dưỡng & Nhận xét
│   │   ├── login.php                   # Đăng nhập quản trị viên
│   │   ├── admin.php                   # Dashboard quản trị
│   │   ├── edit.php                    # Thêm/sửa công thức, nguyên liệu động & dinh dưỡng
│   │   ├── logout.php                  # Đăng xuất
│   │   ├── health.php                  # API kiểm tra sức khỏe
│   │   ├── uploads/                    # Ảnh thành phẩm món ăn mẫu
│   │   └── css/
│   │       └── style.css               # Giao diện website chuẩn HeroUI
│   └── src/
│       └── db.php                      # Kết nối MySQL (PDO + Prepared Statements)
│
├── db/                                 # Cơ sở dữ liệu
│   └── init.sql                        # Schema hoàn chỉnh (recipes, reviews, nutrition, categories)
│
├── report/                             # Tài liệu báo cáo thực hành chi tiết
│   └── BAO_CAO_THUC_HANH_CHI_TIET.md
│
├── screenshots/                        # Ảnh chụp màn hình minh chứng hệ thống
│   └── .gitkeep
│
├── grafana/                            # Cấu hình Grafana
│   ├── dashboards/                     # Dashboards giám sát
│   │   ├── recipe-overview.json        # Dashboard tổng quan hệ thống
│   │   └── truy-van.json               # Dashboard TRUY VẤN (Gauge & Timeseries)
│   └── provisioning/                   # Tự động nạp datasources & dashboards
│       ├── dashboards/
│       │   └── dashboard.yml
│       └── datasources/
│           └── datasource.yml
│
├── loki/                               # Cấu hình Loki
│   └── local-config.yaml               # Cấu hình lưu trữ log tập trung
│
├── mysql-exporter/                     # Cấu hình MySQL Exporter
│   └── .my.cnf                         # Credential kết nối database an toàn
│
├── nginx/                              # Nginx Reverse Proxy
│   ├── certs/                          # Chứng chỉ SSL tự ký
│   │   ├── selfsigned.crt
│   │   └── selfsigned.key
│   └── conf.d/
│       └── default.conf                # Cấu hình proxy, HTTPS, security headers
│
├── prometheus/                         # Cấu hình Prometheus
│   └── prometheus.yml                  # 5 job scrape metrics (cadvisor, node, mysql, nginx, prometheus)
│
├── promtail/                           # Cấu hình Promtail
│   └── config.yml                      # Đọc log từ Docker socket
│
└── scripts/                            # Script tiện ích
    ├── generate_report.py              # Script tạo báo cáo PDF tổng hợp (đường dẫn động)
    └── generate_traffic.ps1            # Script mô phỏng traffic động cho hệ thống
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
- Dashboards có sẵn:
  - `Recipe system overview`: Tổng quan hệ thống (CPU, RAM, Nginx HTTP Requests/sec, MySQL Queries/sec).
  - `TRUY VẤN`: Dashboard trực quan hóa với Gauge và Timeseries.

### Tạo lưu lượng truy cập thử nghiệm (Traffic Generator)

Để tạo số liệu động mô phỏng cho Grafana và Loki, chạy script:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\generate_traffic.ps1
```

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

