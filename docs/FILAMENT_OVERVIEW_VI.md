# Tổng quan thư mục Filament (Admin)

Tài liệu này mô tả cấu trúc, vai trò từng file trong `app/Filament`, luồng hoạt động chung của Filament v2, và một ví dụ phân tích chi tiết để bạn nắm cách các hàm tương tác với model/view như một luồng.

## 1) Cấu trúc tổng thể

- `app/Filament/Pages` — Các trang tuỳ chỉnh không gắn trực tiếp với một Resource CRUD.
  - `app/Filament/Pages/ProductStats.php:11` — Trang thống kê bán hàng, render qua Blade tùy chỉnh.
- `app/Filament/Resources` — Mỗi Resource đại diện cho một model Eloquent và khai báo form/table CRUD + các page con.
  - `CategoryResource.php`, `CommentResource.php`, `ContactResource.php`, `CouponResource.php`, `CustomerResource.php`, `ImageResource.php`, `NewsResource.php`, `OrderitemResource.php`, `OrderResource.php`, `ProductDetailResource.php`, `ProductResource.php`, `UserResource.php`, `WishlistResource.php`.
  - Thư mục con `.../Pages` — Các trang con của từng Resource như List/Create/Edit.

Liên quan view:
- `resources/views/filament/pages/product-stats.blade.php:1` — View của trang ProductStats.

## 2) Mẫu tổ chức của một Resource

Mỗi `Resource` đều tuân theo mẫu:
- `$model` — Chỉ định model Eloquent. Ví dụ `app/Filament/Resources/ProductResource.php:14` sử dụng `App\Models\Product`.
- `form(Form $form)` — Khai báo schema form (Filament Forms components) để tạo/sửa bản ghi. Ví dụ `ProductResource::form` định nghĩa `TextInput`, `Select`, `Textarea`.
- `table(Table $table)` — Khai báo các cột, sort, search, actions của listing. Ví dụ `ProductResource::table` định nghĩa `TextColumn`, `BadgeColumn`, `EditAction`, `DeleteAction`, ...
- `getPages()` — Map các route con tới lớp Page cụ thể (List/Create/Edit). Ví dụ `ProductResource::getPages` ánh xạ tới `ListProducts`, `CreateProduct`, `EditProduct`.
- (Tùy chọn) `getRelations()` — Khai báo RelationManagers nếu có.

Ví dụ đường dẫn của các Page con:
- `app/Filament/Resources/ProductResource/Pages/ListProducts.php:11`
- `app/Filament/Resources/ProductResource/Pages/CreateProduct.php`
- `app/Filament/Resources/ProductResource/Pages/EditProduct.php`

## 3) Danh sách các Resource hiện có

- `app/Filament/Resources/CategoryResource.php:13` — CRUD danh mục (`App\Models\Category`). Pages: `ListCategories`, `CreateCategory`, `EditCategory`.
- `app/Filament/Resources/CommentResource.php:13` — Quản lý bình luận (`App\Models\Comment`). Pages: `ListComments`, `CreateComment`, `EditComment`.
- `app/Filament/Resources/ContactResource.php:13` — Quản lý liên hệ (`App\Models\Contact`). Pages: `ListContacts`, `EditContact`.
- `app/Filament/Resources/CouponResource.php:13` — Quản lý mã giảm giá (`App\Models\Coupon`). Pages: `ListCoupons`, `CreateCoupon`, `EditCoupon`.
- `app/Filament/Resources/CustomerResource.php:13` — Quản lý khách hàng (`App\Models\Customer`). Pages: `ListCustomers`, `CreateCustomer`, `EditCustomer`.
- `app/Filament/Resources/ImageResource.php:13` — Quản lý ảnh (`App\Models\Image`). Pages: `ListImages`, `CreateImage`, `EditImage`.
- `app/Filament/Resources/NewsResource.php:13` — Quản lý tin tức (`App\Models\News`). Pages: `ListNews`, `CreateNews`, `EditNews`.
- `app/Filament/Resources/OrderitemResource.php:13` — Quản lý dòng hàng (`App\Models\OrderItem`). Pages: `ListOrderitems`, `CreateOrderitem`, `EditOrderitem`.
- `app/Filament/Resources/OrderResource.php:13` — Quản lý đơn hàng (`App\Models\Order`). Pages: `ListOrders`, `CreateOrder`, `EditOrder`.
- `app/Filament/Resources/ProductDetailResource.php:13` — Quản lý chi tiết SP (`App\Models\ProductDetail`). Pages: `ListProductDetails`, `CreateProductDetail`, `EditProductDetail`.
- `app/Filament/Resources/ProductResource.php:13` — Quản lý sản phẩm (`App\Models\Product`). Pages: `ListProducts`, `CreateProduct`, `EditProduct`.
- `app/Filament/Resources/UserResource.php:13` — Quản lý người dùng (`App\Models\User`). Pages: `ListUsers`, `CreateUser`, `EditUser`.
- `app/Filament/Resources/WishlistResource.php:13` — Quản lý wishlist (`App\Models\Wishlist`). Pages: `ListWishlists`, `CreateWishlist`, `EditWishlist`.

