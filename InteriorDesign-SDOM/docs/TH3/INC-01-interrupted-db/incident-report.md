# Incident Report — INC-01: MySQL không kết nối được

> Kịch bản diễn tập trong môi trường local/test, không cố ý làm gián đoạn hệ thống production.

| Trường | Nội dung |
|---|---|
| Incident ID | INC-01 |
| Mức độ | SEV-2 (bài tập) |
| Dịch vụ | Website Interior Design |
| Thời gian phát hiện | [ĐIỀN THỜI GIAN THỰC TẾ] |
| Thời gian khôi phục | [ĐIỀN THỜI GIAN THỰC TẾ] |
| Ảnh hưởng | Trang chủ/health endpoint không truy vấn được dữ liệu dự án; form tư vấn tạm thời không hoạt động |
| Trạng thái | [Open/Resolved] |

## Phát hiện và bằng chứng
- GET `/health` trả HTTP 503 khi MySQL test bị dừng.
- Log ghi sự kiện `Application request failed` với exception class, không ghi mật khẩu/DSN.
- Bằng chứng: [đường dẫn ảnh log, health check, trạng thái MySQL].

## Timeline
| Thời điểm | Sự kiện |
|---|---|
| T0 | Dừng MySQL test có kiểm soát |
| T1 | Health check báo lỗi |
| T2 | Xác định nguyên nhân kết nối |
| T3 | Khởi động MySQL và xác minh cấu hình |
| T4 | Smoke test và xác nhận phục hồi |

## Phân tích 5 Whys
1. Vì sao website trả 503? Vì PDO không kết nối được MySQL.
2. Vì sao PDO không kết nối? Dịch vụ MySQL test đang dừng hoặc thông tin kết nối sai.
3. Vì sao không phát hiện sớm? Chưa có cảnh báo tự động ngoài health check.
4. Vì sao health check chưa cảnh báo? Endpoint mới chỉ được kiểm tra thủ công.
5. Vì sao chưa có giám sát liên tục? Phạm vi baseline ban đầu chưa bao gồm monitoring alert.

## Khắc phục và phòng ngừa
- Khôi phục dịch vụ MySQL hoặc sửa cấu hình qua biến môi trường.
- Xác nhận `/health` trả `status=ok`.
- Chạy smoke test.
- Thêm cảnh báo khi health endpoint lỗi liên tiếp; tài liệu hóa hướng dẫn vận hành.
- Không log thông tin xác thực; giới hạn quyền DB theo nguyên tắc tối thiểu.

## MTTD / MTTR
MTTD = thời điểm phát hiện - thời điểm bắt đầu sự cố.
MTTR = thời điểm khôi phục - thời điểm bắt đầu sự cố.
Điền số đo thực tế: [ĐIỀN].
