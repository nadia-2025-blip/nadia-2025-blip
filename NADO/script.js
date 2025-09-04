// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});



// Form submission
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(this);
    const data = {
        fullName: formData.get('fullName'),
        phone: formData.get('phone'),
        email: formData.get('email'),
        city: formData.get('city'),
        timestamp: new Date().toISOString()
    };
    
    // Validate required fields
    if (!data.fullName || !data.phone || !data.city) {
        alert('يرجى ملء جميع الحقول المطلوبة');
        return;
    }
    
    // Validate phone number (basic validation)
    const phoneRegex = /^[0-9+\-\s()]+$/;
    if (!phoneRegex.test(data.phone)) {
        alert('يرجى إدخال رقم هاتف صحيح');
        return;
    }
    
    // Show loading state
    const submitBtn = document.querySelector('.submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
    submitBtn.disabled = true;
    
    // Send data to server
    fetch('register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Show success modal
            document.getElementById('successModal').style.display = 'block';
            // Reset form
            document.getElementById('registrationForm').reset();
        } else {
            alert('حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Modal functionality
const modal = document.getElementById('successModal');
const closeBtn = document.querySelector('.close');

closeBtn.addEventListener('click', function() {
    modal.style.display = 'none';
});

window.addEventListener('click', function(e) {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.dessert-card, .point, .achievement');
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});

// Mobile menu toggle with improved touch handling
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    
    navLinks.classList.toggle('active');
    mobileToggle.classList.toggle('active');
    
    // Prevent body scroll when menu is open
    if (navLinks.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
}

// Touch handling for mobile devices
function initTouchHandling() {
    let startY = 0;
    let startX = 0;
    
    // Prevent pull-to-refresh on mobile
    document.addEventListener('touchstart', function(e) {
        startY = e.touches[0].pageY;
        startX = e.touches[0].pageX;
    }, { passive: true });
    
    document.addEventListener('touchmove', function(e) {
        const currentY = e.touches[0].pageY;
        const currentX = e.touches[0].pageX;
        
        // Prevent pull-to-refresh if scrolling down from top
        if (window.scrollY === 0 && currentY > startY) {
            e.preventDefault();
        }
        
        // Close mobile menu on swipe up
        const navLinks = document.querySelector('.nav-links');
        if (navLinks && navLinks.classList.contains('active')) {
            const deltaY = startY - currentY;
            if (deltaY > 50) { // Swipe up threshold
                toggleMobileMenu();
            }
        }
    }, { passive: false });
}

// Optimize images for mobile
function optimizeImagesForMobile() {
    const images = document.querySelectorAll('img');
    
    images.forEach(img => {
        // Add loading="lazy" for better performance
        if (!img.hasAttribute('loading')) {
            img.setAttribute('loading', 'lazy');
        }
        
        // Add error handling
        img.addEventListener('error', function() {
            this.style.display = 'none';
        });
    });
}

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Improved scroll handling for mobile
function initMobileScrollHandling() {
    let ticking = false;
    
    const handleScroll = throttle(() => {
        const header = document.querySelector('.header');
        const scrollY = window.scrollY;
        
        // Hide/show header on scroll for mobile
        if (window.innerWidth <= 768) {
            if (scrollY > 100) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
        }
    }, 100);
    
    window.addEventListener('scroll', handleScroll, { passive: true });
}

// Viewport height fix for mobile browsers
function fixMobileViewport() {
    const setVH = () => {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    };
    
    setVH();
    window.addEventListener('resize', debounce(setVH, 100));
    window.addEventListener('orientationchange', debounce(setVH, 100));
}

// Phone validation and network detection
function initPhoneValidation() {
    const phoneInput = document.getElementById('phone');
    const phoneWrapper = phoneInput.parentElement;
    const feedbackDiv = document.getElementById('phoneFeedback');
    
    phoneInput.addEventListener('input', function() {
        const phoneNumber = this.value.replace(/\D/g, '');
        validatePhoneNumber(phoneNumber, phoneWrapper, feedbackDiv);
    });
}

function validatePhoneNumber(phoneNumber, wrapper, feedbackDiv) {
    // Reset states
    wrapper.classList.remove('valid', 'invalid');
    
    if (phoneNumber.length === 0) {
        hideFeedback(feedbackDiv);
        return;
    }
    
    if (phoneNumber.length < 10) {
        wrapper.classList.add('invalid');
        showFeedback(feedbackDiv, 'رقم الهاتف يجب أن يكون 10 أرقام', 'error');
        return;
    }
    
    if (phoneNumber.length === 10) {
        const prefix = phoneNumber.substring(0, 2);
        const prefix3 = phoneNumber.substring(0, 3);
        
        let detectedNetwork = null;
        let networkName = '';
        
        // Detect network based on prefix
        if (prefix === '06') {
            detectedNetwork = 'mobilis';
            networkName = 'Mobilis';
        } else if (prefix === '07') {
            detectedNetwork = 'djezzy';
            networkName = 'Djezzy';
        } else if (prefix === '05') {
            detectedNetwork = 'ooredoo';
            networkName = 'Ooredoo';
        }
        
        if (detectedNetwork) {
            wrapper.classList.add('valid');
            showFeedback(feedbackDiv, `رقم صحيح - شبكة ${networkName}`, 'success');
        } else {
            wrapper.classList.add('invalid');
            showFeedback(feedbackDiv, 'رقم غير صحيح - يرجى التحقق من البادئة', 'error');
        }
    }
}

function showFeedback(feedbackDiv, message, type) {
    feedbackDiv.innerHTML = `<div class="feedback-message ${type}">${message}</div>`;
}

function hideFeedback(feedbackDiv) {
    feedbackDiv.innerHTML = '';
}

// Network dropdown functionality
function initNetworkDropdown() {
    const countrySelector = document.getElementById('countrySelector');
    const networkDropdown = document.getElementById('networkDropdown');
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const phoneInput = document.getElementById('phone');
    
    // Toggle dropdown
    countrySelector.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('open');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        countrySelector.classList.remove('open');
    });
    
    // Handle dropdown item selection
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const network = this.dataset.network;
            const networkName = this.querySelector('.dropdown-name').textContent;
            const prefix = this.querySelector('.dropdown-prefix').textContent;
            
            // Update phone input placeholder based on selected network
            if (network === 'mobilis') {
                phoneInput.placeholder = '06X XXX XXXX';
            } else if (network === 'djezzy') {
                phoneInput.placeholder = '07X XXX XXXX';
            } else if (network === 'ooredoo') {
                phoneInput.placeholder = '05X XXX XXXX';
            }
            
            // Close dropdown
            countrySelector.classList.remove('open');
            
            // Show feedback
            const feedbackDiv = document.getElementById('phoneFeedback');
            showFeedback(feedbackDiv, `تم اختيار شبكة ${networkName} - أدخل رقم يبدأ بـ ${prefix}`, 'info');
        });
    });
}

