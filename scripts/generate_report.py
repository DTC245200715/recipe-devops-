from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak
from reportlab.lib.styles import ParagraphStyle
from reportlab.graphics.shapes import Drawing, Rect, String, Line
from reportlab.lib.colors import HexColor

pdf_path = r'D:\recipe-devops\BAO CAO.pdf'

heading = ParagraphStyle(
    'heading',
    fontName='Helvetica-Bold',
    fontSize=16,
    leading=22,
    textColor=HexColor('#1F3A5F'),
    spaceAfter=12,
    spaceBefore=10,
)
body = ParagraphStyle(
    'body',
    fontName='Helvetica',
    fontSize=11,
    leading=16,
    spaceAfter=8,
)

chapters = [
    ('Lời mở đầu', [
        'Hệ thống website chia sẻ công thức nấu ăn được triển khai theo mô hình Docker Compose với mục tiêu làm rõ quy trình đóng gói, triển khai, giám sát và vận hành hệ thống phần mềm.',
        'Bài báo cáo mô tả toàn bộ quy trình triển khai, cấu hình Nginx reverse proxy, tích hợp cơ sở dữ liệu MySQL, hệ thống monitoring Prometheus/Grafana và log tập trung Loki/Promtail.',
        'Mục tiêu của đề tài là thể hiện khả năng triển khai và quản trị hệ thống phần mềm theo hướng thực tế, có tính khả dụng cao và dễ bảo trì.'
    ]),
    ('Chương 1: Tổng quan dự án', [
        'Website chia sẻ công thức nấu ăn là ứng dụng web mô phỏng một nền tảng chia sẻ món ăn, gồm danh mục, công thức, nguyên liệu và hình ảnh minh họa.',
        'Người dùng có thể duyệt danh sách công thức, tìm kiếm theo tên hoặc danh mục, xem thông tin chi tiết một món ăn và nấu theo hướng dẫn ở từng bước.',
        'Hệ thống có tính năng quản trị nội dung bằng quyền admin để thêm, sửa, xoá trong cơ sở dữ liệu và upload ảnh minh họa.',
        'Từ góc nhìn DevOps, dự án cần được triển khai đúng kỹ thuật với Docker, reverse proxy, monitoring và logging tập trung.'
    ]),
    ('Chương 2: Kiến trúc hệ thống', [
        'Kiến trúc hệ thống được thiết kế theo mô hình 3 lớp: Frontend, Backend và Data layer.',
        'Lớp Frontend: Nginx đóng vai trò điểm vào, xử lý HTTPS và phản hồi request tới ứng dụng web.',
        'Lớp Backend: PHP-FPM chạy mã PHP, truy vấn dữ liệu từ MySQL và phục vụ response cho người dùng.',
        'Lớp Data: MySQL lưu trữ dữ liệu như công thức, nguyên liệu, danh mục và người dùng. phpMyAdmin hỗ trợ quản trị database theo giao diện web.',
        'Ngoài ra, hệ thống còn tích hợp monitoring trên Prometheus/Grafana và logging tập trung trên Loki/Promtail.'
    ]),
    ('Chương 3: Cấu trúc Docker Compose', [
        'Docker Compose định nghĩa các dịch vụ chính gồm web, nginx, db, phpmyadmin, prometheus, grafana, loki, promtail, cadvisor, node-exporter, mysqld-exporter và nginx-exporter.',
        'Mỗi service được cấu hình với mạng riêng: frontend và backend. backend là mạng nội bộ, tăng tính an toàn và cô lập service.',
        'Thư mục lưu trữ dữ liệu MySQL, ứng dụng và monitoring được mount bằng named volumes, giúp dữ liệu không mất khi container restart.',
        'Healthcheck được thêm vào web và database để đảm bảo khởi động và tương tác đúng thứ tự. Đây là kỹ thuật quan trọng khi triển khai nhiều services trong cùng stack.'
    ]),
    ('Chương 4: Cấu hình Nginx Reverse Proxy và HTTPS', [
        'Nginx đóng vai trò làm reverse proxy giữa internet và PHP-FPM. Nó chặn trực tiếp truy cập vào app, tập trung xử lý SSL, routing và security headers.',
        'Cấu hình HTTP redirect sang HTTPS giúp người dùng truy cập qua giao diện bảo mật chuẩn hơn.',
        'Certificate tự ký được sinh bằng OpenSSL nếu chưa có file cert, giúp triển khai trên máy local mà không cần mua chứng chỉ thực tế.',
        'Các security headers như X-Frame-Options, X-Content-Type-Options, Referrer-Policy và CSP được cấu hình để giảm các nguy cơ tấn công phổ biến.'
    ]),
    ('Chương 5: Hardening hệ thống', [
        'PHP container được chạy dưới user non-root, giảm quyền tiếp cận hệ thống và hạn chế rủi ro nếu dịch vụ bị xâm nhập.',
        'Backend network được đặt là internal cho phép chỉ các service cần thiết giao tiếp với nhau, giảm bề mặt tấn công.',
        'Mật khẩu và credential được đặt theo nguyên tắc mạnh hơn, đồng thời database và monitor service được tách biệt về chức năng và quyền truy cập.',
        'Nginx còn giới hạn truy cập endpoint /nginx_status chỉ cho các mạng nội bộ, tránh lộ thông tin hệ thống ra internet.',
        'Đây là các biện pháp hardening nền tảng theo chuẩn thực tế trong môi trường production.'
    ]),
    ('Chương 6: Monitoring với Prometheus và Grafana', [
        'Prometheus thu thập các metric từ node-exporter, cadvisor, mysqld-exporter, nginx-exporter và dịch vụ Prometheus bản thân.',
        'Grafana được tích hợp datasource Prometheus để tổng hợp và trực quan hóa tài nguyên hệ thống như CPU, bộ nhớ, request HTTP và tình trạng dịch vụ.',
        'Dashboard mẫu recipe-overview cho thấy các panel theo dõi hiệu năng cơ bản của hệ thống và chính là định dạng trực quan giúp quản trị viên theo dõi trạng thái ứng dụng.',
        'Việc có dashboard giám sát cho phép phát hiện sớm quá tải, downtime hoặc lỗi trong quá trình vận hành.'
    ]),
    ('Chương 7: Logging tập trung với Loki và Promtail', [
        'Loki là hệ thống lưu trữ log tập trung với cấu trúc TSDB, phù hợp cho môi trường container.',
        'Promtail lắng nghe log từ Docker socket và đẩy chúng lên Loki để thực hiện truy vấn log tập trung.',
        'Ví dụ truy vấn LogQL được hỗ trợ: {container=\"recipe_web\"} |= \"User login\"; {container=~\"recipe_.*\"} |= \"ERROR\"; {container=\"recipe_nginx\"} |= \"401\" or \"403\".',
        'Một hệ thống log tập trung giúp quản trị viên dễ dàng chẩn đoán lỗi, theo dõi hoạt động người dùng và phát hiện các vấn đề trong từng service.'
    ]),
    ('Chương 8: Cơ sở dữ liệu và ứng dụng', [
        'Database MySQL 8.0 được khởi tạo dựa trên init.sql với các bảng categories, recipes, ingredients, recipe_ingredients và users.',
        'Dữ liệu mẫu mô phỏng thực tế các món ăn Việt Nam như Phở Bò, Bún Chả, Canh Chua, Bánh Xèo, Trà Sữa Trân Châu và nhiều món khác.',
        'Ứng dụng web có các trang chính: index.php, recipe.php, login.php, admin.php, logout.php và health.php.',
        'Hệ thống hỗ trợ tìm kiếm theo tên, lọc theo danh mục, phân trang và hiển thị chi tiết công thức với nguyên liệu và cách làm từng bước.',
        'Việc dùng PDO + prepared statement giúp tăng độ an toàn cho ứng dụng.'
    ]),
    ('Chương 9: Kết quả đạt được', [
        'Dự án đã triển khai thành công stack dịch vụ bằng Docker Compose với cấu trúc rõ ràng, dễ triển khai trên máy local và môi trường phát triển.',
        'Nginx đã được cấu hình với reverse proxy và HTTPS tự ký, kết hợp security headers để nâng cao bảo mật.',
        'Prometheus + Grafana đã tích hợp thành công để giám sát hệ thống và phân tích tài nguyên hiện tại.',
        'Loki + Promtail đã được tích hợp để thu thập log và hỗ trợ truy vấn LogQL hiệu quả.',
        'Application web đã hoạt động đúng về chức năng với triết lý MVC đơn giản, dữ liệu mẫu chuẩn và quản trị nội dung dễ dàng.'
    ]),
    ('Chương 10: Kết luận', [
        'Đề tài đã hoàn thành được các yêu cầu chính của thực hành: quét triển khai, database, reverse proxy, monitoring, logging và hardening.',
        'Tác động lớn nhất của hệ thống là tăng tính sẵn sàng và dễ quản trị, giúp người vận hành có thể theo dõi trạng thái hệ thống nhanh hơn và phát hiện sự cố kịp thời.',
        'Nền tảng này còn có thể phát triển tiếp theo như triển khai trên cloud, sử dụng Kubernetes, thêm alerting và quy trình CI/CD.',
        'Với repository có 3 commit rõ ràng, README hướng dẫn chạy và báo cáo đầy đủ, dự án đáp ứng đúng mục tiêu của đề tài.'
    ]),
]

