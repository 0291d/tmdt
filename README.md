Chức Năng Theo Vai Trò
- Khách hàng
  - Trang chủ: banner, danh mục nổi bật, sản phẩm mới, tin tức.
  - Duyệt sản phẩm: theo danh mục, tìm kiếm, sắp xếp, phân trang.
  - Chi tiết sản phẩm: ảnh, mô tả, thông số, sản phẩm liên quan.
  - Giỏ hàng: thêm/sửa/xóa, cập nhật số lượng, tính tổng.
  - Mã giảm giá: nhập/áp mã, kiểm tra hiệu lực, khấu trừ vào tổng.
  - Thanh toán: tạo đơn hàng và các dòng chi tiết giỏ hàng.
  - Tài khoản: đăng ký/đăng nhập, cập nhật thông tin.
  - Wishlist: thêm/xóa yêu thích, trang danh sách yêu thích.
  - Bình luận: gửi nhận xét sản phẩm khi đã đăng nhập.
  - Liên hệ: gửi form hỗ trợ, lưu vào cơ sở dữ liệu.
  - Reset mật khẩu: nhập email của tài khoản để nhận link reset mật khẩu (sử dụng docker để test chức năng)
- Quản trị (Filament) 
  - url: localhost/tmdt/public/admin 
  - Account: 
      TK: admin@gmail.com
      Pass: 123456  
  - Danh mục: CRUD danh mục sản phẩm.
  - Sản phẩm: CRUD sản phẩm, quản lý ảnh (Image), chi tiết sản phẩm (ProductDetail).
  - Đơn hàng: xem danh sách, chi tiết; quản lý `Order` và `OrderItem`.
  - Mã giảm giá (Coupon): tạo/sửa/xóa, thiết lập giá trị, hạn dùng, giới hạn.
  - Nội dung (News): CRUD tin tức; bình luận (Comment) duyệt/xóa.
  - Khách hàng & Người dùng: quản lý `Customer` và `User`.
  - Wishlist: xem/loại bỏ mục yêu thích nếu cần.
  - Thống kê: trang thống kê sản phẩm mẫu tại `resources/views/filament/pages/product-stats.blade.php`.

Tệp/Thành Phần Chính
- Routes web: `routes/web.php`
- Giao diện chính: `resources/views/layouts/layout.blade.php`
- Trang khách hàng: `resources/views/pages/*.blade.php`
- Auth & đăng ký: `resources/views/auth/*.blade.php`, `app/Http/Controllers/Auth/RegisterController.php`
- Models: `app/Models/` (Product, ProductDetail, Category, Image, Comment, News, Coupon, Order, OrderItem, Customer, Contact, Wishlist, User,…)
- Filament Resources:
  - `app/Filament/Resources/CategoryResource.php`
  - `app/Filament/Resources/ProductResource.php`
  - `app/Filament/Resources/ProductDetailResource.php`
  - `app/Filament/Resources/OrderResource.php`, `app/Filament/Resources/OrderitemResource.php`
  - `app/Filament/Resources/ImageResource.php`, `app/Filament/Resources/CommentResource.php`
  - `app/Filament/Resources/CouponResource.php`, `app/Filament/Resources/NewsResource.php`
  - `app/Filament/Resources/CustomerResource.php`, `app/Filament/Resources/UserResource.php`
  - `app/Filament/Resources/WishlistResource.php`
- Middleware: `app/Http/Middleware/RequireLoginWithMessage.php` (yêu cầu đăng nhập với thông báo thân thiện).

Luồng Nghiệp Vụ Cốt Lõi
- Thêm giỏ hàng: POST đến route giỏ hàng, lưu vào session, hiển thị tại `resources/views/pages/cart.blade.php`.
- Áp mã giảm giá: kiểm tra mã (còn hạn, số lần dùng, trạng thái), lưu vào session, cập nhật tổng tiền.
- Thanh toán: tạo `orders` và `order_items`, cập nhật số lượt sử dụng coupon.
- Wishlist: thêm/xóa mục yêu thích (cần đăng nhập), trang `resources/views/pages/wishlist.blade.php`.
- Bình luận: POST `/product/{product}/comments` (cần đăng nhập) → tạo `Comment`.