// Fast DOM ready
function domReady(fn) {
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

// Initialize mobile optimizations
domReady(function() {
    // Initialize phone validation
    initPhoneValidation();
    
    // Initialize network dropdown
    initNetworkDropdown();
    
    // Initialize mobile optimizations
    initTouchHandling();
    optimizeImagesForMobile();
    initMobileScrollHandling();
    fixMobileViewport();
    
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', toggleMobileMenu);
        
        // Add touch event for better mobile response
        mobileToggle.addEventListener('touchstart', function(e) {
            e.preventDefault();
            toggleMobileMenu();
        }, { passive: false });
    }
    
    // Close mobile menu when clicking on links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            
            if (navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                mobileToggle.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        const navLinks = document.querySelector('.nav-links');
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const nav = document.querySelector('.nav');
        
        if (navLinks && navLinks.classList.contains('active') && !nav.contains(e.target)) {
            navLinks.classList.remove('active');
            mobileToggle.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
    
    // Improve form inputs for mobile
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        // Prevent zoom on iOS when focusing inputs
        if (input.type !== 'file') {
            input.style.fontSize = '16px';
        }
        
        // Add better touch handling
        input.addEventListener('touchstart', function() {
            this.focus();
        }, { passive: true });
    });
    
    // Optimize slider performance for mobile
    if (window.innerWidth <= 768) {
        // Reduce animation frequency on mobile
        const slides = document.querySelectorAll('.slide');
        slides.forEach(slide => {
            slide.style.transition = 'opacity 0.3s ease';
        });
    }
});

// Countdown Timer
function startCountdown() {
    const hoursElement = document.getElementById('hours');
    const minutesElement = document.getElementById('minutes');
    
    let totalMinutes = 48 * 60; // 48 hours in minutes
    
    const timer = setInterval(() => {
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        
        if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
        if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
        
        totalMinutes--;
        
        if (totalMinutes < 0) {
            clearInterval(timer);
            if (hoursElement) hoursElement.textContent = '00';
            if (minutesElement) minutesElement.textContent = '00';
        }
    }, 60000); // Update every minute
}

