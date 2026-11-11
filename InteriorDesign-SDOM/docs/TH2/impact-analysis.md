# Impact Analysis — CR-01: Tích hợp AI gợi ý phong cách nội thất

## Tóm tắt
Cho phép khách truy cập nhập mô tả nhu cầu/phòng ở; hệ thống trả về gợi ý phong cách nội thất và bảng màu tham khảo. Phạm vi bài lab là prototype, không gửi dữ liệu khách hàng thật tới dịch vụ AI.

## Lý do
Tăng giá trị tư vấn ban đầu và giúp khách hàng định hình phong cách trước khi liên hệ.

## Phạm vi
- Form mô tả không gian, diện tích, ngân sách dự kiến, sở thích.
- Lớp service độc lập `StyleSuggestionService`.
- Kết quả demo dựa trên luật/fixture; adapter AI thật chỉ bật khi có phê duyệt, secret và chính sách dữ liệu.
- Không tự động đặt hàng, thanh toán hoặc đưa ra báo giá ràng buộc.

## Tác động
- UI: thêm khu vực “Gợi ý phong cách”.
- Backend: thêm service và endpoint POST `/style-suggestions`.
- DB: có thể chưa cần lưu prompt; nếu lưu phải có thông báo đồng ý và chính sách lưu trữ.
- Bảo mật: validate input, giới hạn độ dài, rate limit; API key chỉ ở secret manager.
- Hiệu năng/chi phí: timeout, giới hạn request, cache kết quả phù hợp.
- Vận hành: log request ID, trạng thái, latency; không log prompt có dữ liệu cá nhân.
- Kiểm thử: unit test phân loại, integration test adapter, negative test prompt dài/rỗng.
- Rollback: feature flag tắt gợi ý AI, quay về danh sách phong cách tĩnh.

## Tiêu chí chấp nhận
Given mô tả phòng hợp lệ, When người dùng yêu cầu gợi ý, Then hệ thống trả 1–3 phong cách kèm lý do.
Given mô tả rỗng/quá dài, When gửi, Then hệ thống trả lỗi validation và không gọi AI.
Given AI timeout, When xử lý, Then hệ thống hiển thị thông báo thân thiện và gợi ý chọn phong cách thủ công.
Given feature flag OFF, When truy cập, Then chức năng gợi ý không xuất hiện.

## Rủi ro và quyết định
- Không sử dụng dữ liệu cá nhân/ảnh nhà khách hàng trong thử nghiệm.
- Không merge prototype vào main nếu chưa hoàn thành review, test và phê duyệt.
- Kết quả spike là [ĐIỀN: khả thi/không khả thi], bằng chứng tại `docs/TH3/evidence/`.
