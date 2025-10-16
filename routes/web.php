<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserInformationController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home.index');

// Đăng nhập/Đăng ký mặc định của Laravel
Auth::routes();

Route::get('/home', function () {
    return redirect()->route('home.index');
})->name('home');

// Shop: danh sách + tìm kiếm theo q
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/shop/category/{id}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/shop/brand/{brand}', [ShopController::class, 'brand'])->name('shop.brand');

// Static pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'full_name' => ['required','string','max:255'],
        'address' => ['required','string','max:255'],
        'phone' => ['required','string','max:50'],
        'email' => ['required','email','max:255'],
        'content' => ['required','string'],
    ]);
    \App\Models\Contact::create($data);
    return back()->with('status', 'Cảm ơn đã liên hệ với chúng tôi');
})->name('contact.submit');

// News
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

// Product detail
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// Product comments (users only)
Route::post('/product/{product}/comments', function (\Illuminate\Http\Request $request, \App\Models\Product $product) {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    // Chỉ tài khoản 'user' mới được bình luận
    if (strcasecmp((string)$user->role, 'user') !== 0) {
        return back()->with('error', 'Chỉ tài khoản user mới được bình luận.');
    }

    $data = $request->validate([
        'content' => ['required','string','max:2000'],
    ]);

    \App\Models\Comment::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'content' => $data['content'],
    ]);

    return back();
})->middleware('auth')->name('product.comments.store');

// Cart (session-based, simple)
Route::get('/cart', function () {
    return view('pages.cart');
})->name('cart.index');

// Apply coupon to cart
Route::post('/cart/coupon', function (\Illuminate\Http\Request $request) {
    $data = $request->validate(['code' => ['required','string']]);
    $code = strtoupper(trim($data['code']));

    // Chặn áp mã khi giỏ trống
    $cart = session('cart', []);
    $subtotal = 0;
    foreach ($cart as $it) { $subtotal += (int)($it['price'] ?? 0) * (int)($it['quantity'] ?? 1); }
    if ($subtotal <= 0) {
        return back()->with('error', 'Giỏ hàng trống, không thể áp mã giảm giá.');
    }

    $coupon = \App\Models\Coupon::whereRaw('upper(code) = ?', [$code])->first();
    if (!$coupon) {
        return back()->with('error', 'Mã giảm giá không tồn tại.');
    }
    if (\Illuminate\Support\Carbon::parse($coupon->expiry_date)->isPast()) {
        return back()->with('error', 'Mã đã hết hạn.');
    }
    if ($coupon->used_count >= $coupon->max_uses) {
        return back()->with('error', 'Mã đã đạt số lần sử dụng tối đa.');
    }

    session()->put('coupon', [
        'id' => $coupon->id,
        'code' => $coupon->code,
        'percent' => (int) $coupon->discount_percent,
    ]);
    return back()->with('status', 'Áp dụng mã giảm giá thành công.');
})->name('cart.coupon.apply');

// Remove coupon
Route::post('/cart/coupon/remove', function () {
    session()->forget('coupon');
    return back()->with('status', 'Đã bỏ mã giảm giá.');
})->name('cart.coupon.remove');

// Thêm sản phẩm vào giỏ
Route::post('/cart/add', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'product_id' => ['required','string'],
        'quantity' => ['nullable','integer','min:1'],
    ]);
    $product = \App\Models\Product::with('images')->findOrFail($data['product_id']);
    $qty = $data['quantity'] ?? 1;
    $image = ($product->images->where('is_main', true)->first() ?? $product->images->first());
    $imageUrl = $image ? $image->url : asset('img/placeholder.png');
    $cart = session()->get('cart', []);
    if (isset($cart[$product->id])) {
        $cart[$product->id]['quantity'] += (int)$qty;
    } else {
        $cart[$product->id] = [
            'id' => $product->id,
            'name' => $product->name,
            'brand' => $product->brand,
            'price' => (int) $product->price,
            'quantity' => (int) $qty,
            'image' => $imageUrl,
        ];
    }
    session()->put('cart', $cart);
    return back();
})->middleware('login.notice')->name('cart.add');

// Cập nhật số lượng trong giỏ
Route::post('/cart/update', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'product_id' => ['required'],
        'quantity' => ['required','integer','min:1'],
    ]);
    $cart = session()->get('cart', []);
    if (isset($cart[$data['product_id']])) {
        $cart[$data['product_id']]['quantity'] = (int) $data['quantity'];
        session()->put('cart', $cart);
    }
    return back();
})->middleware('login.notice')->name('cart.update');

// Xóa sản phẩm khỏi giỏ
Route::post('/cart/remove', function (\Illuminate\Http\Request $request) {
    $data = $request->validate(['product_id' => ['required']]);
    $cart = session()->get('cart', []);
    if (isset($cart[$data['product_id']])) {
        unset($cart[$data['product_id']]);
        session()->put('cart', $cart);
    }
    return back();
})->middleware('login.notice')->name('cart.remove');

// Wishlist (frontend)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
});

// Payment (VNPAY)
Route::middleware('auth')->group(function () {
    Route::post('/payment/vnpay', [PaymentController::class, 'vnpayPayment'])->name('payment.vnpay');
});

// URL trả về từ VNPAY (không yêu cầu đăng nhập do cổng gọi lại trình duyệt)
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');

// User information (profile)
Route::middleware('auth')->group(function () {
    Route::get('/user/info', [UserInformationController::class, 'show'])->name('user.info');
    Route::post('/user/info', [UserInformationController::class, 'update'])->name('user.info.update');
});

// Order history (frontend)
Route::middleware('auth')->group(function () {
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
});

