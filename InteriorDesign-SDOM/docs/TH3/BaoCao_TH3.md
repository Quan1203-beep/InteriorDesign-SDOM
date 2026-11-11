# BÁO CÁO THỰC HÀNH SỐ 03
## Dự án: Website thiết kế nội thất — Interior Studio

### 1. Mục tiêu
Bổ sung logging và health check; diễn tập backup/restore; xử lý incident INC-01; rollback phiên bản; tiếp tục thực hiện CR-01 theo quy trình kiểm soát thay đổi.

### 2. Nhiệm vụ 1 — Logging và trạng thái vận hành
Dùng Monolog ghi log ra `logs/app.log`, mức log cấu hình qua `APP_LOG_LEVEL`. Mức Debug dùng phát triển; Information ghi sự kiện nghiệp vụ; Warning ghi tình huống bất thường có thể phục hồi; Error ghi request thất bại; Critical dành cho lỗi nghiêm trọng làm hệ thống ngừng hoạt động. Không ghi password, token, API key, thông tin thanh toán hoặc dữ liệu cá nhân không cần thiết. Thư mục logs bị ignore khỏi Git. Endpoint `/health` kiểm tra truy vấn `SELECT 1`; DB hoạt động trả HTTP 200 JSON `status=ok`, lỗi trả HTTP 503.

### 3. Nhiệm vụ 2 — Backup và restore
Dùng `mysqldump --single-transaction` để backup MySQL; file lưu trong `backups/` và không commit. Runbook tại `docs/runbooks/backup-restore.md`; script mẫu `scripts/backup.sh`. Thử restore vào database riêng `interior_design_restore`, không ghi đè DB thật. Kiểm tra bảng và dữ liệu mẫu sau restore. Mục tiêu đề xuất cho bài tập: RPO 24 giờ, RTO 2 giờ; phải ghi thời gian đo thực tế, không xem đây là kết quả đã đạt.

### 4. Nhiệm vụ 3 — Incident INC-01 và rollback
**Kịch bản an toàn:** trong môi trường local/test, dừng MySQL test hoặc dùng cấu hình DB test sai có kiểm soát. Gọi `/health`, ghi nhận HTTP 503 và log. Không cố tình gây sự cố trên production.

**Phản ứng:** phát hiện → xác nhận phạm vi → lưu bằng chứng → xác định nguyên nhân → khôi phục MySQL hoặc sửa biến môi trường → health check → smoke test → thông báo resolved.

**5 Whys:** website 503 vì PDO không kết nối; nguyên nhân trực tiếp là MySQL test dừng hoặc cấu hình kết nối sai; giám sát chưa cảnh báo tự động; quy trình vận hành cần bổ sung cảnh báo và kiểm tra cấu hình trước deploy.

**Rollback:** nếu hotfix làm lỗi chức năng, quay về tag ổn định `v1.0.0` hoặc deploy lại artifact tương ứng; kiểm tra schema compatibility trước khi rollback. Sau sửa lỗi, tạo branch `hotfix/INC-01-db-connection`, thêm regression test, PR và CI xanh; phát hành `v1.0.1` nếu có thay đổi đã được kiểm thử. Điền Incident Report với timeline và MTTD/MTTR đo thực tế.

### 5. Nhiệm vụ 4 — Tiếp tục CR-01
Tiếp tục từ Impact Analysis đã duyệt. Tạo branch `cr/CR-01-style-suggestion`; bổ sung automated tests trước (TDD): test dữ liệu hợp lệ, input rỗng/quá dài, AI timeout, feature flag OFF. Triển khai service độc lập. Với phạm vi bài lab, có thể dùng rule-based mock thay vì gửi dữ liệu khách hàng đến AI thật. Tạo PR draft/WIP để review, không merge nếu chưa đủ tiêu chí; ghi rõ trạng thái trong báo cáo. Nếu được duyệt và hoàn thành, merge theo quy trình, cập nhật tài liệu và kiểm thử.

### 6. Thanh toán online
Theo tài liệu học phần, tích hợp cổng thanh toán sandbox là yêu cầu bắt buộc chung của dự án ở giai đoạn báo cáo sau; không tính là CR-01. Chưa triển khai trong gói mẫu này. Khi thực hiện cần dùng sandbox, callback signature verification, idempotency, không lưu dữ liệu thẻ và không ghi secret vào log/repository.

### 7. Kết luận và minh chứng
TH3 hoàn thiện khả năng quan sát, backup/restore, quy trình incident, rollback và kiểm soát CR-01. Minh chứng: log và health check; backup file ở máy cá nhân (không commit) cùng ảnh lệnh; restore test; runbook; issue/PR INC-01; Incident Report; tag/release v1.0.1 nếu thực sự phát hành; CR-01 issue, Impact Analysis, test và PR draft/merge; lệnh git log.
