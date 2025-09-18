@extends('layouts.layout')

@section('title', 'Wishlist')

@section('content')
{{-- Trang Yêu thích: liệt kê các sản phẩm đã lưu, cách footer một khoảng lớn --}}
<section class="wishlist-page">
  <div class="container">
    <h2 class="wishlist-title">Sản phẩm yêu thích</h2>

    @php $items = $items instanceof \Illuminate\Support\Collection ? $items : collect($items); @endphp

    @if (session('status'))
      <div class="alert alert-info text-center" role="alert">{{ session('status') }}</div>
    @endif

    @if ($items->isEmpty())
      <p class="wishlist-empty">Danh sách yêu thích đang trống.</p>
    @else
      <div class="wishlist-grid">
        @foreach ($items as $it)
          @php
            $p = $it->product;
            $img = ($p?->images?->where('is_main', true)->first() ?? $p?->images?->first());
            $src = $img ? $img->url : asset('img/placeholder.png');
            $price = $p?->price;
            $formattedPrice = $price !== null
                ? number_format((float) $price, 0, ',', '.') . ' VND'
                : null;
          @endphp
          <article class="wish-card">
            <a href="{{ $p ? route('product.show', $p) : '#' }}" class="wish-card__image">
              <img src="{{ $src }}" alt="{{ $p?->name ?? 'Wishlist item' }}">
            </a>
            <div class="wish-card__body">
              <a class="wish-card__name" href="{{ $p ? route('product.show', $p) : '#' }}">{{ $p?->name ?? 'Sản phẩm không khả dụng' }}</a>
              @if (!empty($p?->brand))
                <div class="wish-card__brand">{{ $p?->brand }}</div>
              @endif
              @if ($formattedPrice)
                <div class="wish-card__price">{{ $formattedPrice }}</div>
              @endif
              @if ($p)
                <div class="wish-card__actions">
                  <a class="wish-card__action" href="{{ route('product.show', $p) }}">Xem chi tiết</a>
                </div>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endsection
