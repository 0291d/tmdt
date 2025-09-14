@extends('layouts.layout')

@section('title', $product->name)

@section('content')
<section class="products">
    <div class="container">
        <div class="col1">
            <p>BREW</p>
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
            <div class="image-slider">
                @php
                    $nonMainImages = $product->images->where('is_main', false);
                    $fallback = $product->images; // nếu chưa đánh is_main, vẫn hiển thị tất cả
                @endphp
                @forelse(($nonMainImages->count() ? $nonMainImages : $fallback) as $img)
                    <img src="{{ $img->url }}" alt="{{ $product->name }}">
                @empty
                    <img src="{{ asset('img/placeholder.png') }}" alt="no image">
                @endforelse
            </div>
            <div class="actions">
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="add-to-cart">ADD TO CART</button>
                </form>
                <i class="fa-solid fa-heart wishlist-icon"></i>
            </div>
        </div>

    </div>
</section>
@endsection
