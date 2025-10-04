# Bộ Test Case Tối Giản (Smoke)

Bộ kịch bản kiểm thử rút gọn, bao phủ các luồng quan trọng nhất của hệ thống. Mỗi kịch bản trình bày: Scenario, Preconditions, Steps, Test Data, Expected Result, Actual Result.

Ghi chú: “Actual Result” để trống để cập nhật sau khi chạy test/manual.

## 1) Trang chủ — route `home.index` (`/`)
- Scenario: Trang chủ hiển thị 200
  - Preconditions: N/A
  - Steps: GET `/`
  - Test Data: N/A
  - Expected: HTTP 200; render view `pages.index`
  - Actual:

## 2) Shop — `ShopController@index`
- Scenario: Tìm kiếm hiển thị đúng sản phẩm
  - Preconditions: Có sản phẩm khớp từ khóa
  - Steps: GET `/shop?q=sofa`
  - Test Data: Sản phẩm chứa “sofa” ở name/brand/description
  - Expected: Danh sách chỉ gồm sản phẩm khớp; phân trang 12
  - Actual:

## 3) Chi tiết sản phẩm — `ProductController@show`
- Scenario: Hiển thị chi tiết sản phẩm hợp lệ
  - Preconditions: Có product P1 với images, detail
  - Steps: GET `/product/{product}` (P1)
  - Test Data: product=P1
  - Expected: 200; load images+detail; comments kèm user
  - Actual:

## 4) Tin tức — `NewsController@index`
- Scenario: Danh sách tin phân trang 9
  - Preconditions: >9 bài tin
  - Steps: GET `/news`
  - Test Data: N/A
  - Expected: 200; `news` 9/trang; có images nếu tồn tại
  - Actual:

## 5) Liên hệ — POST `/contact`
- Scenario: Gửi liên hệ hợp lệ được lưu
  - Preconditions: N/A
  - Steps: POST `/contact`
  - Test Data: `full_name,address,phone,email,content` hợp lệ
  - Expected: 302 back; session `status`; DB có bản ghi `contacts`
  - Actual:

## 6) Bình luận sản phẩm — POST `product.comments.store`
- Scenario: Guest bị chặn khi bình luận
  - Preconditions: Chưa đăng nhập
  - Steps: POST `/product/{product}/comments`
  - Test Data: `content` hợp lệ
  - Expected: 302 → route `login`
  - Actual:

## 7) Giỏ hàng — POST `/cart/add`
- Scenario: Thêm sản phẩm lần đầu vào giỏ
  - Preconditions: Đăng nhập; có product P1 có ảnh
  - Steps: POST `/cart/add`
  - Test Data: `product_id=P1`, `quantity=2`
  - Expected: 302 back; session `cart[P1]` tồn tại `quantity=2`; ảnh chính/đầu tiên được chọn
  - Actual:

## 8) Giỏ hàng — POST `/cart/update`
- Scenario: Cập nhật số lượng hợp lệ
  - Preconditions: Đăng nhập; `cart[P1]` tồn tại
  - Steps: POST `/cart/update`
  - Test Data: `product_id=P1`, `quantity=5`
  - Expected: 302 back; `cart[P1].quantity=5`
  - Actual:

## 9) Coupon — POST `/cart/coupon`
- Scenario: Áp mã hợp lệ và lưu session
  - Preconditions: Giỏ có hàng; coupon hợp lệ (vd SAVE10)
  - Steps: POST `/cart/coupon`
  - Test Data: `code=SAVE10`
  - Expected: 302 back; session `coupon={id,code,percent}`; có `status`
  - Actual:

## 10) Wishlist — POST `/wishlist/add`
- Scenario: Thêm idempotent (không tạo trùng)
  - Preconditions: Đăng nhập; có product P1; chưa có (user,P1)
  - Steps: POST `/wishlist/add` 2 lần
  - Test Data: `product_id=P1`
  - Expected: 302 back; DB chỉ có 1 bản ghi (user,P1)
  - Actual:

## 11) Thanh toán VNPAY — GET `/payment/vnpay/return`
- Scenario: Callback thành công tạo order + items và dọn session
  - Preconditions: Có `payment` pending; session `cart` có hàng; `customer` tồn tại; chữ ký hợp lệ; `vnp_ResponseCode='00'`
  - Steps: GET `/payment/vnpay/return` với params hợp lệ
  - Test Data: vnp params hợp lệ
  - Expected: Tạo `orders` + `order_items`; nếu có coupon hợp lệ thì gắn `coupon_id`; `payments` → `success` + `paid_at`; clear `cart`, `coupon`, `vnpay_*`, `payment_*`; 302 → `cart.index` với `status`
  - Actual:

## 12) Hồ sơ người dùng — POST `/user/info`
- Scenario: Cập nhật phone/address hợp lệ
  - Preconditions: Đăng nhập
  - Steps: POST `/user/info`
  - Test Data: `phone` khớp `0\d{9}`, `address` <=255
  - Expected: 302 → `user.info`; DB `customers` được tạo/cập nhật; có `status`
  - Actual:

---

Phụ lục (tùy chọn khi còn thời gian):
- Cart: Guest thêm vào giỏ bị chuyển `/login` (middleware `login.notice`).
- Payment: Khởi tạo `/payment/vnpay` báo lỗi khi thiếu ENV (`VNPAY_TMN_CODE`/`HASH_SECRET`).
- Orders History: Guest bị chặn vào `/orders/history` (302 → login).
- EnsureAdmin: User role!=admin truy cập `/admin/*` bị redirect về `home.index`.
- Model: `Order::totalsFromCart` tính đúng `subtotal/discount/final` với percent.

