@extends('layouts.layout')

@section('title', 'Lịch sử mua hàng')

@section('content')
<section class="order-history">
  <div class="container">
    <h2 class="oh-title">Lịch sử mua hàng</h2>

    @if (session('status'))
      <div class="alert alert-info text-center" role="alert">{{ session('status') }}</div>
    @endif

    @php
      $orders = $orders instanceof \Illuminate\Support\Collection ? $orders : collect($orders);
      $customer = $customer ?? null;
      $user = $user ?? auth()->user();

      function vn_order_status($s) {
        return match((string) $s) {
          'paid' => 'Đã thanh toán',
          'completed' => 'Hoàn tất',
          'cancelled' => 'Đã hủy',
          default => 'Đang xử lý',
        };
      }
    @endphp

    @if ($orders->isEmpty())
      <p class="oh-empty">Bạn chưa có đơn hàng nào.</p>
    @else
      @foreach ($orders as $order)
        @php
          $created = optional($order->created_at)->timezone('Asia/Ho_Chi_Minh');
          $dateStr = $created ? $created->format('d/m/Y H:i') : '';
          $statusLabel = vn_order_status($order->status);
          $orderTotal = (float) ($order->total ?? 0);
        @endphp
        <article class="order-card">
          <header class="order-head">
            <div>
              <div class="oh-row"><span class="oh-label">Mã đơn hàng:</span> <span class="oh-value">{{ $order->id }}</span></div>
              <div class="oh-row"><span class="oh-label">Ngày đặt:</span> <span class="oh-value">{{ $dateStr }}</span></div>
            </div>
            <div class="order-status">{{ $statusLabel }}</div>
          </header>

          <div class="order-items">
            @foreach ($order->items as $it)
              @php
                $p = $it->product;
                $img = ($p?->images?->where('is_main', true)->first() ?? $p?->images?->first());
                $src = $img ? $img->url : asset('img/placeholder.png');
                $qty = (int) $it->quantity;
                $price = (float) $it->price;
                $line = $qty * $price;
              @endphp
              <div class="order-item">
                <div class="oi-image"><img src="{{ $src }}" alt="{{ $p?->name ?? 'Sản phẩm' }}"></div>
                <div class="oi-main">
                  <a class="oi-name" href="{{ $p ? route('product.show', $p) : '#' }}">{{ $p?->name ?? 'Sản phẩm không khả dụng' }}</a>
                  <div class="oi-meta">Số lượng: {{ $qty }} · Giá: {{ number_format($price, 0, ',', '.') }} VND</div>
                </div>
                <div class="oi-total">{{ number_format($line, 0, ',', '.') }} VND</div>
              </div>
            @endforeach
          </div>

          <footer class="order-foot">
            <div class="of-block">
              <div class="of-title">Thông tin thanh toán</div>
              <div class="of-row">Phương thức: <strong>Không xác định</strong></div>
              <div class="of-row">Trạng thái: <strong>{{ $statusLabel }}</strong></div>
              <div class="of-row">Tổng đơn: <strong>{{ number_format($orderTotal, 0, ',', '.') }} VND</strong></div>
            </div>
            <div class="of-block">
              <div class="of-title">Thông tin vận chuyển</div>
              <div class="of-row">Địa chỉ: <strong>{{ $customer?->address ?: 'Chưa cập nhật' }}</strong></div>
              <div class="of-row">Người nhận: <strong>{{ $user?->name }}</strong></div>
              <div class="of-row">SĐT: <strong>{{ $customer?->phone ?: 'Chưa cập nhật' }}</strong></div>
            </div>
          </footer>
        </article>
      @endforeach
    @endif
  </div>
</section>

<style>
.order-history { padding: 24px 0 80px; }
.oh-title { font-size: 28px; margin-bottom: 16px; font-weight: 600; }
.oh-empty { text-align:center; padding: 24px; color: #666; }

.order-card { border: 1px solid #eaeaea; border-radius: 10px; margin-bottom: 20px; background: #fff; overflow: hidden; }
.order-head { display:flex; justify-content:space-between; align-items:flex-start; padding:14px 16px; background:#fafafa; border-bottom:1px solid #eee; }
.order-status { font-weight:600; color:#0a7; }
.oh-row { font-size: 14px; color:#333; }
.oh-label { color:#666; margin-right:6px; }

.order-items { padding: 8px 8px 4px 8px; }
.order-item { display:grid; grid-template-columns: 90px 1fr 160px; gap: 12px; align-items:center; padding:10px 8px; border-bottom:1px dashed #eee; }
.order-item:last-child { border-bottom:none; }
.oi-image { width:90px; height:70px; overflow:hidden; border-radius:8px; border:1px solid #eee; display:flex; align-items:center; justify-content:center; background:#fff; }
.oi-image img { width:100%; height:100%; object-fit:cover; }
.oi-main { display:flex; flex-direction:column; gap:4px; }
.oi-name { font-weight:600; color:#222; text-decoration:none; }
.oi-name:hover { text-decoration:underline; }
.oi-meta { color:#777; font-size:13px; }
.oi-total { text-align:right; font-weight:600; color:#111; }

.order-foot { display:grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 12px 16px 16px; background:#fafafa; border-top:1px solid #eee; }
.of-block { background:#fff; border:1px solid #eee; border-radius:8px; padding:10px 12px; }
.of-title { font-weight:600; margin-bottom:6px; }
.of-row { font-size:14px; color:#444; margin: 2px 0; }

@media (max-width: 768px){
  .order-item { grid-template-columns: 70px 1fr; }
  .oi-total { grid-column: 1 / -1; text-align:left; }
  .order-foot { grid-template-columns: 1fr; }
}
</style>
@endsection

