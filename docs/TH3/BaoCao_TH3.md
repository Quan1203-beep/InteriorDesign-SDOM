# Báo cáo TH3 — Logging, backup, incident và CR-01

## Mục tiêu
Bổ sung logging/health monitoring, backup/restore, xử lý INC-01, rollback và tiếp tục CR-01.

## Health và logging
GET /health chạy SELECT 1, trả 200 khi DB hoạt động và 503 khi DB không kết nối. Khi triển khai cần log có cấu trúc; tuyệt đối không log secret, thông tin thẻ hoặc dữ liệu cá nhân không cần thiết.

## Backup/restore
Runbook ở docs/runbooks/backup-restore.md. Backup chỉ lưu ngoài Git; restore vào database test riêng. Ghi thời gian đo thật để tính RPO/RTO; không điền số liệu giả.

## INC-01 và rollback
Diễn tập trên môi trường local/test bằng cách dừng MySQL test hoặc dùng cấu hình test sai. Ghi timestamp, HTTP status, log, phạm vi ảnh hưởng; khôi phục DB/config, chạy health và smoke test. Rollback về phiên bản ổn định đã xác minh, kiểm tra tương thích schema.

## CR-01
Đề xuất gợi ý phong cách nội thất từ mô tả không gian. Bổ sung acceptance criteria và tests trước triển khai; ưu tiên mock cục bộ để không gửi dữ liệu khách hàng ra ngoài. Tạo PR draft nếu chưa đủ điều kiện merge.

## Minh chứng
Lưu log/health, backup-restore DB test, RPO/RTO, incident timeline, rollback, Issue/PR, tag/release và CR-01 vào docs/TH3/evidence/.
