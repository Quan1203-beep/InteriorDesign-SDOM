# Báo cáo TH1 — Repository, khảo sát và baseline

## Mục tiêu
Thiết lập GitHub repository, Git workflow và course-baseline; khảo sát kiến trúc; đánh giá maintainability; xây dựng unit-test baseline.

## Hệ thống
Website thiết kế nội thất dùng HTML/CSS/JavaScript, PHP 8.2 và MySQL qua PDO. Presentation ở public/, nghiệp vụ ở src/Domain, schema ở database/, unit test ở tests/.

## Git workflow
Dùng main làm nhánh ổn định; branch theo docs/*, feat/*, test/*, fix/*. Commit theo Conventional Commits; mỗi thay đổi gắn Issue, tạo PR, review và merge sau khi kiểm tra. Tạo tag sau khi baseline thực sự được xác nhận: git tag -a course-baseline -m "Course baseline" && git push origin course-baseline.

## Khảo sát luồng
GET / hiển thị trang; POST /consultation nhận dữ liệu, validate, dùng PDO prepared statement lưu DB; GET /health chạy SELECT 1. Luồng: Browser → public/index.php → Validator → PDO/MySQL → HTTP.

## Maintainability
Các rủi ro cần kiểm tra: validation trùng lặp, SQL injection, output chưa escape, cấu hình hard-code, thiếu regression tests. Chạy SonarQube/SonarLint và ghi đúng finding thực tế vào bảng sau: Finding | file:line | severity | cách sửa. Không tự nhận số finding khi chưa chạy công cụ.

## Unit test baseline
ConsultationValidatorTest kiểm tra dữ liệu hợp lệ, tên rỗng/email sai và phone sai. Chạy composer install, composer test; bổ sung số pass/fail và coverage thực tế sau khi chạy.

## Minh chứng
Bổ sung ảnh repo, milestone/issue/PR, tag course-baseline, kết quả phân tích, PHPUnit/coverage và git log vào docs/TH1/evidence/.
