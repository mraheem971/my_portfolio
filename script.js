document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // DOM Elements
    // ==========================================
    const themeToggleBtn = document.getElementById('theme-toggle');
    const menuToggleBtn = document.getElementById('menu-toggle');
    const menuCloseBtn = document.getElementById('menu-close');
    const mobileNav = document.getElementById('mobile-nav');
    const mobileLinks = document.querySelectorAll('.mobile-link');
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('section');
    const contactForm = document.getElementById('contact-form');
    const formMessage = document.getElementById('form-message');
    
    // Interactive Circles Elements
    const cursorOuter = document.getElementById('cursorOuter');
    const cursorDot = document.getElementById('cursorDot');
    const scrollProgressWidget = document.getElementById('scrollProgressWidget');
    const progressRingCircle = document.getElementById('progressRingCircle');

    // ==========================================
    // Dark / Light Theme Toggle
    // ==========================================
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        document.body.classList.add('dark-theme');
        updateThemeIcon(true);
    } else {
        document.body.classList.remove('dark-theme');
        updateThemeIcon(false);
    }

    themeToggleBtn.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark-theme');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateThemeIcon(isDark);
    });

    function updateThemeIcon(isDark) {
        const icon = themeToggleBtn.querySelector('i');
        if (isDark) {
            icon.className = 'fa-solid fa-sun';
        } else {
            icon.className = 'fa-solid fa-moon';
        }
    }

    // ==========================================
    // Mobile Navigation Menu Drawer
    // ==========================================
    menuToggleBtn.addEventListener('click', () => {
        mobileNav.classList.add('open');
    });

    menuCloseBtn.addEventListener('click', () => {
        mobileNav.classList.remove('open');
    });

    mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileNav.classList.remove('open');
        });
    });

    // ==========================================
    // Global Click-Anywhere Circle Ripple Effect
    // ==========================================
    document.addEventListener('click', (e) => {
        createClickRipple(e.clientX, e.clientY);
    });

    function createClickRipple(x, y) {
        // Primary expanding circle
        const circle1 = document.createElement('div');
        circle1.className = 'click-ripple-circle';
        circle1.style.left = `${x}px`;
        circle1.style.top = `${y}px`;
        document.body.appendChild(circle1);

        // Secondary delayed dashed circle
        const circle2 = document.createElement('div');
        circle2.className = 'click-ripple-circle secondary';
        circle2.style.left = `${x}px`;
        circle2.style.top = `${y}px`;
        document.body.appendChild(circle2);

        // Clean up circles from DOM after animation completes
        setTimeout(() => {
            circle1.remove();
            circle2.remove();
        }, 800);
    }

    // ==========================================
    // Custom Interactive Cursor Circles
    // ==========================================
    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let outerX = mouseX;
    let outerY = mouseY;
    let cursorActive = false;

    window.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        if (!cursorActive && cursorOuter && cursorDot) {
            cursorOuter.style.opacity = '1';
            cursorDot.style.opacity = '1';
            cursorActive = true;
        }

        if (cursorDot) {
            cursorDot.style.left = `${mouseX}px`;
            cursorDot.style.top = `${mouseY}px`;
        }
    });

    // Smooth physics loop for outer circle follower
    function renderCursor() {
        if (cursorOuter && cursorActive) {
            outerX += (mouseX - outerX) * 0.18;
            outerY += (mouseY - outerY) * 0.18;
            cursorOuter.style.left = `${outerX}px`;
            cursorOuter.style.top = `${outerY}px`;
        }
        requestAnimationFrame(renderCursor);
    }
    renderCursor();

    // Hover effect for interactive items
    const hoverTargets = document.querySelectorAll('a, button, input, textarea, .btn, .stat-card, .badge-card, .contact-card, .project-card');
    hoverTargets.forEach(el => {
        el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
        el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
    });

    window.addEventListener('mousedown', () => document.body.classList.add('cursor-click'));
    window.addEventListener('mouseup', () => document.body.classList.remove('cursor-click'));

    // ==========================================
    // Circular Scroll Progress Indicator & Back to Top
    // ==========================================
    if (progressRingCircle && scrollProgressWidget) {
        const radius = progressRingCircle.r.baseVal.value; // 24
        const circumference = 2 * Math.PI * radius; // ~150.796
        
        progressRingCircle.style.strokeDasharray = `${circumference} ${circumference}`;
        progressRingCircle.style.strokeDashoffset = circumference;

        function updateScrollCircle() {
            const scrollTotal = document.documentElement.scrollHeight - window.innerHeight;
            const scrollCurrent = window.scrollY;
            const progress = scrollTotal > 0 ? Math.min(Math.max(scrollCurrent / scrollTotal, 0), 1) : 0;
            
            const offset = circumference - (progress * circumference);
            progressRingCircle.style.strokeDashoffset = offset;

            // Show widget when scrolled down past 150px
            if (scrollCurrent > 150) {
                scrollProgressWidget.classList.add('visible');
            } else {
                scrollProgressWidget.classList.remove('visible');
            }
        }

        window.addEventListener('scroll', updateScrollCircle, { passive: true });
        updateScrollCircle();

        // Smooth scroll to top on widget click
        scrollProgressWidget.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ==========================================
    // Active Navigation Link on Scroll
    // ==========================================
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -55% 0px',
        threshold: 0
    };

    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const activeId = entry.target.getAttribute('id');
                
                // Desktop Nav Links
                navLinks.forEach(link => {
                    if (link.getAttribute('href') === `#${activeId}`) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });

                // Mobile Nav Links
                mobileLinks.forEach(link => {
                    if (link.getAttribute('href') === `#${activeId}`) {
                        link.style.color = 'var(--primary-color)';
                        link.style.fontWeight = '700';
                    } else {
                        link.style.color = '';
                        link.style.fontWeight = '';
                    }
                });
            }
        });
    }, observerOptions);

    sections.forEach(section => {
        sectionObserver.observe(section);
    });

    // ==========================================
    // Quick Verification Human Captcha
    // ==========================================
    let captchaNum1 = 0;
    let captchaNum2 = 0;
    const captchaQuestionEl = document.getElementById('captchaQuestion');
    const refreshCaptchaBtn = document.getElementById('refreshCaptchaBtn');
    const captchaAnswerEl = document.getElementById('captchaAnswer');

    function generateCaptcha() {
        captchaNum1 = Math.floor(Math.random() * 8) + 2; // 2 to 9
        captchaNum2 = Math.floor(Math.random() * 7) + 1; // 1 to 7
        if (captchaQuestionEl) {
            captchaQuestionEl.textContent = `${captchaNum1} + ${captchaNum2} = ?`;
        }
        if (captchaAnswerEl) {
            captchaAnswerEl.value = '';
            captchaAnswerEl.style.borderColor = '';
        }
    }

    if (captchaQuestionEl) {
        generateCaptcha();
    }

    if (refreshCaptchaBtn) {
        refreshCaptchaBtn.addEventListener('click', () => {
            generateCaptcha();
        });
    }

    // ==========================================
    // Contact Form Submission Handler
    // ==========================================
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const nameVal = document.getElementById('name').value.trim();
            const emailVal = document.getElementById('email').value.trim();
            const subjectVal = document.getElementById('subject') ? document.getElementById('subject').value.trim() : '';
            const messageVal = document.getElementById('message').value.trim();
            const captchaInputVal = captchaAnswerEl ? parseInt(captchaAnswerEl.value.trim(), 10) : null;

            if (!nameVal || !emailVal || !subjectVal || !messageVal) {
                showFormStatus('Please complete all required fields.', 'error');
                return;
            }

            // Quick Human Verification Check
            if (captchaAnswerEl && captchaInputVal !== (captchaNum1 + captchaNum2)) {
                captchaAnswerEl.style.borderColor = '#ef4444';
                showFormStatus("Verification failed: Please enter the correct answer to prove you're human.", 'error');
                generateCaptcha();
                captchaAnswerEl.focus();
                return;
            }

            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>Sending Message...</span> <i class="fa-solid fa-spinner fa-spin"></i>';

            const formData = new FormData(contactForm);

            fetch('api/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (data.success) {
                    showFormStatus(data.message, 'success');
                    contactForm.reset();
                    generateCaptcha();
                } else {
                    showFormStatus(data.message || 'Failed to send message. Please try again.', 'error');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                showFormStatus('An unexpected network error occurred. Please try again.', 'error');
            });
        });
    }

    function showFormStatus(msg, type) {
        formMessage.textContent = msg;
        formMessage.className = `form-status-msg ${type}`;
        
        setTimeout(() => {
            formMessage.className = 'form-status-msg hidden';
        }, 5500);
    }

    // ==========================================
    // Dynamic Scroll Reveal Animations (Up & Down)
    // Elements fly in animatedly from outside whenever entering viewport
    // ==========================================
    const revealElements = document.querySelectorAll('.reveal');

    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                } else {
                    // Remove when scrolled past, so scrolling back (up or down) triggers animation again
                    entry.target.classList.remove('is-revealed');
                }
            });
        }, {
            root: null,
            threshold: 0.12,
            rootMargin: '0px 0px -30px 0px'
        });

        revealElements.forEach(el => {
            revealObserver.observe(el);
        });
    }
});
