@extends('layouts.layout')

@section('title', 'Cart')

@section('content')
{{-- Trang giỏ hàng: lấy dữ liệu từ session, cho phép cập nhật số lượng/xóa, áp mã giảm giá và thanh toán --}}
@php
    $cart = session('cart', []);
    $items = array_values($cart);
    $subtotal = 0;
    foreach ($items as $it) { $subtotal += ((int)($it['price'] ?? 0)) * ((int)($it['quantity'] ?? 0)); }
    $appliedCoupon = session('coupon');
    $discountPercent = (int)($appliedCoupon['percent'] ?? 0);
    $discountAmount = $discountPercent ? (int) floor($subtotal * $discountPercent / 100) : 0;
    $totalAfter = max(0, $subtotal - $discountAmount);
@endphp

<div class="container">
    <div class="cart-header">
        <h2>Giỏ hàng của bạn</h2>
        <div class="title-underline"></div>
    </div>

    {{-- Khu vực chính chia 2 cột: danh sách item (trái) + tổng tiền và mã giảm giá (phải) --}}
    <div class="cart-section">
        <div class="row gx-4">
            <div class="col-12 col-lg-8">
                {{-- Nếu giỏ trống hiển thị thông báo --}}
                @if(empty($items))
                    <div class="cart-empty">
                        <p>Giỏ hàng của bạn đang trống</p>
                    </div>
                @else
                    {{-- Hiển thị từng item: ảnh, tên, brand, giá, controls +/- và xóa --}}
                    @foreach ($items as $it)
                        <div class="card mb-3 p-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $it['image'] ?? asset('img/placeholder.png') }}" alt="{{ $it['name'] }}" style="width:90px;height:90px;object-fit:cover">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $it['name'] }}</div>
                                    <div class="text-muted small">{{ $it['brand'] ?? '' }}</div>
                                    <div class="text-danger fw-semibold">{{ number_format((int)$it['price'], 0, ',', '.') }} VND</div>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('cart.update') }}" class="d-flex align-items-center gap-2 cart-update-form" data-product-id="{{ $it['id'] }}" data-price="{{ (int) $it['price'] }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $it['id'] }}">
                                        <div class="input-group" style="width:140px">
                                            <button class="btn btn-outline-secondary btn-sm btn-qty" type="button" data-action="minus">-</button>
                                            <input name="quantity" type="number" min="1" value="{{ (int)$it['quantity'] }}" class="form-control text-center qty-input">
                                            <button class="btn btn-outline-secondary btn-sm btn-qty" type="button" data-action="plus">+</button>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm d-none" type="submit">Cập nhật</button>
                                    </form>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('cart.remove') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $it['id'] }}">
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Xóa</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="col-12 col-lg-4">
                {{-- Thông tin đơn hàng tạm tính + áp/huỷ mã giảm giá --}}
                <div class="order-card p-3">
                    <h5>Thông tin đơn hàng</h5>
                    <div class="line"></div>
                    <div class="order-total d-flex justify-content-between">
                        <div>Tổng tiền:</div>
                        <div class="amount" id="cart-subtotal" data-initial="{{ $subtotal }}">{{ number_format($subtotal, 0, ',', '.') }} VND</div>
                    </div>
                    <div class="mt-2">
                        <form method="POST" action="{{ route('cart.coupon.apply') }}" class="d-flex gap-2">
                            @csrf
                            <input type="text" name="code" class="form-control" placeholder="Mã giảm giá" value="{{ $appliedCoupon['code'] ?? '' }}">
                            <button class="btn btn-outline-primary" type="submit">Áp dụng</button>
                            @if($appliedCoupon)
                                <button class="btn btn-outline-secondary" formaction="{{ route('cart.coupon.remove') }}" formmethod="POST">Bỏ mã</button>
                            @endif
                        </form>
                    </div>
                    @if($discountPercent)
                    <div class="d-flex justify-content-between mt-2 text-success">
                        <div>Giảm ({{ $discountPercent }}%):</div>
                        <div>-{{ number_format($discountAmount, 0, ',', '.') }} VND</div>
                    </div>
                    <div class="d-flex justify-content-between mt-1 fw-bold">
                        <div>Thành tiền:</div>
                        <div>{{ number_format($totalAfter, 0, ',', '.') }} VND</div>
                    </div>
                    @endif
                    <div style="margin-top:14px;">
                        <form method="POST" action="{{ route('payment.vnpay') }}">
                            @csrf
                            <button class="btn-checkout btn btn-primary w-100" type="submit">THANH TOÁN</button>
                        </form>
                    </div>

                    <ul class="policy-list mt-3">
                        <li><i class="fa-solid fa-check"></i>
                            <div><strong>Không rủi ro.</strong> Đặt hàng trước, thanh toán sau tại nhà. Miễn phí giao hàng & lắp đặt tại tất cả quận huyện thuộc Hà Nội, Khu đô thị Ecopark</div>
                        </li>
                        <li><i class="fa-solid fa-check"></i>
                            <div><strong>Giao hàng nhanh:</strong>  Đơn hàng của quý khách sẽ được giao hàng trong vòng 3 ngày, vui lòng đợi nhân viên tư vấn xác nhận lịch giao hàng trước khi thực hiện chuyển khoản đơn hàng.</div>
                        </li>
                        <li><i class="fa-solid fa-check"></i>
                            <div><strong>Bảo hành:</strong> Miễn phí 1 đổi 1 - Bảo hành 2 năm - Bảo trì trọn đời (**)</div>
                        </li>
                        <li><i class="fa-solid fa-check"></i>
                            <div><strong>Chất lượng:</strong> Tất cả sản phẩm được thiết kế bởi các chuyên gia thiết kế nội thất đến từ Châu Âu.</div>
                        </li>
                    </ul>

                    <div class="policy-note small text-muted">
                        Cảm ơn đã mua hàng
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="features-wrap mt-4">
    <div class="container">
        <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="icon"><i class="fa-solid fa-truck"></i></div>
                    <div class="title">Giao Hàng & Lắp Đặt</div>
                    <div class="subtitle">Miễn Phí</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="icon"><i class="fa-solid fa-refresh"></i></div>
                    <div class="title">Đổi Trả 1 - 1</div>
                    <div class="subtitle">Miễn Phí</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <div class="title">Bảo Hành đến 5 Năm</div>
                    <div class="subtitle">Miễn Phí</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="icon"><i class="fa-solid fa-phone-volume"></i></div>
                    <div class="title">Tư Vấn Thiết Kế</div>
                    <div class="subtitle">Miễn Phí</div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const forms = document.querySelectorAll('.cart-update-form');

  function formatVND(n) {
    try { return Number(n).toLocaleString('vi-VN') + ' VND'; } catch (e) { return n + ' VND'; }
  }

  function recalcSubtotal() {
    let total = 0;
    let totalQty = 0;
    document.querySelectorAll('.cart-update-form').forEach(f => {
      const price = Number(f.getAttribute('data-price') || 0);
      const qty = Number(f.querySelector('.qty-input')?.value || 0);
      total += price * qty;
      totalQty += qty;
    });
    const el = document.getElementById('cart-subtotal');
    if (el) el.textContent = formatVND(total);
    const headerCount = document.getElementById('cart-count');
    if (headerCount) headerCount.textContent = totalQty > 0 ? ` (${totalQty})` : '';
  }

  function postUpdate(form, productId, quantity) {
    const token = form.querySelector('input[name="_token"]').value;
    const fd = new FormData();
    fd.append('_token', token);
    fd.append('product_id', productId);
    fd.append('quantity', quantity);
    fetch(form.action, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } }).catch(() => {});
  }

  function clampQty(n) { n = Number(n||1); if (!Number.isFinite(n) || n < 1) n = 1; return Math.floor(n); }

  forms.forEach(form => {
    const pid = form.getAttribute('data-product-id');
    const qtyInput = form.querySelector('.qty-input');
    form.querySelectorAll('.btn-qty').forEach(btn => {
      btn.addEventListener('click', () => {
        const action = btn.getAttribute('data-action');
        let val = clampQty(qtyInput.value);
        val = action === 'plus' ? val + 1 : Math.max(1, val - 1);
        qtyInput.value = val;
        recalcSubtotal();
        postUpdate(form, pid, val);
      });
    });

    let timer;
    qtyInput.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        const val = clampQty(qtyInput.value);
        qtyInput.value = val;
        recalcSubtotal();
        postUpdate(form, pid, val);
      }, 400);
    });
  });
});
</script>
@endsection