story = []
# title page
story.append(Paragraph('BÁO CÁO', ParagraphStyle('title', fontName='Helvetica-Bold', fontSize=24, leading=28, textColor=HexColor('#1F3A5F'), alignment=1, spaceAfter=8)))
story.append(Paragraph('Triển khai và Quản trị Hệ thống Phần mềm', ParagraphStyle('subtitle', fontName='Helvetica-Bold', fontSize=16, leading=20, textColor=HexColor('#FF7F50'), alignment=1, spaceAfter=8)))
story.append(Paragraph('Đề tài: Website chia sẻ công thức nấu ăn', ParagraphStyle('meta', fontName='Helvetica', fontSize=12, leading=18, alignment=1, spaceAfter=4)))
story.append(Paragraph('Sinh viên: Thang', ParagraphStyle('meta', fontName='Helvetica', fontSize=12, leading=18, alignment=1, spaceAfter=4)))
story.append(Paragraph('Mã số: DTC245200715', ParagraphStyle('meta', fontName='Helvetica', fontSize=12, leading=18, alignment=1, spaceAfter=4)))
story.append(PageBreak())

for chapter, content in chapters:
    story.append(Paragraph(chapter, heading))
    for para in content:
        story.append(Paragraph(para, body))
    story.append(Spacer(1, 10))
    story.append(PageBreak())

