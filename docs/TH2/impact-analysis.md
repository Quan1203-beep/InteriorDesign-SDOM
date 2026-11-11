# Impact Analysis — CR-01: Gợi ý phong cách nội thất

## Mô tả
Người dùng nhập loại không gian, diện tích, ngân sách và sở thích; hệ thống trả 1–3 phong cách gợi ý. Phạm vi lab dùng rule-based/mock cục bộ, không gửi dữ liệu khách hàng đến AI ngoài.

## Ảnh hưởng
UI: form và vùng kết quả. Backend: service gợi ý độc lập. DB: không cần lưu prompt. Security: giới hạn input, validate enum, rate limit khi public. Operations: timeout, request ID, feature flag. Tests: input hợp lệ/rỗng/quá dài, timeout, feature flag OFF.

## Acceptance criteria
- Given dữ liệu hợp lệ, When gửi, Then trả 1–3 phong cách.
- Given input rỗng/quá dài, When gửi, Then trả lỗi validation.
- Given feature flag OFF, When gửi, Then thông báo chức năng chưa khả dụng.
- Given service timeout, When gửi, Then trả thông báo an toàn, không lộ stack trace.

## Rollback
Tắt feature flag hoặc revert commit; tránh migration phá hủy dữ liệu.
