# Test cases — Interior Design Website

| ID | Use case | Tiền điều kiện / Dữ liệu | Bước thực hiện | Kết quả mong đợi |
|---|---|---|---|---|
| TC-UC01-01 | Xem danh sách dự án | Có dự án published | Mở `/` | Hiển thị các dự án đã xuất bản |
| TC-UC01-02 | Không hiện dự án nháp | Có dự án is_published=0 | Mở `/` | Dự án nháp không xuất hiện |
| TC-UC02-01 | Gửi tư vấn hợp lệ | Tên, email, phone, room_type hợp lệ | POST `/consultation` | HTTP 200, trả ID, bản ghi được lưu |
| TC-UC02-02 | Email không hợp lệ | `email=abc` | POST `/consultation` | HTTP 422, có lỗi email |
| TC-UC02-03 | Thiếu họ tên | customer_name rỗng | POST `/consultation` | HTTP 422 |
| TC-UC02-04 | Số điện thoại sai | phone=`abc` | POST `/consultation` | HTTP 422 |
| TC-UC03-01 | Health check | DB hoạt động | GET `/health` | JSON status=ok |
| TC-UC03-02 | DB mất kết nối | Tắt MySQL ở môi trường test | GET `/health` | HTTP 503, log lỗi không lộ thông tin nhạy cảm |

Kết quả thực tế: điền sau khi chạy. Không tự đánh dấu Pass nếu chưa thực thi.
