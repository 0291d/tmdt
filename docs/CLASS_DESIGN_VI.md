# Thiết Kế Lớp (Class) & Tương Tác (VI)

Mục tiêu: Từ bản mô tả thực thể trong `docs/ENTITY_RELATIONSHIPS_VI.md`, liệt kê các lớp (class) tương ứng trong tầng Model, mô tả chi tiết trách nhiệm và định nghĩa quan hệ giữa các class, kèm các ghi chú về tương tác.

## Nguyên tắc chung
- ID: domain chính dùng hỗn hợp BIGINT (vd: `users`, `categories`, `news`) và UUID (vd: `products`, `orders`, `order_items`, `product_details`, `images`, `coupons`, `customers`, `wishlists`, `contacts`, `comments`).
- Quan hệ sử dụng Eloquent: `belongsTo`, `hasOne`, `hasMany`, `morphTo`, `morphMany` theo mô tả.
- Xóa chuỗi (cascade): nhiều FK được cấu hình cascade để đảm bảo toàn vẹn dữ liệu.

---

## App\Models\User (bảng `users`)
- Khoá chính: `id` BIGINT.
- Thuộc tính chính: `name`, `email` (unique), `email_verified_at` (nullable), `password`, `role` enum[`user`,`admin`] (mặc định `user`), `remember_token`, `timestamps`.
- Quan hệ:
  - `hasOne(Customer)` – người dùng có hồ sơ khách hàng.
  - `hasMany(Order)` – lịch sử đơn hàng qua `customer` (gián tiếp) hoặc quan hệ trực tiếp nếu có.
  - `hasMany(Comment)` – bình luận do người dùng tạo.
  - `hasMany(Wishlist)` – các dòng sản phẩm yêu thích.
- Trách nhiệm/Ghi chú:
  - Quản lý xác thực, phân quyền; kết nối đến dữ liệu khách hàng/hoạt động mua sắm và tương tác.

## App\Models\Customer (bảng `customers`)
- Khoá chính: `id` UUID; FK: `user_id` → `users.id`.
- Thuộc tính chính: `phone`, `address`, `timestamps`.
- Quan hệ:
  - `belongsTo(User)` – thuộc về một người dùng.
  - `hasMany(Order)` – các đơn hàng của khách.
  - `hasMany(Contact)` – các yêu cầu liên hệ của khách.
- Trách nhiệm/Ghi chú:
  - Lớp đại diện hồ sơ khách; là gốc quan hệ cho đặt hàng và liên hệ.

## App\Models\Category (bảng `categories`)
- Khoá chính: `id` BIGINT.
- Thuộc tính chính: `name`, `timestamps`.
- Quan hệ:
  - `hasMany(Product)` – danh sách sản phẩm trong danh mục.
- Trách nhiệm/Ghi chú:
  - Phân loại sản phẩm; thường dùng để lọc/duyệt.

## App\Models\Product (bảng `products`)
- Khoá chính: `id` UUID; FK: `category_id` → `categories.id`.
- Thuộc tính chính: `name`, `brand`, `description` (nullable), `price` DECIMAL(10,2), `stock` INT (nullable), `timestamps`.
- Quan hệ:
  - `belongsTo(Category)` – thuộc về một danh mục.
  - `hasOne(ProductDetail)` – chi tiết kỹ thuật 1-1.
  - `morphMany(Image)` – bộ ảnh (có ảnh chính `is_main`).
  - `hasMany(Comment)` – bình luận cho sản phẩm.
  - `hasMany(OrderItem)` – xuất hiện trong các dòng đơn hàng.
  - `hasMany(Wishlist)` – các yêu thích tham chiếu tới sản phẩm.
- Trách nhiệm/Ghi chú:
  - Thực thể trung tâm của bán hàng; xóa sản phẩm kéo theo xóa ảnh/bình luận/wishlist/order_items (nếu cấu hình cascade).

## App\Models\ProductDetail (bảng `product_details`)
- Khoá chính: `id` UUID; FK: `product_id` → `products.id`.
- Thuộc tính chính: `width`, `length`, `height`, `origin` TEXT, `finishes` TEXT, `timestamps`.
- Quan hệ:
  - `belongsTo(Product)`.
- Trách nhiệm/Ghi chú:
  - Chi tiết kỹ thuật 1-1 với `Product`. Về DB, ràng buộc UNIQUE trên `product_id` có thể không bật; đảm bảo 1-1 ở tầng ứng dụng.

## App\Models\Image (bảng `images`) – polymorphic
- Khoá chính: `id` UUID.
- Thuộc tính chính: `imageable_id` UUID, `imageable_type` STRING, `path` STRING, `is_main` BOOL (mặc định false), `timestamps`.
- Quan hệ:
  - `morphTo(imageable)` – có thể thuộc `Product` hoặc `News`.