Ghi chú: Mỗi Resource còn khai báo `navigationIcon`, `navigationGroup` để nhóm menu trong panel.

## 4) Luồng hoạt động chung (Resource)

- Người dùng truy cập `/admin` và chọn menu của một Resource (ví dụ Sản phẩm).
- Filament định tuyến tới Page tương ứng: `List*`, `Create*`, `Edit*` (xem `getPages`).
- Các Page con (kế thừa `ListRecords`, `CreateRecord`, `EditRecord`) sẽ gọi ngược `Resource::table()` hoặc `Resource::form()` để cấu hình bảng/form.
- Hành động (Create/Edit/Delete) do Filament xử lý, thao tác trực tiếp với model Eloquent được gán ở `$model` và lưu dữ liệu vào DB.

## 5) Ví dụ chi tiết: ProductResource

- Khai báo model và menu:
  - `app/Filament/Resources/ProductResource.php:14` — `$model = App\Models\Product`.
  - `app/Filament/Resources/ProductResource.php:17` — `navigationIcon`, `navigationGroup`.
- Form (tạo/sửa):
  - `app/Filament/Resources/ProductResource.php:19` — `form(Form $form)` trả về schema gồm `TextInput('name','brand')`, `TextInput('price')` (mask + dehydrate thành số), `Textarea('description')`, `TextInput('stock')`, `Select('category_id')->relationship('category','name')`.
  - Tác động: Khi submit, Filament map state của từng component về cột tương ứng trong bảng `products` thông qua model `App\Models\Product` (xem `app/Models/Product.php:10`).
- Bảng (listing):
  - `app/Filament/Resources/ProductResource.php:41` — `table(Table $table)` định nghĩa các cột hiển thị, search/sort, định dạng số (`number_format`) và actions (`EditAction`, `DeleteAction`).
  - Tác động: Truy vấn Eloquent tự động, hiển thị dữ liệu và cho phép bulk actions.
- Pages:
  - `app/Filament/Resources/ProductResource.php:86` — `getPages()` ánh xạ routes con (`/`, `/create`, `/{record}/edit`) tới các lớp `ListProducts`, `CreateProduct`, `EditProduct`.
  - `app/Filament/Resources/ProductResource/Pages/ListProducts.php:11` — Kế thừa `ListRecords`, khai báo `getActions()` để hiển thị nút tạo mới; có `getTableRecordsPerPageSelectOptions()` để chỉnh số bản ghi/trang.

Liên kết model:
- `app/Models/Product.php:10` — Khai báo `fillable`, `casts`, và các quan hệ như `category()`, `images()`, `comments()`, ... được dùng trong `Select('category_id')->relationship('category','name')` và các `TextColumn('category.name')`.

## 6) Ví dụ trang tuỳ chỉnh: ProductStats

- Lớp Page:
  - `app/Filament/Pages/ProductStats.php:11` — Kế thừa `Filament\Pages\Page`, set `$view = 'filament.pages.product-stats'`.
  - `mount()` lấy dữ liệu: tổng số lượng đã bán theo `OrderItem` (group theo `product_id`, eager load `product`) và lọc theo danh mục nếu có.
  - Tác động: Không ghi DB; chỉ đọc từ `OrderItem`, `Product`, `Category` để tính mảng `labels`/`series`.
- View:
  - `resources/views/filament/pages/product-stats.blade.php:1` — Dùng Chart.js (CDN) để vẽ biểu đồ cột từ `labels`/`series`; có bộ lọc danh mục gửi qua GET.
  - Luồng: Người dùng tick checkbox danh mục -> form GET auto-submit -> `mount()` chạy lại -> cập nhật dữ liệu biểu đồ.

