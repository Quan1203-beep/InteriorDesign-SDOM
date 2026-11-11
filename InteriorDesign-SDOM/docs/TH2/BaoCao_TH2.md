# BÁO CÁO THỰC HÀNH SỐ 02
## Dự án: Website thiết kế nội thất — Interior Studio

### 1. Mục tiêu
Chuẩn hóa Issue/PR; thiết lập CI tự động; tách cấu hình và secrets; kiểm kê dependency; xây dựng test case và automated test; phát hành v1.0.0; chuẩn bị CR-01.

### 2. Nhiệm vụ 1 — Issue Template và Pull Request Template
Tạo ba issue form: Task, Bug report, Change Request tại `.github/ISSUE_TEMPLATE/`; tạo `.github/pull_request_template.md`. Form yêu cầu mô tả, bước tái hiện, kết quả mong đợi/thực tế, tiêu chí chấp nhận, môi trường và ảnh minh chứng. Gắn labels `task`, `bug`, `enhancement`, `change-request`. Tạo milestone `TH2`.

### 3. Nhiệm vụ 2 — Continuous Integration
Workflow `.github/workflows/ci.yml` chạy khi push vào `main` và khi tạo Pull Request vào `main`. Runner Ubuntu; cài PHP 8.2, extensions PDO MySQL/mbstring; cài Composer dependencies; validate composer; chạy PHPUnit; `composer audit`. Job thất bại thì không được merge. Nếu dự án bật branch protection, yêu cầu status check `test` thành công trước khi merge.

### 4. Nhiệm vụ 3 — Configuration, secrets và dependency
**4.1. Cấu hình.** Các biến `APP_ENV`, `APP_DEBUG`, `APP_LOG_LEVEL`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` được đọc từ môi trường. `.env` nằm trong `.gitignore`; chỉ commit `.env.example` với giá trị mẫu không nhạy cảm. Dev/Test/Prod dùng credentials và DB riêng.

**4.2. Kiểm tra lịch sử.** Rà soát `git log -p` và tìm chuỗi nhạy cảm. Nếu lỡ commit secret: thu hồi/rotate ngay, xóa khỏi lịch sử theo quy trình; xóa file ở commit mới không làm secret cũ an toàn.

**4.3. GitHub Actions secrets.** Chỉ lưu token/credential cần thiết trong Settings → Secrets and variables → Actions; tham chiếu `${{ secrets.NAME }}`. Workflow mẫu hiện chỉ chạy test local service và không cần production secret.

**4.4. Dependency.** PHP dùng Composer. Kiểm kê bằng:
```bash
composer show --direct
composer show --tree
composer audit
```
Ghi tên, phiên bản, mục đích, trực tiếp/gián tiếp, rủi ro và kế hoạch cập nhật. Không tự ý dùng `composer update` trên main; cập nhật trên branch riêng và chạy test.

### 5. Nhiệm vụ 4 — Test case và automated test
Test case nằm tại `docs/testing/test-cases.md`. Use case chính:
- UC01 xem dự án: chỉ dự án published được hiển thị.
- UC02 gửi tư vấn: dữ liệu hợp lệ được lưu; dữ liệu sai bị từ chối HTTP 422.
- UC03 health check: DB hoạt động trả JSON OK; DB mất kết nối trả 503.
Automated unit tests kiểm tra validator. Integration test cho PDO và trình duyệt có thể bổ sung sau khi có test DB/runner ổn định. Smoke test nằm tại `docs/testing/smoke-test.md`.

### 6. Nhiệm vụ 5 — CHANGELOG và release v1.0.0
Cập nhật `CHANGELOG.md`; merge PR trên main; xác nhận CI xanh; chạy smoke test trên môi trường kiểm thử; tạo tag:
```bash
git switch main
git pull
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```
Tạo GitHub Release từ tag `v1.0.0`, ghi nội dung thay đổi và đính kèm artifact phù hợp. Không phát hành nếu smoke test chưa đạt.

### 7. Nhiệm vụ 6 — CR-01 và Impact Analysis
CR-01 đề xuất: “Bổ sung chức năng gợi ý phong cách nội thất dựa trên mô tả không gian của khách hàng”. Tạo issue dạng Change Request và `docs/TH2/impact-analysis.md`; xác định phạm vi UI, backend, DB, bảo mật, chi phí, hiệu năng, logging, kiểm thử, rollback. Tạo spike branch `spike/CR-01-style-suggestion` để thử nghiệm; không merge spike. Tạo branch `cr/CR-01-style-suggestion` cho triển khai chính thức theo quy trình. Tiêu chí Given/When/Then nằm trong Impact Analysis.

### 8. Kết luận và minh chứng
TH2 bổ sung chuẩn hóa cộng tác, CI, cấu hình an toàn, dependency inventory, test case và release v1.0.0. Minh chứng: 3 issue forms; PR template; tab Actions/Checks; kết quả composer test/audit; `.gitignore` và `.env.example`; dependency list; test cases; CHANGELOG; tag/release; smoke test; Impact Analysis; danh sách PR và lệnh git log.
