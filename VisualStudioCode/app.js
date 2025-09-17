// Lọc sản phẩm theo tên
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    if (searchInput) {
        searchInput.addEventListener("input", () => {
            let filter = searchInput.value.toLowerCase();
            let products = document.querySelectorAll(".product-card h3");
            products.forEach(p => {
                let card = p.closest(".product-card");
                if (p.textContent.toLowerCase().includes(filter)) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            });
        });
    }

    // Menu mobile
    const hamburger = document.querySelector(".hamburger");
    const menu = document.querySelector(".menu");
    if (hamburger) {
        hamburger.addEventListener("click", () => {
            menu.style.display = menu.style.display === "flex" ? "none" : "flex";
            menu.style.flexDirection = "column";
        });
    }
});

// Bắt sự kiện DOM load
document.addEventListener("DOMContentLoaded", () => {
    // Form đăng nhập
    const loginForm = document.querySelector(".form-login form");
    const loginEmail = document.getElementById("login-email");
    const loginPassword = document.getElementById("login-password");

    // Form đăng ký
    const registerForm = document.querySelector(".form-register form");
    const registerEmail = document.getElementById("register-email");

    // Xử lý đăng nhập
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const email = loginEmail.value.trim();
        const password = loginPassword.value.trim();

        if (!email || !password) {
            alert("Vui lòng nhập đầy đủ thông tin đăng nhập!");
            return;
        }

        // Lấy tài khoản đã lưu trong localStorage (nếu có)
        const savedEmail = localStorage.getItem("userEmail");
        const savedPassword = localStorage.getItem("userPassword");

        if (email === savedEmail && password === savedPassword) {
            alert("Đăng nhập thành công!");
            localStorage.setItem("isLoggedIn", "true");
            window.location.href = "index.html"; // quay lại trang chủ
        } else {
            alert("Sai tài khoản hoặc mật khẩu!");
        }
    });

    // Xử lý đăng ký
    registerForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const email = registerEmail.value.trim();

        if (!email) {
            alert("Vui lòng nhập email!");
            return;
        }

        // Regex check email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("Email không hợp lệ!");
            return;
        }

        // Giả lập tạo mật khẩu ngẫu nhiên
        const randomPassword = Math.random().toString(36).slice(-8);

        // Lưu vào localStorage
        localStorage.setItem("userEmail", email);
        localStorage.setItem("userPassword", randomPassword);

        alert(`Đăng ký thành công! Mật khẩu đã được gửi tới email của bạn (giả lập: ${randomPassword})`);
        registerEmail.value = "";
    });
});


//banner
const slides = document.querySelector('.banner-slides');
const slideCount = document.querySelectorAll('.banner-slide').length;
const indicatorsContainer = document.querySelector('.banner-indicators');
let currentIndex = 0;

// tạo indicators
for (let i = 0; i < slideCount; i++) {
    const ind = document.createElement('span');
    ind.classList.add('banner-indicator');
    if (i === 0) ind.classList.add('active');
    ind.addEventListener('click', () => moveToSlide(i));
    indicatorsContainer.appendChild(ind);
}
const indicators = document.querySelectorAll('.banner-indicator');

function updateIndicators() {
    indicators.forEach(ind => ind.classList.remove('active'));
    indicators[currentIndex].classList.add('active');
}

function moveToSlide(index) {
    currentIndex = (index + slideCount) % slideCount;
    slides.style.transform = `translateX(-${currentIndex * 100}%)`;
    updateIndicators();
}

// Tự động chạy
setInterval(() => {
    moveToSlide(currentIndex + 1);
}, 4000);