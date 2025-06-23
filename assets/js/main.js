document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const closeMenu = document.querySelector('.close-menu');

    if (mobileMenuToggle && mobileMenu && closeMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
        });

        closeMenu.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
        });
    }

    // Mobile Submenu Toggle
    const hasSubmenu = document.querySelectorAll('.mobile-nav .has-submenu > a');

    if (hasSubmenu) {
        hasSubmenu.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const submenu = parent.querySelector('.submenu');

                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none';
                    this.querySelector('i').classList.remove('fa-chevron-up');
                    this.querySelector('i').classList.add('fa-chevron-down');
                } else {
                    submenu.style.display = 'block';
                    this.querySelector('i').classList.remove('fa-chevron-down');
                    this.querySelector('i').classList.add('fa-chevron-up');
                }
            });
        });
    }

    // Property Type Tabs in Search
    const propertyTypeTabs = document.querySelectorAll('.property-type-tabs .tab');
    const propertyTypeInput = document.getElementById('property_type');

    if (propertyTypeTabs.length > 0 && propertyTypeInput) {
        propertyTypeTabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                propertyTypeTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                propertyTypeInput.value = this.getAttribute('data-type');
            });
        });
    }

    // Back to Top Button
    const backToTopButton = document.getElementById('backToTop');

    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('active');
            } else {
                backToTopButton.classList.remove('active');
            }
        });

        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Sticky Header
    const header = document.querySelector('.site-header');

    if (header) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 50) {
                header.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
                header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            }
        });
    }
    
    // General Contact Form Submission
    const contactForm = document.getElementById('contact-form');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(contactForm);
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });

            const submitButton = contactForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton ? submitButton.textContent : 'Send Message';
            if (submitButton) {
                submitButton.textContent = 'Sending...';
                submitButton.disabled = true;
            }

            fetch('process_inquiry.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formDataObj).toString()
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error('HTTP error! Status: ' + response.status + ' - Response: ' + text); });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message); // Re-enabling alert for success
                    contactForm.reset();
                } else {
                    alert('Error: ' + data.message); // Re-enabling alert for error
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error); // Keep console error for silent debugging
                alert('An error occurred during submission. Please try again.');
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                }
            });
        });
    }

    // Property Inquiry Form Submission
    const propertyInquiryForm = document.getElementById('property-inquiry-form');

    if (propertyInquiryForm) {
        propertyInquiryForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(propertyInquiryForm);
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });

            const submitButton = propertyInquiryForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton ? submitButton.textContent : 'Send Message';
            if (submitButton) {
                submitButton.textContent = 'Sending...';
                submitButton.disabled = true;
            }

            fetch('process_inquiry.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(formDataObj).toString()
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error('HTTP error! Status: ' + response.status + ' - Response: ' + text); });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message); // Re-enabling alert for success
                    propertyInquiryForm.reset();
                } else {
                    alert('Error: ' + data.message); // Re-enabling alert for error
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error); // Keep console error for silent debugging
                alert('An error occurred during submission. Please try again.');
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                }
            });
        });
    }

    // === Review Carousel ===/
    const slides = document.querySelectorAll(".review-slide");
    const prevBtn = document.getElementById("prev-review");
    const nextBtn = document.getElementById("next-review");

    if (slides.length && prevBtn && nextBtn) {
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle("active", i === index);
            });
        }

        prevBtn.addEventListener("click", () => {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        });

        nextBtn.addEventListener("click", () => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        });

        // Auto slide every 6s
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 6000);

        showSlide(currentSlide); // show initial
    }
}); // End of DOMContentLoaded