// Particle effect for hero button
function createParticles(button) {
    const particles = button.querySelector('.btn-particles');
    if (particles) {
        particles.style.left = '-100%';
        setTimeout(() => {
            particles.style.left = '100%';
        }, 100);
    }
}

// Add click tracking for analytics (placeholder)
function trackClick(element, action) {
    // This would integrate with analytics services like Google Analytics
    console.log(`Clicked: ${element} - Action: ${action}`);
}

// Add event listeners for tracking
document.addEventListener('DOMContentLoaded', function() {
    // Start countdown timer
    startCountdown();
    
    // Add particle effect to hero button
    const heroBtn = document.querySelector('.btn-primary-hero');
    if (heroBtn) {
        heroBtn.addEventListener('mouseenter', function() {
            createParticles(this);
        });
    }
    
    // Track CTA button clicks
    document.querySelectorAll('.btn-primary, .cta-btn, .btn-primary-hero').forEach(btn => {
        btn.addEventListener('click', function() {
            trackClick('CTA Button', 'Register Now');
        });
    });
    
    // Track form submission attempts
    const form = document.getElementById('registrationForm');
    if (form) {
        form.addEventListener('submit', function() {
            trackClick('Registration Form', 'Submit');
        });
    }
    
    // Smooth scroll for scroll indicator
    const scrollArrow = document.querySelector('.scroll-arrow');
    if (scrollArrow) {
        scrollArrow.addEventListener('click', function() {
            document.querySelector('#about').scrollIntoView({
                behavior: 'smooth'
            });
        });
    }
    
    // Handle review form submission
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const reviewData = {
                name: formData.get('reviewName'),
                rating: formData.get('rating'),
                text: formData.get('reviewText'),
                timestamp: new Date().toISOString()
            };
            
            // Validate form
            if (!reviewData.name || !reviewData.rating || !reviewData.text) {
                alert('يرجى ملء جميع الحقول');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('.submit-review-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
            submitBtn.disabled = true;
            
            // Add new review to the grid
            addNewReview(reviewData);
            
            // Reset form
            this.reset();
            
            // Reset button
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                alert('تم إضافة تقييمك بنجاح! شكراً لك.');
            }, 1000);
        });
    }
});

