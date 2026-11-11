# Runbook MySQL backup/restore

Chỉ chạy trên database test hoặc môi trường được cho phép. Không commit file dump.

## Backup
```bash
mkdir -p backups
mysqldump --single-transaction -h 127.0.0.1 -u root -p interior_design > backups/interior_design_$(date +%Y%m%d_%H%M%S).sql
```

## Restore test
```sql
CREATE DATABASE interior_design_restore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
```bash
mysql -h 127.0.0.1 -u root -p interior_design_restore < backups/<backup-file>.sql
```
Nếu dump có CREATE DATABASE/USE, tạo dump tương thích DB test hoặc dùng MySQL cô lập. Kiểm tra bảng, số bản ghi và ghi lại thời gian để tính RPO/RTO.
