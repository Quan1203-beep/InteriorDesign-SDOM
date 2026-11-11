# Smoke test checklist
- [ ] Trang chủ HTTP 200 và CSS/JS tải được.
- [ ] /health trả 200 khi MySQL test chạy.
- [ ] /health trả 503 khi MySQL test dừng có kiểm soát.
- [ ] Form hợp lệ được lưu vào consultation_requests.
- [ ] Email sai bị từ chối HTTP 422.
- [ ] Không có secret/PII trong log.
- Ghi ngày giờ, môi trường, commit SHA và ảnh tại docs/TH2/evidence/.