# Final diagram page
story.append(Paragraph('Hình minh họa: Kiến trúc hệ thống triển khai', heading))
drawing = Drawing(530, 220)
boxes = [
    ('User', 30, 120, 110, 40),
    ('Nginx', 170, 120, 110, 40),
    ('PHP-FPM', 315, 120, 110, 40),
    ('MySQL', 255, 40, 110, 40),
    ('Prometheus', 30, 220, 110, 40),
    ('Grafana', 200, 220, 110, 40),
    ('Loki', 370, 220, 110, 40),
]
for label, x, y, w, h in boxes:
    drawing.add(Rect(x, y, w, h, fillColor=HexColor('#EAF2FF'), strokeColor=HexColor('#1F3A5F'), strokeWidth=1.5))
    drawing.add(String(x + w / 2, y + h / 2, label, textAnchor='middle', fontSize=10, fillColor=HexColor('#1F3A5F')))
for a, b, c, d in [
    (140, 140, 170, 140),
    (280, 140, 315, 140),
    (310, 80, 310, 120),
    (140, 260, 200, 260),
    (310, 260, 370, 260),
    (255, 140, 255, 80),
]:
    drawing.add(Line(a, b, c, d, strokeColor=HexColor('#FF7F50'), strokeWidth=2))
story.append(drawing)
story.append(Spacer(1, 18))
story.append(Paragraph('Hệ thống bao gồm người dùng truy cập qua HTTPS/Nginx, ứng dụng xử lý yêu cầu qua PHP-FPM, dữ liệu lưu trong MySQL, đồng thời có dịch vụ giám sát và log tập trung qua Prometheus/Grafana và Loki/Promtail.', body))

# Finally write document

doc = SimpleDocTemplate(pdf_path, pagesize=A4, rightMargin=18 * mm, leftMargin=18 * mm, topMargin=15 * mm, bottomMargin=15 * mm)
doc.build(story)
print(f'Created {pdf_path}')
