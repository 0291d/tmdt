# Q&A Chức năng Dự án (Laravel 9)

## 🔐 Đăng ký / Đăng nhập
- Cài đặt auth: dùng Laravel UI với `Auth::routes()` và scaffolding controller/view mặc định.
  - routes/web.php: `routes/web.php`
  - Controllers: `app/Http/Controllers/Auth/LoginController.php`, `app/Http/Controllers/Auth/RegisterController.php`, `app/Http/Controllers/Auth/ForgotPasswordController.php`, `app/Http/Controllers/Auth/ResetPasswordController.php`, `app/Http/Controllers/Auth/VerificationController.php`, `app/Http/Controllers/Auth/ConfirmPasswordController.php`
  - Views: `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`, `resources/views/auth/passwords/reset.blade.php`, ...
- Roles: 2 vai trò chính là `user` và `admin` (cột `role` enum).
  - Migration users: `database/migrations/2014_10_12_000000_create_users_table.php`
  - Bảo vệ admin: `app/Http/Middleware/EnsureAdmin.php`, `app/Models/User.php:canAccessFilament()`, `app/Http/Kernel.php`
- Giới hạn chỉ role “user” mới được bình luận: kiểm tra role trong route POST comment + middleware `auth`.
  - Route: `routes/web.php` (POST `/product/{product}/comments`)
- Mật khẩu lưu thế nào: băm bằng bcrypt qua `Hash::make` khi đăng ký.
  - `app/Http/Controllers/Auth/RegisterController.php`
- `remember_token` dùng để “remember me” (ghi nhớ đăng nhập) trong phiên làm việc dài.
  - `database/migrations/2014_10_12_000000_create_users_table.php`
- Sanctum / API token: có dùng Sanctum (`HasApiTokens` trên `App\Models\User`) và route mẫu `/api/user`.
  - `composer.json`, `app/Models/User.php`, `routes/api.php`
- Cơ chế bảo mật Laravel sử dụng: CSRF (`App\Http\Middleware\VerifyCsrfToken` trong nhóm `web`), hash mật khẩu, session auth, middleware `auth`, (có sẵn cấu trúc email verification nếu bật), throttle cho API.
  - `app/Http/Kernel.php`, `app/Http/Middleware/VerifyCsrfToken.php`

## 🛍️ Duyệt sản phẩm, tìm kiếm & lọc
- Hiển thị danh sách: `ShopController@index` lấy categories (kèm tổng sản phẩm), brands (group), và products (eager load images) theo thứ tự mới nhất.
  - `app/Http/Controllers/ShopController.php`
- Tìm kiếm theo tên/brand/mô tả: sử dụng `LIKE` trong where nhóm theo từ khóa `q`.
  - `app/Http/Controllers/ShopController.php`
- Lọc theo danh mục/brand: các action `category($id)` và `brand($brand)` trả về cùng view.
  - `app/Http/Controllers/ShopController.php`
- Phân trang (pagination): có, `paginate(12)`/`paginate(12)->appends(...)`.
  - `app/Http/Controllers/ShopController.php`


## 💬 Chi tiết sản phẩm & bình luận
- Xem chi tiết: `ProductController@show` nạp `images`, `detail` và danh sách bình luận (kèm user) rồi r ender view.
  - `app/Http/Controllers/ProductController.php`, `resources/views/pages/product/show.blade.php`
- Cấu trúc bảng `comments`: `id` (UUID), `user_id` (bigint FK users), `product_id` (UUID FK products), `content`, timestamps.
  - `database/migrations/2025_09_07_151833_create_comments_table.php`
- Ngăn người chưa đăng nhập bình luận: route dùng `middleware('auth')` và trong handler kiểm tra `role === user` mới cho tạo.
  - `routes/web.php` (POST `/product/{product}/comments`)
- Quan hệ 1–1 products ↔ product_details: `Product::hasOne(ProductDetail::class)`, FK `product_details.product_id`.
  - `app/Models/Product.php`, `database/migrations/2025_08_30_194024_create_product_details_table.php`
- Sửa/xoá bình luận: chưa có ở FE; phía Admin có thể CRUD qua Filament.
  - `app/Filament/Resources/CommentResource.php`

## 🛒 Giỏ hàng & Mã giảm giá
- Cơ chế: lưu giỏ hàng trong session (`session('cart')`), mã giảm giá trong session (`session('coupon')`).
  - Routes: `routes/web.php` (GET `/cart`, POST `/cart/add`, `/cart/update`, `/cart/remove`, `/cart/coupon`, `/cart/coupon/remove`)
