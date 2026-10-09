# Tổng Hợp Command Line Thường Dùng - MD-CARS Showroom

> Tài liệu tổng hợp toàn bộ các câu lệnh CLI cần thiết để vận hành, phát triển, kiểm tra và bảo trì dự án trong môi trường Docker & Laravel.

---

## 1. Quản Lý Docker Container

Các lệnh này chạy từ thư mục gốc của dự án (`d:\car-showroom`):

| Mục đích | Câu lệnh |
|---|---|
| Khởi động toàn bộ container (chạy nền) | `docker compose up -d` |
| Tắt toàn bộ container | `docker compose down` |
| Kiểm tra trạng thái các container | `docker compose ps` |
| Khởi động lại container ứng dụng (`app`) | `docker compose restart app` |
| Khởi động lại container Nginx | `docker compose restart nginx` |
| Khởi động lại container MySQL | `docker compose restart mysql` |
| Xem log container ứng dụng theo thời gian thực | `docker logs -f car_app` |
| Xem log MySQL | `docker logs -f car_mysql` |
| Truy cập dòng lệnh (shell) bên trong container `app` | `docker exec -it car_app sh` |
| Truy cập trực tiếp vào MySQL database | `docker exec -it car_mysql mysql -u car -pcar car_showroom` |

---

## 2. Database & Migrations

Thực thi thông qua container `car_app`:

```bash
# 1. Kiểm tra danh sách và trạng thái các file migration (Đã chạy / Chưa chạy)
docker exec car_app php artisan migrate:status

# 2. Chạy các migration mới chưa thực thi
docker exec car_app php artisan migrate

# 3. Rollback (quay lui) batch migration gần nhất
docker exec car_app php artisan migrate:rollback

# 4. Rollback số bước cụ thể (ví dụ quay lui 1 bước gần nhất)
docker exec car_app php artisan migrate:rollback --step=1

# 5. Xóa toàn bộ bảng và chạy lại toàn bộ migration từ đầu (LƯU Ý: MẤT HẾT DỮ LIỆU)
docker exec car_app php artisan migrate:fresh

# 6. Reset toàn bộ bảng + chạy seeder dữ liệu mẫu ban đầu
docker exec car_app php artisan migrate:fresh --seed

# 7. Chạy riêng Seeder
docker exec car_app php artisan db:seed

# 8. Chạy một Seeder class cụ thể
docker exec car_app php artisan db:seed --class=ShowroomDatabaseSeeder
```

---

## 3. Quản Lý Cache & Cấu Hình (.env, Routes, Views)

> 💡 **Mẹo:** Mỗi khi sửa đổi file `.env`, cập nhật route trong `routes/` hoặc sửa template Blade mà giao diện chưa nhận, hãy chạy các lệnh sau:

```bash
# 1. Xóa cache cấu hình (bắt buộc chạy sau khi sửa file .env)
docker exec car_app php artisan config:clear

# 2. Cache cấu hình lại (dùng khi deploy production để tối ưu tốc độ)
docker exec car_app php artisan config:cache

# 3. Xóa cache routes
docker exec car_app php artisan route:clear

# 4. Xem toàn bộ danh sách routes trong hệ thống
docker exec car_app php artisan route:list

# 5. Xóa cache Blade Views
docker exec car_app php artisan view:clear

# 6. Xóa cache dữ liệu ứng dụng
docker exec car_app php artisan cache:clear

# 7. XÓA TẤT CẢ CÁC LOẠI CACHE (Lệnh All-In-One tiện lợi nhất)
docker exec car_app php artisan optimize:clear
```

---

## 4. Kiểm Chuẩn Code (Coding Standards & Verification)

Theo quy định bắt buộc trong `AGENTS.md` trước khi bàn giao code:

```bash
# 1. Kiểm tra và tự động định dạng code theo chuẩn PSR-12 (Laravel Pint)
docker exec car_app composer lint

# 2. Phân tích tĩnh code để phát hiện lỗi logic / type / query (PHPStan)
docker exec car_app composer stan

# 3. Chạy toàn bộ Test tự động (khi cần)
docker exec car_app composer test
```

*(Nếu bạn đứng trong thư mục `apps/laravel/` trên máy host có cài sẵn Composer và PHP thì có thể gõ trực tiếp `composer lint`, `composer stan`)*

---

## 5. Tinker (Thực Thi Code PHP Trực Tiếp)

Dùng để test nhanh logic, truy vấn database hoặc kiểm tra dữ liệu:

```bash
# Mở môi trường dòng lệnh tương tác PHP Tinker
docker exec -it car_app php artisan tinker

# Hoặc thực thi nhanh một lệnh PHP không cần mở console:
docker exec car_app php artisan tinker --execute="echo App\Models\User::count();"
```

---

## 6. Storage & Uploads (Liên kết Thư mục Lưu trữ)

```bash
# Tạo symbolic link từ storage/app/public sang public/storage (dành cho ảnh xe, media)
docker exec car_app php artisan storage:link
```

---

## 7. Queue Worker (Xử lý tác vụ nền: Gửi Email, Xử lý ảnh)

Nếu cấu hình `QUEUE_CONNECTION=database` trong `.env` để gửi email không đồng bộ:

```bash
# 1. Lắng nghe và xử lý các Jobs trong hàng đợi
docker exec car_app php artisan queue:work

# 2. Xem danh sách các jobs xử lý thất bại
docker exec car_app php artisan queue:failed

# 3. Thử lại toàn bộ các jobs thất bại
docker exec car_app php artisan queue:retry all

# 4. Xóa toàn bộ các jobs thất bại
docker exec car_app php artisan queue:flush
```

---

## 8. Frontend Assets (NPM / Vite)

Chạy trong thư mục `apps/laravel/` trên máy host (yêu cầu Node.js):

```bash
# 1. Cài đặt các thư viện frontend
npm install

# 2. Chạy dev server với hot reload
npm run dev

# 3. Build tối ưu hóa mã nguồn cho môi trường production
npm run build
```

---

## 9. Xem Log Ứng Dụng (Troubleshooting / Gửi Email)

Khi gặp lỗi 500 hoặc muốn xem email được ghi log:

```bash
# Cách 1: Xem bằng lệnh Linux bên trong container (theo dõi real-time)
docker exec car_app tail -f storage/logs/laravel.log

# Cách 2: Xem trực tiếp bằng PowerShell trên Windows (từ thư mục d:\car-showroom)
Get-Content -Path apps\laravel\storage\logs\laravel.log -Wait -Tail 50

# Xóa trắng file log khi log quá dài:
docker exec car_app truncate -s 0 storage/logs/laravel.log
```
