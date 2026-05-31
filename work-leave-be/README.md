# 📋 Work Leave Management — Backend API

> Laravel 12 · JWT Auth · RESTful API · MySQL

---

## 📦 Tech Stack

| Layer     | Technology                             |
| --------- | -------------------------------------- |
| Framework | Laravel 12                             |
| Auth      | JWT (`php-open-source-saver/jwt-auth`) |
| Database  | MySQL 8+                               |
| PHP       | 8.2+                                   |
| Server    | PHP Built-in / Apache (XAMPP)          |

---

## ⚙️ Yêu cầu môi trường

- PHP >= 8.2
- Composer
- MySQL 8+
- XAMPP

---

## 🚀 Cài đặt & Chạy

### 1. Clone project

```bash
git clone https://github.com/QuangDzu/work-leave-be.git
cd work-leave-be
```

### 2. Cài dependencies

```bash
composer install
```

### 3. Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Mở file `.env`, sửa thông tin database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leave_management
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Cài JWT

```bash
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

### 5. Sửa 2 file cấu hình quan trọng

#### `config/auth.php` — đổi api driver thành jwt

```php
'api' => [
    'driver'   => 'jwt',
    'provider' => 'users',
],
```

#### `app/Http/Kernel.php` — đăng ký middleware role

```php
protected $middlewareAliases = [
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

> **Laravel 12:** Nếu không có `Kernel.php`, mở `bootstrap/app.php` và thêm:
>
> ```php
> ->withMiddleware(function (Middleware $middleware) {
>     $middleware->alias([
>         'role'     => \App\Http\Middleware\RoleMiddleware::class,
>         'auth.api' => \App\Http\Middleware\ApiAuthenticate::class,
>     ]);
> })
> ```

### 6. Tạo database & chạy migration

Tạo database MySQL trước:

```sql
CREATE DATABASE leave_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sau đó chạy migration + seed:

```bash
php artisan migrate --seed
```

Kết quả mong đợi:

```
+----------+--------------------------+-------------+
| Role     | Email                    | Password    |
+----------+--------------------------+-------------+
| Admin    | admin@company.com        | password123 |
| Manager  | manager@company.com      | password123 |
| Employee | an.nguyen@company.com    | password123 |
| Employee | bich.tran@company.com    | password123 |
+----------+--------------------------+-------------+
```

### 7. Khởi động server

**Cách 1 — PHP Built-in:**

```bash
# XAMPP
D:\xampp\php\php.exe -S 127.0.0.1:8001 -t public

# macOS/Linux
php -S 127.0.0.1:8001 -t public
```

**Cách 2 — Artisan serve:**

```bash
php artisan serve --port=8001
```

Server chạy tại: `http://127.0.0.1:8001`

---

## 🗄️ Import Database (cách thay thế migrate)

Nếu muốn import trực tiếp thay vì chạy migrate:

### phpMyAdmin

1. Mở `http://localhost/phpmyadmin`
2. Tạo database `leave_management`
3. Chọn database → tab **Import**
4. Chọn file `database/leave_management.sql`
5. Click **Go**

### Command line

```bash
mysql -u root -p leave_management < database/leave_management.sql
```

---

## 🔑 Tài khoản test

| Role     | Email                 | Password    | Quyền                          |
| -------- | --------------------- | ----------- | ------------------------------ |
| Admin    | admin@company.com     | password123 | Toàn quyền                     |
| Manager  | manager@company.com   | password123 | Duyệt/từ chối đơn, xem nhân sự |
| Employee | an.nguyen@company.com | password123 | Tạo/xem/hủy đơn của mình       |
| Employee | bich.tran@company.com | password123 | Tạo/xem/hủy đơn của mình       |

---

## 📡 API Endpoints

Base URL: `http://127.0.0.1:8001/api`

### Auth

| Method | Endpoint        | Auth | Mô tả                   |
| ------ | --------------- | ---- | ----------------------- |
| POST   | `/auth/login`   | —    | Đăng nhập, trả token    |
| POST   | `/auth/logout`  | ✓    | Đăng xuất               |
| POST   | `/auth/refresh` | ✓    | Làm mới token           |
| GET    | `/auth/me`      | ✓    | Thông tin user hiện tại |

### Users (Admin only)

| Method | Endpoint      | Mô tả                             |
| ------ | ------------- | --------------------------------- |
| GET    | `/users`      | Danh sách (có phân trang, search) |
| GET    | `/users/{id}` | Chi tiết user                     |
| POST   | `/users`      | Tạo user mới                      |
| PUT    | `/users/{id}` | Cập nhật user                     |
| DELETE | `/users/{id}` | Xóa mềm                           |

### Leave Applications

| Method | Endpoint               | Role          | Mô tả                                  |
| ------ | ---------------------- | ------------- | -------------------------------------- |
| GET    | `/leaves`              | all           | Danh sách (employee chỉ thấy của mình) |
| GET    | `/leaves/{id}`         | all           | Chi tiết                               |
| POST   | `/leaves`              | all           | Tạo đơn                                |
| PUT    | `/leaves/{id}`         | owner         | Cập nhật (chỉ new/pending)             |
| DELETE | `/leaves/{id}`         | owner         | Xóa mềm (chỉ new/pending)              |
| PUT    | `/leaves/{id}/approve` | manager/admin | Duyệt đơn                              |
| PUT    | `/leaves/{id}/reject`  | manager/admin | Từ chối đơn                            |
| PUT    | `/leaves/{id}/cancel`  | owner         | Hủy đơn                                |

### Query params (GET /leaves)

```
?status=pending          # lọc theo trạng thái
?type=annual             # lọc theo loại
?from_date=2026-01-01    # từ ngày
?to_date=2026-12-31      # đến ngày
?page=1                  # phân trang
```

---

## 📬 Test bằng Postman

1. Import file `postman_collection.json` vào Postman
2. Tạo environment với variable:
    - `base_url` = `http://127.0.0.1:8001/api`
3. Chạy request **Login (Admin)** — token tự động lưu vào `{{token}}`
4. Test các endpoint còn lại

### Test flow cơ bản

```
1. POST /auth/login (admin)           → lấy token
2. GET  /auth/me                      → verify token
3. GET  /users                        → danh sách users (admin)
4. GET  /leaves                       → danh sách đơn (admin thấy tất cả)
5. POST /auth/login (employee)        → đổi sang token employee
6. POST /leaves                       → tạo đơn mới
7. GET  /leaves                       → chỉ thấy đơn của mình
8. GET  /users                        → 403 Forbidden
9. POST /auth/login (manager)         → đổi sang token manager
10. PUT /leaves/{id}/approve          → duyệt đơn pending
```

---

## 📂 Cấu trúc project

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php      # Login, logout, refresh, me
│   │   ├── LeaveController.php     # CRUD + approve/reject/cancel
│   │   └── UserController.php      # CRUD users (admin)
│   ├── Middleware/
│   │   ├── ApiAuthenticate.php     # JWT auth cho Laravel 12
│   │   └── RoleMiddleware.php      # Role-based access
│   ├── Requests/
│   │   ├── LeaveStoreRequest.php   # Validate tạo đơn
│   │   └── LeaveUpdateRequest.php  # Validate cập nhật đơn
│   └── Resources/
│       ├── LeaveResource.php       # JSON transform đơn nghỉ
│       └── UserResource.php        # JSON transform user
├── Models/
│   ├── LeaveApplication.php        # Model đơn nghỉ + scopes
│   └── User.php                    # Model user + JWT
└── Services/
    └── LeaveApplicationService.php # Business logic

database/
├── migrations/                     # Schema migrations
├── seeders/DatabaseSeeder.php      # Dữ liệu mẫu
└── leave_management.sql            # Export DB sẵn dùng

routes/
└── api.php                         # Tất cả API routes
```

---

## 🔄 Response Format

### Success

```json
{
    "success": true,
    "message": "...",
    "data": {},
    "meta": {}
}
```

### Error

```json
{
    "success": false,
    "message": "...",
    "errors": {}
}
```

### HTTP Status Codes

| Code | Ý nghĩa          |
| ---- | ---------------- |
| 200  | OK               |
| 201  | Created          |
| 400  | Bad Request      |
| 401  | Unauthorized     |
| 403  | Forbidden        |
| 404  | Not Found        |
| 422  | Validation Error |

---

## 🧮 Logic nghiệp vụ

### Tính ngày nghỉ thực tế

- Trừ thứ 7, Chủ nhật
- Trừ ngày lễ cố định: 1/1, 30/4, 1/5, 2/9

### Workflow đơn nghỉ

```
new → pending → approved / rejected
new / pending → cancelled (bởi chủ đơn)
```

### Quota phép năm

- Mặc định 12 ngày/năm (`remaining_days` trong bảng users)
- Khi **approved** → trừ `remaining_days`
- Khi **cancelled** sau approved → hoàn lại

### Kiểm tra trùng lịch

- Không cho tạo đơn nếu trùng ngày với đơn `approved` hoặc `pending` khác

---

## ❗ Lưu ý khi test

1. `start_date` phải >= ngày hôm nay
2. Chỉ admin mới gọi được `/users`
3. Employee chỉ thấy đơn của chính mình ở `/leaves`
4. Token JWT hết hạn sau **60 phút**, dùng `/auth/refresh` để gia hạn
5. Nếu dùng XAMPP: đảm bảo **Apache đã tắt** trước khi chạy PHP built-in server để tránh conflict port
