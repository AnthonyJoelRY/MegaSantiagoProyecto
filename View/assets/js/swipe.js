function enableSwipe(element, onSwipeLeft, onSwipeRight) {
  let startX = 0;
  let endX = 0;

  element.addEventListener("touchstart", (e) => {
    startX = e.touches[0].clientX;
  }, { passive: true });

  element.addEventListener("touchend", (e) => {
    endX = e.changedTouches[0].clientX;
    const diff = startX - endX;

    if (Math.abs(diff) > 50) {
      diff > 0 ? onSwipeLeft() : onSwipeRight();
    }
  }, { passive: true });
}

window.addEventListener("load", () => {
  const navCarousel = document.querySelector(".nav-mobile-carousel");
  if (!navCarousel) return;

  enableSwipe(
    navCarousel,
    () => { navCarousel.scrollLeft += 160; },
    () => { navCarousel.scrollLeft -= 160; }
  );
});
