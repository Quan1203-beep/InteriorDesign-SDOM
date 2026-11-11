# Runbook: Backup & Restore MySQL

## Phạm vi và an toàn
Chỉ thực hiện trên database của dự án và dữ liệu thử nghiệm/được phép. Không commit file backup vào Git. Hạn chế quyền truy cập thư mục backup.

## Backup
```bash
mkdir -p backups
mysqldump -h 127.0.0.1 -u root -p --single-transaction --routines --triggers interior_design > backups/interior_design_$(date +%Y%m%d_%H%M%S).sql
```

## Kiểm tra backup
```bash
test -s backups/interior_design_YYYYMMDD_HHMMSS.sql
head -n 5 backups/interior_design_YYYYMMDD_HHMMSS.sql
```
Kiểm tra bằng cách restore vào database thử nghiệm riêng, không ghi đè production.

## Restore vào database thử nghiệm
```bash
mysql -u root -p -e "CREATE DATABASE interior_design_restore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p interior_design_restore < backups/interior_design_YYYYMMDD_HHMMSS.sql
mysql -u root -p -e "SHOW TABLES FROM interior_design_restore;"
```

## Sau phục hồi
- Kiểm tra số bảng và số bản ghi mẫu.
- Chạy `docs/testing/smoke-test.md`.
- Ghi thời điểm bắt đầu/kết thúc, dung lượng backup, kết quả và người thực hiện.

## Mục tiêu đề xuất
RPO mục tiêu cho bài lab: 24 giờ; RTO mục tiêu: 2 giờ. Đây là mục tiêu bài tập đề xuất, cần đo thực tế để xác nhận.
