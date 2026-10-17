# BÁO CÁO THỰC HÀNH CHUYÊN SÂU
# HỌC PHẦN: TRIỂN KHAI VÀ QUẢN TRỊ HỆ THỐNG PHẦN MỀM

---

<!-- TRANG 1: TRANG BÌA -->
### TRƯỜNG ĐẠI HỌC CÔNG NGHỆ THÔNG TIN & TRUYỀN THÔNG
### KHOA CÔNG NGHỆ THÔNG TIN
### BỘ MÔN: HỆ THỐNG THÔNG TIN & QUẢN TRỊ MẠNG

<br><br><br>

# BÁO CÁO KẾT QUẢ THỰC HÀNH VẬN HÀNH HỆ THỐNG

### **ĐỀ TÀI SỐ 29:**
## **TRIỂN KHAI WEBSITE CÔNG THỨC NẤU ĂN BẰNG DOCKER, TÍCH HỢP GIÁM SÁT VÀ THU THẬP LOG**

<br><br><br>

**Sinh viên thực hiện:** Triệu Đức Thắng  
**Mã số sinh viên:** DTC245200715  
**Lớp sinh hoạt:** CNTTK23B  
**Khóa học:** 2023 – 2027  
**Giảng viên hướng dẫn:** Bộ môn Quản trị và Triển khai Hệ thống Phần mềm  
**Học kỳ:** I — Năm học: 2025 – 2026  
**Kho lưu trữ mã nguồn (GitHub):** [https://github.com/DTC245200715/recipe-devops](https://github.com/DTC245200715/recipe-devops)  

<br><br><br>

---

\newpage

<!-- TRANG 2: LỜI MỞ ĐẦU, MỤC TIÊU & PHẠM VI -->
# LỜI MỞ ĐẦU, MỤC TIÊU & PHẠM VI DỰ ÁN

### 1. Lời mở đầu
Trong xu thế chuyển đổi số và kiến trúc phân tán hiện đại, công nghệ đóng gói Container (Docker) đã trở thành tiêu chuẩn cốt lõi nhằm giải quyết bài toán sai lệch môi trường và tối ưu hóa hạ tầng máy chủ. Tuy nhiên, một hệ thống hướng dịch vụ chỉ thực sự hoàn thiện khi được tích hợp năng lực quan sát toàn diện (**Observability**): Giám sát số liệu tài nguyên thời gian thực (**Metrics**) và Thu thập xử lý nhật ký tập trung (**Logs**). Đề tài **"Đề 29: Triển khai website công thức nấu ăn bằng Docker, tích hợp giám sát và thu thập log"** được triển khai nhằm mô phỏng toàn diện chu trình DevOps thực tế: điều phối 12 containers dịch vụ, bảo mật đa lớp (Hardening, SSL, Security Headers), giám sát với Prometheus - Grafana và quản lý log với Loki - Promtail.

### 2. Mục tiêu dự án
*   **Đóng gói & Điều phối hạ tầng:** Đóng gói độc lập 12 containers bằng Docker Compose, phân chia 2 không gian mạng ảo hóa và duy trì dữ liệu liên tục qua Named Volumes.
*   **Reverse Proxy & Bảo mật web:** Triển khai Nginx làm Gateway duy nhất, thực thi chuyển hướng HTTP 301 sang HTTPS, cấp chứng chỉ SSL tự ký và cấu hình 4 Security Headers tiêu chuẩn (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy).
*   **Gia cố an ninh (Hardening):** Vận hành container PHP dưới quyền người dùng phi đặc quyền `appuser` (UID 1000); cô lập mạng backend (`internal: true`); áp đặt danh sách kiểm soát truy cập (ACL) cho endpoint `/nginx_status`.
*   **Năng lực quan sát hệ thống:** Thu thập 5 targets qua 4 Exporters chuyên dụng; xây dựng câu truy vấn PromQL đo lường CPU, RAM, QPS, RPS; thu thập nhật ký tự động qua Docker Engine Socket với Promtail và phân tích chuyên sâu bằng 4 truy vấn LogQL trên Grafana.
*   **Quản lý phiên bản & Vận hành:** Quản lý mã nguồn trên GitHub với 3 mốc commit minh chứng, tài liệu hướng dẫn kỹ thuật chi tiết và cẩm nang xử lý sự cố.

### 3. Phạm vi dự án
Website chia sẻ công thức nấu ăn "Bếp Ngon" (PHP 8.2-FPM + MySQL 8.0 kết nối PDO Prepared Statements chống SQL Injection), gồm 5 bảng dữ liệu nạp sẵn 12 món ăn mẫu; hạ tầng vận hành gồm 12 containers được điều phối tự động trên Docker Compose.

---

### MỤC LỤC TỔNG QUAN (CẤU TRÚC RÚT GỌN 5 CHƯƠNG)
*   **Chương 1:** Tổng Quan Dự Án, Kiến Trúc Hệ Thống & Docker Compose (Trang 3 - 6)
*   **Chương 2:** Triển Khai Website PHP, Cơ Sở Dữ Liệu MySQL & Quản Trị phpMyAdmin (Trang 7 - 9)
*   **Chương 3:** Nginx Reverse Proxy, HTTPS & Gia Cố Bảo Mật Hệ Thống (Trang 10 - 13)
*   **Chương 4:** Hệ Thống Giám Sát Hiệu Năng & Nhật Ký Tập Trung (Prometheus, Grafana, Loki, Promtail) (Trang 14 - 18)
*   **Chương 5:** Quản Lý Mã Nguồn GitHub, Quy Trình Vận Hành & Khắc Phục Sự Cố (Trang 19 - 20)

\newpage

<!-- TRANG 3: CHƯƠNG 1 (TỔNG QUAN & KIẾN TRÚC 3 LỚP) -->
# CHƯƠNG 1: TỔNG QUAN DỰ ÁN, KIẾN TRÚC HỆ THỐNG & DOCKER COMPOSE

### 1.1. Bối cảnh và yêu cầu kỹ thuật
Hệ thống "Bếp Ngon" được thiết kế dựa trên triết lý Cơ sở hạ tầng dưới dạng mã (Infrastructure as Code - IaC). Toàn bộ hệ thống có thể triển khai tự động chỉ với một câu lệnh (`docker compose up -d`), đảm bảo tính đóng gói, khả năng cô lập lỗi và an toàn bảo mật đa tầng từ cổng tiếp nhận cho đến tầng cơ sở dữ liệu.

### 1.2. Mô hình kiến trúc 3 lớp (3-Tier Architecture)
Hệ thống được tổ chức mạch lạc thành 3 tầng chức năng độc lập:
*   **Tầng Presentation (Gateway):** Do `recipe_nginx` đảm nhận, tiếp nhận kết nối qua cổng 80 và 443. Thực hiện chấm dứt phiên mã hóa SSL (SSL Termination), áp đặt tiêu đề an ninh HTTP, phục vụ tệp tĩnh và chuyển tiếp yêu cầu xử lý động.
*   **Tầng Application Logic:** Do `recipe_web` (PHP 8.2-FPM) xử lý, chỉ lắng nghe trên cổng TCP nội bộ 9000. Thực thi toàn bộ mã nguồn nghiệp vụ, xác thực người dùng và kết xuất giao diện HTML.
*   **Tầng Data:** Do `recipe_mysql` (MySQL 8.0) đảm nhận, lưu trữ dữ liệu bền vững trên Named Volume và được hỗ trợ quản trị trực quan thông qua `recipe_phpmyadmin` (Port 8080).

### 1.3. Luồng luân chuyển dữ liệu và Dual Observability Pipeline
1.  **Luồng xử lý yêu cầu người dùng:** Client gửi yêu cầu HTTPS (`:443`). Nginx kiểm tra định tuyến, chuyển tiếp yêu cầu `.php` qua mạng nội bộ tới PHP-FPM (`:9000`). PHP-FPM kết nối CSDL MySQL (`:3306`) lấy dữ liệu và phản hồi kết quả HTML về Nginx.
2.  **Luồng Metrics (Pull-model):** Prometheus định kỳ mỗi 15 giây cào chỉ số từ 4 Exporters nội bộ (cAdvisor, Node-exporter, Mysqld-exporter, Nginx-exporter), lưu trữ vào TSDB để Grafana truy vấn.
3.  **Luồng Logs (Push-model):** Toàn bộ container đẩy log ra `stdout/stderr`. Promtail đọc trực tiếp qua Docker Socket (`/var/run/docker.sock`), tự động gán nhãn định danh và đẩy về Loki (`:3100`).

\newpage

<!-- TRANG 4: CHƯƠNG 1 (SƠ ĐỒ KIẾN TRÚC & MINH CHỨNG) -->
### 1.4. Sơ đồ kiến trúc tổng thể và luồng dịch vụ

```
                                [ CLIENT / WEB BROWSER ]
                                           │
                     HTTP (:8000 - Redirect 301) / HTTPS (:443 - SSL)
                                           ▼
┌───────────────────────────────────────────────────────────────────────────────────────┐
│ 1. TẦNG CỔNG VÀO (GATEWAY / FRONTEND NETWORK)                                         │
│                                                                                       │
│   ┌─────────────────────────────────────────┐   ┌───────────────────────────────────┐ │
│   │   Nginx Reverse Proxy (recipe_nginx)    │   │   phpMyAdmin (recipe_phpmyadmin)  │ │
│   │   - Host Port: 8000:80, 443:443         │   │   - Host Port: 8080:80            │ │
│   │   - Chuyển hướng 301, SSL, Sec-Headers  │   │   - Quản trị giao diện CSDL       │ │
│   └────────────────────┬────────────────────┘   └─────────────────┬─────────────────┘ │
└────────────────────────┼──────────────────────────────────────────┼───────────────────┘
                         │ FastCGI (TCP :9000)                      │ MySQL TCP :3306
┌────────────────────────┼──────────────────────────────────────────┼───────────────────┐
│ 2. TẦNG BACKEND & DỮ LIỆU (BACKEND NETWORK - ISOLATED: internal: true)                │
│                        ▼                                          │                   │
│   ┌─────────────────────────────────────────┐                     │                   │
│   │   PHP 8.2-FPM (recipe_web)              │                     │                   │
│   │   - Chạy quyền 'appuser' (UID 1000)     │                     │                   │
│   │   - Kết nối PDO Prepared Statements     │                     │                   │
│   └────────────────────┬────────────────────┘                     │                   │
│                        │ Kết nối CSDL nội bộ (:3306)              │                   │
│                        ▼                                          ▼                   │
│   ┌─────────────────────────────────────────────────────────────────────────────────┐ │
│   │   MySQL 8.0 RDBMS (recipe_mysql)                                                │ │
│   │   - Volume: mysql_data (Lưu trữ bền vững)                                      │ │
│   │   - Không publish cổng ra Host (Cô lập hoàn toàn)                               │ │
│   └─────────────────────────────────────────────────────────────────────────────────┘ │
└───────────────────────────────────────────────────────────────────────────────────────┘
```

```
[HÌNH 1.1: Sơ đồ kiến trúc tổng thể hệ thống 3 lớp kết hợp luồng Monitoring (Prometheus/Grafana) và Logging (Loki/Promtail) - Yêu cầu: Bản vẽ sơ đồ khối chi tiết đầy đủ 12 dịch vụ, 2 phân vùng mạng và luồng dữ liệu]
```

\newpage

<!-- TRANG 5: CHƯƠNG 1 (CẤU TRÚC THƯ MỤC & ĐẶC TẢ 12 CONTAINERS) -->
### 1.5. Cấu trúc thư mục mã nguồn và Đặc tả 12 Containers dịch vụ
Toàn bộ mã nguồn dự án được tổ chức tại `D:\recipe-devops` gồm các thư mục chức năng: `app/` (mã PHP, Dockerfile), `db/` (`init.sql`), `nginx/` (`default.conf`, `certs/`), `prometheus/` (`prometheus.yml`), `grafana/` (dashboards/provisioning), `loki/`, `promtail/`, `mysql-exporter/` và `scripts/`.

| STT | Tên Container | Base Image | Ánh xạ cổng (Host:Container) | Mạng kết nối | Chức năng chi tiết |
|:---:|:---|:---|:---:|:---:|:---|
| 1 | `recipe_web` | Build `./app/Dockerfile` | Không (Cô lập) | `backend` | Xử lý logic PHP 8.2-FPM, chạy user `appuser` (UID 1000) |
| 2 | `recipe_nginx` | `nginx:1.25-alpine` | `8000:80`, `443:443` | `frontend`, `backend` | Gateway, SSL, Security Headers, Proxy FastCGI |
| 3 | `recipe_mysql` | `mysql:8.0` | Không (Cô lập) | `backend` | CSDL MySQL lưu trữ dữ liệu món ăn, tài khoản |
| 4 | `recipe_phpmyadmin` | `phpmyadmin/phpmyadmin:5.2` | `8080:80` | `frontend`, `backend` | Quản trị CSDL qua giao diện web |
| 5 | `recipe_prometheus` | `prom/prometheus:latest` | `9090:9090` | `frontend`, `backend` | TSDB cào số liệu 15s từ 5 targets, xử lý PromQL |
| 6 | `recipe_grafana` | `grafana/grafana:latest` | `3000:3000` | `frontend`, `backend` | Trực quan hóa số liệu qua Dashboard & Log Explore |
| 7 | `recipe_loki` | `grafana/loki:latest` | `3100:3100` | `frontend`, `backend` | Gom và đánh chỉ mục metadata nhật ký |
| 8 | `recipe_promtail` | `grafana/promtail:latest` | Không | `backend` | Đọc log qua Docker socket, đẩy về Loki |
| 9 | `recipe_cadvisor` | `gcr.io/cadvisor/cadvisor:v0.47.2` | Không | `backend` | Đo CPU, RAM, Network I/O từng container |
| 10| `recipe_node_exporter` | `prom/node-exporter:latest` | Không | `backend` | Thu thập chỉ số phần cứng và HĐH của Host |
| 11| `recipe_mysql_exporter`| `prom/mysqld-exporter:latest` | Không | `backend` | Trích xuất QPS, Threads, Buffer Pool từ MySQL |
| 12| `recipe_nginx_exporter`| `nginx/nginx-prometheus-exporter`| Không | `backend` | Trích xuất RPS, kết nối active từ `/nginx_status` |

\newpage

<!-- TRANG 6: CHƯƠNG 1 (MẠNG DOCKER, VOLUMES & MINH CHỨNG CONTAINER) -->
### 1.6. Phân vùng mạng và Quản lý lưu trữ bền vững (Named Volumes)
Trong [docker-compose.yml](file:///d:/recipe-devops/docker-compose.yml), hệ thống định nghĩa 2 mạng riêng biệt:
```yaml
networks:
  frontend:
    driver: bridge
  backend:
    driver: bridge
    internal: true
```
*   **Mạng `frontend`:** Cấu hình Bridge cho phép giao tiếp với bên ngoài qua NAT. Chỉ các dịch vụ giao diện (`nginx`, `phpmyadmin`, `prometheus`, `grafana`, `loki`) mới mở cổng ra máy chủ Host.
*   **Mạng `backend` (`internal: true`):** Kích hoạt cờ `internal: true` khiến Docker cấm hoàn toàn lưu lượng định tuyến ra Internet hoặc truy cập trực tiếp từ máy chủ Host vào mạng nội bộ. Các thành phần nhạy cảm (`web`, `db`, 4 Exporters) được bảo vệ tuyệt đối trước nguy cơ quét cổng.
*   **Named Volumes (`mysql_data`, `prometheus_data`, `grafana_data`):** Đảm bảo toàn bộ dữ liệu CSDL món ăn, lịch sử time-series của Prometheus và cấu hình Grafana được lưu an toàn trên ổ đĩa vật lý của Host, không bị mất khi container restart.

```
[HÌNH 1.2: Trạng thái 12 Containers dịch vụ đang vận hành ổn định - Yêu cầu chụp rõ: Kết quả thực thi lệnh docker compose ps -a hiển thị đầy đủ 12 dịch vụ với trạng thái "Up" (running) hoặc "Up (healthy)"]
```

\newpage

<!-- TRANG 7: CHƯƠNG 2 (TRIỂN KHAI WEBSITE PHP & MYSQL) -->
# CHƯƠNG 2: TRIỂN KHAI WEBSITE PHP, CƠ SỞ DỮ LIỆU MYSQL & QUẢN TRỊ PHPMYADMIN

### 2.1. Thiết kế ứng dụng Web PHP 8.2-FPM an toàn
Ứng dụng "Bếp Ngon" được thiết kế tối ưu, tuân thủ nghiêm ngặt các quy chuẩn an toàn thông tin:
*   **Kết nối CSDL an toàn qua PDO ([app/src/db.php](file:///d:/recipe-devops/app/src/db.php)):**
```php
$dsn = "mysql:host=" . (getenv('DB_HOST') ?: 'db') . ";dbname=" . (getenv('DB_NAME') ?: 'recipe_db') . ";charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Buộc MySQL Engine xử lý Prepared Statements nguyên bản
];
$pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), $options);
```
Cơ chế tắt chế độ giả lập (`ATTR_EMULATE_PREPARES => false`) giúp loại bỏ hoàn toàn khả năng bị tấn công SQL Injection.
*   **Giao diện người dùng:** Sử dụng phông chữ Poppins và FontAwesome. Hỗ trợ tìm kiếm thời gian thực, lọc 5 danh mục món ăn, hiển thị danh sách bài viết dạng thẻ (Card Layout).
*   **Trang chi tiết món ăn ([recipe.php](file:///d:/recipe-devops/app/public/recipe.php)):** Phân tách rõ ràng bảng định lượng nguyên liệu và các bước hướng dẫn nấu chi tiết.
*   **Khu vực quản trị ([admin.php](file:///d:/recipe-devops/app/public/admin.php), [edit.php](file:///d:/recipe-devops/app/public/edit.php)):** Xác thực đăng nhập an toàn, cho phép thêm, sửa, xóa công thức và tải ảnh lên thư mục `/uploads`.

### 2.2. Cấu trúc cơ sở dữ liệu và nạp dữ liệu mẫu (Seed Data)
Kịch bản [db/init.sql](file:///d:/recipe-devops/db/init.sql) tự động khởi tạo cơ sở dữ liệu gồm 5 bảng:
*   `categories` (5 danh mục), `recipes` (thông tin công thức, thời gian nấu, khẩu phần, độ khó), `ingredients` & `recipe_ingredients` (quan hệ N-N quản lý định lượng nguyên liệu), `users` (tài khoản quản trị băm mật khẩu).
*   Hệ thống nạp sẵn 12 món ăn mẫu đặc sắc của ẩm thực Việt Nam: Phở Bò, Bún Chả, Canh Chua Cá Lóc, Gỏi Cuốn, Chè Khúc Bạch, Đậu Hũ Tứ Xuyên, Trà Sữa Trân Châu, Bánh Xèo Nam Bộ, Cà Phê Trứng, Nem Rán Hà Nội, Bún Bò Huế Đặc Biệt, Gan Cháy Tỏi.

\newpage

<!-- TRANG 8: CHƯƠNG 2 (MINH CHỨNG WEBSITE) -->
### 2.3. Minh chứng giao diện website người dùng

```
[HÌNH 2.1: Giao diện trang chủ Website Bếp Ngon trên nền tảng HTTPS - Yêu cầu chụp rõ: URL https://localhost, biểu tượng ổ khóa bảo mật, thanh tìm kiếm, bộ lọc danh mục và danh sách các thẻ món ăn hiển thị đầy đủ hình ảnh, thời gian nấu và độ khó]
```

```
[HÌNH 2.2: Giao diện chi tiết công thức món ăn (Phở Bò / Bún Bò Huế) - Yêu cầu chụp rõ: Tiêu đề món ăn, ảnh minh họa chất lượng cao, bảng định lượng nguyên liệu chi tiết và các bước hướng dẫn nấu tuần tự]
```

\newpage

<!-- TRANG 9: CHƯƠNG 2 (ADMIN, PHPMYADMIN & HEALTHCHECK) -->
### 2.4. Quản trị hệ thống qua phpMyAdmin và Health Endpoint
*   **phpMyAdmin (Port 8080):** Giúp người quản trị theo dõi trực quan các bản ghi trong bảng `recipes`, kiểm tra khóa ngoại và tối ưu hóa chỉ mục.
*   **Kiểm tra sức khỏe hệ thống ([app/public/health.php](file:///d:/recipe-devops/app/public/health.php)):** Thực hiện truy vấn trực tiếp `SELECT 1` xuống MySQL để xác thực trạng thái kết nối thực tế trước khi phản hồi JSON HTTP 200.

```
[HÌNH 2.3: Giao diện Dashboard quản trị công thức món ăn - Yêu cầu chụp rõ: URL https://localhost/admin.php, danh sách 12 công thức nấu ăn, các nút thao tác Thêm, Sửa và Xóa bài viết]
```

```
[HÌNH 2.4: Giao diện quản trị CSDL phpMyAdmin - Yêu cầu chụp rõ: URL http://localhost:8080, cơ sở dữ liệu recipe_db, danh sách 12 dòng dữ liệu trong bảng recipes]
```

\newpage

<!-- TRANG 10: CHƯƠNG 3 (NGINX, HTTPS & HARDENING) -->
# CHƯƠNG 3: NGINX REVERSE PROXY, HTTPS & GIA CỐ BẢO MẬT HỆ THỐNG

### 3.1. Cấu hình Nginx Reverse Proxy, Chuyển hướng 301 & SSL
Trong [nginx/conf.d/default.conf](file:///d:/recipe-devops/nginx/conf.d/default.conf), Nginx đảm nhận vai trò Gateway bảo vệ máy chủ ứng dụng:
*   **Chuyển hướng bắt buộc HTTP sang HTTPS:** Khối máy chủ cổng 80 trả về mã trạng thái `301 Moved Permanently` với `Location: https://$host$request_uri;`.
*   **Mã hóa kênh truyền:** Lắng nghe trên cổng 443 với giao thức SSL, áp dụng chứng chỉ số tự ký chuẩn x509 (cặp khóa RSA 2048-bit `selfsigned.crt` và `selfsigned.key`).

### 3.2. Cấu hình 4 Tiêu đề an ninh HTTP (Security Headers)
```nginx
add_header X-Frame-Options "SAMEORIGIN";
add_header X-Content-Type-Options "nosniff";
add_header Referrer-Policy "strict-origin-when-cross-origin";
add_header Content-Security-Policy "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: https:;";
```
*   `X-Frame-Options: SAMEORIGIN`: Ngăn chặn website bị nhúng vào iframe của trang web khác, chống tấn công Clickjacking.
*   `X-Content-Type-Options: nosniff`: Buộc trình duyệt tuân thủ MIME type công bố, chống tấn công MIME Sniffing.
*   `Referrer-Policy: strict-origin-when-cross-origin`: Giới hạn thông tin đường dẫn gửi đi khi chuyển trang, chống rò rỉ dữ liệu.
*   `Content-Security-Policy`: Thiết lập danh sách nguồn tài nguyên an toàn, ngăn chặn tấn công Cross-Site Scripting (XSS).

### 3.3. Các biện pháp gia cố bảo mật chuyên sâu (Hardening)
1.  **Chạy Container phi đặc quyền (Non-root với `appuser`):** Trong [app/Dockerfile](file:///d:/recipe-devops/app/Dockerfile), khởi tạo người dùng `appuser` (UID 1000) và cấp quyền thư mục ứng dụng. Tiến trình `php-fpm` thực thi hoàn toàn dưới user non-root, triệt tiêu rủi ro Container Escape chiếm quyền host.
2.  **Cách ly mạng backend:** Mạng `backend` được cấu hình `internal: true`, MySQL không publish cổng ra ngoài Host, bảo vệ CSDL tuyệt đối trước các đợt quét cổng.
3.  **Hạn chế truy cập `/nginx_status`:** Chỉ cho phép các dải IP nội bộ Docker (`172.16.0.0/12`, `192.168.0.0/16`, `10.0.0.0/8`) truy cập cào số liệu; người dùng từ bên ngoài sẽ nhận mã lỗi `403 Forbidden`.
4.  **Quản lý bí mật an toàn:** Tách biệt thông tin mật khẩu vào [.env](file:///d:/recipe-devops/.env) và khai báo trong [.gitignore](file:///d:/recipe-devops/.gitignore).

\newpage

<!-- TRANG 11: CHƯƠNG 3 (MINH CHỨNG HTTPS & HEADERS) -->
### 3.4. Minh chứng kiểm thử chuyển hướng HTTP và Security Headers

```
[HÌNH 3.1: Kiểm thử cơ chế chuyển hướng HTTP sang HTTPS bằng cURL - Yêu cầu chụp rõ: Lệnh curl.exe -I http://localhost:8000 hiển thị mã trạng thái HTTP/1.1 301 Moved Permanently cùng tiêu đề Location: https://localhost/]
```

```
[HÌNH 3.2: Kiểm tra các Security Headers bằng cURL - Yêu cầu chụp rõ: Lệnh curl.exe -k -I https://localhost trả về mã trạng thái HTTP/1.1 200 OK và hiển thị đầy đủ 4 tiêu đề X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Content-Security-Policy]
```

\newpage

<!-- TRANG 12: CHƯƠNG 3 (MINH CHỨNG HARDENING) -->
### 3.5. Minh chứng kiểm thử các biện pháp gia cố bảo mật

```
[HÌNH 3.3: Xác minh người dùng phi đặc quyền trong container PHP - Yêu cầu chụp rõ: Lệnh docker exec recipe_web whoami hiển thị "appuser" và lệnh docker exec recipe_web id hiển thị "uid=1000(appuser) gid=1000(appuser)"]
```

```
[HÌNH 3.4: Kiểm tra chặn truy cập trái phép vào endpoint /nginx_status - Yêu cầu chụp rõ: Lệnh curl.exe -k -i https://localhost/nginx_status bị chặn lại với mã lỗi HTTP/1.1 403 Forbidden]
```

\newpage

<!-- TRANG 13: CHƯƠNG 3 (CẤU HÌNH NGINX & DOCKERFILE CHI TIẾT) -->
### 3.6. Trích dẫn cấu hình Nginx và Dockerfile Hardening
*   **Trích đoạn cấu hình vhost Nginx ([nginx/conf.d/default.conf](file:///d:/recipe-devops/nginx/conf.d/default.conf)):**
```nginx
server {
    listen 443 ssl;
    server_name localhost;
    ssl_certificate /etc/nginx/certs/selfsigned.crt;
    ssl_certificate_key /etc/nginx/certs/selfsigned.key;

    # 4 Security Headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    add_header Content-Security-Policy "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: https:;";

    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass web:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location = /nginx_status {
        stub_status;
        allow 172.16.0.0/12;
        allow 192.168.0.0/16;
        allow 10.0.0.0/8;
        deny all;
    }
}
```

*   **Dockerfile đóng gói an toàn ([app/Dockerfile](file:///d:/recipe-devops/app/Dockerfile)):**
```dockerfile
FROM php:8.2-fpm-alpine
RUN apk add --no-cache curl && docker-php-ext-install pdo pdo_mysql

# Khởi tạo user non-root
RUN addgroup -g 1000 appuser && \
    adduser -u 1000 -G appuser -s /bin/sh -D appuser && \
    mkdir -p /var/www/html/public/uploads && \
    chown -R appuser:appuser /var/www/html

WORKDIR /var/www/html
USER appuser
```

\newpage

<!-- TRANG 14: CHƯƠNG 4 (MONITORING & LOGGING) -->
# CHƯƠNG 4: HỆ THỐNG GIÁM SÁT HIỆU NĂNG & NHẬT KÝ TẬP TRUNG

### 4.1. Hệ thống giám sát Prometheus và 4 Exporters
Prometheus cào số liệu định kỳ 15 giây từ 5 targets (`prometheus.yml`):
*   `cadvisor:8080`: Đo chi tiết CPU, RAM, Network I/O từng container.
*   `node-exporter:9100`: Giám sát tải phần cứng và hệ điều hành của Host.
*   `mysql-exporter:9104`: Đo lường QPS, Threads, Buffer Pool từ MySQL.
*   `nginx-exporter:9113`: Trích xuất số lượng request HTTP và kết nối active từ Nginx.
*   `localhost:9090`: Tự giám sát máy chủ Prometheus.

### 4.2. Phân tích 4 câu truy vấn PromQL cốt lõi
1.  **CPU Host sử dụng (%):**
    ```promql
    100 - (avg by (instance) (rate(node_cpu_seconds_total{mode="idle"}[1m])) * 100)
    ```
    *Cơ chế:* Đo tốc độ tăng thời gian CPU ở trạng thái `idle` trong 1 phút, lấy bù trừ khỏi 100% để thu được tải CPU thực tế.
2.  **RAM Host đang sử dụng (%):**
    ```promql
    (1 - (node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes)) * 100
    ```
    *Cơ chế:* Sử dụng `MemAvailable` phản ánh chính xác lượng RAM cấp phát được ngay mà không gây tráo đổi đĩa (swap).
3.  **Tốc độ yêu cầu HTTP Nginx (Request Rate - req/s):**
    ```promql
    rate(nginx_http_requests_total[1m])
    ```
    *Cơ chế:* Tính đạo hàm biến đếm số request trên cửa sổ 1 phút để tính số yêu cầu/giây.
4.  **Tốc độ truy vấn MySQL (QPS):**
    ```promql
    rate(mysql_global_status_queries[1m])
    ```
    *Cơ chế:* Tính số lượng câu lệnh SQL được MySQL xử lý trung bình mỗi giây.

### 4.3. Hệ thống thu thập nhật ký tập trung Loki & Promtail
*   **Cơ chế Promtail:** Kết nối trực tiếp Unix Socket `/var/run/docker.sock`, tự động phát hiện container mỗi 5 giây, bóc tách nhãn `container`, `service`, `logstream` và đẩy về Loki `:3100`.
*   **Ưu điểm của Loki:** Chỉ đánh chỉ mục nhãn (Metadata Labels) thay vì toàn văn bản như ELK, tiết kiệm bộ nhớ RAM và tối ưu hóa lưu trữ cho môi trường container.

\newpage

<!-- TRANG 15: CHƯƠNG 4 (MINH CHỨNG PROMETHEUS & GRAFANA OVERVIEW) -->
### 4.4. Minh chứng trạng thái Targets và Dashboard tổng quan

```
[HÌNH 4.1: Trạng thái 5 Targets thu thập trên giao diện quản trị Prometheus - Yêu cầu chụp rõ: URL http://localhost:9090/targets hiển thị 5 endpoints (cadvisor, mysql, nginx, node-exporter, prometheus) đều ở trạng thái State: UP với biểu tượng màu xanh lá cây]
```

```
[HÌNH 4.2: Bảng điều khiển tổng quan "Recipe System Overview" trên Grafana - Yêu cầu chụp rõ: URL http://localhost:3000, 4 biểu đồ Memory usage, CPU usage, HTTP requests per second và MySQL queries per second hiển thị các đỉnh dữ liệu đo đạc thực tế]
```

\newpage

<!-- TRANG 16: CHƯƠNG 4 (DASHBOARD TRUY VẤN - GAUGE & TIMESERIES) -->
### 4.5. Thiết kế Dashboard "TRUY VẤN" trên Grafana
Dashboard "TRUY VẤN" được thiết kế chuyên biệt cho việc kiểm tra tải với các đồng hồ đo áp lực dạng tròn (Gauge Panels) kết hợp biểu đồ chuỗi thời gian:
*   Đồng hồ đo phần trăm CPU máy chủ tức thời.
*   Đồng hồ đo tốc độ truy vấn MySQL QPS tức thời.
*   Biểu đồ đường theo dõi lưu lượng yêu cầu Nginx phản hồi các đợt tải mô phỏng từ kịch bản `generate_traffic.ps1`.

```
[HÌNH 4.3: Bảng điều khiển "TRUY VẤN" trên Grafana hiển thị các Gauge Panels - Yêu cầu chụp rõ: Các đồng hồ đo hiển thị tỷ lệ phần trăm CPU hệ thống (ví dụ: 4.02%), tốc độ truy vấn MySQL (1.00 queries/sec) và biểu đồ Nginx Request Rate]
```

\newpage

<!-- TRANG 17: CHƯƠNG 4 (PHÂN TÍCH 4 CÂU TRUY VẤN LOGQL) -->
### 4.6. Phân tích 4 câu truy vấn LogQL thực tế trên Grafana Explore
1.  **Truy vấn 1: Lấy toàn bộ luồng log nghiệp vụ của ứng dụng Web PHP-FPM:**
    ```logql
    {container="recipe_web"}
    ```
    *Ý nghĩa:* Theo dõi các yêu cầu biên dịch trang web (`GET /index.php 200`, `GET /recipe.php 200`).
2.  **Truy vấn 2: Lọc các truy vấn HTTP GET trên Web Server Nginx:**
    ```logql
    {container="recipe_nginx"} |= "GET"
    ```
    *Ý nghĩa:* Loại bỏ các log khác, chỉ lọc các dòng log có phương thức GET đi qua Reverse Proxy Nginx.
3.  **Truy vấn 3: Quét tìm lỗi (error/ERROR) trên toàn bộ 12 Containers đồng thời:**
    ```logql
    {container=~"recipe_.*"} |~ "(?i)error"
    ```
    *Ý nghĩa:* Dùng regex `=~ "recipe_.*"` quét 12 containers và lọc từ khóa không phân biệt hoa thường để phát hiện sự cố kịp thời.
4.  **Truy vấn 4: Chuyển đổi Log thành chỉ số đo lường tốc độ (Metric Query):**
    ```logql
    rate({container="recipe_nginx"}[1m])
    ```
    *Ý nghĩa:* Đếm số lượng dòng log/yêu cầu mỗi giây của Nginx trong vòng 1 phút và hiển thị dưới dạng biểu đồ đường (Graph View).

\newpage

<!-- TRANG 18: CHƯƠNG 4 (MINH CHỨNG LOGQL) -->
### 4.7. Minh chứng thực thi 4 câu truy vấn LogQL trên Grafana Explore

```
[HÌNH 4.4: Kết quả thực thi LogQL Truy vấn 1 & Truy vấn 2 trên Grafana Explore - Yêu cầu chụp rõ: Truy vấn {container="recipe_web"} hiển thị log PHP và truy vấn {container="recipe_nginx"} |= "GET" hiển thị log Access Nginx]
```

```
[HÌNH 4.5: Kết quả thực thi LogQL Truy vấn 3 & Truy vấn 4 trên Grafana Explore - Yêu cầu chụp rõ: Truy vấn {container=~"recipe_.*"} |~ "(?i)error" bôi đỏ từ khóa lỗi và biểu đồ Graph của truy vấn rate({container="recipe_nginx"}[1m])]
```

\newpage

<!-- TRANG 19: CHƯƠNG 5 (GITHUB & QUY TRÌNH VẬN HÀNH) -->
# CHƯƠNG 5: QUẢN LÝ MÃ NGUỒN GITHUB, VẬN HÀNH & KHẮC PHỤC SỰ CỐ

### 5.1. Cấu trúc Repository trên GitHub và 3 Mốc Commit
Kho lưu trữ mã nguồn dự án: [https://github.com/DTC245200715/recipe-devops](https://github.com/DTC245200715/recipe-devops).  
Lịch sử Git Commit phản ánh chính xác 3 mốc phát triển theo yêu cầu đề bài:
*   **Commit 1 (`829f1c2`):** Cấu hình Nginx Reverse Proxy, HTTPS tự ký và các Security Headers.
*   **Commit 2 (`006db22`):** Tích hợp Prometheus + Grafana giám sát Container, Web server và Database qua 4 Exporters.
*   **Commit 3 (`6c3ae20` & `0a2de0a`):** Triển khai hệ thống log tập trung Loki + Promtail, truy vấn bằng LogQL và hoàn thiện tài liệu vận hành.

```
[HÌNH 5.1: Minh chứng lịch sử cam kết mã nguồn trên GitHub Repository - Yêu cầu chụp rõ: URL https://github.com/DTC245200715/recipe-devops/commits/main hiển thị danh sách các commit rõ ràng, có đầy đủ mã hash và nội dung chuẩn hóa]
```

### 5.2. Hướng dẫn quy trình vận hành hệ thống
1.  **Thiết lập cấu hình môi trường:** `cp .env.example .env` (thiết lập mật khẩu).
2.  **Kiểm tra cú pháp cấu hình:** `docker compose config --quiet` (xác thực tệp YAML).
3.  **Khởi chạy toàn bộ hệ thống:** `docker compose up -d --build` (chờ 30-60s để MySQL healthy).
4.  **Kiểm tra trạng thái dịch vụ:** `docker compose ps` (xác nhận 12 containers đều đang chạy).
5.  **Mô phỏng lưu lượng truy cập:** `powershell -ExecutionPolicy Bypass -File scripts\generate_traffic.ps1`.
6.  **Dừng hệ thống an toàn:** `docker compose down` (bảo toàn dữ liệu trong các Named Volumes).

\newpage

<!-- TRANG 20: CHƯƠNG 5 (TROUBLESHOOTING) & KẾT LUẬN -->
### 5.3. Cẩm nang chẩn đoán và khắc phục sự cố (Troubleshooting Guide)
| Hiện tượng sự cố | Nguyên nhân kỹ thuật | Biện pháp chẩn đoán & Khắc phục |
|:---|:---|:---|
| **Mã lỗi `502 Bad Gateway`** | PHP-FPM bị treo, chết container hoặc Nginx không kết nối được `web:9000`. | Kiểm tra: `docker compose ps web` và `docker compose logs --tail=50 web`. Khởi động lại: `docker compose restart web`. |
| **Lỗi kết nối CSDL MySQL** | MySQL đang khởi tạo (`mysqld init`), sai thông tin mật khẩu hoặc lỗi mạng. | Kiểm tra trạng thái: `docker compose logs db`. Xác nhận container đạt `healthy`. Kiểm tra biến `DB_HOST`, `DB_USER` trong `.env`. |
| **Prometheus báo Target `DOWN`** | MySQL Exporter sai mật khẩu tài khoản `exporter` trong `.my.cnf`. | Kiểm tra: `docker compose logs mysql-exporter`. Xác minh quyền: `SHOW GRANTS FOR 'exporter'@'%';`. |
| **Grafana không nhận Log** | Promtail không có quyền đọc `/var/run/docker.sock` hoặc sai URL đẩy log. | Kiểm tra: `docker compose logs promtail`. Đảm bảo `clients.url` trỏ đúng về `http://loki:3100/loki/api/v1/push`. |

---

# KẾT LUẬN & HƯỚNG PHÁT TRIỂN ĐỀ TÀI

### 1. Tổng kết kết quả đạt được
Đề tài **"Đề 29: Triển khai website công thức nấu ăn bằng Docker, tích hợp giám sát và thu thập log"** đã hoàn thành xuất sắc 100% mục tiêu:
*   [x] Đóng gói và vận hành ổn định 12 containers bằng Docker Compose, phân vùng mạng và lưu trữ Volume bền vững.
*   [x] Triển khai website PHP 8.2-FPM an toàn với PDO Prepared Statements, nạp sẵn 12 công thức nấu ăn và tích hợp phpMyAdmin.
*   [x] Cấu hình Nginx Reverse Proxy chuyển hướng HTTP 301 sang HTTPS, cấp chứng chỉ SSL và 4 Security Headers.
*   [x] Gia cố bảo mật hệ thống toàn diện: user non-root `appuser`, mạng backend `internal: true`, chặn endpoint `/nginx_status`.
*   [x] Xây dựng hệ thống giám sát Prometheus cào 5 targets và Dashboard Grafana trực quan hóa thời gian thực.
*   [x] Thu thập log tự động qua Docker Socket với Promtail, lưu trữ trên Loki và kiểm thử thành công 4 câu truy vấn LogQL.
*   [x] Quản lý mã nguồn minh bạch trên GitHub với 3 mốc commit chuẩn hóa và tài liệu vận hành chi tiết.

### 2. Hướng phát triển trong tương lai
Chuyển đổi kiến trúc sang Kubernetes (K8s) với cơ chế tự động mở rộng Pods (HPA); tích hợp tự động cấp chứng chỉ Let's Encrypt qua Certbot; cấu hình Prometheus Alertmanager gửi cảnh báo qua Telegram/Slack; xây dựng đường ống CI/CD tự động kiểm thử và triển khai bằng GitHub Actions.

<br>

**XÁC NHẬN CỦA GIẢNG VIÊN** &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; **SINH VIÊN THỰC HIỆN**  
*(Ký và ghi rõ họ tên)* &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; *(Ký và ghi rõ họ tên)*  
<br><br><br>
&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; **Triệu Đức Thắng**
