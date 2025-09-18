<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserInformationController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Validator;
// Trang chu
Route::get('/', [HomeController::class, 'index'])->name('home.index');

// Dang nhap/Dang ky mac dinh cua Laravel
Auth::routes();

Route::get('/home', function () {
    return redirect()->route('home.index');
})->name('home');

// Shop: danh sach + tim kiem theo q
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/shop/category/{id}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/shop/brand/{brand}', [ShopController::class, 'brand'])->name('shop.brand');

// Static pages
// Trang tĩnh
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
// Tin tuc: danh sach + chi tiet
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');


// Product detail
// Chi tiet san pham
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// Product comments (users only)
// Binh luan san pham (chi role=user duoc phep)
Route::post('/product/{product}/comments', function (\Illuminate\Http\Request $request, \App\Models\Product $product) {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    // Only role 'user' can comment
    if (strcasecmp((string)$user->role, 'user') !== 0) {
        return back()->with('status', 'Chỉ tài khoản user mới được bình luận.');
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
// Gio hang (session-based, giao dien don gian)
Route::get('/cart', function () {
    return view('pages.cart');
})->name('cart.index');
// Apply coupon to cart
// Ap ma giam gia vao gio
Route::post('/cart/coupon', function (\Illuminate\Http\Request $request) {
    $data = $request->validate(['code' => ['required','string']]);
    $code = strtoupper(trim($data['code']));

    // Chặn áp mã khi giỏ trống
    $cart = session('cart', []);
    $subtotal = 0;
    foreach ($cart as $it) { $subtotal += (int)($it['price'] ?? 0) * (int)($it['quantity'] ?? 1); }
    if ($subtotal <= 0) {
        return back()->with('status', 'Giỏ hàng trống, không thể áp mã giảm giá.');
    }

    $coupon = \App\Models\Coupon::whereRaw('upper(code) = ?', [$code])->first();
    if (!$coupon) {
        return back()->with('status', 'Mã giảm giá không tồn tại.');
    }
    if (\Illuminate\Support\Carbon::parse($coupon->expiry_date)->isPast()) {
        return back()->with('status', 'Mã đã hết hạn.');
    }
    if ($coupon->used_count >= $coupon->max_uses) {
        return back()->with('status', 'Mã đã đạt số lần sử dụng tối đa.');
    }
    session()->put('coupon', [
        'id' => $coupon->id,
        'code' => $coupon->code,
        'percent' => (int) $coupon->discount_percent,
    ]);
    return back()->with('status', 'Áp dụng mã giảm giá thành công.');
})->name('cart.coupon.apply');
// Remove coupon
// Huy ma giam gia
Route::post('/cart/coupon/remove', function () {
    session()->forget('coupon');
    return back()->with('status', 'Đã bỏ mã giảm giá.');
})->name('cart.coupon.remove');
// Them san pham vao gio
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
// Cap nhat so luong trong gio
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
// Xoa san pham khoi gio
Route::post('/cart/remove', function (\Illuminate\Http\Request $request) {
    $data = $request->validate(['product_id' => ['required']]);
    $cart = session()->get('cart', []);
    if (isset($cart[$data['product_id']])) {
        unset($cart[$data['product_id']]);
        session()->put('cart', $cart);
    }
    return back();
})->middleware('login.notice')->name('cart.remove');

// Checkout: tạo Order status=paid từ session cart
// Thanh toan: tao Order tu session cart
Route::post('/cart/checkout', function () {
    $cart = session('cart', []);
    if (empty($cart)) return back();

    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login')->with('status', 'Bạn cần đăng nhập để thanh toán.');
    }

    $customer = \App\Models\Customer::where('user_id', $user->id)->first();
    if (!$customer || !trim((string)$customer->phone) || !trim((string)$customer->address)) {
        return redirect()->route('user.info')->with('status', 'Bạn phải nhập đủ số điện thoại và địa chỉ trước khi thanh toán.');
    }

    // Compute totals from cart + coupon
    $subtotal = 0;
    foreach ($cart as $it) {
        $subtotal += (int)($it['price'] ?? 0) * (int)($it['quantity'] ?? 1);
    }
    $applied = session('coupon');
    $percent = (int)($applied['percent'] ?? 0);
    $discountAmount = $percent ? (int) floor($subtotal * $percent / 100) : 0;
    $finalTotal = max(0, $subtotal - $discountAmount);

    $order = \App\Models\Order::create([
        'customer_id' => $customer->id,
        'status' => 'paid',
        'discount_percent' => $percent,
        'discount_amount' => $discountAmount,
        'total' => $finalTotal,
    ]);

    foreach ($cart as $it) {
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $it['id'],
            'quantity' => (int)($it['quantity'] ?? 1),
            'price' => (int)($it['price'] ?? 0),
        ]);
    }
    // Attach coupon if applied
    if ($applied && isset($applied['id'])) {
        $coupon = \App\Models\Coupon::find($applied['id']);
        if ($coupon && \Illuminate\Support\Carbon::parse($coupon->expiry_date)->isFuture() && $coupon->used_count < $coupon->max_uses) {
            $order->coupon_id = $coupon->id;
            $order->save();
            \App\Models\Coupon::whereKey($coupon->id)->update(['used_count' => $coupon->used_count + 1]);
        }
    }
    session()->forget('cart');
    session()->forget('coupon');
    return redirect()->route('cart.index');
})->middleware('auth')->name('cart.checkout');

// Wishlist (frontend)
// Wishlist (frontend, can dang nhap)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
});

// User information (profile)
Route::middleware('auth')->group(function () {
    Route::get('/user/info', [UserInformationController::class, 'show'])->name('user.info');
    Route::post('/user/info', [UserInformationController::class, 'update'])->name('user.info.update');
});
