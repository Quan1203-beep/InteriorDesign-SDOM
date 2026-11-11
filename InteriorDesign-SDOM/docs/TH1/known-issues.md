# Known issues — Baseline TH1
1. Chưa có trang quản trị hoàn chỉnh để CRUD dự án; dữ liệu dự án hiện được nạp bằng SQL.
2. Chưa có phân quyền người dùng quản trị.
3. Chưa có CSRF token cho biểu mẫu; cần bổ sung trước khi triển khai công khai.
4. Cần giới hạn tần suất gửi form và thêm CAPTCHA/chống spam.
5. Hình ảnh demo dùng URL ngoài; production nên lưu trữ và tối ưu ảnh có bản quyền.
