// Equalize heights of news cards by extending only the text area
// Works for both the news index grid (.card-list) and the home slider (.product-section .slider)
(function () {
  const containersSelectors = [
    '.card-list',                 // news index
    '.product-section .slider'    // home section sliders
  ];

  function debounce(fn, wait) {
    let t;
    return function () {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, arguments), wait);
    };
  }

  function imagesReady(container) {
    const imgs = Array.from(container.querySelectorAll('img'));
    return imgs.every(img => img.complete && img.naturalHeight > 0);
  }

  function equalizeInContainer(container) {
    const bodies = Array.from(container.querySelectorAll('.card .content-card'));
    if (!bodies.length) return;

    // Reset before measuring
    bodies.forEach(b => (b.style.minHeight = ''));

    // Calculate max body content height (below image only)
    let maxBody = 0;
    bodies.forEach(b => {
      // Use scrollHeight to get the natural content height including padding
      const h = Math.ceil(b.scrollHeight);
      if (h > maxBody) maxBody = h;
    });

    // Apply min-height so the total card heights match while image stays fixed
    bodies.forEach(b => (b.style.minHeight = maxBody + 'px'));
  }

  function run() {
    containersSelectors.forEach(sel => {
      document.querySelectorAll(sel).forEach(container => {
        if (!container) return;
        // If images are not loaded yet, wait; otherwise equalize immediately
        if (imagesReady(container)) {
          equalizeInContainer(container);
        } else {
          const onLoad = () => equalizeInContainer(container);
          const imgs = container.querySelectorAll('img');
          let pending = imgs.length;
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

        // Recompute when the container resizes (e.g., responsive breakpoints)
        if (window.ResizeObserver) {
          const ro = new ResizeObserver(debounce(() => equalizeInContainer(container), 100));
          ro.observe(container);
        }
      });
    });
  }

  // Initial and responsive triggers
  document.addEventListener('DOMContentLoaded', run);
  window.addEventListener('load', run);
  window.addEventListener('resize', debounce(run, 100));
})();

