# Báo cáo tổng kết dự án TMĐT (Laravel)

Cập nhật: 2025-10-02

## 1) Kết quả đạt được
- Routing & luồng người dùng: trang chủ, shop (tìm kiếm, lọc danh mục/brand), chi tiết sản phẩm, tin tức, about/contact, giỏ hàng, wishlist, hồ sơ người dùng, lịch sử đơn hàng (routes/web.php).
- Cart & Coupon: giỏ hàng theo session; thêm/cập nhật/xóa; áp/hủy mã giảm giá, tính toán tổng tiền và giảm giá (routes/web.php, app/Models/Order.php:totalsFromCart).
- Thanh toán VNPAY: tạo giao dịch, ký tham số, redirect tới cổng; nhận callback, xác thực hash SHA512, tạo Order + OrderItems, gắn coupon, cập nhật Payment, dọn session (app/Http/Controllers/PaymentController.php).
- Bình luận sản phẩm: người dùng role=user đăng bình luận; hiển thị kèm thông tin user (routes/web.php, app/Http/Controllers/ProductController.php).
- Wishlist: thêm idempotent và trang danh sách wishlist của user (app/Http/Controllers/WishlistController.php, resources/views/pages/wishlist.blade.php).
- Hồ sơ người dùng: cập nhật số điện thoại/địa chỉ với validate, ràng buộc cập nhật trước thanh toán (app/Http/Controllers/UserInformationController.php).
- Lịch sử đơn hàng: trang tổng hợp đơn đã mua, hiển thị item, tổng tiền, trạng thái (app/Http/Controllers/OrderController.php, resources/views/pages/orders/history.blade.php).
- Middleware: nhắc đăng nhập khi thao tác cần thiết (app/Http/Middleware/RequireLoginWithMessage.php), chặn truy cập admin nếu không phải admin (app/Http/Middleware/EnsureAdmin.php).
- Migrations & Models: đầy đủ bảng sản phẩm, chi tiết, hình ảnh (morph), tin tức, bình luận, coupon, khách hàng, đơn hàng, order_items, payments, wishlists; quan hệ Eloquent tương ứng (app/Models/*, database/migrations/*).
- Admin (Filament): tài nguyên CRUD cho danh mục, sản phẩm, chi tiết sản phẩm, hình ảnh, bình luận, coupon, tin tức, đơn hàng, order item, khách hàng, người dùng, wishlist (app/Filament/Resources/*).
- Giao diện: Blade layout, các trang shop, chi tiết SP (kèm feedback), giỏ hàng, wishlist, about, contact, tin tức, lịch sử đơn hàng; CSS/JS tĩnh phục vụ layout và slider.

## 2) Nội dung chưa đạt/thiếu
- Kiểm thử: tài liệu test chỉ có Expected, chưa ghi nhận Actual (docs/TEST_CASES.md); chưa có test tự động cho luồng chính, chưa có test tích hợp VNPAY sandbox.
- Tồn tại sai lệch kiểu dữ liệu giá: DB dùng decimal(15,2) nhưng model `Product` cast `price` thành integer, nguy cơ làm tròn/mất phần thập phân (app/Models/Product.php, database/migrations/2025_09_18_111104_adjust_price_precision.php).
- Quản lý tồn kho: chưa giảm `stock` khi đơn hàng thành công; không khóa tồn, thiếu kiểm soát over‑sell.
- Phí vận chuyển & phương thức giao hàng: chưa có tính phí/tuỳ chọn ship trong Order; chưa có địa chỉ giao hàng riêng cho đơn.
- Quy trình trạng thái đơn: mới set `paid` sau thanh toán; thiếu chuyển `completed/cancelled`, thiếu hành động hậu kiểm (fulfillment, hủy, hoàn tiền).
- Bảo mật/giới hạn: bình luận chưa có rate‑limit/anti‑spam; coupon chưa có ràng buộc người dùng/lượt dùng theo user; cạnh tranh tăng `used_count` chưa có khoá.
- Trải nghiệm giỏ hàng: giỏ hàng theo session, không đồng bộ đa thiết bị; chưa có lưu DB theo user.
- Thông báo: chưa gửi email/notification cho đặt hàng thành công, cập nhật trạng thái, liên hệ.
- Nhật ký & quan sát: thiếu logging có cấu trúc, thiếu theo dõi lỗi/tỷ lệ chuyển đổi thanh toán.
- API: chưa có API REST cho mobile/SPA; chưa có phân quyền/guard riêng cho API.
- CI/CD: chưa cấu hình pipeline build/test/deploy.

## 3) Định hướng phát triển
- Hoàn thiện kiểm thử
  - Viết Feature test cho: giỏ hàng, coupon, wishlist, bình luận, lịch sử đơn, hồ sơ người dùng, VNPAY callback (mock/sandbox).
  - Thêm bộ test dữ liệu seed; cập nhật trường “Actual” trong docs/TEST_CASES.md khi chạy manual.
- Chuẩn hóa dữ liệu tiền tệ
  - Đổi cast `Product::$casts['price']` sang decimal/string; chuẩn hóa mọi tính toán về số nguyên (vnđ) hoặc decimal nhất quán.
- Quản lý tồn kho & đơn hàng
  - Trừ tồn kho khi tạo OrderItems thành công; rollback khi thanh toán fail.
  - Bổ sung trạng thái `processing/completed/cancelled` và các hành động quản trị tương ứng (Filament actions).
- Vận chuyển & phí
  - Thêm bảng `shipping_methods`, cấu hình phí; tính tổng đơn có ship fee, hiển thị trên UI và lưu vào Order.
- Nâng cao bảo mật & độ tin cậy
  - Thêm throttle/bảo vệ spam cho bình luận; xác thực nội dung.
  - Khoá lạc quan/bi quan khi tăng `Coupon.used_count`; kiểm tra đồng thời.
  - Ký và xác thực webhook/callback; log giao dịch đầy đủ (Payment.payload).
- Trải nghiệm người dùng
  - Đồng bộ giỏ hàng vào DB theo user; merge giỏ guest sau khi đăng nhập.
  - Gửi email xác nhận đơn hàng, trạng thái; trang chi tiết đơn hàng.
- Quan sát & vận hành
  - Bổ sung logging có cấu trúc, dashboard đơn/thu nhập; alert lỗi thanh toán.
  - Thiết lập CI chạy test/lint; workflow deploy.
- API & mở rộng kênh
  - Xây dựng API REST: sản phẩm, giỏ hàng, đơn, thanh toán; chuẩn hoá auth/token.

Tài liệu tham chiếu chính: docs/FEATURES_MAP_VI.md, docs/TEST_CASES.md, routes/web.php, app/Http/Controllers/*, app/Models/*, database/migrations/*.

