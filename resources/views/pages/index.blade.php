@extends('layouts.layout')

@section('title', 'Home')

@section('content')
@php
    // provided by controller
    $idsByName   = $idsByName   ?? collect();
    $newProducts = $newProducts ?? collect();
    $latestNews  = $latestNews  ?? collect();
@endphp

<!-- Banner (keep classes for existing CSS) -->
<section class="banner">
    <div class="banner-slides">
        <div class="banner-slide" style="background-image: url('/img/slide1.jpg');"></div>
        <div class="banner-slide" style="background-image: url('/img/slide2.jpg');"></div>
        <div class="banner-slide" style="background-image: url('/img/slide3.jpg');"></div>
        <div class="banner-slide" style="background-image: url('/img/vl.jpg');"></div>
        <div class="banner-slide" style="background-image: url('/img/vq.jpg');"></div>
        <div class="banner-slide" style="background-image: url('/img/vw.jpg');"></div>
    </div>
    <div class="banner-indicators"></div>
    @yield('home-banner-extra')

</section>

<!-- Category grid (keep original classes) -->
<section class="category-grid">
    @php
        $sofaId = $idsByName['Sofa'] ?? null;
        $tableId = $idsByName['Table'] ?? null;
        $bedId   = $idsByName['Bed'] ?? null;
        $armId   = $idsByName['Armchair'] ?? null;
        $chairId = $idsByName['Chair'] ?? null;
    @endphp
    <a href="{{ $sofaId ? route('shop.category', $sofaId) : route('shop') }}" class="grid-item large">
        <img src="{{ asset('img/sofa.jpg') }}" alt="Sofa">
        <span class="label">SOFA</span>
    </a>

    <div class="right-grid">
        @php
        $idsByName = $idsByName ?? collect();
        @endphp
        <a href="{{ $tableId ? route('shop.category', $tableId) : route('shop') }}" class="grid-item small">
            <img src="{{ asset('img/ss.jpg') }}" alt="Bàn ăn">
            <span class="label">TABLE</span>
        </a>

        @php
        $idsByName = $idsByName ?? collect();
        @endphp
        <a href="{{ $bedId ? route('shop.category', $bedId) : route('shop') }}" class="grid-item small">
            <img src="{{ asset('img/da.jpg') }}" alt="Giường">
            <span class="label">BED</span>
        </a>

        @php
        $idsByName = $idsByName ?? collect();
        @endphp
        <a href="{{ $armId ? route('shop.category', $armId) : route('shop') }}" class="grid-item small">
            <img src="{{ asset('img/aa.jpg') }}" alt="Armchair">
            <span class="label">ARMCHAIR</span>
        </a>

        @php
        $idsByName = $idsByName ?? collect();
        @endphp
        <a href="{{ $chairId ? route('shop.category', $chairId) : route('shop') }}" class="grid-item small">
            <img src="{{ asset('img/xx.jpg') }}" alt="Ghế đơn">
            <span class="label">CHAIR</span>
        </a>
    </div>
</section>

<!-- New Arrivals (keep slider classes) -->
<section class="product-section">
    <div class="section-header">
        <h2>NEW ARRIVALS</h2>
        <a href="{{ route('shop') }}" class="view-all">Xem tất cả</a>
    </div>
    <div class="slider-container">
        <button class="slider-btn prev"><i class="fas fa-chevron-left"></i></button>
        <div class="slider-wrapper">
            <div class="slider">
                @foreach ($newProducts as $p)
                    @php
                        $image = ($p->images->where('is_main', true)->first() ?? $p->images->first());
                        $src = $image ? $image->url : asset('img/placeholder.png');
                    @endphp
                    <div class="product-card">
                        <a href="{{ route('product.show', $p) }}" class="product">
                            <img src="{{ $src }}" alt="{{ $p->name }}">
                            <div class="content-card">
                                <p>{{ $p->brand }}</p>
                                <div class="description">{{ $p->name }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <button class="slider-btn next"><i class="fas fa-chevron-right"></i></button>
    </div>
</section>

<!-- News preview (keep card classes similar to news.html) -->
<section class="product-section">
    <div class="section-header">
        <h2>NEWS</h2>
        <a href="{{ route('news.index') }}" class="view-all">Xem tất cả</a>
    </div>
    <div class="slider-container">
        <button class="slider-btn prev"><i class="fas fa-chevron-left"></i></button>
        <div class="slider-wrapper">
            <div class="slider">
                @foreach ($latestNews as $n)
                    @php
                        $cover = $n->images->where('is_main', true)->first() ?? $n->images->first();
                        $src = $cover ? $cover->url : asset('img/blog-banner.png');
                    @endphp
                    <div class="inspiration-card">
                        <a href="{{ route('news.show', $n) }}" class="card">
                            <div class="container-news">
                                <div class="thumbnail">
                                    <img src="{{ $src }}" alt="thumb">
                                    <div class="content-card">
                                        <p>{{ $n->title }}</p>
                                        <div class="description">{{ \Illuminate\Support\Str::limit(strip_tags($n->content), 120) }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <button class="slider-btn next"><i class="fas fa-chevron-right"></i></button>
    </div>
 </section>

  <!-- Banner slider logic from VisualStudioCode/bum.js -->
  <script src="{{ asset('js/bum.js') }}"></script>
 <script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.product-section .slider-container').forEach(function (container) {
    const wrapper = container.querySelector('.slider-wrapper');
    const prev = container.querySelector('.slider-btn.prev');
    const next = container.querySelector('.slider-btn.next');
    if (!wrapper || !prev || !next) return;
    const scrollAmount = wrapper.clientWidth * 0.9;
    prev.addEventListener('click', () => wrapper.scrollBy({ left: -scrollAmount, behavior: 'smooth' }));
    next.addEventListener('click', () => wrapper.scrollBy({ left:  scrollAmount, behavior: 'smooth' }));
  });
});
</script>
@endsection