- Trách nhiệm/Ghi chú:
  - Lưu đa hình cho ảnh; dùng INDEX (`imageable_id`, `imageable_type`). Ảnh chính xác định bằng `is_main`.

## App\Models\News (bảng `news`)
- Khoá chính: `id` BIGINT.
- Thuộc tính chính: `title` STRING, `content` TEXT, `timestamps`.
- Quan hệ:
  - `morphMany(Image)` – bộ ảnh cho tin bài.
- Trách nhiệm/Ghi chú:
  - Nội dung tin tức/giới thiệu, chia sẻ cơ chế ảnh với `Product` qua polymorphic.

## App\Models\Coupon (bảng `coupons`)
- Khoá chính: `id` UUID.
- Thuộc tính chính: `code` UNIQUE, `discount_percent` INT, `expiry_date` DATE, `max_uses` INT (mặc định 1), `used_count` INT (mặc định 0), `timestamps`.
- Quan hệ:
  - `hasMany(Order)` – các đơn hàng áp dụng mã này (có thể null ở đơn hàng).
- Trách nhiệm/Ghi chú:
  - Logic hợp lệ dựa trên `discount_percent`, `expiry_date`, `max_uses`, `used_count` (áp dụng ở tầng ứng dụng/routes). Thường kèm các kiểm tra hợp lệ trước khi gán vào `Order`.

## App\Models\Order (bảng `orders`)
- Khoá chính: `id` UUID; FK: `customer_id` → `customers.id`, `coupon_id` (nullable) → `coupons.id`.
- Thuộc tính chính: `total` DECIMAL(10,2) (mặc định 0), `status` enum[`pending`,`paid`,`completed`,`cancelled`] (mặc định `pending`), `discount_percent` INT (mặc định 0), `discount_amount` INT (mặc định 0), `timestamps`.
- Quan hệ:
  - `belongsTo(Customer)` – chủ sở hữu đơn.
  - `belongsTo(Coupon)` – mã giảm giá (có thể null).
  - `hasMany(OrderItem)` – các dòng sản phẩm trong đơn.
- Trách nhiệm/Ghi chú:
  - Tổng đơn được cập nhật từ `OrderItem` (xem hook ở `OrderItem`). Áp dụng chiết khấu khi có `Coupon`.

## App\Models\OrderItem (bảng `order_items`)
- Khoá chính: `id` UUID; FK: `order_id` → `orders.id`, `product_id` → `products.id`.
- Thuộc tính chính: `quantity` INT, `price` DECIMAL(10,2) (đơn giá tại thời điểm đặt), `timestamps`.
- Quan hệ:
  - `belongsTo(Order)`.
  - `belongsTo(Product)`.
- Trách nhiệm/Ghi chú:
  - Hook (model boot): tự động cộng/trừ lại `Order.total` khi thêm/sửa/xóa dòng; đảm bảo tổng luôn nhất quán với các `OrderItem`.

## App\Models\Comment (bảng `comments`)
- Khoá chính: `id` UUID; FK: `user_id` → `users.id`, `product_id` (nullable) → `products.id`.
- Thuộc tính chính: `content` TEXT, `timestamps`.
- Quan hệ:
  - `belongsTo(User)`.
  - `belongsTo(Product)`.
- Trách nhiệm/Ghi chú:
  - Bình luận cho sản phẩm; có thể mở rộng để gắn với thực thể khác nếu cần.

## App\Models\Wishlist (bảng `wishlists`)
- Khoá chính: `id` UUID; FK: `user_id` → `users.id`, `product_id` → `products.id`.
- Thuộc tính chính: `timestamps`; ràng buộc UNIQUE(`user_id`,`product_id`).
- Quan hệ:
  - `belongsTo(User)`.
  - `belongsTo(Product)`.
- Trách nhiệm/Ghi chú:
  - Chống trùng nhờ UNIQUE cặp người dùng + sản phẩm; dùng cho tính năng Yêu thích.

## App\Models\Contact (bảng `contacts`)
- Khoá chính: `id` UUID; FK: `customer_id` → `customers.id`.
- Thuộc tính chính: `full_name`, `address`, `phone`, `email`, `content`, `timestamps`.
- Quan hệ:
  - `belongsTo(Customer)`.
- Trách nhiệm/Ghi chú:
  - Lưu các yêu cầu liên hệ/hỗ trợ từ khách hàng.

---

## Tương tác & Luồng chính giữa các class
- User ↔ Customer
  - 1-1: `User.hasOne(Customer)`. Mọi luồng đặt hàng/ liên hệ của khách đi qua `Customer` gắn với `User`.
- Catalog
  - `Category.hasMany(Product)`; `Product.hasOne(ProductDetail)`; `Product.morphMany(Image)` (quản lý ảnh, trong đó một ảnh có thể là ảnh chính `is_main`).
