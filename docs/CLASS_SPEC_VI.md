# Đặc Tả Lớp Dự Án (VI)

Mục tiêu: Liệt kê các lớp thực tế trong dự án (app/Models), mô tả Thuộc tính, Phương thức (hành động nghiệp vụ, không phải quan hệ), và Quan hệ Eloquent. Các ví dụ Payment/Shipping/Admin không có trong dự án nên không đưa vào file này.

Nguồn tham chiếu: docs/ENTITY_RELATIONSHIPS_VI.md và mã nguồn trong app/Models.

---

## App\Models\Order
- Thuộc tính: id, customer_id, coupon_id, total, discount_percent, discount_amount, status, timestamps
- Phương thức (hành động):
  - addOrderItem(product, quantity, price?)
  - updateOrderItem(orderItem|id, quantity, price?)
  - removeOrderItem(orderItem|id)
  - applyCoupon(code)
  - removeCoupon()
  - calculateTotal() → { subtotal, discount, total }
  - refreshTotals()
  - updateStatus(status)
  - cancelOrder(reason?)
  - hasProduct(productId) → bool
  - getItemByProduct(productId) → OrderItem|null
- Quan hệ: belongsTo(Customer), belongsTo(Coupon), hasMany(OrderItem)

## App\Models\OrderItem
- Thuộc tính: id, order_id, product_id, quantity, price, timestamps
- Phương thức (hành động):
  - subtotal() → quantity * price
  - setPriceFromProduct(product)
  - fromProduct(product, quantity)
- Quan hệ: belongsTo(Order), belongsTo(Product)

## App\Models\Product
- Thuộc tính: id (UUID), category_id, name, brand, description, price, stock, timestamps
- Phương thức (hành động):
  - getDetails() → ProductDetail|array|null
  - updateStock(quantityChange, absolute=false)
  - calculateDiscount(percent?=null, fixedAmount?=0) → { original, discount, final }
  - priceForOrder() → price chuẩn hóa để ghi vào OrderItem
  - mainImage() → Image|null
  - mainImageUrl() → string|null
  - inStock(quantity=1) → bool
  - reserve(quantity)
  - release(quantity)
- Quan hệ: belongsTo(Category), hasOne(ProductDetail), morphMany(Image), hasMany(Comment), hasMany(OrderItem), hasMany(Wishlist)

## App\Models\ProductDetail
- Thuộc tính: id, product_id, width, length, height, origin, finishes, timestamps
- Phương thức (hành động):
  - updateFromArray(data)
- Quan hệ: belongsTo(Product)

## App\Models\Category
- Thuộc tính: id, name, timestamps
- Phương thức (hành động):
  - addProduct(data) → Product
  - productCount() → int
- Quan hệ: hasMany(Product)

## App\Models\Coupon
- Thuộc tính: id, code, discount_percent, expiry_date, max_uses, used_count, timestamps
- Phương thức (hành động):
  - isValidFor(order) → bool
  - discountFor(subtotal) → amount
  - registerUse()
- Quan hệ: hasMany(Order)

## App\Models\Customer
- Thuộc tính: id, user_id, phone, address, timestamps
- Phương thức (hành động):
  - viewOrderHistory(limit=20) → Collection<Order>
  - placeOrder(items, couponCode?=null) → Order
  - defaultContact() → Contact|null
- Quan hệ: belongsTo(User), hasMany(Order), hasMany(Contact)

## App\Models\User
- Thuộc tính: id, name, email, email_verified_at, password, role, remember_token, timestamps
- Phương thức (hành động):
  - register(data) → User
  - login(email, password) → User|AuthResult
  - updateProfile(data)
  - ensureCustomer() → Customer
  - isAdmin() → bool
  - placeOrder(items, couponCode?=null) → Order (ủy quyền cho Customer)
- Quan hệ: hasOne(Customer), hasMany(Order) [gián tiếp qua Customer], hasMany(Comment), hasMany(Wishlist)

## App\Models\Image
- Thuộc tính: id, imageable_id, imageable_type, path, is_main, timestamps
- Phương thức (hành động):
  - getUrl() → string
  - existsOnDisk() → bool
  - setAsMain()
  - deleteWithFile()
- Quan hệ: morphTo(imageable)

## App\Models\News
- Thuộc tính: id, title, content, timestamps
- Phương thức (hành động):
  - addImage(image)
  - removeImage(image)
  - setMainImage(image)
- Quan hệ: morphMany(Image)

## App\Models\Comment
- Thuộc tính: id, user_id, product_id, content, timestamps
- Phương thức (hành động):
  - post(user, product, content) → Comment
- Quan hệ: belongsTo(User), belongsTo(Product)

## App\Models\Wishlist
- Thuộc tính: id, user_id, product_id, timestamps
- Phương thức (hành động):
  - add(user, product) → Wishlist
  - remove(user, product)
  - toggle(user, product) → Wishlist|bool
  - exists(user, product) → bool
- Quan hệ: belongsTo(User), belongsTo(Product)

## App\Models\Contact
- Thuộc tính: id, customer_id, full_name, address, phone, email, content, timestamps
- Phương thức (hành động):
  - createFor(customer, data) → Contact
- Quan hệ: belongsTo(Customer)

---

Ghi chú chung
- Tính tiền: tránh số thực khi thao tác tiền tệ; dùng DECIMAL (chuỗi) hoặc đơn vị nhỏ nhất (cents) nhất quán giữa DB và code.
- Đồng bộ tổng đơn: sau add/update/remove OrderItem hoặc apply/remove coupon, luôn gọi refreshTotals().
- Toàn vẹn dữ liệu: cân nhắc transaction khi thay đổi nhiều bảng (Order, OrderItem, Product stock, Coupon used_count).
