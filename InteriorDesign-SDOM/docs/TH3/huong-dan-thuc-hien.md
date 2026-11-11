# Hướng dẫn thao tác TH3

1. Tạo branch `feat/22-logging`; cấu hình Monolog, log rotation ở production và `/health`; merge sau CI.
2. Tạo branch `chore/24-backup-restore`; hoàn thiện runbook, chạy backup và restore database test; chụp minh chứng, không commit backup.
3. Diễn tập INC-01 trong local/test: dừng MySQL test; gọi `/health`; ghi timeline; bật MySQL lại; smoke test.
4. Tạo branch hotfix, regression test, PR, merge khi CI xanh; chỉ phát hành `v1.0.1` nếu thực tế đã sửa và kiểm thử thành công.
5. Với CR-01, tạo test trước, triển khai service, PR draft để review. Không merge nếu tiêu chí chấp nhận chưa đạt.
6. Ghi Incident Report, Impact Analysis, kết quả và lệnh git log.

```bash
git log --first-parent --oneline course-baseline..main
git log --first-parent --no-merges --oneline course-baseline..main
```