- Nội dung
  - `News.morphMany(Image)` chia sẻ cơ chế ảnh đa hình với `Product`.
- Tương tác người dùng
  - `User.hasMany(Comment)`, `Product.hasMany(Comment)`: bình luận của người dùng cho sản phẩm.
  - `User.hasMany(Wishlist)`, `Product.hasMany(Wishlist)`: yêu thích với UNIQUE(`user_id`,`product_id`).
- Đơn hàng
  - `Customer.hasMany(Order)`; `Order.hasMany(OrderItem)`; `OrderItem.belongsTo(Product)`.
  - `Coupon.hasMany(Order)`, `Order.belongsTo(Coupon)` (nullable): áp dụng giảm giá khi hợp lệ.
  - Hook tại `OrderItem`: khi thêm/sửa/xóa item, tổng `Order.total` được cập nhật; đồng thời có thể áp dụng lại giảm giá nếu có coupon.
- Toàn vẹn dữ liệu & hiệu năng
  - Cascade delete trên nhiều quan hệ (vd: xóa `Product` sẽ xóa `Image`/`OrderItem`/`Comment`/`Wishlist` liên quan).
  - Chỉ mục: polymorphic `images` dùng INDEX (`imageable_id`,`imageable_type`); UNIQUE trên `wishlists`.

## Gợi ý định nghĩa quan hệ (Eloquent, dạng khái niệm)
- User: `customer()`, `orders()`, `comments()`, `wishlists()`
- Customer: `user()`, `orders()`, `contacts()`
- Category: `products()`
- Product: `category()`, `detail()`, `images()`, `comments()`, `orderItems()`, `wishlists()`
- ProductDetail: `product()`
- Image: `imageable()`
- News: `images()`
- Coupon: `orders()`
- Order: `customer()`, `coupon()`, `items()`
- OrderItem: `order()`, `product()`
- Comment: `user()`, `product()`
- Wishlist: `user()`, `product()`
- Contact: `customer()`

---

## Tóm tắt Cardinality
- User 1–1 Customer
- Category 1–n Product
- Product 1–1 ProductDetail
- Product 1–n Image (polymorphic), News 1–n Image
- Product 1–n Comment, User 1–n Comment
- Customer 1–n Order, Order 1–n OrderItem, Product 1–n OrderItem
- Coupon 1–n Order (Order có thể không có Coupon)
- User 1–n Wishlist, Product 1–n Wishlist (UNIQUE bởi cặp user + product)
- Customer 1–n Contact

Ghi chú: Các chi tiết thuộc tính/quan hệ được trích từ `docs/ENTITY_RELATIONSHIPS_VI.md`. Khi có thay đổi migration về kiểu dữ liệu/precision, cập nhật tương ứng trong Model và tài liệu này.

---

## Phương thức theo code (Models)
- App\Models\User
  - `customer()`: hasOne(Customer)
  - `orders()`: hasMany(Order)
  - `comments()`: hasMany(Comment)
  - `wishlists()`: hasMany(Wishlist)

- App\Models\Customer
  - `user()`: belongsTo(User)
  - `orders()`: hasMany(Order)
  - `contacts()`: hasMany(Contact)

- App\Models\Category
  - `products()`: hasMany(Product)

- App\Models\Product
  - `category()`: belongsTo(Category)
  - `detail()`: hasOne(ProductDetail)
  - `images()`: morphMany(Image, 'imageable')
  - `comments()`: hasMany(Comment)
  - `orderItems()`: hasMany(OrderItem)
  - `wishlists()`: hasMany(Wishlist)

- App\Models\ProductDetail
  - `product()`: belongsTo(Product)

- App\Models\Image
  - `imageable()`: morphTo()
  - `getUrlAttribute()`: accessor trả về URL public của ảnh
  - `existsOnDisk()`: kiểm tra file tồn tại trên disk `public`

- App\Models\News
  - `images()`: morphMany(Image, 'imageable')

- App\Models\Coupon
  - `orders()`: hasMany(Order)

- App\Models\Order
  - `customer()`: belongsTo(Customer)
  - `coupon()`: belongsTo(Coupon)
  - `items()`: hasMany(OrderItem)
  - `setTotalAttribute($value)`: mutator chặn set thủ công, tổng được tính từ OrderItem

- App\Models\OrderItem
  - `order()`: belongsTo(Order)
  - `product()`: belongsTo(Product)
  - `booted()`: lifecycle hooks: `creating` (gán price mặc định từ Product), `saved`/`deleted` (tính lại subtotal/discount/total cho Order)

- App\Models\Comment
  - `user()`: belongsTo(User)
  - `product()`: belongsTo(Product)

- App\Models\Wishlist
  - `user()`: belongsTo(User)
  - `product()`: belongsTo(Product)

- App\Models\Contact
  - `customer()`: belongsTo(Customer)

---
