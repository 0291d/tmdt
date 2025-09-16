@extends('layouts.layout')

@section('title', 'Wishlist')

@section('content')
{{-- Trang Yêu thích: liệt kê các sản phẩm đã lưu, cách footer một khoảng lớn --}}
<section class="wishlist-page">
  <div class="container">
    <h2 class="wishlist-title">Sản phẩm yêu thích</h2>

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
          @endphp
          <div class="wish-card">
            <a href="{{ route('product.show', $p) }}" class="image-wrap">
              <img src="{{ $src }}" alt="{{ $p->name }}">
            </a>
            <div class="meta">
              <a class="name" href="{{ route('product.show', $p) }}">{{ $p->name }}</a>
              <div class="brand">{{ $p->brand }}</div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endsection
