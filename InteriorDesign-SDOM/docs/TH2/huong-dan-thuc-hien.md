# Hướng dẫn thao tác TH2

1. Tạo branch `chore/7-issue-pr-templates`; thêm issue forms và PR template; mở PR và merge.
2. Tạo branch `ci/9-build-test-workflow`; thêm `.github/workflows/ci.yml`; push và xem tab Actions. Sửa lỗi đến khi xanh rồi merge.
3. Tạo branch `refactor/11-externalize-config`; thêm dotenv, `.env.example`, `.gitignore`; kiểm tra không có `.env` tracked bằng `git ls-files .env`.
4. Chạy `composer show --direct`, `composer show --tree`, `composer audit`; ghi phiên bản thực tế.
5. Tạo test case, bổ sung PHPUnit test, chạy `composer test`.
6. Cập nhật smoke test, CHANGELOG, merge các PR rồi tạo tag `v1.0.0` và GitHub Release.
7. Tạo issue CR-01 và Impact Analysis; spike không merge; chỉ triển khai chính thức sau khi được duyệt.

Lệnh rà soát secret đơn giản:
```bash
git ls-files | grep -E '(^|/)\.env$|password|secret|token'
git log --all --oneline
```
Nếu phát hiện secret đã commit, rotate/thu hồi ngay; không chỉ xóa file ở commit mới.