// Function to add new review to the grid
function addNewReview(reviewData) {
    const testimonialsGrid = document.querySelector('.testimonials-grid');
    
    // Generate random avatar color
    const colors = ['#FFD700', '#FFB6C1', '#D2B48C', '#FFA500', '#FF6B6B', '#9C27B0'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    
    // Generate stars HTML
    let starsHTML = '';
    for (let i = 0; i < 5; i++) {
        if (i < reviewData.rating) {
            starsHTML += '<i class="fas fa-star"></i>';
        } else {
            starsHTML += '<i class="far fa-star"></i>';
        }
    }
    
    // Create new review card
    const newReviewHTML = `
        <div class="testimonial-card new-review">
            <div class="testimonial-header">
                <div class="testimonial-avatar">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMzAiIGZpbGw9IiR7cmFuZG9tQ29sb3J9Ii8+CjxjaXJjbGUgY3g9IjMwIiBjeT0iMjUiIHI9IjEwIiBmaWxsPSIjOEI0NTEzIi8+CjxwYXRoIGQ9Ik0xNSA0NUMxNSAzNy4yNjg0IDIxLjI2ODQgMzEgMjkgMzFIMzFDMzguNzMxNiAzMSA0NSAzNy4yNjg0IDQ1IDQ1VjUwSDE1VjQ1WiIgZmlsbD0iIzhCNDUxMyIvPgo8L3N2Zz4K" alt="${reviewData.name}">
                </div>
                <div class="testimonial-info">
                    <h4>${reviewData.name}</h4>
                    <div class="stars">
                        ${starsHTML}
                    </div>
                </div>
            </div>
            <div class="testimonial-content">
                <p>"${reviewData.text}"</p>
            </div>
        </div>
    `;
    
    // Add to beginning of grid
    testimonialsGrid.insertAdjacentHTML('afterbegin', newReviewHTML);
    
    // Scroll to new review
    setTimeout(() => {
        const newReview = testimonialsGrid.querySelector('.new-review');
        newReview.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Remove new-review class after animation
        setTimeout(() => {
            newReview.classList.remove('new-review');
        }, 2000);
    }, 100);
}

// Certificate modal functionality
function initCertificateModal() {
    // Create modal HTML
    const modalHTML = `
        <div id="certificateModal" class="certificate-modal">
            <div class="certificate-modal-content">
                <span class="certificate-close">&times;</span>
                <img id="certificateModalImage" src="" alt="شهادة">
                <div class="certificate-modal-info">
                    <h3>شهادة إتمام الدورة</h3>
                    <p>الحلويات التقليدية والعصرية - معهد الأستاذة نادية</p>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Get modal elements
    const modal = document.getElementById('certificateModal');
    const modalImg = document.getElementById('certificateModalImage');
    const closeBtn = document.querySelector('.certificate-close');
    
    // Add click event to certificate overlays
    document.querySelectorAll('.certificate-overlay').forEach(overlay => {
        overlay.addEventListener('click', function() {
            const img = this.parentElement.querySelector('img');
            modalImg.src = img.src;
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });
    
    // Close modal events
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
}

// Optimized lazy loading
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                imageObserver.unobserve(img);
            }
        });
    }, { rootMargin: '50px' });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Preload critical images
const criticalImages = [
    'images/logo.jpeg',
    'mgs/istockphoto-184660869-612x612.jpg'
];
criticalImages.forEach(src => {
    const link = document.createElement('link');
    link.rel = 'preload';
    link.as = 'image';
    link.href = src;
    document.head.appendChild(link);
});

// Certificate slider functionality
function initCertificateSlider() {
    const slides = document.querySelectorAll('.certificates-slider .slide');
    const dots = document.querySelectorAll('.certificates-slider .dot');
    let currentSlide = 0;
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    // Auto slide every 3 seconds
    setInterval(nextSlide, 3000);
    
    // Dot click functionality
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            showSlide(currentSlide);
        });
    });
}

// Gallery slider functionality
let currentGallerySlide = 0;
const gallerySlides = document.querySelectorAll('.gallery-slider .slide');
const galleryDots = document.querySelectorAll('.gallery-slider .dot');

function showGallerySlide(index) {
    gallerySlides.forEach(slide => slide.classList.remove('active'));
    galleryDots.forEach(dot => dot.classList.remove('active'));
    
    gallerySlides[index].classList.add('active');
    galleryDots[index].classList.add('active');
}

function changeSlide(direction) {
    currentGallerySlide += direction;
    
    if (currentGallerySlide >= gallerySlides.length) {
        currentGallerySlide = 0;
    } else if (currentGallerySlide < 0) {
        currentGallerySlide = gallerySlides.length - 1;
    }
    
    showGallerySlide(currentGallerySlide);
}

function currentSlide(index) {
    currentGallerySlide = index - 1;
    showGallerySlide(currentGallerySlide);
}

// Optimized auto slide
let galleryInterval;
function startGallerySlider() {
    galleryInterval = setInterval(() => {
        changeSlide(1);
    }, 4000);
}

// Pause on visibility change
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(galleryInterval);
    } else {
        startGallerySlider();
    }
});

startGallerySlider();

// Second gallery slider functionality
let currentGallerySlide2 = 0;
const gallerySlides2 = document.querySelectorAll('.video-section .slide');
const galleryDots2 = document.querySelectorAll('.video-section .dot');

function showGallerySlide2(index) {
    gallerySlides2.forEach(slide => slide.classList.remove('active'));
    galleryDots2.forEach(dot => dot.classList.remove('active'));
    
    gallerySlides2[index].classList.add('active');
    galleryDots2[index].classList.add('active');
}

function changeSlide2(direction) {
    currentGallerySlide2 += direction;
    
    if (currentGallerySlide2 >= gallerySlides2.length) {
        currentGallerySlide2 = 0;
    } else if (currentGallerySlide2 < 0) {
        currentGallerySlide2 = gallerySlides2.length - 1;
    }
    
    showGallerySlide2(currentGallerySlide2);
}

function currentSlide2(index) {
    currentGallerySlide2 = index - 1;
    showGallerySlide2(currentGallerySlide2);
}

// Optimized second gallery
let gallery2Interval;
function startGallery2Slider() {
    gallery2Interval = setInterval(() => {
        changeSlide2(-1);
    }, 3500);
}

document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(gallery2Interval);
    } else {
        startGallery2Slider();
    }
});

startGallery2Slider();

// Toggle city input
function toggleCityInput() {
    const citySelect = document.getElementById('city');
    const customCityInput = document.getElementById('customCity');
    
    if (citySelect.value === 'other') {
        customCityInput.style.display = 'block';
        customCityInput.required = true;
        citySelect.required = false;
    } else {
        customCityInput.style.display = 'none';
        customCityInput.required = false;
        citySelect.required = true;
    }
}

// Initialize certificate slider when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        initCertificateSlider();
    }, 500);
});