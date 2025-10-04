# Tổng quan các file ảnh hưởng dự án và Prompt gợi ý cho ChatGPT

Tài liệu này liệt kê các file mã nguồn “ảnh hưởng trực tiếp” tới website (điều hướng, hiển thị, xử lý nghiệp vụ), phân nhóm theo vai trò; đồng thời cung cấp một Prompt mẫu để bạn có thể giao việc cho ChatGPT hiệu quả và nhất quán.

## 1) Phạm vi & Kiến trúc

- Nền tảng: Laravel 9, Laravel UI (auth), Filament v2 (admin), encore/laravel-admin (có thể xung đột đường dẫn `/admin` nếu dùng song song), VNPAY, SePay.
- FE: các controller dưới `app/Http/Controllers` + blade views dưới `resources/views/pages/*`.
- Admin (Filament): resources/pages dưới `app/Filament/*`.
- Quyền vào admin: dựa vào `role` trong `users` và `canAccessFilament()` trên `App\\Models\\User`.

## 2) Routes & điều phối

- `routes/web.php` — Toàn bộ route FE: trang chủ, shop, news, product, cart, wishlist, user info, orders, payment VNPAY (return). Gọi `Auth::routes()` để bật các route auth mặc định.
- `app/Http/Kernel.php` — Đăng ký middleware global, nhóm `web`, alias route middleware (đặc biệt: `ensure.admin` và `login.notice`).
- `app/Providers/RouteServiceProvider.php` — Cấu hình nhóm route `web` và `api` (mặc định).

## 3) Controllers (Frontend)

- `app/Http/Controllers/HomeController.php` — Trang chủ: load danh mục theo tên, SP mới, tin mới; trả view `pages.index`.
- `app/Http/Controllers/ShopController.php` — Trang shop + lọc theo từ khóa/brand/category; phân trang; trả view `pages.shop`.
- `app/Http/Controllers/NewsController.php` — Danh sách/chi tiết tin; trả view `pages.news.*`.
- `app/Http/Controllers/ProductController.php` — Chi tiết sản phẩm; eager load images, detail, comments; trả view `pages.product.show`.
- `app/Http/Controllers/WishlistController.php` — Danh sách và thêm wishlist cho user đăng nhập; trả view `pages.wishlist`.
- `app/Http/Controllers/OrderController.php` — Lịch sử đơn hàng user hiện tại; trả view `pages.orders.history`.
- `app/Http/Controllers/UserInformationController.php` — Trang thông tin khách hàng (Customer) gắn với user; cập nhật hồ sơ.

### Auth Controllers
- `app/Http/Controllers/Auth/LoginController.php` — Đăng nhập; admin → `/admin`, user → trang chủ. Đảm bảo tạo Customer cho user thường sau login.
- `app/Http/Controllers/Auth/RegisterController.php` — Đăng ký; tạo user role=user, tạo Customer; điều hướng sau đăng ký.
- `app/Http/Controllers/Auth/ForgotPasswordController.php` — Gửi email đặt lại mật khẩu (đã custom message).
- `app/Http/Controllers/Auth/ResetPasswordController.php` — Đặt lại mật khẩu (mặc định).
- `app/Http/Controllers/Auth/VerificationController.php` — Xác thực email (mặc định).

## 4) Middleware

- `app/Http/Middleware/EnsureAdmin.php` — Bảo vệ khu vực `/admin*`: nếu chưa đăng nhập → `/admin/login`; nếu role ≠ admin → `home.index`. Cho phép đi qua các URL đăng nhập/khôi phục mật khẩu của Filament.
- `app/Http/Middleware/RequireLoginWithMessage.php` — Nếu chưa login, redirect tới `/login` kèm flash `status`. Dùng cho một số hành động cart/wishlist.
- `app/Http/Middleware/Authenticate.php` — Mặc định: chưa login và request không kỳ vọng JSON → chuyển tới `login`.
- `app/Http/Middleware/RedirectIfAuthenticated.php` — Mặc định: đã đăng nhập → chuyển tới `RouteServiceProvider::HOME`.
- Các middleware mặc định khác (ít ảnh hưởng business): `EncryptCookies.php`, `PreventRequestsDuringMaintenance.php`, `TrimStrings.php`, `TrustHosts.php`, `TrustProxies.php`, `ValidateSignature.php`, `VerifyCsrfToken.php`.

## 5) Admin (Filament)

- `app/Filament/Resources/*Resource.php` — Khai báo CRUD cho các model (Category, Product, Order, User, v.v.). Mỗi resource định nghĩa:
  - `form(Form $form)` — Schema form tạo/sửa bản ghi (Filament Forms).
  - `table(Table $table)` — Cột, tìm kiếm, sắp xếp, actions của listing (Filament Tables).
  - `getPages()` — Ánh xạ các route con (`/`, `/create`, `/{record}/edit`) sang các lớp `List*/Create*/Edit*`.
  - (Tùy chọn) `getRelations()` — Quan hệ con qua RelationManagers.
