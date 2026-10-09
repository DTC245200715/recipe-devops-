# Triển Khai Hệ Thống Website Công Thức Nấu Ăn (Recipe)

## 1. Giới thiệu
Đây là dự án triển khai hệ thống quản trị nội dung website chia sẻ công thức nấu ăn sử dụng WordPress, MySQL, Nginx, cùng các công cụ giám sát Prometheus, Grafana, Loki và Promtail thông qua Docker Compose.

## 2. Cấu trúc thư mục
- `app/`: Thư mục mã nguồn WordPress.
- `database/`: Thư mục chứa dữ liệu MySQL.
- `nginx/`: Cấu hình Nginx (reverse proxy, security headers).
- `prometheus/`: Cấu hình Prometheus thu thập metrics.
- `grafana/`: Cấu hình datasources và dashboards.
- `loki/`, `promtail/`: Cấu hình hệ thống logging tập trung.
- `docker-compose.yml`: File cấu hình toàn bộ dịch vụ.
- `.env`: File chứa các biến môi trường (mật khẩu, tên database...).

## 3. Hướng dẫn khởi chạy

1. Yêu cầu cài đặt Docker và Docker Compose.
2. Đảm bảo file `.env` đã được cấu hình các biến môi trường.
3. Khởi chạy toàn bộ hệ thống với lệnh:
   ```bash
   docker-compose up -d
   ```
4. Truy cập các dịch vụ:
   - Website chính: `http://localhost`
   - phpMyAdmin: `http://localhost:8080`
   - Grafana: `http://localhost:3000` (User: admin / Pass: như trong `.env`)
   - Prometheus: `http://localhost:9090`

## 4. Các tính năng bảo mật (Hardening)
- Chạy các dịch vụ Nginx, Prometheus, Grafana với quyền non-root (khai báo user trong compose hoặc qua image mặc định của nhà phát hành).
- Network Isolation: Tách biệt frontend và backend networks (cơ sở dữ liệu chỉ kết nối backend).
- Security headers trên Nginx.
- Mật khẩu thiết lập qua `.env` (không hardcode).

## 5. Demo LogQL
Vào Grafana -> Explore, chọn Datasource Loki và truy vấn:
- Xem log Nginx: `{container="recipe_nginx"}`
- Đếm số lượng log lỗi (nếu có): `sum(rate({container="recipe_nginx"} |= "error" [5m]))`
