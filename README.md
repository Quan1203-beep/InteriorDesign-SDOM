# InteriorDesign-SDOM
Website giới thiệu dịch vụ thiết kế nội thất và tiếp nhận yêu cầu tư vấn.

## Công nghệ
HTML5, CSS3, JavaScript, PHP 8.2+, MySQL 8, PDO, Composer, PHPUnit.

## Cấu trúc
- `public/`: giao diện và front controller.
- `src/`: domain, cấu hình và truy cập dữ liệu.
- `database/`: schema khởi tạo MySQL.
- `tests/`: unit tests.
- `docs/TH1`, `docs/TH2`, `docs/TH3`: báo cáo và tài liệu thực hành.
- `.github/workflows/`: CI trên GitHub Actions.

## Chạy local (PHP built-in server)
1. Cài PHP 8.2+, Composer và MySQL 8.
2. Tạo database bằng cách import `database/schema.sql` vào MySQL.
3. Sao chép `.env.example` thành `.env`, cập nhật thông tin DB.
4. Chạy `composer install`.
5. Chạy `php -S 127.0.0.1:8000 -t public`.
6. Mở http://127.0.0.1:8000 và kiểm tra http://127.0.0.1:8000/health.

## Kiểm tra
```bash
composer validate --strict
composer install
composer test
```

## Bảo mật
Không commit `.env`, credentials, token, backup hoặc dữ liệu khách hàng. Chỉ dùng dữ liệu giả lập khi kiểm thử.

## Tình trạng nộp bài
Các tài liệu và mã nguồn TH1–TH3 được lưu trong repository. Những minh chứng phụ thuộc thao tác thực tế trên GitHub/máy cá nhân (Issue, PR, tag, Release, ảnh chạy test, kết quả SonarQube, thời gian backup/restore và incident) cần được bổ sung tại `docs/*/evidence/`; báo cáo không tự nhận các thao tác đó đã hoàn tất khi chưa có bằng chứng.
