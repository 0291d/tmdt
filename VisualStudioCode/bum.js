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

setInterval(() => {
    moveToSlide(currentIndex + 1);
}, 4000);