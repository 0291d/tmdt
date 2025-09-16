@extends('layouts.layout')

@section('title', $news->title)

@section('content')
{{-- Trang chi tiết tin: tiêu đề + nội dung HTML đã định dạng --}}
<div class="container-widget">
    <div class="title-widget">
        <h1><strong>{{ $news->title }}</strong></h1>
    </div>
    <div class="main-widget">
        {!! $news->content !!}
    </div>
    
</div>
@endsection
