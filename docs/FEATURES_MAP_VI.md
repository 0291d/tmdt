BREW Furniture Shop — Bản đồ tính năng & vai trò tệp (Tiếng Việt)

Tài liệu này liệt kê các chức năng chính và giải thích vai trò của từng tệp quan trọng trong dự án.

1) Định tuyến (Routes)
- routes/web.php: Khai báo toàn bộ route giao diện người dùng.
  - Trang chủ: GET `/` → HomeController@index.
  - Auth: `Auth::routes()` đăng nhập/đăng ký mặc định.
  - Shop: GET `/shop`, `/shop/category/{id}`, `/shop/brand/{brand}` → danh sách/lọc sản phẩm.
  - Trang tĩnh: `/about`, `/contact` + POST `/contact` lưu liên hệ vào DB.
  - News: GET `/news`, `/news/{news}` → danh sách/tin chi tiết.
  - Product detail: GET `/product/{product}`.
  - Comment sản phẩm: POST `/product/{product}/comments` (auth, role=user) → tạo Comment.
  - Giỏ hàng: GET `/cart`; POST `/cart/add|update|remove` (middleware `login.notice` thông báo yêu cầu đăng nhập khi cần).
  - Coupon: POST `/cart/coupon`, `/cart/coupon/remove` → áp/hủy mã giảm giá trong session.
  - Checkout: POST `/cart/checkout` → tạo Order + OrderItems từ session cart.
  - User info: GET/POST `/user/info` (auth) → xem/cập nhật thông tin người dùng.
  - Wishlist: GET `/wishlist` (index), POST `/wishlist/add` thêm mục yêu thích (auth).

2) Middleware
- app/Http/Middleware/RequireLoginWithMessage.php: Nếu chưa đăng nhập, chuyển hướng tới `/login` kèm thông báo; dùng cho các hành động giỏ hàng.
- app/Http/Kernel.php: gán alias `login.notice` cho middleware trên.

3) Controllers (ứng dụng người dùng)
- HomeController.php: Chuẩn bị dữ liệu cho trang chủ (ID danh mục theo tên, sản phẩm mới, tin mới) và trả về `pages/index`.
- ShopController.php: Hiển thị danh sách sản phẩm, lọc theo danh mục/brand, xử lý từ khóa tìm kiếm.
- ProductController.php: Trang chi tiết sản phẩm; load ảnh, detail, và danh sách bình luận kèm user.
- NewsController.php: Danh sách tin (phân trang), trang chi tiết tin; kèm ảnh cover nếu có.
- UserInformationController.php: Hiển thị/cập nhật thông tin người dùng (địa chỉ, điện thoại...).
- WishlistController.php: Trang wishlist của user; thêm sản phẩm vào wishlist, tránh trùng.

4) Models (Eloquent)
- Category.php: Danh mục sản phẩm.
- Product.php: Sản phẩm; dùng UUID; thuộc tính fillable; quan hệ: `category`, `detail` (1-1), `images` (morphMany), `comments` (1-n), `orderItems` (1-n), `wishlists` (1-n).
- ProductDetail.php: Kích thước, xuất xứ, hoàn thiện… cho 1 sản phẩm.
- Image.php: Bảng ảnh polymorphic (news, product). Cột `is_main` đánh dấu ảnh chính.
- Comment.php: Bình luận sản phẩm; thuộc `user`, `product`.
- Coupon.php: Mã giảm giá; percent, expiry_date, max_uses, used_count.
- Order.php: Đơn hàng; lưu tổng tiền, phần trăm giảm, số tiền giảm, quan hệ các OrderItem.
- OrderItem.php: Sản phẩm trong đơn hàng.
- Customer.php: Hồ sơ khách hàng (gắn User) chứa địa chỉ/điện thoại.
- Contact.php: Bản ghi liên hệ từ form liên hệ.
- News.php: Bài viết tin tức; quan hệ `images` (morphMany).
- Wishlist.php: Yêu thích; khóa chính UUID; `user_id` số nguyên (foreignId), `product_id` UUID.
- User.php: Tài khoản; quan hệ `customer`, `orders`, `comments`, `wishlists`.

5) Migrations (lược kê tiêu biểu)
- database/migrations/2025_08_30_192629_create_orders_table.php: Cấu trúc bảng orders (có giảm giá). OrderItems ở migration khác (đi kèm model).
- database/migrations/2025_09_16_000001_create_wishlists_table.php: Bảng `wishlists` (id UUID, `user_id` foreignId, `product_id` UUID, ràng buộc unique cặp user-product).

