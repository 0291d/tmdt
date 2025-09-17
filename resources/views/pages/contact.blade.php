@extends('layouts.layout')

@section('title', 'Store & Contact - BREW')

@section('content')
{{-- Trang liên hệ: thông tin showroom + bản đồ + form gửi liên hệ lưu vào DB --}}
<section class="page-contact">
<div class="white-spacing"></div>
<div class="container">
    {{-- Thông tin cửa hàng/showroom --}}
    <div class="row mb-4">
        <div class="col-12 col-md-6">
            <div class="subtitle">Contacts</div>
            <h1>Vietnam Store</h1>
        </div>
        <div class="col-12 col-md-6 info-block">
            <strong>Visit our showroom</strong>
            <p>Monday - Sunday , 09:00 am to 07:00 pm</p>
            <strong>Contact us</strong>
            <p>Hotline: +84 421 92 29 498</p>
            <strong>Showroom HN</strong>
            <p>BREW SHOWROOM, Dịch Vọng Hậu, Cầu Giấy, Hà Nội.<br>VIETNAM</p>
        </div>
    </div>

    {{-- Bản đồ Google Maps --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="map-box">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.0176697460574!2d105.78444541117399!3d21.031978980537428!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4be8d6409f%3A0x84085138006934d9!2zMzEgUC4gROG7i2NoIFbhu41uZyBI4bqtdSwgROG7i2NoIFbhu41uZyBI4bqtdSwgQ-G6p3UgR2nhuqV5LCBIw6AgTuG7mWksIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1757438734647!5m2!1svi!2s"
                    allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>

    {{-- Form liên hệ: POST tới route('contact.submit') --}}
    <div class="row">
        <div class="col-12">
            <p class="text-center text-muted mb-4">
                Please contact us via email/phone or submit your inquiry in the form below.
            </p>
            @if (session('status'))
              <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('contact.submit') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Full name" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Address *</label>
                        <input type="text" name="address" class="form-control" placeholder="Address" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Phone *</label>
                        <input type="tel" name="phone" class="form-control" placeholder="Phone" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-12">
                        <label>Content *</label>
                        <textarea name="content" class="form-control" rows="5" placeholder="Content" required></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
    </div>
    </div>
    <div class="white-spacing"></div>
</div>
</section>
@endsection