## 7) Ghi chú quyền truy cập

- Để vào panel, `User` cần cho phép truy cập Filament (ví dụ triển khai `FilamentUser::canAccessFilament()` khớp vai trò `admin`).
- Kiểm tra `app/Models/User.php:1` và middleware bảo vệ `/admin` nếu có.

---

Tài liệu này phản ánh mã nguồn hiện có trong repo, giúp bạn định hướng nhanh khi bổ sung Resource mới, chỉnh form/table, hay tạo Page tuỳ chỉnh hiển thị số liệu.

## Phụ lục: Danh sách file dưới `app/Filament`

- app/Filament/Pages/ProductStats.php
- app/Filament/Resources/CategoryResource.php
- app/Filament/Resources/CommentResource.php
- app/Filament/Resources/ContactResource.php
- app/Filament/Resources/CouponResource.php
- app/Filament/Resources/CustomerResource.php
- app/Filament/Resources/ImageResource.php
- app/Filament/Resources/NewsResource.php
- app/Filament/Resources/OrderitemResource.php
- app/Filament/Resources/OrderResource.php
- app/Filament/Resources/ProductDetailResource.php
- app/Filament/Resources/ProductResource.php
- app/Filament/Resources/UserResource.php
- app/Filament/Resources/WishlistResource.php
- app/Filament/Resources/CategoryResource/Pages/CreateCategory.php
- app/Filament/Resources/CategoryResource/Pages/EditCategory.php
- app/Filament/Resources/CategoryResource/Pages/ListCategories.php
- app/Filament/Resources/CommentResource/Pages/CreateComment.php
- app/Filament/Resources/CommentResource/Pages/EditComment.php
- app/Filament/Resources/CommentResource/Pages/ListComments.php
- app/Filament/Resources/ContactResource/Pages/EditContact.php
- app/Filament/Resources/ContactResource/Pages/ListContacts.php
- app/Filament/Resources/CouponResource/Pages/CreateCoupon.php
- app/Filament/Resources/CouponResource/Pages/EditCoupon.php
- app/Filament/Resources/CouponResource/Pages/ListCoupons.php
- app/Filament/Resources/CustomerResource/Pages/CreateCustomer.php
- app/Filament/Resources/CustomerResource/Pages/EditCustomer.php
- app/Filament/Resources/CustomerResource/Pages/ListCustomers.php
- app/Filament/Resources/ImageResource/Pages/CreateImage.php
- app/Filament/Resources/ImageResource/Pages/EditImage.php
- app/Filament/Resources/ImageResource/Pages/ListImages.php
- app/Filament/Resources/NewsResource/Pages/CreateNews.php
- app/Filament/Resources/NewsResource/Pages/EditNews.php
- app/Filament/Resources/NewsResource/Pages/ListNews.php
- app/Filament/Resources/OrderitemResource/Pages/CreateOrderitem.php
- app/Filament/Resources/OrderitemResource/Pages/EditOrderitem.php
- app/Filament/Resources/OrderitemResource/Pages/ListOrderitems.php
- app/Filament/Resources/OrderResource/Pages/CreateOrder.php
- app/Filament/Resources/OrderResource/Pages/EditOrder.php
- app/Filament/Resources/OrderResource/Pages/ListOrders.php
- app/Filament/Resources/ProductDetailResource/Pages/CreateProductDetail.php
- app/Filament/Resources/ProductDetailResource/Pages/EditProductDetail.php
- app/Filament/Resources/ProductDetailResource/Pages/ListProductDetails.php
- app/Filament/Resources/ProductResource/Pages/CreateProduct.php
- app/Filament/Resources/ProductResource/Pages/EditProduct.php
- app/Filament/Resources/ProductResource/Pages/ListProducts.php
- app/Filament/Resources/UserResource/Pages/CreateUser.php
- app/Filament/Resources/UserResource/Pages/EditUser.php
- app/Filament/Resources/UserResource/Pages/ListUsers.php
- app/Filament/Resources/WishlistResource/Pages/CreateWishlist.php
- app/Filament/Resources/WishlistResource/Pages/EditWishlist.php
- app/Filament/Resources/WishlistResource/Pages/ListWishlists.php
