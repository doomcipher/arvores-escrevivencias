window.Carousel = (function () {
  let currentSlideIndex = 0;
  let slides = [];
  let indicators = [];
  let autoSlideInterval;

  function setup(selectors = {
    slide: '.carousel-item',
    indicator: '.carousel-indicators button'
  }) {
    slides = Array.from(document.querySelectorAll(selectors.slide));
    indicators = Array.from(document.querySelectorAll(selectors.indicator));
    if (!slides.length) return;

    showSlide(0);
    autoSlideInterval = setInterval(autoSlide, 5000);
  }

  function showSlide(index) {
    if (!slides.length) return;

    slides.forEach(s => s.classList.remove('active'));
    indicators.forEach(i => i.classList.remove('active'));

    if (index >= slides.length) currentSlideIndex = 0;
    else if (index < 0) currentSlideIndex = slides.length - 1;
    else currentSlideIndex = index;

    slides[currentSlideIndex].classList.add('active');
    if (indicators[currentSlideIndex]) {
      indicators[currentSlideIndex].classList.add('active');
    }
  }

  function moveSlide(direction) {
    currentSlideIndex += direction;
    showSlide(currentSlideIndex);
    resetAutoSlide();
  }

  function currentSlide(index) {
    showSlide(index);
    resetAutoSlide();
  }

  function autoSlide() {
    currentSlideIndex++;
    showSlide(currentSlideIndex);
  }

  function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    autoSlideInterval = setInterval(autoSlide, 5000);
  }

  return {
    setup,
    moveSlide,
    currentSlide
  };
})();
