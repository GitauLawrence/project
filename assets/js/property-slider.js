document.addEventListener('DOMContentLoaded', function() {
    // Main slider elements
    const mainSlider = document.getElementById('property-slider-main');
    const navSlider = document.getElementById('property-slider-nav');

    if (mainSlider && navSlider) {
        // Get slides from DOM
        const mainSlides = Array.from(mainSlider.querySelectorAll('.property-slide'));
        const navSlides = Array.from(navSlider.querySelectorAll('.property-slide-nav'));

        // Initialize first slide if exists
        if (navSlides.length > 0) {
            navSlides[0].classList.add('active');
            preloadImages(mainSlides);
        }

        // Thumbnail click handler
        navSlides.forEach((navSlide) => {
            navSlide.addEventListener('click', function() {
                const index = this.dataset.index || navSlides.indexOf(this);
                
                if (mainSlides[index]) {
                    // Update UI
                    navSlides.forEach(s => s.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Scroll to corresponding slide
                    smoothScrollTo(mainSlider, mainSlides[index].offsetLeft, 500);
                    centerThumbnail(this);
                }
            });
        });

        // Preload images for better performance
        function preloadImages(slides) {
            slides.forEach(slide => {
                const img = slide.querySelector('img');
                if (img && !img.complete) {
                    img.onload = () => img.classList.add('loaded');
                    img.onerror = () => {
                        img.src = '/assets/images/default-property.jpg';
                        img.classList.add('error');
                    };
                }
            });
        }

        // Smooth scroll with fallback
        function smoothScrollTo(element, target, duration) {
            const start = element.scrollLeft;
            const change = target - start;
            const startTime = performance.now();
            
            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                element.scrollLeft = start + change * progress;
                if (progress < 1) requestAnimationFrame(animate);
            };
            
            requestAnimationFrame(animate);
        }

        // Center active thumbnail
        function centerThumbnail(thumb) {
            const containerWidth = navSlider.offsetWidth;
            const thumbPos = thumb.offsetLeft;
            const thumbWidth = thumb.offsetWidth;
            const maxScroll = navSlider.scrollWidth - containerWidth;
            const scrollTo = Math.max(0, Math.min(
                thumbPos - (containerWidth / 2) + (thumbWidth / 2),
                maxScroll
            ));
            
            smoothScrollTo(navSlider, scrollTo, 300);
        }

        // Modern scroll detection
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const index = mainSlides.indexOf(entry.target);
                    if (index >= 0 && navSlides[index]) {
                        navSlides.forEach(s => s.classList.remove('active'));
                        navSlides[index].classList.add('active');
                        centerThumbnail(navSlides[index]);
                    }
                }
            });
        }, {
            root: mainSlider,
            threshold: 0.5
        });

        // Observe all slides
        mainSlides.forEach(slide => observer.observe(slide));
    }
});