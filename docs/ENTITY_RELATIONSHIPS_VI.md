Bản Đồ Thực Thể & Quan Hệ (VI)

Mục tiêu: Tóm tắt các bảng, thuộc tính chính và mối quan hệ trong CSDL để hỗ trợ phân tích nhanh và viết truy vấn/feature.

Ghi Chú Chung
- Khóa chính hỗn hợp: một số bảng dùng `BIGINT` tự tăng (vd: `users`, `categories`, `news`), nhiều bảng domain dùng `UUID` (vd: `products`, `orders`, `order_items`, `product_details`, `images`, `coupons`, `customers`, `wishlists`, `contacts`, `comments`).
- Chuẩn EOL: LF theo `.gitattributes`.
- Filament dùng cho CRUD; ngoài ra repo có thêm các bảng của laravel-admin (không phải domain chính).

Users (`users`)
- Thuộc tính: `id` BIGINT PK, `name`, `email` UNIQUE, `email_verified_at` nullable, `password`, `role` ENUM[`user`,`admin`] default `user`, `remember_token`, `timestamps`.
- Quan hệ: `hasOne(Customer)`, `hasMany(Order)`, `hasMany(Comment)`, `hasMany(Wishlist)`.

Customers (`customers`)
- Thuộc tính: `id` UUID PK, `user_id` FK→`users.id`, `phone`, `address`, `timestamps`.
- Quan hệ: `belongsTo(User)`, `hasMany(Order)`, `hasMany(Contact)`.

Categories (`categories`)
- Thuộc tính: `id` BIGINT PK, `name`, `timestamps`.
- Quan hệ: `hasMany(Product)`.

Products (`products`)
- Thuộc tính: `id` UUID PK, `category_id` FK→`categories.id`, `name`, `brand`, `description` nullable, `price` DECIMAL(10,2), `stock` INT nullable, `timestamps`.
- Quan hệ: `belongsTo(Category)`, `hasOne(ProductDetail)`, `morphMany(Image)`, `hasMany(Comment)`, `hasMany(OrderItem)`, `hasMany(Wishlist)`.

Product Details (`product_details`)
- Thuộc tính: `id` UUID PK, `product_id` FK→`products.id`, `width` INT, `length` INT, `height` INT, `origin` TEXT, `finishes` TEXT, `timestamps`.
- Quan hệ: `belongsTo(Product)`.
- Ghi chú: Model thiết kế 1–1 (Product `hasOne` ProductDetail). Ràng buộc UNIQUE trên `product_id` đã được drop, nên đảm bảo 1–1 ở tầng ứng dụng.

Images (`images`) — Polymorphic
- Thuộc tính: `id` UUID PK, `imageable_id` UUID, `imageable_type` STRING, `path` STRING, `is_main` BOOL default false, `timestamps`, INDEX (`imageable_id`,`imageable_type`).
- Quan hệ: `morphTo(imageable)`.
- Được dùng bởi: `Product`, `News` (có `morphMany(Image)`).

News (`news`)
- Thuộc tính: `id` BIGINT PK, `title` STRING, `content` TEXT, `timestamps`.
- Quan hệ: `morphMany(Image)`.

Coupons (`coupons`)
- Thuộc tính: `id` UUID PK, `code` UNIQUE, `discount_percent` INT, `expiry_date` DATE, `max_uses` INT default 1, `used_count` INT default 0, `timestamps`.
- Quan hệ: `hasMany(Order)`.

Orders (`orders`)
- Thuộc tính: `id` UUID PK, `customer_id` UUID FK→`customers.id`, `coupon_id` UUID nullable FK→`coupons.id`, `total` DECIMAL(10,2) default 0, `status` ENUM[`pending`,`paid`,`completed`,`cancelled`] default `pending`, `timestamps`.
- Bổ sung: `discount_percent` INT default 0, `discount_amount` INT default 0 (migration 2025_09_13_203000).
- Quan hệ: `belongsTo(Customer)`, `belongsTo(Coupon)`, `hasMany(OrderItem)`.

Order Items (`order_items`)
- Thuộc tính: `id` UUID PK, `order_id` UUID FK→`orders.id`, `product_id` UUID FK→`products.id`, `quantity` INT, `price` DECIMAL(10,2) (đơn giá tại thời điểm đặt), `timestamps` (bổ sung sau).
- Quan hệ: `belongsTo(Order)`, `belongsTo(Product)`.

Comments (`comments`)
- Thuộc tính: `id` UUID PK, `user_id` BIGINT FK→`users.id`, `product_id` UUID nullable FK→`products.id`, `content` TEXT, `timestamps`.
- Quan hệ: `belongsTo(User)`, `belongsTo(Product)`.

Wishlists (`wishlists`)
- Thuộc tính: `id` UUID PK, `user_id` BIGINT FK→`users.id`, `product_id` UUID FK→`products.id`, `timestamps`, UNIQUE(`user_id`,`product_id`).
- Quan hệ: `belongsTo(User)`, `belongsTo(Product)`.

Contacts (`contacts`)
- Thuộc tính: `id` UUID PK, `customer_id` UUID FK→`customers.id`, `full_name` STRING, `address` STRING, `phone` STRING, `email` STRING, `content` TEXT, `timestamps`.
- Quan hệ: `belongsTo(Customer)`.

Tổng Quan Quan Hệ (Cardinality)
- `User` 1–1 `Customer` (qua `user_id`).
- `Category` 1–n `Product`.
- `Product` 1–1 `ProductDetail` (thiết kế; DB không unique).
- `Product` 1–n `Image` (polymorphic), `News` 1–n `Image`.
- `Product` 1–n `Comment`, `User` 1–n `Comment`.
- `Customer` 1–n `Order`, `Order` 1–n `OrderItem`, `Product` 1–n `OrderItem`.
- `Coupon` 1–n `Order` (một đơn có thể null coupon).
- `User` 1–n `Wishlist`, `Product` 1–n `Wishlist` (unique theo cặp người dùng + sản phẩm).
- `Customer` 1–n `Contact`.

Ràng Buộc/Chính Sách Dữ Liệu
- Xóa chuỗi: nhiều FK cấu hình `cascadeOnDelete()` (vd: `orders → order_items`, `products → images/order_items/comments/wishlists`).
- Tính tổng đơn: `OrderItem` có hooks (model boot) để tự cập nhật `Order.total` khi thêm/sửa/xóa item.
- Quy tắc coupon: theo trường `discount_percent`, `expiry_date`, `max_uses`, `used_count` (logic sử dụng trên layer ứng dụng và routes).

Gợi Ý Truy Vấn/Index
- Tra cứu hình polymorphic: dùng INDEX (`imageable_id`,`imageable_type`).
- Anti-dup wishlist: UNIQUE(`user_id`,`product_id`).
- Tra cứu đơn theo khách: INDEX ngầm trên FK `customer_id` 

