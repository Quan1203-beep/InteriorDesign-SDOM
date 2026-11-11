# Interior Design Website — SDOM Lab 1–3

Website giới thiệu dịch vụ thiết kế nội thất, danh mục phong cách, dự án và biểu mẫu yêu cầu tư vấn.
Stack: PHP 8.2+, MySQL 8, HTML/CSS/JavaScript, Composer, PHPUnit.

## Yêu cầu
- PHP >= 8.2, extensions `pdo_mysql`, `mbstring`, `openssl`
- MySQL 8+
- Composer 2
- Git

## Chạy local
1. `composer install`
2. Sao chép `.env.example` thành `.env`, sửa thông tin DB.
3. Tạo database `interior_design` và import `database/schema.sql`.
4. Chạy `php -S 127.0.0.1:8000 -t public`
5. Mở `http://127.0.0.1:8000`

## Kiểm thử
- `composer test`
- `composer test:coverage` (cần Xdebug hoặc PCOV để xuất coverage)
- `composer audit`

## Lưu ý
Không commit `.env`, mật khẩu, token, dữ liệu khách hàng thật hoặc bản backup chứa dữ liệu cá nhân.
Các báo cáo trong `docs/TH1..TH3` là bản soạn theo dự án mẫu. Cập nhật các ô `[ĐIỀN KẾT QUẢ THỰC TẾ]` và ảnh minh chứng sau khi chạy trên máy/GitHub của bạn.
