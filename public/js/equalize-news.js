//  Cân bằng chiều cao của các thẻ tin tức bằng cách kéo dài phần nội dung văn bản
(function() {
    const containersSelectors = [
        '.card-list', // class áp dụng
    ];

    function debounce(fn, wait) {
        let t;
        return function() {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, arguments), wait);
        };
    }

    function imagesReady(container) {
        const imgs = Array.from(container.querySelectorAll('img'));
        return imgs.every(img => img.complete && img.naturalHeight > 0);
    }
    // array.from : chuyển kết quả từ nodelist sang mảng 
    function equalizeInContainer(container) {
        const bodies = Array.from(container.querySelectorAll('.card .content-card'));
        if (!bodies.length) return;

        // Reset chiều cao 
        bodies.forEach(b => (b.style.minHeight = ''));

        // tính chiều cao lớn nhất của các thẻ 
        let maxBody = 0;
        bodies.forEach(b => {
            // tính chiều cao của thẻ + cả padding 
            const h = Math.ceil(b.scrollHeight);
            if (h > maxBody) maxBody = h;
        });

        // gán lại minheight (phần nội dung)
        bodies.forEach(b => (b.style.minHeight = maxBody + 'px'));
    }

    function run() {
        // duyệt từng sel trong containerselector
        containersSelectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(container => {
                if (!container) return;
                // đảm bảo ảnh đã load 
                if (imagesReady(container)) {
                    equalizeInContainer(container);
                } else {
                    const onLoad = () => equalizeInContainer(container);
                    const imgs = container.querySelectorAll('img');
                    let pending = imgs.length;

                    // Với mỗi ảnh:
                    // Nếu đã tải xong thì giảm pending
                    // Nếu chưa tải thì đợi sự kiện load hoặc error
                    // Khi pending = 0 (tất cả ảnh đã xử lý xong) thì EqualizeInContainer
                    imgs.forEach(img => {
                        if (img.complete && img.naturalHeight > 0) {
                            if (--pending === 0) onLoad();
                        } else {
                            img.addEventListener('load', () => {
                                if (--pending === 0) onLoad();
                            }, { once: true });
                            img.addEventListener('error', () => {
                                if (--pending === 0) onLoad();
                            }, { once: true });
                        }
                    });
                }
            });
        });
    }

    // Kích hoạt hàm run khi:
    // DOM vừa tải xong
    // Toàn bộ trang (bao gồm ảnh) tải xong
    // Cửa sổ bị thay đổi kích thước
    document.addEventListener('DOMContentLoaded', run);
    window.addEventListener('load', run);
    window.addEventListener('resize', debounce(run, 100));
})();