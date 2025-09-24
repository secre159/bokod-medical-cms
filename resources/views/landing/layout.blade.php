<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bokod Medical CMS - Professional Healthcare Management System')</title>
    <meta name="description" content="@yield('description', 'Professional healthcare management system for medical practices. Manage patients, appointments, prescriptions, and more with ease.')">
    
    {{-- Custom Favicon --}}
    @if(config('app.favicon'))
        <link rel="icon" type="image/png" href="{{ asset(config('app.favicon')) }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset(config('app.favicon')) }}">
        <link rel="apple-touch-icon" href="{{ asset(config('app.favicon')) }}">
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --text-dark: #1a202c;
            --text-light: #718096;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --error-color: #f56565;
        }
        
        * {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        
        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .loading-screen.hidden {
            opacity: 0;
            visibility: hidden;
        }
        
        .loader {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Particle Background */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: particleFloat 15s infinite linear;
        }
        
        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }
        
        .medical-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M30 15c8.284 0 15 6.716 15 15s-6.716 15-15 15-15-6.716-15-15 6.716-15 15-15zm0 2c-7.18 0-13 5.82-13 13s5.82 13 13 13 13-5.82 13-13-5.82-13-13-13z'/%3E%3Cpath d='M30 25v10m-5-5h10'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        /* Enhanced Animations */
        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        
        .feature-card:hover::before {
            left: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .stats-counter {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(2deg); }
            66% { transform: translateY(-10px) rotate(-2deg); }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }
        
        .section-padding {
            padding: 6rem 0;
        }
        
        /* Scroll Animations */
        .scroll-animate {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .scroll-animate.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Testimonial Cards Enhancement */
        .testimonial-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px) rotateY(5deg);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        /* Interactive Demo Styles */
        .demo-container {
            perspective: 1000px;
        }
        
        .demo-screen {
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }
        
        .demo-screen:hover {
            transform: rotateY(10deg) rotateX(5deg);
        }
        
        /* Pricing Cards */
        .pricing-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        .pricing-card.featured {
            transform: scale(1.05);
            border: 2px solid var(--primary-color);
        }
        
        .pricing-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        
        /* FAQ Accordion */
        .faq-item {
            transition: all 0.3s ease;
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .faq-item.active .faq-answer {
            max-height: 200px;
        }
        
        /* Form Enhancements */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-input {
            transition: all 0.3s ease;
            border: 2px solid #e2e8f0;
        }
        
        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* Mobile Enhancements */
        @media (max-width: 768px) {
            .section-padding {
                padding: 4rem 0;
            }
            
            .stats-counter {
                font-size: 2.5rem;
            }
            
            .feature-card:hover {
                transform: translateY(-4px) scale(1.01);
            }
            
            .pricing-card.featured {
                transform: none;
                margin: 1rem 0;
            }
        }
        
        /* Typing Animation */
        .typing-text {
            overflow: hidden;
            border-right: .15em solid orange;
            white-space: nowrap;
            margin: 0 auto;
            letter-spacing: .15em;
            animation: typing 3.5s steps(40, end), blink-caret .75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: orange; }
        }
        
        /* Progress Bar */
        .progress-bar {
            height: 4px;
            background: var(--primary-color);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: width 0.3s ease;
        }
        
        /* Desktop-First Enhancements */
        .desktop-showcase {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
        }
        
        .desktop-showcase::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .monitor-mockup {
            transform-style: preserve-3d;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .monitor-mockup:hover {
            transform: rotateY(5deg) rotateX(2deg) scale(1.02);
        }
        
        .desktop-feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        @media (max-width: 768px) {
            .desktop-feature-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
        
        /* Touch-Optimized Elements */
        .touch-manipulation {
            touch-action: manipulation;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        .touch-target {
            min-height: 44px;
            min-width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Mobile-First Typography */
        .responsive-text {
            font-size: clamp(1rem, 4vw, 1.5rem);
            line-height: 1.4;
        }
        
        .responsive-heading {
            font-size: clamp(1.5rem, 6vw, 3rem);
            line-height: 1.2;
        }
        
        /* Mobile Navigation Enhancements */
        .mobile-nav-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }
        
        .mobile-nav-item:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
        
        .mobile-nav-item:last-child {
            border-bottom: none;
        }
        
        /* Desktop Power Indicators */
        .power-indicator {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        /* Improved Feature Cards for Mobile */
        @media (max-width: 640px) {
            .feature-card {
                margin-bottom: 1rem;
            }
            
            .feature-card h3 {
                font-size: 1.125rem;
            }
            
            .feature-card p {
                font-size: 0.875rem;
                line-height: 1.5;
            }
        }
        
        /* Desktop-Specific Animations */
        @media (min-width: 1024px) {
            .desktop-animation {
                animation: desktopFloat 8s ease-in-out infinite;
            }
            
            @keyframes desktopFloat {
                0%, 100% { transform: translateY(0px) rotateY(0deg); }
                25% { transform: translateY(-10px) rotateY(2deg); }
                50% { transform: translateY(-20px) rotateY(0deg); }
                75% { transform: translateY(-10px) rotateY(-2deg); }
            }
        }
        
        /* Mobile Performance Optimizations */
        @media (max-width: 768px) {
            .animate-float {
                animation: none;
            }
            
            .particle {
                display: none;
            }
            
            .parallax {
                transform: none !important;
            }
        }
        
        /* Improved Button Interactions */
        .btn-touch {
            position: relative;
            overflow: hidden;
            transform: translateZ(0);
        }
        
        .btn-touch::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-touch:active::after {
            width: 300px;
            height: 300px;
        }
    </style>
    </style>
</head>
<body class="antialiased">
    @yield('content')

    <!-- Scripts -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        // Loading Screen
        window.addEventListener('load', function() {
            setTimeout(() => {
                const loadingScreen = document.querySelector('.loading-screen');
                if (loadingScreen) {
                    loadingScreen.classList.add('hidden');
                }
            }, 1000);
        });
        
        // Progress Bar
        function updateProgressBar() {
            const progressBar = document.querySelector('.progress-bar');
            if (!progressBar) return;
            
            const scrollTop = window.scrollY;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const progress = (scrollTop / docHeight) * 100;
            
            progressBar.style.width = progress + '%';
        }
        
        window.addEventListener('scroll', updateProgressBar);
        
        // Particle System
        function createParticles() {
            const particles = document.querySelector('.particles');
            if (!particles) return;
            
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particles.appendChild(particle);
            }
        }
        
        // Scroll Animation Observer
        function initScrollAnimations() {
            const animatedElements = document.querySelectorAll('.scroll-animate');
            
            const scrollObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            animatedElements.forEach(el => {
                scrollObserver.observe(el);
            });
        }
        
        // Smooth scrolling for anchor links
        function initSmoothScrolling() {
            const links = document.querySelectorAll('a[href^="#"]');
            
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - 70; // Account for fixed nav
                        
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        }
        
        // Enhanced Counter Animation
        function animateCounters() {
            const counters = document.querySelectorAll('[data-count]');
            
            counters.forEach((counter, index) => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 3000; // 3 seconds
                const increment = target / (duration / 16);
                let current = 0;
                
                // Stagger animation start
                setTimeout(() => {
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        counter.textContent = Math.floor(current).toLocaleString();
                        
                        // Add pulse effect
                        counter.style.transform = 'scale(1.05)';
                        setTimeout(() => {
                            counter.style.transform = 'scale(1)';
                        }, 100);
                    }, 16);
                }, index * 200);
            });
        }
        
        // Typing Effect
        function initTypingEffect() {
            const typingElements = document.querySelectorAll('[data-typing]');
            
            typingElements.forEach(element => {
                const text = element.getAttribute('data-typing');
                const speed = parseInt(element.getAttribute('data-speed')) || 50;
                let index = 0;
                
                element.textContent = '';
                
                function typeChar() {
                    if (index < text.length) {
                        element.textContent += text.charAt(index);
                        index++;
                        setTimeout(typeChar, speed);
                    }
                }
                
                // Start typing when element is visible
                const typingObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            setTimeout(typeChar, 500);
                            typingObserver.unobserve(entry.target);
                        }
                    });
                });
                
                typingObserver.observe(element);
            });
        }
        
        // FAQ Accordion
        function initFAQ() {
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                const answer = item.querySelector('.faq-answer');
                
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    
                    // Close all other items
                    faqItems.forEach(otherItem => {
                        otherItem.classList.remove('active');
                    });
                    
                    // Toggle current item
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });
        }
        
        // Form Enhancements
        function initFormEnhancements() {
            const forms = document.querySelectorAll('.enhanced-form');
            
            forms.forEach(form => {
                const inputs = form.querySelectorAll('.form-input');
                
                inputs.forEach(input => {
                    // Floating label effect
                    input.addEventListener('focus', () => {
                        input.parentElement.classList.add('focused');
                    });
                    
                    input.addEventListener('blur', () => {
                        if (!input.value) {
                            input.parentElement.classList.remove('focused');
                        }
                    });
                    
                    // Real-time validation feedback
                    input.addEventListener('input', () => {
                        validateField(input);
                    });
                });
                
                // Form submission
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = form.querySelector('[type="submit"]');
                    const originalText = submitBtn.textContent;
                    
                    // Show loading state
                    submitBtn.textContent = 'Sending...';
                    submitBtn.disabled = true;
                    
                    // Simulate form submission
                    setTimeout(() => {
                        showSuccessMessage();
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        form.reset();
                    }, 2000);
                });
            });
        }
        
        function validateField(field) {
            const value = field.value.trim();
            const type = field.type;
            let isValid = true;
            
            // Basic validation
            if (field.required && !value) {
                isValid = false;
            } else if (type === 'email' && value && !isValidEmail(value)) {
                isValid = false;
            }
            
            // Visual feedback
            if (isValid) {
                field.classList.remove('error');
                field.classList.add('success');
            } else {
                field.classList.remove('success');
                field.classList.add('error');
            }
        }
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function showSuccessMessage() {
            // Create success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50';
            notification.innerHTML = '<i class="fas fa-check mr-2"></i>Message sent successfully!';
            
            document.body.appendChild(notification);
            
            // Slide in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Slide out and remove
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
        
        // Interactive Demo
        function initInteractiveDemo() {
            const demoTabs = document.querySelectorAll('.demo-tab');
            const demoContent = document.querySelectorAll('.demo-content');
            
            demoTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = tab.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and content
                    demoTabs.forEach(t => t.classList.remove('active'));
                    demoContent.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(target).classList.add('active');
                });
            });
        }
        
        // Parallax Effect
        function initParallax() {
            const parallaxElements = document.querySelectorAll('.parallax');
            
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                
                parallaxElements.forEach(element => {
                    const rate = scrolled * -0.5;
                    element.style.transform = `translateY(${rate}px)`;
                });
            });
        }
        
        // Stats Observer
        function initStatsObserver() {
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.target.id === 'stats') {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            const statsSection = document.getElementById('stats');
            if (statsSection) {
                observer.observe(statsSection);
            }
        }
        
        // Mouse movement effect for hero section
        function initMouseEffect() {
            const hero = document.querySelector('.hero-section');
            if (!hero) return;
            
            hero.addEventListener('mousemove', (e) => {
                const { clientX, clientY } = e;
                const { innerWidth, innerHeight } = window;
                
                const xPercent = (clientX / innerWidth - 0.5) * 2;
                const yPercent = (clientY / innerHeight - 0.5) * 2;
                
                const floatingElements = hero.querySelectorAll('.animate-float');
                floatingElements.forEach((element, index) => {
                    const intensity = (index + 1) * 5;
                    element.style.transform = `translate(${xPercent * intensity}px, ${yPercent * intensity}px)`;
                });
            });
        }
        
        // Initialize all functionality
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            initScrollAnimations();
            initSmoothScrolling();
            initTypingEffect();
            initFAQ();
            initFormEnhancements();
            initInteractiveDemo();
            initParallax();
            initStatsObserver();
            initMouseEffect();
        });
        
        // Mobile menu enhancement
        function initMobileMenu() {
            const mobileMenu = document.querySelector('[x-data]');
            
            // Close menu when clicking on link
            const mobileLinks = document.querySelectorAll('.md\\:hidden a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', () => {
                    // Trigger Alpine.js close
                    mobileMenu.__x.$data.open = false;
                });
            });
        }
        
        // Initialize mobile enhancements
        document.addEventListener('alpine:init', () => {
            initMobileMenu();
        });
    </script>
    
    @yield('scripts')
</body>
</html>