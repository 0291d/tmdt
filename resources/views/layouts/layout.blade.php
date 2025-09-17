<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Shop')</title>

    {{-- Nạp CSS: framework trước, custom sau để override dễ --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/catagory.css') }}">
    <link rel="stylesheet" href="{{ asset('css/aboutus.css') }}">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
    <link rel="stylesheet" href="{{ asset('css/news.css') }}">
    <link rel="stylesheet" href="{{ asset('css/products.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detailProducts.css') }}?v={{ @filemtime(public_path('css/detailProducts.css')) }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
    {{-- Navbar: logo, tìm kiếm, tài khoản, giỏ hàng --}}
    <div class="navbar">
        <div class="navbar-left">
            <div><a href="{{ route('home.index') }}" class="logo">BREW</a></div>
        </div>
        <div class="navbar-right">
            {{-- Ô tìm kiếm điều hướng sang trang shop với ?q= --}}
            <div class="search-bar">
                <input type="text" placeholder="Bạn tìm kiếm gì...">
            </div>
            <img class="user-icon" src="{{ asset('img/usc.jpg') }}" alt="usericon">
            @guest
              <a href="{{ url('/login') }}">Log In</a>
            @else
              {{-- Dropdown tài khoản: thông tin người dùng, yêu thích, admin, logout --}}
              <div class="user-menu" style="position:relative; display:inline-block;">
                <button id="userMenuBtn" style="background:none;border:none;color:inherit;cursor:pointer;">
                  {{ auth()->user()->name ?? 'Account' }} <i class="fa-solid fa-caret-down"></i>
                </button>
                <div id="userDropdown" style="position:absolute; right:0; top:28px; background:#fff; border:1px solid #eee; border-radius:6px; padding:12px; min-width:220px; display:none; z-index:20;">
                  <div class="mb-2"><a href="{{ route('user.info') }}">Thông tin người dùng</a></div>
                  <div class="mb-2"><a href="{{ route('wishlist.index') }}">Yêu thích</a></div>
                  @if (strcasecmp((string)auth()->user()->role, 'admin') === 0)
                    <div class="mb-2"><a href="/admin">Quản trị</a></div>
                  @endif
                  <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Log out</button>
                  </form>
                </div>
              </div>
              <script>
                document.addEventListener('DOMContentLoaded', function(){
                  const btn = document.getElementById('userMenuBtn');
                  const dd = document.getElementById('userDropdown');
                  if(btn && dd){
                    btn.addEventListener('click', function(e){ e.stopPropagation(); dd.style.display = dd.style.display === 'block' ? 'none' : 'block'; });
                    document.addEventListener('click', function(){ dd.style.display = 'none'; });
                  }
                });
              </script>
              <script>
                document.addEventListener('DOMContentLoaded', function(){
                  const input = document.querySelector('.search-bar input');
                  if(!input) return;
                  try {
                    const params = new URLSearchParams(window.location.search);
                    const q = params.get('q');
                    if (q) input.value = q;
                  } catch(e) {}
                  input.addEventListener('keydown', function(e){
                    if (e.key === 'Enter') {
                      e.preventDefault();
                      const term = (input.value || '').trim();
                      const base = "{{ route('shop') }}";
                      const url = term ? `${base}?q=${encodeURIComponent(term)}` : base;
                      window.location.href = url;
                    }
                  });
                });
              </script>
            @endguest
            @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
            <a href="{{ route('cart.index') }}" id="cart-link">Cart<span id="cart-count">{{ $cartCount ? ' ('.$cartCount.')' : '' }}</span></a>
        </div>
    </div>

    {{-- Menu chính: link nhanh các trang --}}
    <section class="menu-bar">
        <nav>
            <ul>
                <li><a href="{{ route('shop') }}">SHOP</a></li>
                <li><a href="{{ route('about') }}">ABOUT US</a></li>
                <li><a href="{{ route('contact') }}">STORE & CONTACT</a></li>
                <li><a href="{{ route('news.index') }}">NEWS</a></li>
            </ul>
        </nav>
    </section>

    <!-- Ná»™i dung -->
    {{-- Nội dung từng trang --}}@yield('content')

    {{-- Footer + popup thông tin nhóm --}}
    <footer class="footer" id="footer">
        <div class="footer-content">
            <div class="footer-container">
                <div class="content1">
                    <h2>COME VISIT US</h2>
                    <p>31 Dịch vọng hậu, Cầu giấy, Hà nội</p>
                    <p>MON - FRIDAY: 8AM - 8PM</p>
                    <p>SATURDAY: 9AM - 7PM</p>
                    <p>SUNDAY: 9AM - 8PM</p>
                </div>
                <div class="content2">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.017669744039!2d105.78445077565232!3d21.0319789806182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4be8d6409f%3A0x84085138006934d9!2zMzEgUC4gROG7i2NoIFbhu41uZyBI4bqtdSwgROG7i2NoIFbhu41uZyBI4bqtdSwgQ-G6p3UgR2nhuqV5LCBIw6AgTuG7mWksIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1756659923650!5m2!1svi!2s"
                        allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
        <div id="info-icon"><i class="fa-solid fa-users"></i></div>
        <div id="info-popup">
            <div id="info-header">
                ThÃ´ng tin thÃ nh viÃªn
                <button id="close-btn">X</button>
            </div>
            <div id="info-body">
                <ul>
                    <li>2354800195 â€“ LÆ°Æ¡ng Tháº¿ CÆ°á»ng</li>
                    <li>2354800017 â€“ Äáº·ng Gia Báº£o</li>
                    <li>2354800176 â€“ Nguyá»…n VÄƒn HoÃ ng</li>
                    <li>2354800243 â€“ Nguyá»…n Quá»‘c VÆ°Æ¡ng</li>
                </ul>
            </div>
        </div>
        <script>
            const infoIcon = document.getElementById('info-icon');
            const infoPopup = document.getElementById('info-popup');
            const closeBtn = document.getElementById('close-btn');
            infoIcon.addEventListener('click', () => {
                infoPopup.style.display = infoPopup.style.display === 'flex' ? 'none' : 'flex';
            });
            closeBtn.addEventListener('click', () => {
                infoPopup.style.display = 'none';
            });
        </script>
        <script async src="https://dochat.vn/code.js?id=9250829223305513694"></script>
    </footer>
</body>
</html>