6) Seeders
- database/seeders/DatabaseSeeder.php: Gọi CategorySeeder, ProductSeeder, ProductDetailSeeder, UserSeeder, NewsSeeder...
- Các seeder còn lại khởi tạo dữ liệu mẫu: danh mục/sản phẩm/chi tiết/tin tức.

7) Views (Blade)
- layouts/layout.blade.php: Layout chính; navbar, menu, tìm kiếm; dropdown user (link “Thông tin người dùng”, “Yêu thích”); include CSS & Bootstrap.
- pages/index.blade.php: Trang chủ; banner slide (ảnh trong `public/img`), lưới danh mục, slider sản phẩm mới, tin tức mới.
- pages/shop.blade.php: Danh sách/ô sản phẩm; link vào chi tiết.
- pages/product/show.blade.php: Chi tiết sản phẩm; ảnh dạng slider dọc; bảng thông số; thêm giỏ hàng; nút trái tim (wishlist); khối FEEDBACK (form + danh sách bình luận + bộ đếm ký tự).
- pages/cart.blade.php: Giỏ hàng từ session; cập nhật số lượng/xóa; áp/hủy coupon; tính tổng.
- pages/wishlist.blade.php: Danh sách yêu thích của user; mỗi sản phẩm bọc trong thẻ “wish-card”.
- pages/news/index.blade.php & pages/news/show.blade.php: Danh sách/chi tiết tin tức (hình ảnh + nội dung tóm tắt/đầy đủ).
- pages/contact.blade.php: Form liên hệ; POST lưu vào DB.
- pages/about.blade.php: Trang giới thiệu.
- pages/user_information.blade.php: Thông tin người dùng (địa chỉ/điện thoại...).

8) Tài nguyên tĩnh
- public/css/style.css: Style chung cho trang chủ (banner, slider...), body nền `#f9f9f9`.
- public/css/shop.css, products.css, header.css, navbar.css, footer.css, catagory.css, aboutus.css, contact.css, news.css: Style theo trang/chức năng.
- public/css/detailProducts.css: Toàn bộ CSS cho trang chi tiết: layout cột, ảnh, nút thêm giỏ, FEEDBACK (textarea đếm ký tự, nút gửi), wishlist button, trang wishlist (khoảng trống lớn trước footer).
- public/js/bum.js: Script điều khiển banner slides (auto-slide, indicators...).
- public/img/*: Ảnh tĩnh danh mục, banner, placeholder, v.v.

9) Admin (Filament Resources)
- app/Filament/Resources/*.php: Mỗi resource định nghĩa form/table CRUD trong trang quản trị.
  - CategoryResource, ProductResource, ProductDetailResource: Quản lý sản phẩm & thuộc tính.
  - ImageResource: Quản lý ảnh morph.
  - CommentResource: Quản lý bình luận.
  - CouponResource: Quản lý mã giảm giá.
  - NewsResource: Quản lý tin tức.
  - OrderResource, OrderitemResource: Quản lý đơn hàng và item.
  - CustomerResource, UserResource: Quản lý khách hàng & người dùng.
  - WishlistResource: Quản lý các bản ghi yêu thích (hiển thị ID, người dùng, sản phẩm).

10) Luồng nghiệp vụ chính (tóm tắt)
- Tìm kiếm: Ô search trên layout đọc query `q` và điều hướng tới trang Shop với tham số.
- Thêm giỏ hàng: POST `/cart/add` (login.notice) → lưu vào session `cart`.
- Áp mã giảm giá: POST `/cart/coupon` → kiểm tra tính hợp lệ, lưu `coupon` vào session.
- Thanh toán: POST `/cart/checkout` (auth) → tạo Order + OrderItems; cập nhật `used_count` cho coupon.
- Bình luận: POST `/product/{product}/comments` (auth + role=user) → tạo Comment; FEEDBACK render danh sách.
- Wishlist: POST `/wishlist/add` (auth) → tạo record nếu chưa tồn tại; trang `/wishlist` liệt kê.

11) Ghi chú & thực hành tốt
- Ảnh tĩnh nên tham chiếu bằng `asset('img/...')` để đúng base URL trên XAMPP/thư mục con.
- Đã thêm query version cho `detailProducts.css` trong layout để tránh cache khi sửa CSS.
- IDs: Product/Wishlist.id dùng UUID; User.id dùng big integer; Wishlist.user_id là foreignId để khớp users.

