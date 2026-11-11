# BÁO CÁO THỰC HÀNH SỐ 01
## Dự án: Website thiết kế nội thất — Interior Studio

### 1. Mục tiêu
Thiết lập repository GitHub; tạo course-baseline có thể tái lập; khảo sát cấu trúc và luồng xử lý; đánh giá maintainability; xây dựng baseline unit test và coverage cho nghiệp vụ cốt lõi.

### 2. Mô tả dự án
Website giới thiệu dịch vụ thiết kế nội thất, danh mục dự án theo phong cách, trang thông tin dự án và biểu mẫu tiếp nhận yêu cầu tư vấn. Stack: HTML/CSS/JavaScript, PHP 8.2, MySQL 8. Website dùng PHP xử lý request, PDO truy vấn MySQL, HTML/CSS hiển thị và JavaScript gửi form bất đồng bộ.

### 3. Nhiệm vụ 1 — Repository, Git workflow và course-baseline
**3.1. Repository.** Tạo repository GitHub private tên `interior-design-website`. Thư mục chính: `public/`, `src/`, `database/`, `tests/`, `docs/`, `.github/`. Thêm README, `.gitignore`, `.env.example`; tuyệt đối không commit `.env`, mật khẩu hoặc dữ liệu khách hàng.

**3.2. Git workflow.** Dùng `main` làm nhánh ổn định. Mỗi nhiệm vụ tạo branch `docs/`, `feat/`, `test/`, `chore/` hoặc `fix/`; commit theo dạng `feat(consultation): add request form`; push branch; tạo Pull Request; review; CI (khi có ở TH2) xanh rồi merge. Một PR gắn với issue bằng `Closes #N`.

**3.3. Course baseline.** Kiểm tra cài đặt PHP, Composer, MySQL; chạy website local; import schema; chạy test baseline; lưu ảnh vào `docs/TH1/evidence/`. Sau khi merge các thay đổi nền tảng, tạo tag:
```bash
git tag -a course-baseline -m "Course baseline"
git push origin course-baseline
```
Tag là mốc bất biến để so sánh thay đổi các buổi sau.

**3.4. Milestone/Issues/PR.** Tạo milestone `TH1`; issues đề xuất: #1 cập nhật README; #3 test baseline validator; #5 báo cáo TH1. Branch/PR thực tế cần cập nhật theo số GitHub cấp.

### 4. Nhiệm vụ 2 — Khảo sát và mô tả hệ thống hiện hữu
**4.1. Thành phần.**
- Presentation: `public/index.php`, `public/assets/*`.
- Domain: `src/Domain/ConsultationValidator.php`.
- Repository/Data access: `src/Repository/*`, PDO.
- Config: `.env` được nạp bởi phpdotenv; không đưa file thật vào Git.
- Database: `design_projects`, `consultation_requests`.
- Tests: PHPUnit trong `tests/`.

**4.2. Luồng xem dự án.** Browser GET `/` → PHP nạp cấu hình → PDO kết nối MySQL → ProjectRepository lấy bản ghi `is_published=1` → PHP escape dữ liệu bằng `htmlspecialchars` → trình duyệt render HTML/CSS.

**4.3. Luồng gửi tư vấn.** Người dùng nhập form → JavaScript POST `/consultation` → PHP validate dữ liệu → ConsultationRepository dùng prepared statement INSERT → Monolog ghi event có request ID/bản ghi ID → trả JSON kết quả.

**4.4. Sơ đồ kiến trúc dạng chữ.**
```text
Browser (HTML/CSS/JS)
        | GET /, POST /consultation
        v
PHP Front Controller (public/index.php)
        |---- Config (.env)
        |---- Domain Validator
        |---- Repository (PDO)
        |             |
        |             v
        |          MySQL 8
        |---- Monolog -> logs/app.log
        v
HTTP HTML / JSON
```

### 5. Nhiệm vụ 3 — Đánh giá maintainability
Dùng SonarQube/SonarQube for IDE hoặc PHP linter để phân tích source. Các nhóm rủi ro cần kiểm tra:
1. **Validation tập trung:** nếu validation bị lặp ở nhiều endpoint, thay đổi quy tắc email/phone sẽ phải sửa nhiều nơi. Giải pháp: dùng `ConsultationValidator`.
2. **SQL injection:** nối chuỗi input vào SQL là rủi ro nghiêm trọng. Giải pháp: PDO prepared statements.
3. **Output escaping:** dữ liệu dự án render trực tiếp có thể gây XSS. Giải pháp: `htmlspecialchars`.
4. **Hard-code config:** cấu hình DB trong source gây khó triển khai và lộ bí mật. Giải pháp: `.env` ngoài Git.
5. **Thiếu kiểm thử:** sửa validation có thể làm hỏng form. Giải pháp: PHPUnit regression tests.

**Lưu ý báo cáo:** chỉ ghi finding thực sự SonarQube trả về. Bảng kết quả thực tế: [ĐIỀN rule, file, dòng, severity, cách sửa]. Không tự nhận đã có 3 finding nếu chưa chạy công cụ.

### 6. Nhiệm vụ 4 — Baseline kiểm thử
Test unit `ConsultationValidatorTest` gồm:
- dữ liệu hợp lệ → không có lỗi;
- email sai → có lỗi `email`;
- họ tên rỗng → có lỗi `customer_name`;
- số điện thoại sai → có lỗi `phone`.

Chạy:
```bash
composer install
composer test
composer test:coverage
```
Kết quả thực tế: [ĐIỀN số test pass/fail]. Coverage: [ĐIỀN % line coverage và báo cáo]. Coverage chỉ đo code được thực thi, không chứng minh toàn bộ chức năng đúng.

### 7. Kết luận
TH1 tạo nền tảng quản lý mã nguồn, hiểu kiến trúc, ghi nhận rủi ro bảo trì và xây dựng test baseline cho chức năng kiểm tra dữ liệu yêu cầu tư vấn. Các thiếu sót được ghi trong `docs/TH1/known-issues.md` để làm đầu vào cho CR-01.

### 8. Minh chứng cần chụp
GitHub repository; README; branch/commit/PR; Milestone TH1; tag `course-baseline`; sơ đồ kiến trúc; kết quả SonarQube; PHPUnit; coverage report; hai lệnh git log theo yêu cầu giảng viên.
