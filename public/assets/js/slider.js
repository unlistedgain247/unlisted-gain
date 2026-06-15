document.addEventListener("DOMContentLoaded", () => {
  const sections = document.querySelectorAll(".popular-shares-section");

  sections.forEach((section) => {
    const viewport = section.querySelector(".cards-viewport");
    const track = section.querySelector(".cards-track");
    const nextBtn = section.querySelector(".next");
    const prevBtn = section.querySelector(".prev");

    let index = 0;
    let autoScrollTimer;

    function getSlideWidth() {
      const card = track.querySelector(".share-card");
      const gap = 20;
      return card.offsetWidth + gap;
    }

    function getMaxIndex() {
      const visibleCards = Math.floor(viewport.offsetWidth / getSlideWidth());
      return track.children.length - visibleCards;
    }

    function updateSlider() {
      const move = index * getSlideWidth();
      track.style.transform = `translateX(-${move}px)`;
    }

    function moveNext() {
      index = index < getMaxIndex() ? index + 1 : 0;
      updateSlider();
    }

    function movePrev() {
      index = index > 0 ? index - 1 : getMaxIndex();
      updateSlider();
    }

    function startAutoScroll() {
      autoScrollTimer = setInterval(moveNext, 3000);
    }

    function stopAutoScroll() {
      clearInterval(autoScrollTimer);
    }

    nextBtn.addEventListener("click", () => {
      stopAutoScroll();
      moveNext();
      startAutoScroll();
    });

    prevBtn.addEventListener("click", () => {
      stopAutoScroll();
      movePrev();
      startAutoScroll();
    });

    viewport.addEventListener("mouseenter", stopAutoScroll);
    viewport.addEventListener("mouseleave", startAutoScroll);

    startAutoScroll();

    window.addEventListener("resize", () => {
      index = 0;
      updateSlider();
    });
  });
});