- `app/Filament/Resources/*/Pages/*.php` — Các lớp trang con `ListRecords`, `CreateRecord`, `EditRecord`.
- `app/Filament/Pages/ProductStats.php` + `resources/views/filament/pages/product-stats.blade.php` — Trang thống kê bán hàng tùy chỉnh dùng Chart.js.
- Quyền vào panel: `app/Models/User.php` cần implement `Filament\\Models\\Contracts\\FilamentUser` và `canAccessFilament()` trả về true nếu `role=admin`.

## 6) Thanh toán & Webhook

- `app/Http/Controllers/PaymentController.php` — Khởi tạo thanh toán VNPAY từ cart (session), redirect sang VNPAY; xử lý return URL: xác thực chữ ký, tạo Order/OrderItems, áp mã giảm giá, cập nhật Payment, dọn session; thông báo kết quả.
  - Env quan trọng: `VNPAY_URL`, `VNPAY_TMN_CODE`, `VNPAY_HASH_SECRET`, `VNPAY_RETURN_URL`.
- `app/Listeners/SePayWebhookListener.php` — Lắng nghe `SePayWebhookEvent` từ gói `sepayvn/laravel-sepay`, thông báo top-up cho `User` tương ứng.
- `app/Providers/EventServiceProvider.php` — Map `SePayWebhookEvent` → `SePayWebhookListener`; và `Registered` → `SendEmailVerificationNotification`.

## 7) Models (tiêu biểu)

- `app/Models/User.php` — Model auth; đã implement `FilamentUser` để kiểm soát quyền vào admin. Quan hệ: `customer`, `orders`, `comments`, `wishlists`.
- `app/Models/Product.php` — UUID key, `fillable`, `casts`, quan hệ `category`, `detail`, `images`, `comments`, `orderItems`, `wishlists`.
- Các model khác: `Category`, `Comment`, `Contact`, `Coupon`, `Customer`, `Image`, `News`, `Order`, `OrderItem`, `ProductDetail`, `Wishlist`, `Payment` — dùng ở FE controllers và/hoặc Filament Resources.

## 8) Views (FE tiêu biểu)

- `resources/views/pages/product/show.blade.php` — Chi tiết SP + bình luận.
- `resources/views/pages/wishlist.blade.php` — Danh sách wishlist.
- `resources/views/pages/cart.blade.php` — Giỏ hàng session.
- `resources/views/pages/orders/history.blade.php` — Lịch sử đơn hàng.
- `resources/views/filament/pages/product-stats.blade.php` — View biểu đồ cho trang ProductStats.

## 9) Cấu hình & khởi động

- `app/Providers/AppServiceProvider.php` — FE dùng Bootstrap paginator, admin (đường `/admin`) giữ Tailwind/Livewire. Thiết lập `livewire.asset_url` khi app deploy trong subfolder (giúp load đúng asset Livewire).
- `config/session.php` — Driver, domain, secure cookie, same-site. Ảnh hưởng trực tiếp giữ phiên đăng nhập khi deploy domain/subdomain/HTTPS.
- `config/admin.php` — Cấu hình encore/laravel-admin (roles, menu, tables...). Có thể xung đột path `/admin` với Filament nếu chạy song song.
- `.env` — `APP_URL`, `SESSION_DOMAIN`, `SESSION_SECURE_COOKIE`, cấu hình VNPAY/SePay…

## 10) Migration đáng chú ý

- `database/migrations/2025_09_18_111104_adjust_price_precision.php` — Tăng độ chính xác tiền (`price/total`) ở `products`, `order_items`, `orders`. Ảnh hưởng format hiển thị và tính toán tổng.

## 11) encore/laravel-admin (tùy chọn)

- `app/Admin/bootstrap.php` — Vô hiệu một số field mặc định (map, editor).
- `app/Admin/routes.php` — (hiện trống) — gói encore tự đăng ký route riêng, có thể chiếm `/admin` nếu bật đầy đủ provider.

---

## 12) Luồng hoạt động mẫu

- Truy cập admin:
  1) Đăng nhập → `LoginController::redirectTo()` → admin thì chuyển `/admin`.
  2) Middleware `ensure.admin` kiểm tra auth + role.
  3) `User::canAccessFilament()` quyết định vào panel hay nhận 403.
  4) Nếu dùng encore/laravel-admin song song, cần tránh trùng path `/admin`.

