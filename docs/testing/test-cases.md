# Test cases
| ID | Tình huống | Kết quả mong đợi |
|---|---|---|
| TC-01 | Tên/email hợp lệ | Validator trả [] |
| TC-02 | Tên rỗng | Lỗi customer_name |
| TC-03 | Email sai | Lỗi email |
| TC-04 | Phone chứa chữ | Lỗi phone |
| TC-05 | DB down, GET /health | HTTP 503 |
| TC-06 | Form hợp lệ, DB up | Lưu yêu cầu và JSON ok |
