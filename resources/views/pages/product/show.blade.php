@extends('layouts.layout')

@section('title', $product->name)

@section('content')
{{-- Chi tiết sản phẩm: cột trái thông tin, cột phải hình ảnh + hành động --}}
<section class="products">
    <div class="container">
        <div class="col1">
            <p>BREW</p>
            {{-- stroupper: in hoa --}}
            <h1>{{ strtoupper($product->brand ?? '') }}</h1>
            <h1>{{ strtoupper($product->name) }}</h1>
            @if($product->description)
                <p class="description">{{ $product->description }}</p>
            @endif

            <table class="spec-table">
                <tr>
                    <td class="label">BRAND</td>
                    <td class="value">{{ $product->brand }}</td>
                </tr>
                @if(optional($product->detail)->width && optional($product->detail)->length && optional($product->detail)->height)
                <tr>
                    <td class="label">DIMENSIONS</td>
                    <td class="value">W {{ $product->detail->width }} X D {{ $product->detail->length }} X H {{ $product->detail->height }} CM</td>
                </tr>
                @endif
                @if(optional($product->detail)->finishes)
                <tr>
                    <td class="label">FINISHES</td>
                    <td class="value">{{ $product->detail->finishes }}</td>
                </tr>
                @endif
                @if(optional($product->detail)->origin)
                <tr>
                    <td class="label">ORIGIN</td>
                    <td class="value">{{ strtoupper($product->detail->origin) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">PRICE</td>
                    <td class="value">{{ number_format($product->price, 0, ',', '.') }} VND</td>
                </tr>
            </table>
        </div>

        <div class="col2">
            {{-- Slider ảnh: ưu tiên ảnh không phải is_main, nếu không có dùng fallback --}}
            <div class="image-slider">
                @php
                    $nonMainImages = $product->images->where('is_main', false);
                    $fallback = $product->images; 
                @endphp
                @forelse(($nonMainImages->count() ? $nonMainImages : $fallback) as $img)
                    <img src="{{ $img->url }}" alt="{{ $product->name }}">
                @empty
                    <img src="{{ asset('img/placeholder.png') }}" alt="no image">
                @endforelse
            </div>
            {{-- Hành động: thêm giỏ + thêm wishlist (yêu cầu đăng nhập) --}}
            <div class="actions">
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="add-to-cart">ADD TO CART</button>
                </form>
                @auth
                <form method="POST" action="{{ route('wishlist.add') }}" class="wishlist-form-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="wishlist-btn" title="Thêm vào yêu thích">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                </form>
                @else
                    <a href="{{ route('login') }}" class="wishlist-btn" title="Đăng nhập để thêm yêu thích">
                        <i class="fa-solid fa-heart"></i>
                    </a>
                @endauth
            </div>
        </div>

    </div>
</section>

{{-- Khối FEEDBACK: form bình luận (role=user), danh sách bình luận --}}
<section class="feedback-section">
    <h2 class="feedback-title">FEEDBACK</h2>

    @if (session('status'))
        <div class="alert alert-info text-center" role="alert">{{ session('status') }}</div>
    @endif

    @auth
        @if (strcasecmp((string)auth()->user()->role, 'user') === 0)
            <div class="feedback-toolbar">
                <form method="POST" action="{{ route('product.comments.store', $product) }}" class="feedback-form" id="comment-form">
                    @csrf
                    <div class="input-with-counter">
                        <textarea class="js-textarea" name="content" maxlength="3000" placeholder="Nhập nội dung bình luận..." required>{{ old('content') }}</textarea>
                        <span class="counter js-counter">0/3000</span>
                    </div>
                    <button type="submit" class="submit-btn">Gửi bình luận</button>
                </form>
            </div>
        @else
            <p class="feedback-hint">Chỉ tài khoản user mới được bình luận.</p>
        @endif
    @else
        <p class="feedback-hint">Vui lòng đăng nhập bằng tài khoản user để gửi phản hồi.</p>
    @endauth

    <div class="feedback-list">
        @php($list = isset($comments) ? $comments : $product->comments()->with('user')->latest()->get())
        @forelse ($list as $c)
            <div class="feedback-item">
                <div class="avatar">{{ strtoupper(mb_substr($c->user->name ?? 'U', 0, 1, 'UTF-8')) }}</div>
                <div class="bubble">
                    <div class="meta">
                        <span class="name">{{ $c->user->name ?? 'User' }}</span>
                        <span class="time">{{ optional($c->created_at)->diffForHumans() }}</span>
                    </div>
                    <div class="content">{{ $c->content }}</div>
                </div>
            </div>
        @empty
            <p class="no-feedback">Chưa có phản hồi nào. Hãy là người đầu tiên!</p>
        @endforelse
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('comment-form');
  if(!form) return;
  const ta = form.querySelector('.js-textarea');
  const counter = form.querySelector('.js-counter');
  const max = parseInt(ta.getAttribute('maxlength') || '3000', 10);
  const autoGrow = () => {
    ta.style.height = 'auto';
    const h = Math.min(180, ta.scrollHeight);
    ta.style.height = h + 'px';
  };
  const update = () => {
    const len = (ta.value || '').length;
    if(counter) counter.textContent = `${len}/${max}`;
    autoGrow();
  };
  ta.addEventListener('input', update);
  update();
});
</script>
@endsection