- Thanh toán VNPAY:
  1) POST `/payment/vnpay` → `PaymentController@vnpayPayment` đọc cart + coupon trong session; ghi `payments` trạng thái pending; ký tham số, redirect sang VNPAY.
  2) GET `/payment/vnpay/return` → `PaymentController@vnpayReturn` xác thực chữ ký; tạo `orders` + `order_items`; cập nhật coupon, payment; dọn session; flash kết quả.

- Wishlist: `WishlistController@index/add` sử dụng middleware `auth` và flash `status` khi thêm.

---

## 13) File mặc định/ít ảnh hưởng trực tiếp business

- Middleware: `EncryptCookies`, `PreventRequestsDuringMaintenance`, `TrimStrings`, `TrustHosts`, `TrustProxies`, `ValidateSignature`, `VerifyCsrfToken`.
- Providers: `RouteServiceProvider`, `AuthServiceProvider` (nếu không tùy biến).
- Một số Auth controllers (Reset/Verify) theo boilerplate Laravel UI.

---

## 14) Prompt mẫu để giao việc cho ChatGPT

Mục đích: Giúp ChatGPT hiểu cấu trúc dự án, hành xử theo chuẩn, và sửa đúng chỗ. Bạn có thể copy nguyên khối này khi đặt yêu cầu.

```
Bạn là trợ lý code đang làm việc trên dự án Laravel 9 có Filament v2 (admin), Laravel UI (auth), VNPAY, và một số middleware tùy chỉnh.

Bối cảnh kỹ thuật tóm tắt:
- FE controllers ở app/Http/Controllers, routes ở routes/web.php, views ở resources/views/pages.
- Admin (Filament) ở app/Filament: các *Resource.php (form/table/getPages) và *Resource/Pages/* (List/Create/Edit). Có Page tùy chỉnh ProductStats + view tương ứng.
- Quyền vào admin: App/Models/User implements FilamentUser và canAccessFilament() cho role=admin. Middleware ensure.admin bảo vệ /admin/*.
- Thanh toán: PaymentController xử lý VNPAY (env: VNPAY_TMN_CODE, VNPAY_HASH_SECRET, VNPAY_RETURN_URL).
- Deploy có thể ở subfolder; AppServiceProvider đã set livewire.asset_url; session phụ thuộc APP_URL, SESSION_DOMAIN, SESSION_SECURE_COOKIE.

Yêu cầu công việc:
- Hãy thực hiện đúng phạm vi yêu cầu, sửa tối thiểu, đúng style code hiện có.
- Nếu phải sửa nhiều file, dùng patch nhỏ, giải thích ngắn gọn lý do.
- Nếu liên quan admin, kiểm tra User::canAccessFilament(), middleware ensure.admin, xung đột đường /admin với encore/laravel-admin.
- Với thanh toán, giữ nguyên quy trình ký và kiểm tra chữ ký VNPAY.

Quy tắc khi trả lời:
- Liệt kê file sẽ sửa (đường dẫn). Dùng patch dạng apply_patch khi đề xuất thay đổi.
- Không tự ý thêm gói hoặc đổi cấu trúc trừ khi cần thiết và có giải thích.
- Nếu cần cấu hình, chỉ rõ biến .env hoặc config liên quan.
- Đề xuất cách kiểm tra (artisan cache:clear, route:clear; hoặc route:list) khi phù hợp.

Nếu có điều gì chưa rõ, hãy hỏi lại để chốt giả định trước khi sửa.
```

Giải thích:
- Khối “Bối cảnh kỹ thuật” giúp ChatGPT nắm bản đồ dự án, tránh sửa sai nơi.
- “Yêu cầu công việc” và “Quy tắc” định hình phong cách sửa code: tối thiểu, có lý do, có chỉ dẫn kiểm tra.
- Có thể mở rộng Prompt với tiêu chí format (PSR-12), ngôn ngữ hiển thị (VI/EN), hay phạm vi thư mục được sửa khi cần.

---

## 15) Gợi ý khi deploy & debug

- Admin 403: kiểm tra `User::canAccessFilament()`, middleware `ensure.admin`, `.env` (APP_URL, SESSION_DOMAIN, SESSION_SECURE_COOKIE), và xung đột `/admin`. Chạy: `php artisan config:clear && php artisan route:clear && php artisan cache:clear`.
- Filament assets khi app nằm trong subfolder: AppServiceProvider đã set `livewire.asset_url` theo `request()->getBaseUrl()`.
- Session: nếu domain/subdomain/https thay đổi, cập nhật `SESSION_DOMAIN`, `SESSION_SECURE_COOKIE`, bật HTTPS ở server.

```
Mẹo sử dụng tài liệu này: Khi giao task cho ChatGPT, đính kèm link file cần sửa, trích đoạn code quan trọng, và copy kèm Prompt mẫu để trợ lý bám đúng ngữ cảnh dự án.
```

