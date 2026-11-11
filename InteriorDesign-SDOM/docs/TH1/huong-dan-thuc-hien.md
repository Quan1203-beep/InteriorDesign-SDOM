# Hướng dẫn thao tác TH1

## Khởi tạo repository
```bash
git init
git branch -M main
git add .
git commit -m "chore: initialize interior design website"
git remote add origin https://github.com/<username>/interior-design-website.git
git push -u origin main
```
Tạo repository private trên GitHub trước khi push.

## Tạo milestone và issue
GitHub → Issues → Milestones → New milestone → `TH1`. Tạo issues theo nội dung báo cáo; thay số issue bằng số thật GitHub cấp.

## Kiểm tra local
```bash
php -v
composer -V
composer install
cp .env.example .env
# chỉnh DB trong .env
mysql -u root -p < database/schema.sql
php -S 127.0.0.1:8000 -t public
```
Mở trình duyệt `http://127.0.0.1:8000` và `http://127.0.0.1:8000/health`.

## Baseline test và tag
```bash
composer test
composer test:coverage
git tag -a course-baseline -m "Course baseline"
git push origin course-baseline
```
Tạo tag sau khi đã merge các PR baseline vào main.

## Lệnh nộp bài theo đề
```bash
git log --first-parent --oneline course-baseline..main
git log --first-parent --no-merges --oneline course-baseline..main
```
Chụp màn hình kết quả thực tế.