- “Thêm vào giỏ”: load sản phẩm + ảnh chính, gộp số lượng nếu đã có, rồi ghi lại `session('cart')`.
  - `routes/web.php` (POST `/cart/add`)
- Dữ liệu giỏ hàng lưu ở đâu/tồn tại bao lâu: lưu trong session; thời gian phụ thuộc cấu hình `session.lifetime` (phút) trong `config/session.php` và driver session.
- Vì sao không lưu DB: đơn giản hóa, tránh ghi DB liên tục cho người chưa checkout; không đồng bộ đa thiết bị (chấp nhận đánh đổi) nhưng nhanh và nhẹ cho frontend.
- Áp dụng mã giảm giá: validate `code`, kiểm tra tồn tại, hạn dùng, số lượt, sau đó set `session('coupon') = {id, code, percent}`.
  - `routes/web.php` (POST `/cart/coupon`)
- Hủy mã giảm giá: xoá `session('coupon')`.
  - `routes/web.php` (POST `/cart/coupon/remove`)
- Tính tổng tiền + giảm giá: dùng `Order::totalsFromCart($cart, $coupon)` để tính `subtotal`, `%`, `discount_amount`, `final_total`.
  - `app/Models/Order.php`
- Kiểm tra tồn kho khi thêm giỏ: hiện chưa có ở FE route; có thể bổ sung check `stock` của product trước khi tăng số lượng.

## 💖 Wishlist, Hồ sơ người dùng & Đơn hàng
- Bảng `wishlists`: `id` (UUID), `user_id` (FK users), `product_id` (UUID FK products), timestamps, unique `(user_id, product_id)` để ngăn trùng một sản phẩm cho cùng user.
  - `database/migrations/2025_09_16_000001_create_wishlists_table.php`
- Tránh thêm trùng: `WishlistController@add` kiểm tra `exists()` trước khi `create()`.
  - `app/Http/Controllers/WishlistController.php`
- Chưa đăng nhập mà bấm “Thêm vào wishlist”: route thuộc nhóm `auth` → sẽ chuyển hướng tới `/login`.
  - `routes/web.php`
- Hồ sơ người dùng: hiển thị thông tin `User` (name/email) và `Customer` (phone/address); cập nhật qua `UserInformationController@update`.
  - `resources/views/pages/user_information.blade.php`, `app/Http/Controllers/UserInformationController.php`
- Lịch sử đơn hàng: tìm `Customer` theo `user_id`, lấy `Order` kèm `items.product.images` cho customer đó.
  - `app/Http/Controllers/OrderController.php`, `resources/views/pages/orders/history.blade.php`
- `orders` vs `order_items`: `orders` (đơn tổng, trạng thái, coupon, total), `order_items` (các dòng sản phẩm: product_id, quantity, price).
  - `database/migrations/2025_08_30_192629_create_orders_table.php`, `database/migrations/2025_08_30_192752_create_order-items_table.php`, `database/migrations/2025_09_13_182416_add_timestamps_to_order_items_table.php`
- Trạng thái đơn hàng dùng ENUM: lợi ích (ràng buộc giá trị hợp lệ, dễ đọc); hạn chế (đổi/expand cần migration, khó i18n trực tiếp, phụ thuộc DB).
  - `database/migrations/2025_08_30_192629_create_orders_table.php`
- UUID làm khoá chính: ưu (khó đoán ID, phù hợp hệ phân tán, tránh lộ số lượng bản ghi); nhược (index lớn hơn, join chậm hơn so với int).
  - Model dùng `HasUuids`: `app/Models/Product.php`, `app/Models/Order.php`, `app/Models/Image.php`, ...
- UUID tạo thế nào: Laravel trait `HasUuids` (dựa trên `Str::uuid()`) tự set khi tạo model.
- Vì sao cài `doctrine/dbal`: để hỗ trợ `->change()` khi đổi kiểu cột (vd tăng precision tiền tệ lên `decimal(15,2)`).
  - `composer.json`, `database/migrations/2025_09_18_111104_adjust_price_precision.php`
- Có dùng trigger MySQL không: không dùng trigger; dùng `DEFAULT CURRENT_TIMESTAMP` + `ON UPDATE CURRENT_TIMESTAMP` cho `updated_at` qua migration.
  - `database/migrations/2025_09_17_180500_update_timestamp_triggers_for_updated_at.php`

