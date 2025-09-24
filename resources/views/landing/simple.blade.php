<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BOKOD CMS - Complete School Health Management</title>
    <meta name="description" content="BOKOD CMS - Professional clinic management system for Benguet State University with comprehensive patient tracking, pharmacy management, vital signs monitoring, and intelligent reporting.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .hero-bg {
            background: linear-gradient(135deg, #1a5d3a 0%, #2d7a4f 50%, #16a34a 100%);
            position: relative;
            background-size: 400% 400%;
            animation: gradientShift 8s ease-in-out infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
            position: relative;
            overflow: hidden;
        }
        
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        
        .feature-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }
        
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: rgba(99, 102, 241, 0.1);
        }
        
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(135deg, #f7d917 0%, #fde047 100%);
            border-radius: 1px;
        }
        
        .nav-link:hover {
            color: #1a5d3a !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f7d917 0%, #fde047 100%);
            color: #1a5d3a !important;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px 0 rgba(247, 217, 23, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #fde047 0%, #f7d917 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(247, 217, 23, 0.4);
            color: #1a5d3a !important;
        }
        
        .btn-secondary {
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .stats-card {
            transition: transform 0.2s ease;
        }
        
        .stats-card:hover {
            transform: scale(1.05);
        }
        
        .section-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .icon-glow {
            position: relative;
        }
        
        .icon-glow::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: radial-gradient(circle, currentColor 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: inherit;
            z-index: -1;
        }
        
        .feature-card:hover .icon-glow::before {
            opacity: 0.2;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #1a5d3a 0%, #2d7a4f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Floating particles animation */
        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            background: rgba(247, 217, 23, 0.3);
            border-radius: 50%;
            animation: float 6s infinite linear;
        }
        
        .particle:nth-child(1) { left: 10%; width: 12px; height: 12px; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; width: 16px; height: 16px; animation-delay: 1s; }
        .particle:nth-child(3) { left: 30%; width: 8px; height: 8px; animation-delay: 2s; }
        .particle:nth-child(4) { left: 50%; width: 14px; height: 14px; animation-delay: 3s; }
        .particle:nth-child(5) { left: 70%; width: 10px; height: 10px; animation-delay: 4s; }
        .particle:nth-child(6) { left: 80%; width: 18px; height: 18px; animation-delay: 5s; }
        .particle:nth-child(7) { left: 90%; width: 6px; height: 6px; animation-delay: 6s; }
        
        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg) scale(0.5); opacity: 0; }
            10% { opacity: 1; transform: translateY(90vh) rotate(36deg) scale(1); }
            50% { opacity: 1; transform: translateY(50vh) rotate(180deg) scale(1.2); }
            90% { opacity: 1; transform: translateY(10vh) rotate(324deg) scale(1); }
            100% { transform: translateY(-100px) rotate(360deg) scale(0.5); opacity: 0; }
        }
        
        /* Pulsing effect for particles */
        .particle:nth-child(odd) {
            background: rgba(247, 217, 23, 0.6);
            animation: float 8s infinite linear, pulse 2s infinite ease-in-out;
        }
        
        .particle:nth-child(even) {
            background: rgba(255, 255, 255, 0.4);
            animation: float 6s infinite linear, pulse 1.5s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.5); }
        }
        
        /* Improved card hover effects */
        .hover-lift {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .hover-lift:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        /* Glowing border effect */
        .glow-border {
            position: relative;
            border: 2px solid transparent;
            background: linear-gradient(white, white) padding-box,
                       linear-gradient(135deg, #f7d917, #1a5d3a) border-box;
        }
        
        /* Typing animation */
        .typing-animation {
            overflow: hidden;
            border-right: 3px solid #f7d917;
            white-space: nowrap;
            animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent; }
            50% { border-color: #f7d917; }
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm shadow-lg sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-green-700 rounded-lg flex items-center justify-center border border-yellow-400">
                            <i class="fas fa-hospital text-yellow-300 text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gradient">BOKOD CMS</span>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="nav-link text-gray-600 hover:text-gray-900 font-medium">Features</a>
                    <a href="#about" class="nav-link text-gray-600 hover:text-gray-900 font-medium">About</a>
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn-primary text-white px-6 py-2 rounded-lg font-semibold">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link text-gray-600 hover:text-gray-900 font-medium">Sign In</a>
                        <a href="{{ route('register') }}" class="btn-primary text-white px-6 py-2 rounded-lg font-semibold">
                            Get Started
                        </a>
                    @endauth
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none focus:bg-gray-100">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-100 shadow-lg">
            <div class="px-4 py-4 space-y-2">
                <a href="#features" class="nav-link block text-gray-600 hover:text-gray-900 font-medium py-2 px-3 rounded-lg hover:bg-gray-50 transition-all">
                    <i class="fas fa-star mr-2"></i>Features
                </a>
                <a href="#about" class="nav-link block text-gray-600 hover:text-gray-900 font-medium py-2 px-3 rounded-lg hover:bg-gray-50 transition-all">
                    <i class="fas fa-info-circle mr-2"></i>About
                </a>
                @auth
                    <a href="{{ route('dashboard.index') }}" class="btn-primary block text-white px-4 py-3 rounded-lg text-center font-semibold mt-3">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-yellow-400 text-green-800 block px-4 py-3 rounded-lg text-center font-bold mb-2 shadow-md border border-yellow-300">
                        <i class="fas fa-user-graduate mr-2"></i>Register as BSU Student
                    </a>
                    <a href="{{ route('login') }}" class="nav-link block text-gray-600 hover:text-gray-900 font-medium py-2 px-3 rounded-lg hover:bg-gray-50 transition-all">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- UPDATED DESIGN INDICATOR -->
    <!-- <div class="bg-yellow-400 text-green-800 text-center py-4 font-bold text-lg animate-pulse border-4 border-red-500">
        🚀 BOKOD CMS - NEW ENHANCED DESIGN IS ACTIVE! 🚀<br>
        <span class="text-sm">Floating particles, testimonials, FAQ, animated gradients & more!</span>
    </div> -->
    
    <!-- Hero Section -->
    <section class="hero-bg text-white py-24 relative overflow-hidden">
        <!-- Floating Particles -->
        <div class="floating-particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        
        <!-- Hero Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-32 h-32 border border-yellow-300/20 rounded-full animate-pulse"></div>
            <div class="absolute top-40 right-20 w-20 h-20 border border-yellow-300/20 rounded-full animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute bottom-20 left-1/4 w-16 h-16 border border-yellow-300/20 rounded-full animate-pulse" style="animation-delay: 2s"></div>
            <div class="absolute bottom-40 right-1/3 w-24 h-24 border border-yellow-300/20 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="animate-fadeInUp">
                <div class="inline-block p-2 bg-yellow-400/20 backdrop-blur-sm rounded-full mb-6 border border-yellow-400/30">
                    <i class="fas fa-hospital text-3xl text-yellow-300"></i>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    <span class="block text-gradient">Complete School</span>
                    <span class="block">Health Management</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-green-100 max-w-3xl mx-auto leading-relaxed">
                    Comprehensive clinic management with patient records, appointment scheduling, prescription tracking, inventory management, and real-time health monitoring
                </p>
                
                <!-- Hero Stats -->
                <div class="flex flex-wrap justify-center gap-8 mb-10 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-300 mb-1">{{ number_format($stats['total_patients'] ?? 847) }}</div>
                        <div class="text-green-100">BSU Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-300 mb-1">{{ number_format($stats['total_appointments'] ?? 156) }}+</div>
                        <div class="text-green-100">Health Visits</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-300 mb-1">{{ number_format($stats['total_medicines'] ?? 89) }}+</div>
                        <div class="text-green-100">Medicines Tracked</div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn-primary bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                        </a>
                    @else
                        <!-- Student Registration CTA -->
                        <a href="{{ route('register') }}" class="btn-primary bg-yellow-400 text-green-800 px-8 py-4 rounded-lg font-bold shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 border-2 border-yellow-300">
                            <i class="fas fa-user-graduate mr-2"></i>Register as BSU Student
                        </a>
                        <a href="{{ route('login') }}" class="glass-effect border-2 border-white/30 text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/10 transition-all duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </a>
                    @endauth
                </div>
                
                <!-- Additional CTA for students -->
                @guest
                <div class="mt-6">
                    <p class="text-green-100 text-sm mb-3">
                        <i class="fas fa-graduation-cap mr-2"></i>BSU Students: Create your account to book appointments online!
                    </p>
                    <div class="flex flex-wrap justify-center gap-4 text-xs text-green-200">
                        <span><i class="fas fa-check mr-1"></i>Free health services</span>
                        <span><i class="fas fa-check mr-1"></i>Skip walk-in queues</span>
                        <span><i class="fas fa-check mr-1"></i>Track your health records</span>
                        <span><i class="fas fa-check mr-1"></i>Emergency contact alerts</span>
                    </div>
                </div>
                @endguest
            </div>
        </div>
    </section>

    <!-- Feature Showcase Banner -->
    <section class="py-16 bg-gradient-to-r from-green-600 via-green-700 to-green-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 left-10 w-20 h-20 border border-yellow-300/30 rounded-full animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-16 h-16 border border-yellow-300/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                🏆 Comprehensive Healthcare Management Platform
            </h2>
            <p class="text-xl text-green-100 mb-8 max-w-4xl mx-auto">
                Designed specifically for Benguet State University - BOKOD CMS provides comprehensive health management with professional-grade security and intuitive design tailored for our campus community.
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-400/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-yellow-300/30">
                        <i class="fas fa-shield-check text-yellow-300 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-yellow-300 mb-1">HIPAA Compliant</h4>
                    <p class="text-green-200 text-sm">Enterprise Security</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-400/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-yellow-300/30">
                        <i class="fas fa-mobile-alt text-yellow-300 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-yellow-300 mb-1">Mobile Ready</h4>
                    <p class="text-green-200 text-sm">Access Anywhere</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-400/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-yellow-300/30">
                        <i class="fas fa-clock text-yellow-300 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-yellow-300 mb-1">Real-Time</h4>
                    <p class="text-green-200 text-sm">Instant Updates</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-400/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-yellow-300/30">
                        <i class="fas fa-users text-yellow-300 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-yellow-300 mb-1">Multi-User</h4>
                    <p class="text-green-200 text-sm">Role-Based Access</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-block p-2 bg-yellow-100 rounded-lg mb-4 border border-yellow-200">
                    <i class="fas fa-star text-green-700 text-2xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <span class="text-gradient">Powerful Features</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Everything you need to manage your school clinic effectively with modern, user-friendly tools designed for healthcare professionals
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card hover-lift glow-border bg-white rounded-xl p-8 shadow-lg border border-gray-100">
                    <div class="icon-glow w-16 h-16 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center mb-6 shadow-lg border border-yellow-400/50">
                        <i class="fas fa-user-graduate text-yellow-300 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Complete Patient Profiles</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Full student health profiles with BMI calculation, blood pressure monitoring, emergency contacts, medical history, and vital signs tracking with automated health status assessments.</p>
                    <div class="flex items-center text-green-700 font-medium">
                        <span class="text-sm">Advanced health tracking</span>
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </div>
                </div>
                
                <div class="feature-card hover-lift glow-border bg-white rounded-xl p-8 shadow-lg border border-gray-100">
                    <div class="icon-glow w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-6 shadow-lg border border-green-600/50">
                        <i class="fas fa-calendar-check text-green-700 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Advanced Appointment System</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Full appointment lifecycle management with approval workflows, reschedule requests, cancellation tracking, overdue monitoring, and automated status updates with diagnosis recording.</p>
                    <div class="flex items-center text-green-700 font-medium">
                        <span class="text-sm">Workflow automation</span>
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </div>
                </div>
                
                <div class="feature-card hover-lift glow-border bg-white rounded-xl p-8 shadow-lg border border-gray-100">
                    <div class="icon-glow w-16 h-16 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center mb-6 shadow-lg border border-yellow-400/50">
                        <i class="fas fa-pills text-yellow-300 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Prescription & Pharmacy Management</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Complete prescription lifecycle with dosage instructions, frequency tracking, expiration monitoring, dispensing records, and automated stock deduction with pregnancy category warnings.</p>
                    <div class="flex items-center text-green-700 font-medium">
                        <span class="text-sm">Full pharmacy system</span>
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </div>
                </div>
                
                <div class="feature-card hover-lift glow-border bg-white rounded-xl p-8 shadow-lg border border-gray-100">
                    <div class="icon-glow w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-6 shadow-lg border border-green-600/50">
                        <i class="fas fa-warehouse text-green-700 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Smart Inventory Management</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Comprehensive medicine inventory with expiry tracking, low stock alerts, batch management, therapeutic classification, drug interaction warnings, and automated stock movement logging.</p>
                    <div class="flex items-center text-green-700 font-medium">
                        <span class="text-sm">Intelligent tracking</span>
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </div>
                </div>
                
                <div class="feature-card hover-lift glow-border bg-white rounded-xl p-8 shadow-lg border border-gray-100">
                    <div class="icon-glow w-16 h-16 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center mb-6 shadow-lg border border-yellow-400/50">
                        <i class="fas fa-chart-line text-yellow-300 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Health Analytics & Visits</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Complete patient visit tracking with vital signs monitoring, disease pattern analysis, follow-up scheduling, and comprehensive health status reporting with trend visualization.</p>
                    <div class="flex items-center text-green-700 font-medium">
                        <span class="text-sm">Data-driven insights</span>
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </div>
                </div>
                
                <div class="feature-card hover-lift glow-border bg-white rounded-xl p-8 shadow-lg border border-gray-100">
                    <div class="icon-glow w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mb-6 shadow-lg border border-green-600/50">
                        <i class="fas fa-bell text-green-700 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Smart Notification System</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Advanced notification system with appointment reminders, medication alerts, health checkup notifications, priority-based messaging, and automated patient portal communications.</p>
                    <div class="flex items-center text-green-700 font-medium">
                        <span class="text-sm">Automated alerts</span>
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </div>
                </div>
            </div>
            
            <!-- Additional Features Grid -->
            <div class="mt-20">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Advanced Capabilities</h3>
                    <p class="text-lg text-gray-600">Professional-grade features that set us apart</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-stethoscope text-indigo-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Medical Notes System</h4>
                        <p class="text-sm text-gray-600">Priority-based medical notes with treatment observations, diagnoses, and confidential documentation</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-user-circle text-pink-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Patient Portal</h4>
                        <p class="text-sm text-gray-600">Dedicated patient portal for appointment booking, health record access, and direct communication</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-search text-yellow-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Global Search</h4>
                        <p class="text-sm text-gray-600">Powerful search across all patient records, appointments, medications, and medical notes</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-heartbeat text-emerald-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Vital Signs Monitoring</h4>
                        <p class="text-sm text-gray-600">Comprehensive tracking of temperature, blood pressure, pulse, respiratory rate, and oxygen saturation</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-archive text-cyan-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Patient Archiving</h4>
                        <p class="text-sm text-gray-600">Secure patient record archiving system with data retention and retrieval capabilities</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-history text-rose-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Medication History</h4>
                        <p class="text-sm text-gray-600">Complete medication history tracking with dosage changes, reactions, and treatment outcomes</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-violet-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-users-cog text-violet-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Multi-User Management</h4>
                        <p class="text-sm text-gray-600">Role-based access control for nurses, administrators, and healthcare staff with permission management</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-cog text-amber-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">System Configuration</h4>
                        <p class="text-sm text-gray-600">Customizable system settings, clinic preferences, and workflow configuration options</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="py-20 bg-white relative">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-purple-50 opacity-50"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">BSU Bokod Campus Health Stats</h2>
                <p class="text-xl text-gray-600">Real data from our campus health management system</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="stats-card text-center bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($stats['total_patients'] ?? 847) }}</div>
                    <div class="text-gray-600 font-medium">Students Managed</div>
                </div>
                <div class="stats-card text-center bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heartbeat text-green-600 text-xl"></i>
                    </div>
                    <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($stats['total_appointments'] ?? 156) }}</div>
                    <div class="text-gray-600 font-medium">Health Visits</div>
                </div>
                <div class="stats-card text-center bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-pills text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($stats['total_medicines'] ?? 89) }}</div>
                    <div class="text-gray-600 font-medium">Medications Tracked</div>
                </div>
                <div class="stats-card text-center bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-orange-600 text-xl"></i>
                    </div>
                    <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($stats['total_users'] ?? 12) }}</div>
                    <div class="text-gray-600 font-medium">Healthcare Staff</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-block p-2 bg-yellow-100 rounded-lg mb-4 border border-yellow-200">
                    <i class="fas fa-quote-left text-green-700 text-2xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <span class="text-gradient">What Schools Say</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Trusted by the BSU community - from students to faculty and staff
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="hover-lift bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400 mr-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 italic leading-relaxed">
                        "BOKOD CMS has revolutionized how we manage student health records. The prescription tracking and vital signs monitoring have made our clinic operations much more efficient."
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-yellow-300"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Maria Santos</h4>
                            <p class="text-gray-500 text-sm">University Nurse, BSU Main Campus</p>
                        </div>
                    </div>
                </div>
                
                <div class="hover-lift bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400 mr-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 italic leading-relaxed">
                        "The inventory management feature is outstanding. We no longer worry about medicine expiry dates or low stock situations. Everything is automated and reliable."
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-green-700"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Dr. James Palangdan</h4>
                            <p class="text-gray-500 text-sm">Medical Officer, BSU Main Campus</p>
                        </div>
                    </div>
                </div>
                
                <div class="hover-lift bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400 mr-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 italic leading-relaxed">
                        "The appointment system with approval workflows has streamlined our clinic operations. Parents and students love the patient portal access too."
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-yellow-300"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Grace Balanoy</h4>
                            <p class="text-gray-500 text-sm">Health Coordinator, BSU College of Medicine</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-block p-2 bg-yellow-100 rounded-lg mb-6 border border-yellow-200">
                        <i class="fas fa-shield-alt text-green-700 text-2xl"></i>
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">
                        <span class="text-gradient">Built for</span> School Healthcare
                    </h2>
                    <div class="space-y-6 text-lg text-gray-600">
                        <p class="leading-relaxed">
                            BOKOD CMS is a comprehensive healthcare management system designed specifically for Benguet State University. Built to serve our diverse campus community, from undergraduate students to graduate researchers and faculty, with detailed patient profiles, BMI calculations, and advanced prescription management tailored for university healthcare needs.
                        </p>
                        <p class="leading-relaxed">
                            Designed for the unique needs of university healthcare, the system features sophisticated appointment workflows perfect for managing student health visits, faculty consultations, and staff medical needs. Our integrated pharmacy module supports the complex medication requirements across different colleges and departments within BSU.
                        </p>
                        <p class="leading-relaxed">
                            Advanced features include vital signs monitoring, medical notes with priority classification, patient visit tracking with next appointment scheduling, smart notifications system, and a dedicated patient portal. Built with enterprise-grade security and HIPAA compliance from the ground up.
                        </p>
                    </div>
                    
                    <!-- Key Benefits -->
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1 border border-yellow-200">
                                <i class="fas fa-check text-green-700 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">HIPAA Compliant</h4>
                                <p class="text-gray-600 text-sm">Full compliance with healthcare privacy regulations</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1 border border-yellow-200">
                                <i class="fas fa-check text-green-700 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Easy to Use</h4>
                                <p class="text-gray-600 text-sm">Intuitive interface designed for busy healthcare staff</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1 border border-yellow-200">
                                <i class="fas fa-check text-green-700 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Scalable</h4>
                                <p class="text-gray-600 text-sm">Grows from single schools to district-wide implementations</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1 border border-yellow-200">
                                <i class="fas fa-check text-green-700 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">24/7 Support</h4>
                                <p class="text-gray-600 text-sm">Dedicated support team when you need assistance</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Visual Element -->
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 bg-blue-50 rounded-xl">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-graduate text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Patient Profile System</div>
                                    <div class="text-gray-600 text-sm">BMI calculation • Blood pressure monitoring • Emergency contacts</div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: 95%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 bg-green-50 rounded-xl">
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-pills text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Prescription Management</div>
                                    <div class="text-gray-600 text-sm">Dosage tracking • Expiry monitoring • Drug interactions</div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: 88%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 bg-purple-50 rounded-xl">
                                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-warehouse text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Inventory Control</div>
                                    <div class="text-gray-600 text-sm">Stock movements • Batch tracking • Low stock alerts</div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="bg-purple-500 h-2 rounded-full" style="width: 92%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 bg-orange-50 rounded-xl">
                                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-heartbeat text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Vital Signs Tracking</div>
                                    <div class="text-gray-600 text-sm">Temperature • Pulse • BP • SpO2 • Respiratory rate</div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="bg-orange-500 h-2 rounded-full" style="width: 90%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-blue-100 rounded-full opacity-20"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-purple-100 rounded-full opacity-20"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Campus Location Section -->
    <section class="py-20 bg-gradient-to-br from-green-50 to-yellow-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-block p-2 bg-yellow-100 rounded-lg mb-4 border border-yellow-200">
                    <i class="fas fa-map-marker-alt text-green-700 text-2xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <span class="text-gradient">Our Campus</span> Location
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Located at the heart of Benguet State University, BOKOD CMS serves the entire campus community
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Campus Map -->
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-2xl p-4 border border-gray-100">
                        <!-- Interactive Google Maps -->
                        <div class="w-full h-80 rounded-xl overflow-hidden relative">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3827.8945674829845!2d120.83368906545476!3d16.519621391181005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTbCsDMxJzEwLjYiTiAxMjDCsDUwJzAxLjMiRQ!5e0!3m2!1sen!2sph!4v1642012345678!5m2!1sen!2sph&q=16.519621391181005,120.83368906545476" 
                                width="100%" 
                                height="320" 
                                style="border:0; border-radius: 12px;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Benguet State University Campus Location">
                            </iframe>
                            
                            <!-- Map overlay info -->
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-green-800 px-3 py-2 rounded-lg text-sm font-bold shadow-lg border border-yellow-200">
                                <i class="fas fa-map-marker-alt text-yellow-600 mr-1"></i>
                                BSU Campus
                            </div>
                            
                            <!-- Coordinates display -->
                            <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-sm text-gray-700 px-3 py-1 rounded-lg text-xs shadow-lg border border-gray-200">
                                16.5196°N, 120.8337°E
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="mt-4 flex justify-center space-x-3">
                            <a href="https://www.google.com/maps?q=16.519621391181005,120.83368906545476" 
                               target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors shadow-md">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Open in Google Maps
                            </a>
                            <a href="https://www.google.com/maps/dir//16.519621391181005,120.83368906545476" 
                               target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-500 text-green-800 text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors shadow-md">
                                <i class="fas fa-route mr-2"></i>
                                Get Directions
                            </a>
                        </div>
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-yellow-400 rounded-full opacity-60"></div>
                    <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-green-600 rounded-full opacity-60"></div>
                </div>
                
                <!-- Location Details -->
                <div class="space-y-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">
                            📍 Campus Healthcare Hub
                        </h3>
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-university text-green-700 text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Main Campus</h4>
                                    <p class="text-gray-600 text-sm">Benguet State University, Bokod Road</p>
                                    <p class="text-gray-600 text-sm">La Trinidad, Benguet</p>
                                    <p class="text-gray-500 text-xs mt-1">📍 16.5196°N, 120.8337°E</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clinic-medical text-green-700 text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">University Health Center</h4>
                                    <p class="text-gray-600 text-sm">Serving students, faculty, and staff</p>
                                    <p class="text-gray-600 text-sm">Open: Monday - Friday, 8:00 AM - 5:00 PM</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone text-green-700 text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Contact Information</h4>
                                    <p class="text-gray-600 text-sm">Health Center: (074) XXX-XXXX</p>
                                    <p class="text-gray-600 text-sm">Emergency: Available 24/7</p>
                                    <p class="text-gray-500 text-xs mt-1">GPS: 16.519621, 120.833689</p>
                                </div>
                            </div>
                            
                            <!-- Quick Navigation -->
                            <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-signs text-green-700 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-2">Navigation</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="https://www.google.com/maps?q=16.519621391181005,120.83368906545476" 
                                           target="_blank" 
                                           class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full hover:bg-green-200 transition-colors">
                                            <i class="fab fa-google mr-1"></i>Google Maps
                                        </a>
                                        <a href="https://waze.com/ul?q=16.519621391181005,120.83368906545476" 
                                           target="_blank" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors">
                                            <i class="fas fa-route mr-1"></i>Waze
                                        </a>
                                        <a href="geo:16.519621391181005,120.83368906545476" 
                                           class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full hover:bg-yellow-200 transition-colors">
                                            <i class="fas fa-mobile-alt mr-1"></i>Mobile GPS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campus Landmarks -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-landmark text-yellow-600 mr-2"></i>
                            Campus Landmarks
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-600">BSU Basketball Court</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-600">Andres Cosalan Court</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-600">BSU Covered Court</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                                <span class="text-gray-600">Palanza Area</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-block p-2 bg-yellow-100 rounded-lg mb-4 border border-yellow-200">
                    <i class="fas fa-question-circle text-green-700 text-2xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <span class="text-gradient">Frequently Asked</span> Questions
                </h2>
                <p class="text-xl text-gray-600">Get answers to common questions about BOKOD CMS</p>
            </div>
            
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg hover:shadow-lg transition-shadow">
                    <button class="faq-button w-full px-6 py-4 text-left bg-white hover:bg-gray-50 rounded-lg focus:outline-none" onclick="toggleFAQ(1)">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900">Is BOKOD CMS suitable for small schools?</span>
                            <i class="fas fa-chevron-down text-green-700 transform transition-transform duration-200" id="icon-1"></i>
                        </div>
                    </button>
                    <div class="faq-content hidden px-6 pb-4" id="content-1">
                        <p class="text-gray-600">Yes! BOKOD CMS is designed to scale from small rural schools to large university campuses. The system adapts to your needs whether you have 50 students or 5,000.</p>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg hover:shadow-lg transition-shadow">
                    <button class="faq-button w-full px-6 py-4 text-left bg-white hover:bg-gray-50 rounded-lg focus:outline-none" onclick="toggleFAQ(2)">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900">How secure is student health data?</span>
                            <i class="fas fa-chevron-down text-green-700 transform transition-transform duration-200" id="icon-2"></i>
                        </div>
                    </button>
                    <div class="faq-content hidden px-6 pb-4" id="content-2">
                        <p class="text-gray-600">BOKOD CMS is built with enterprise-grade security and HIPAA compliance. All data is encrypted, access is role-based, and we maintain detailed audit logs for all system activities.</p>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg hover:shadow-lg transition-shadow">
                    <button class="faq-button w-full px-6 py-4 text-left bg-white hover:bg-gray-50 rounded-lg focus:outline-none" onclick="toggleFAQ(3)">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900">Can parents access their child's health records?</span>
                            <i class="fas fa-chevron-down text-green-700 transform transition-transform duration-200" id="icon-3"></i>
                        </div>
                    </button>
                    <div class="faq-content hidden px-6 pb-4" id="content-3">
                        <p class="text-gray-600">Yes, through our secure patient portal, parents can view health records, schedule appointments, and receive important health notifications about their children.</p>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg hover:shadow-lg transition-shadow">
                    <button class="faq-button w-full px-6 py-4 text-left bg-white hover:bg-gray-50 rounded-lg focus:outline-none" onclick="toggleFAQ(4)">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900">What kind of support do you provide?</span>
                            <i class="fas fa-chevron-down text-green-700 transform transition-transform duration-200" id="icon-4"></i>
                        </div>
                    </button>
                    <div class="faq-content hidden px-6 pb-4" id="content-4">
                        <p class="text-gray-600">We provide comprehensive technical support including system setup, user guidance, troubleshooting, and regular system updates. Our team understands the unique needs of educational institutions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="hero-bg text-white py-24 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-20 h-20 border border-white/30 rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-16 h-16 border border-white/30 rounded-full animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/4 w-12 h-12 border border-white/30 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
        </div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-block p-3 bg-yellow-400/20 backdrop-blur-sm rounded-full mb-6 border border-yellow-400/30">
                <i class="fas fa-rocket text-yellow-300 text-2xl"></i>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                Ready for Professional-Grade School Healthcare Management?
            </h2>
            <p class="text-xl md:text-2xl mb-8 text-green-100 max-w-3xl mx-auto leading-relaxed">
                Experience the complete healthcare management solution with advanced patient tracking, pharmacy management, vital signs monitoring, and intelligent reporting.
            </p>
            
            <!-- Trust Indicators -->
            <div class="flex flex-wrap justify-center items-center gap-8 mb-10 text-green-200">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-shield-check text-yellow-300"></i>
                    <span class="text-sm font-medium">HIPAA Compliant</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-cloud-upload-alt text-yellow-300"></i>
                    <span class="text-sm font-medium">Cloud-Based</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-headset text-yellow-300"></i>
                    <span class="text-sm font-medium">24/7 Support</span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="btn-primary bg-white text-blue-600 px-10 py-4 rounded-xl font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300">
                        <i class="fas fa-tachometer-alt mr-3"></i>Access Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-primary bg-white text-blue-600 px-10 py-4 rounded-xl font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300">
                        <i class="fas fa-user-plus mr-3"></i>Start Free Trial
                    </a>
                    <a href="{{ route('login') }}" class="glass-effect border-2 border-white/30 text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-white/10 transition-all duration-300">
                        <i class="fas fa-sign-in-alt mr-3"></i>Sign In
                    </a>
                @endauth
            </div>
            
            <p class="mt-6 text-green-200 text-sm">
                No setup fees • Easy deployment • Dedicated support
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16 relative">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
                <div class="md:col-span-1">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-green-700 rounded-lg flex items-center justify-center border border-yellow-400">
                            <i class="fas fa-hospital text-yellow-300"></i>
                        </div>
                        <h3 class="text-xl font-bold">BOKOD CMS</h3>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Professional healthcare management system designed specifically for Benguet State University campus community.
                    </p>
                    <div class="flex space-x-4">
                        <div class="w-10 h-10 bg-gray-700 hover:bg-blue-600 rounded-lg flex items-center justify-center cursor-pointer transition-colors">
                            <i class="fab fa-twitter text-white text-sm"></i>
                        </div>
                        <div class="w-10 h-10 bg-gray-700 hover:bg-blue-800 rounded-lg flex items-center justify-center cursor-pointer transition-colors">
                            <i class="fab fa-facebook text-white text-sm"></i>
                        </div>
                        <div class="w-10 h-10 bg-gray-700 hover:bg-blue-600 rounded-lg flex items-center justify-center cursor-pointer transition-colors">
                            <i class="fab fa-linkedin text-white text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Features -->
                <div>
                    <h4 class="text-lg font-semibold mb-6 text-white">Core Features</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors flex items-center space-x-2">
                            <i class="fas fa-user-graduate text-blue-400 text-sm w-4"></i><span>Student Records</span>
                        </a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors flex items-center space-x-2">
                            <i class="fas fa-calendar-check text-green-400 text-sm w-4"></i><span>Smart Scheduling</span>
                        </a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors flex items-center space-x-2">
                            <i class="fas fa-pills text-purple-400 text-sm w-4"></i><span>Medication Tracking</span>
                        </a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors flex items-center space-x-2">
                            <i class="fas fa-chart-bar text-orange-400 text-sm w-4"></i><span>Health Analytics</span>
                        </a></li>
                    </ul>
                </div>
                
                <!-- Support -->
                <div>
                    <h4 class="text-lg font-semibold mb-6 text-white">Support & Resources</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">User Guide</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Technical Support</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">System Status</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Security & Privacy</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-semibold mb-6 text-white">Get in Touch</h4>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-envelope text-blue-400 text-sm mt-1"></i>
                            <div>
                                <p class="text-gray-300 text-sm">Email Support</p>
                                <p class="text-white font-medium">support@bokodcms.com</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-phone text-green-400 text-sm mt-1"></i>
                            <div>
                                <p class="text-gray-300 text-sm">Phone Support</p>
                                <p class="text-white font-medium">Available on Request</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-clock text-orange-400 text-sm mt-1"></i>
                            <div>
                                <p class="text-gray-300 text-sm">Support Hours</p>
                                <p class="text-white font-medium">Business Hours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Section -->
            <div class="border-t border-gray-700 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="text-gray-300">
                        <p>&copy; {{ date('Y') }} BOKOD CMS. All rights reserved.</p>
                        <p class="text-sm mt-1">Built with ❤️ for the Benguet State University community.</p>
                    </div>
                    <div class="flex items-center space-x-6 text-sm text-gray-300">
                        <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                        <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                        <a href="#" class="hover:text-white transition-colors">HIPAA Compliance</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle with animations
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const btn = this;
            
            menu.classList.toggle('hidden');
            
            // Toggle button icon
            const icon = btn.querySelector('i');
            if (menu.classList.contains('hidden')) {
                icon.className = 'fas fa-bars text-xl';
            } else {
                icon.className = 'fas fa-times text-xl';
            }
        });
        
        // Smooth scrolling for anchor links with offset
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80; // Account for fixed navbar
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                    document.getElementById('mobile-menu-btn').querySelector('i').className = 'fas fa-bars text-xl';
                }
            });
        });
        
        // Scroll animations
        function animateOnScroll() {
            const elements = document.querySelectorAll('.feature-card, .stats-card');
            
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('animate-fadeInUp');
                }
            });
        }
        
        // Counter animation for stats
        function animateCounters() {
            const counters = document.querySelectorAll('.stats-card .text-4xl');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target') || counter.textContent.replace(/,/g, ''));
                counter.setAttribute('data-target', target);
                
                const elementTop = counter.getBoundingClientRect().top;
                if (elementTop < window.innerHeight - 100 && !counter.classList.contains('animated')) {
                    counter.classList.add('animated');
                    animateValue(counter, 0, target, 2000);
                }
            });
        }
        
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const current = Math.floor(progress * (end - start) + start);
                element.textContent = current.toLocaleString();
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
        
        // Navbar background on scroll
        function handleNavbarScroll() {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg');
                navbar.classList.remove('shadow-sm');
            } else {
                navbar.classList.add('shadow-sm');
                navbar.classList.remove('shadow-lg');
            }
        }
        
        // Event listeners
        window.addEventListener('scroll', () => {
            animateOnScroll();
            animateCounters();
            handleNavbarScroll();
        });
        
        window.addEventListener('load', () => {
            animateOnScroll();
            animateCounters();
        });
        
        // Add hover effects to feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1)';
            });
        });
        
        // Typing effect for hero title (optional enhancement)
        function typeWriter(element, text, speed = 100) {
            let i = 0;
            element.innerHTML = '';
            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }
        
        // FAQ Toggle Function
        function toggleFAQ(id) {
            const content = document.getElementById(`content-${id}`);
            const icon = document.getElementById(`icon-${id}`);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
        
        // Particle Animation Enhancement
        function createFloatingParticles() {
            const particleContainer = document.querySelector('.floating-particles');
            if (particleContainer) {
                for (let i = 0; i < 15; i++) {
                    const particle = document.createElement('div');
                    particle.classList.add('particle');
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 6 + 's';
                    particle.style.animationDuration = (Math.random() * 3 + 4) + 's';
                    particleContainer.appendChild(particle);
                }
            }
        }
        
        // Enhanced scroll animations
        function enhancedAnimateOnScroll() {
            const elements = document.querySelectorAll('.feature-card, .stats-card, .hover-lift');
            
            elements.forEach((element, index) => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    setTimeout(() => {
                        element.classList.add('animate-fadeInUp');
                        element.style.opacity = '1';
                    }, index * 100);
                }
            });
        }
        
        // Initialize animations when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Add initial animation classes
            setTimeout(() => {
                document.querySelector('.hero-bg .animate-fadeInUp')?.classList.add('opacity-100');
            }, 100);
            
            // Create additional floating particles
            createFloatingParticles();
            
            // Initialize intersection observer for better animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-fadeInUp');
                        }
                    });
                }, { threshold: 0.1 });
                
                document.querySelectorAll('.hover-lift').forEach(el => {
                    observer.observe(el);
                });
            }
        });
    </script>
</body>
</html>