# Báo cáo TH2 — CI, cấu hình và release

## Mục tiêu
Chuẩn hóa Issue/PR, CI, secrets/configuration, dependency, test case, release v1.0.0 và chuẩn bị CR-01.

## Issue/PR
Repository có form Task, Bug, Change Request và pull request template. Tạo milestone TH2, gắn các issue/PR thực tế và lưu liên kết minh chứng.

## CI
.github/workflows/php.yml chạy checkout, setup PHP 8.2, composer validate --strict, composer install và composer test trên push/PR. Chỉ kết luận CI đạt khi Actions hiển thị xanh.

## Cấu hình và dependency
.env.example là mẫu; .env, vendor, logs và backups không commit. Không lưu mật khẩu/token/dữ liệu khách hàng. Kiểm kê bằng composer show --direct và composer audit; ghi kết quả thực tế.

## Test cases và smoke test
Xem docs/testing/test-cases.md và docs/testing/smoke-test.md. Kiểm tra trang chủ, health khi DB up/down, form hợp lệ và email sai.

## Release và CR-01
Sau CI xanh và smoke test đạt, tạo tag v1.0.0 và GitHub Release có artifact. CR-01 đề xuất gợi ý phong cách nội thất từ mô tả không gian; Impact Analysis ở docs/TH2/impact-analysis.md. Không ghi release/issue/PR là hoàn thành nếu chưa có URL và bằng chứng.

## Minh chứng
Lưu Actions, composer, audit, smoke test, dependency inventory, tag/Release, Issue/PR và Impact Analysis vào docs/TH2/evidence/.
