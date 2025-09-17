@extends('layouts.layout')

@section('content')
{{-- Trang shop: sidebar bộ lọc + danh sách sản phẩm --}}
<section class="shop-content">
    {{-- Sidebar: liệt kê danh mục và thương hiệu --}}
    <div class="sidebar">
        
        <h4 class="sidebar-title">CATEGORY</h4>
        <ul>
            @foreach ($categories as $category)
            <li>
                <a href="{{ route('shop.category', $category->id) }}">
                    {{ $category->name }}
                </a>
                <span>({{ $category->products_count }})</span>
            </li>
            @endforeach
        </ul>

       
        <h4 class="sidebar-title">BRANDS</h4>
        <ul>
            @foreach ($brands as $brand)
            <li>
                <a href="{{ route('shop.brand', $brand->brand) }}">
                    {{ strtoupper($brand->brand) }}
                </a>
                <span>({{ $brand->product_count }})</span>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Khu vực chính: kết quả tìm kiếm và phân trang --}}
    <div class="main-content">
        <div class="main-content container mt-5">
            <h2 class="mb-4 text-center">{{ isset($q) && $q ? 'Kết quả tìm kiếm: '.e($q) : '' }}</h2>
            <div class="row">
                @forelse($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 product-card">
                        @php
                        $image = $product->images->where('is_main', true)->first();
                        $src = ($image && $image->existsOnDisk()) ? $image->url : asset('img/placeholder.png');
                        @endphp
                        <div class="product image-wrap">
                            <img src="{{ $src }}" class="card-img-top" alt="{{ $product->name }}">
                            <div class="img-overlay">
                                <div class="overlay-content">
                                    <div class="overlay-price">{{ number_format($product->price, 0, ',', '.') }} VND</div>
                                    <form method="POST" action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="overlay-add">ADD TO CART</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title product-name"><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h5>
                            <p class="card-text text-muted">{{ $product->brand }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center">Chưa có sản phẩm nào.</p>
                @endforelse
            </div>

            <div class="pagination-wrap">
                {{ $products->onEachSide(1)->links() }}
            </div>
        </div>
        @yield('shop-main-content')
    </div>
</section>
@endsection
