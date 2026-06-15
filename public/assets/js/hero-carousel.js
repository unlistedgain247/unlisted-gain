document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.carousel-track');
    const originalImages = document.querySelectorAll('.carousel-img');
    
    if (!track || originalImages.length === 0) return;

    // Clone the first image and append to end for seamless loop
    const firstClone = originalImages[0].cloneNode(true);
    track.appendChild(firstClone);

    const allImages = document.querySelectorAll('.carousel-img');
    let currentIndex = 0;
    let isTransitioning = false;

    // Update track width to accommodate the cloned image
    track.style.width = `${allImages.length * 100}%`;

    const getSlideWidth = () => {
        return track.parentElement.clientWidth;
    };

    const slideTo = (index, transition = true) => {
        const width = getSlideWidth();
        if (transition) {
            track.style.transition = 'transform 0.8s ease-in-out';
        } else {
            track.style.transition = 'none';
        }
        track.style.transform = `translateX(-${index * width}px)`;
    };

    setInterval(() => {
        if (isTransitioning) return;
        
        currentIndex++;
        slideTo(currentIndex);

        // If we reached the clone (at the end)
        if (currentIndex === originalImages.length) {
            isTransitioning = true;
            setTimeout(() => {
                // Jump back to the start without transition
                currentIndex = 0;
                slideTo(currentIndex, false);
                isTransitioning = false;
            }, 800); // Same as transition duration
        }
    }, 3000);

    // Handle window resize
    window.addEventListener('resize', () => {
        slideTo(currentIndex, false);
    });
});
