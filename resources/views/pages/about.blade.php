@extends('layouts.layout')

@section('title', 'About Us - BREW')

@section('content')
{{-- Trang giới thiệu: ảnh banner + nội dung About/Mission/Vision --}}
<section class="container py-4">
    <div class="main-img mb-4">
        <img src="{{ asset('img/about.jpg') }}" alt="About Us Image" class="img-fluid w-100">
    </div>

    <div class="main-content-wrapper">
        <div class="main-content">
            <section class="mb-4">
                <h2>About Us</h2>
                <p>
                    Chúng tôi là thương hiệu nội thất mang hơi thở hiện đại châu Âu, kết hợp hài hòa với vẻ đẹp mộc mạc của thiên nhiên. Mỗi sản phẩm là một câu chuyện, nơi thiết kế tinh tế gặp gỡ chất liệu bền vững, tạo nên không gian sống vừa sang trọng vừa gần gũi. Với niềm đam mê sáng tạo, chúng tôi mang đến những giải pháp nội thất độc đáo, giúp bạn biến ngôi nhà thành tổ ấm tràn đầy cảm hứng.
                </p>
            </section>

            <section class="mb-4">
                <h2>Mission</h2>
                <p>
                    Sứ mệnh của chúng tôi là kiến tạo không gian sống bền vững và thẩm mỹ, nơi mỗi món nội thất không chỉ là vật dụng mà còn là nguồn cảm hứng. Chúng tôi cam kết sử dụng vật liệu thân thiện môi trường và thiết kế vượt thời gian, để mỗi ngôi nhà đều phản ánh phong cách riêng biệt và hài hòa với thiên nhiên.
                </p>
            </section>

            <section>
                <h2>Vision</h2>
                <p>
                    Chúng tôi hướng tới trở thành thương hiệu nội thất dẫn đầu, nơi hiện đại và thiên nhiên giao thoa hoàn hảo. Tầm nhìn của chúng tôi là mang đến những không gian sống trưởng thành, nơi con người và thiên nhiên cộng hưởng, tạo nên một thế giới đẹp hơn, bền vững hơn.
                </p>
            </section>
        </div>
    </div>
</section>
@endsection