## 🖼️ Hình ảnh & Polymorphic
- `imageable_id` và `imageable_type`: cặp cột để ánh xạ đa hình (morph) cho nhiều thực thể có ảnh (Product, News...).
  - Migration: `database/migrations/2025_08_30_183334_create_images_table.php`
  - Model: `app/Models/Image.php` (quan hệ `morphTo()` và accessor `url` qua disk `public`)
- Lưu trữ ảnh: dùng `Storage::disk('public')` → URL public từ `storage/app/public` (cần symbolic link tới `public/storage`).
  - `app/Models/Image.php`
- Xác thực định dạng ảnh: chưa thấy logic riêng ở FE; phần Admin có thể cấu hình validation trong các Filament Resource khi dùng field upload.

## 🧑‍💻 Khu vực quản trị (Admin)
- Chỉ admin truy cập `/admin`: middleware `EnsureAdmin` chặn route `/admin*`; Filament cũng kiểm tra `User::canAccessFilament()` trả về true khi `role=admin`.
  - `app/Http/Middleware/EnsureAdmin.php`, `app/Models/User.php`, `app/Http/Kernel.php`
- Admin làm gì: CRUD cho Category, Product, Order, OrderItem, Coupon, Comment, Contact, Customer, Image, User, Wishlist; trang tùy chỉnh ProductStats.
  - `app/Filament/Resources/*`, `app/Filament/Pages/ProductStats.php`, `resources/views/filament/pages/product-stats.blade.php`
- Lý do dùng gói có sẵn: tốc độ phát triển nhanh, UI/UX tốt, có sẵn form/table/search/filter, policy & authorization tích hợp, giảm lỗi lặp.

## 💳 Thanh toán VNPAY & SEPay
- Luồng VNPAY:
  1) Người dùng đã đăng nhập, có `Customer` (phone/address) và có giỏ hàng session.
  2) `PaymentController@vnpayPayment` tính tổng, tạo bản ghi `payments` ở trạng thái `pending`, ký HMAC và redirect sang VNPAY.
  3) VNPAY redirect về `payment.vnpay.return` kèm tham số `vnp_*`.
  4) `PaymentController@vnpayReturn` xác thực HMAC, nếu thành công thì tạo `orders` + `order_items`, cập nhật `payments` (`success`), tăng `coupons.used_count`, xoá session cart/coupon.
  - `app/Http/Controllers/PaymentController.php`, `routes/web.php`
- Callback VNPAY: endpoint `GET /payment/vnpay/return` (không bắt buộc auth do trình duyệt quay về).
  - `routes/web.php`
- Xác thực callback: sắp xếp params `vnp_*`, băm HMAC `sha512` với `VNPAY_HASH_SECRET` và so sánh `vnp_SecureHash`.
  - `app/Http/Controllers/PaymentController.php`
- Cập nhật đơn hàng sau thanh toán: tạo Order + các OrderItem từ session cart, liên kết coupon nếu còn hạn và số lượt, đổi trạng thái `paid`.
  - `app/Http/Controllers/PaymentController.php`, `app/Models/Order.php`
- Trường hợp đóng trình duyệt giữa chừng: nếu không quay về callback, payment vẫn ở trạng thái `pending` (ghi nhận để đối soát), đơn hàng chưa được tạo.
- Vai trò bảng `payments`: lưu log giao dịch (provider, số tiền, mã tham chiếu, mã ngân hàng, trạng thái, thời điểm thanh toán, payload trả về), liên kết order.
  - `database/migrations/2025_09_20_120000_create_payments_table.php`
- SEPay: bảng `sepay_transactions` để lưu các giao dịch từ SEPay; luồng tích hợp FE chưa được nối vào controller trong repo này.
  - `database/migrations/2025_09_19_103539_create_sepay_table.php`

## 📰 Trang Tin tức & Liên hệ
- Tin tức: lưu ở bảng riêng `news`, có CRUD bên Admin; FE có `NewsController@index|show` và views tương ứng.
  - `database/migrations/2025_08_30_181754_create_news_table.php`, `app/Http/Controllers/NewsController.php`, `resources/views/pages/news/index.blade.php`, `resources/views/pages/news/show.blade.php`
- Liên hệ: form `/contact` validate dữ liệu và `Contact::create(...)` để lưu vào bảng `contacts`.
  - Route: `routes/web.php` (GET/POST `/contact`)
  - Bảng/Model: `database/migrations/2025_09_13_200000_create_contacts_table.php`, `database/migrations/2025_09_17_120100_add_customer_id_to_contacts_table.php`, `app/Models/Contact.php`

