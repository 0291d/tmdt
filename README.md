**BREW Furniture Shop — Tổng Quan Dự Án**

Dự án là một website thương mại điện tử đơn giản cho cửa hàng nội thất (BREW), xây dựng trên Laravel + Blade, có phần quản trị bằng Filament. Bên dưới là thống kê nhanh các chức năng và gắn với tệp mã tương ứng để tiện tra cứu.

**Tính Năng Chính**
- **Trang chủ:** Banner slide, danh mục nổi bật, sản phẩm mới, tin tức mới.
  - `resources/views/pages/index.blade.php`
  - `public/js/bum.js` (logic slide banner)
  - `public/css/style.css`

- **Danh mục sản phẩm (Shop):** Liệt kê, lọc theo danh mục/brand, tìm kiếm từ thanh tìm kiếm.
  - `app/Http/Controllers/ShopController.php`
  - `resources/views/pages/shop.blade.php`
  - `public/css/shop.css`
  - Route: `routes/web.php:19`–`routes/web.php:21`

- **Chi tiết sản phẩm:** Thông tin chi tiết + ảnh, thêm giỏ hàng, wishlist , bình luận.
  - Controller: `app/Http/Controllers/ProductController.php:1`
  - View: `resources/views/pages/product/show.blade.php:1`
  - CSS: `public/css/detailProducts.css`
  - Model liên quan: `app/Models/Product.php`, `app/Models/ProductDetail.php`, `app/Models/Image.php`, `app/Models/Comment.php`, `app/Models/Wishlist.php`
  - Route: `routes/web.php:44` (show), `routes/web.php` (wishlist & comment)

- **Giỏ hàng (session) + Mã giảm giá:** Thêm/sửa/xóa giỏ; áp/huỷ mã; tính giảm giá.
  - Routes (closure): `routes/web.php:120`–`routes/web.php:178`
  - View: `resources/views/pages/cart.blade.php`
  - Model: `app/Models/Coupon.php`

- **Thanh toán (Checkout) & Đơn hàng:** Tạo `orders` và `order_items` từ giỏ; gắn mã giảm giá.
  - Logic trong route: `routes/web.php:180`–`routes/web.php:235`
  - Model: `app/Models/Order.php`, `app/Models/OrderItem.php`
  - Migration ví dụ: `database/migrations/2025_08_30_192629_create_orders_table.php`

- **Tin tức (News):** Danh sách + chi tiết, ảnh mô tả.
  - Controller: `app/Http/Controllers/NewsController.php` (nếu có; routes chỉ định)
  - Model: `app/Models/News.php`
  - View: hiển thị preview ở trang chủ `resources/views/pages/index.blade.php`

- **Liên hệ:** Form lưu thông tin liên hệ vào DB.
  - Route: `routes/web.php:25`–`routes/web.php:40`
  - Model: `app/Models/Contact.php`
  - View: `resources/views/pages/contact.blade.php`

- **Bình luận sản phẩm (FEEDBACK):** User role=user mới được gửi; hiển thị danh sách bình luận.
  - Model: `app/Models/Comment.php`
  - Route POST: `routes/web.php` (đường dẫn `/product/{product}/comments`)
  - View: phần FEEDBACK trong `resources/views/pages/product/show.blade.php`

- **Wishlist (Yêu thích):** Nhấn trái tim để thêm; trang riêng liệt kê sản phẩm yêu thích.
  - Model: `app/Models/Wishlist.php`
  - Controller: `app/Http/Controllers/WishlistController.php`
  - View: `resources/views/pages/wishlist.blade.php`
  - Routes: `routes/web.php` (nhóm `/wishlist`)
  - Migration: `database/migrations/2025_09_16_000001_create_wishlists_table.php`

- **Tài khoản người dùng:** Đăng nhập/đăng ký (Auth scaffolding), thông tin user, menu người dùng.
  - Auth routes: `routes/web.php:11`
  - Thông tin tài khoản: `app/Http/Controllers/UserInformationController.php` (show/update), routes nhóm `auth`
  - Layout: `resources/views/layouts/layout.blade.php` (menu người dùng, link “Yêu thích”, “Thông tin người dùng”)

- **Quản trị (Filament):** CRUD cho các bảng chính.
  - Category: `app/Filament/Resources/CategoryResource.php`
  - Product: `app/Filament/Resources/ProductResource.php`
  - Product Detail: `app/Filament/Resources/ProductDetailResource.php`
  - Order / OrderItem: `app/Filament/Resources/OrderResource.php`, `app/Filament/Resources/OrderitemResource.php`
  - Image: `app/Filament/Resources/ImageResource.php`
  - Coupon: `app/Filament/Resources/CouponResource.php`
  - Comment: `app/Filament/Resources/CommentResource.php`
  - News: `app/Filament/Resources/NewsResource.php`
  - Customer, User: `app/Filament/Resources/CustomerResource.php`, `app/Filament/Resources/UserResource.php`
  - Wishlist: `app/Filament/Resources/WishlistResource.php`

**Kiến Trúc & Tệp Quan Trọng**
- **Routes:** `routes/web.php`
- **Controllers:**
  - `app/Http/Controllers/HomeController.php`
  - `app/Http/Controllers/ShopController.php`
  - `app/Http/Controllers/ProductController.php`
  - `app/Http/Controllers/NewsController.php`, `app/Http/Controllers/WishlistController.php`
  - `app/Http/Controllers/UserInformationController.php`
- **Middleware:** `app/Http/Middleware/RequireLoginWithMessage.php` (alias `login.notice`)
- **Models:** `app/Models/*.php` (Product, ProductDetail, Category, Image, Comment, News, Coupon, Order, OrderItem, Customer, Contact, Wishlist, User...)
- **Views:** `resources/views/pages/*.blade.php`, `resources/views/layouts/layout.blade.php`
- **Assets:** `public/css/*.css`, `public/js/*.js`, `public/img/*`

**CSDL & Quan Hệ**
- Product 1–1 ProductDetail: `Product::detail()` — `app/Models/Product.php:22`
- Product 1–n Image (morph): `Product::images()` — `app/Models/Product.php:27`
- Product 1–n Comment: `Product::comments()` — `app/Models/Product.php:32`
- Product 1–n OrderItem: `Product::orderItems()` — `app/Models/Product.php:37`
- Product 1–n Wishlist: `Product::wishlists()` — `app/Models/Product.php:42`
- User 1–n Comment: `User::comments()` — `app/Models/User.php:40`
- User 1–n Wishlist: `User::wishlists()` — `app/Models/User.php:45`
- News 1–n Image (morph): `app/Models/News.php:12`

Migrations tiêu biểu:
- Đơn hàng: `database/migrations/2025_08_30_192629_create_orders_table.php`
- Wishlist: `database/migrations/2025_09_16_000001_create_wishlists_table.php`

**Luồng Chức Năng Nổi Bật**
- Thêm vào giỏ: POST `cart.add` → session cart → `resources/views/pages/cart.blade.php`
- Áp mã giảm giá: POST `cart.coupon.apply` → kiểm tra hạn mức và thời hạn → session `coupon`
- Thanh toán: POST `cart.checkout` → tạo `orders` và `order_items`, tăng `used_count` cho coupon
- Gửi bình luận: POST `/product/{product}/comments` (auth, role=user) → `Comment::create`
- Yêu thích: POST `/wishlist/add` (auth) → `Wishlist::create` nếu chưa tồn tại


