@extends('layouts.layout')

@section('title', 'News - BREW')

@section('content')
{{-- Trang danh sách tin tức: banner + lưới thẻ tin, mỗi thẻ lấy ảnh cover nếu có --}}
<section class="news">
    <div class="container-news">
        <div class="banner1">
            <img src="{{ asset('img/blog-banner.png') }}" alt="blog-banner">
            <div class="content-banner">
                <h1>NEWS</h1>
            </div>
        </div>

        <div class="card-list">
            @forelse ($news as $item)
                @php
                    $cover = $item->images->where('is_main', true)->first() ?? $item->images->first();
                    $img = $cover ? $cover->url : asset('img/blog-banner.png');
                @endphp
                <a href="{{ route('news.show', $item) }}" class="card">
                    <div class="container-news">
                        <div class="thumbnail">
                            <img src="{{ $img }}" alt="thumb">
                            <div class="content-card">
                                <p>{{ $item->title }}</p>
                                <div class="description">{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 120) }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-center py-4">Chưa có bài viết.</p>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $news->links() }}
        </div>
    </div>
</section>
@endsection
