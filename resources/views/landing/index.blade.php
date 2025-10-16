@extends('landing.layout')

@section('title', 'Bokod Medical CMS - Modern Healthcare Management System')
@section('description', 'Streamline your medical practice with our comprehensive CMS. Manage patients, appointments, prescriptions, and healthcare records efficiently.')

@section('content')
<!-- Loading Screen -->
<div class="loading-screen">
    <div class="text-center">
        <div class="loader mb-4"></div>
        <h3 class="text-white text-xl font-semibold">Loading Bokod Medical CMS</h3>
        <p class="text-gray-200 mt-2">Preparing your healthcare management experience...</p>
    </div>
</div>

<!-- Progress Bar -->
<div class="progress-bar"></div>

<!-- Navigation -->
<nav class="bg-white shadow-lg fixed w-full z-50" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    @if(config('app.favicon'))
                        <img src="{{ asset(config('app.favicon')) }}" alt="Bokod Medical CMS" class="h-8 w-8 mr-3">
                    @else
                        <i class="fas fa-hospital-alt text-2xl text-indigo-600 mr-3"></i>
                    @endif
                    <span class="text-xl font-bold text-gray-900">Bokod Medical CMS</span>
                </div>
                <div class="hidden md:ml-10 md:flex md:space-x-8">
                    <a href="#features" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors">Features</a>
                    <a href="#demo" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors">Demo</a>
                    <a href="#testimonials" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors">Reviews</a>
                    <a href="#faq" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors">FAQ</a>
                    <a href="#contact" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors">Contact</a>
                </div>
            </div>
            
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="text-indigo-600 hover:text-indigo-900 px-3 py-2 text-sm font-medium transition-colors">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors">Sign In</a>
                    <a href="{{ route('register') }}" class="btn-primary text-white px-4 py-2 rounded-md text-sm font-medium">Get Started</a>
                @endauth
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 p-2 rounded-md">
                    <i :class="open ? 'fas fa-times' : 'fas fa-bars'" class="h-6 w-6"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div x-show="open" x-transition class="md:hidden">
        <div class="bg-white border-t shadow-lg">
            <a href="#features" class="mobile-nav-item text-gray-700 hover:text-indigo-600 block touch-target font-medium">
                <i class="fas fa-desktop mr-3 text-indigo-500"></i>Desktop Features
            </a>
            <a href="#demo" class="mobile-nav-item text-gray-700 hover:text-indigo-600 block touch-target font-medium">
                <i class="fas fa-play-circle mr-3 text-green-500"></i>Interactive Demo
            </a>
            <a href="#testimonials" class="mobile-nav-item text-gray-700 hover:text-indigo-600 block touch-target font-medium">
                <i class="fas fa-star mr-3 text-yellow-500"></i>Reviews
            </a>
            <a href="#faq" class="mobile-nav-item text-gray-700 hover:text-indigo-600 block touch-target font-medium">
                <i class="fas fa-question-circle mr-3 text-blue-500"></i>FAQ
            </a>
            <a href="#contact" class="mobile-nav-item text-gray-700 hover:text-indigo-600 block touch-target font-medium">
                <i class="fas fa-envelope mr-3 text-red-500"></i>Contact
            </a>
            
            <div class="p-4 border-t bg-gray-50">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="btn-primary btn-touch text-white block text-center py-3 rounded-lg font-medium shadow-lg">
                        <i class="fas fa-tachometer-alt mr-2"></i>Access Dashboard
                    </a>
                @else
                    <div class="space-y-3">
                        <a href="{{ route('login') }}" class="block text-center py-3 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary btn-touch text-white block text-center py-3 rounded-lg font-medium shadow-lg">
                            <i class="fas fa-rocket mr-2"></i>Start Free Trial
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section gradient-bg medical-pattern min-h-screen flex items-center pt-16 relative overflow-hidden">
    <!-- Particle Background -->
    <div class="particles"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="lg:flex lg:items-center lg:justify-between">
            <div class="lg:w-1/2 lg:pr-8">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                    <span data-typing="BSU-Bokod Campus Clinic" data-speed="80">BSU-Bokod Campus Clinic</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-100 mb-6 sm:mb-8 leading-relaxed">
                    Comprehensive campus health management system for Benguet State University - Bokod Campus. Manage student health records, track medical visits, handle health emergencies, and ensure campus-wide wellness with advanced digital solutions.
                </p>
                
                <!-- School Health Features Highlights -->
                <div class="hidden md:flex items-center space-x-6 mb-8 text-sm text-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-user-graduate text-yellow-300 mr-2"></i>
                        <span>Student Health Records</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-syringe text-yellow-300 mr-2"></i>
                        <span>Immunization Tracking</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-ambulance text-yellow-300 mr-2"></i>
                        <span>Emergency Management</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-school text-yellow-300 mr-2"></i>
                        <span>School Compliance</span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn-primary text-white px-8 py-3 rounded-lg text-lg font-semibold inline-flex items-center justify-center bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary text-white px-8 py-3 rounded-lg text-lg font-semibold inline-flex items-center justify-center bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30">
                            <i class="fas fa-rocket mr-2"></i>
                            Get Started Free
                        </a>
                        <a href="{{ route('login') }}" class="text-white px-8 py-3 rounded-lg text-lg font-semibold inline-flex items-center justify-center border-2 border-white border-opacity-50 hover:bg-white hover:bg-opacity-10 transition-all">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </a>
                    @endauth
                </div>
                
                <!-- Key Features Preview -->
                <div class="mt-12 grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-2">
                            <i class="fas fa-users text-2xl text-white"></i>
                        </div>
                        <p class="text-sm text-gray-200">Patient Management</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-2">
                            <i class="fas fa-calendar-check text-2xl text-white"></i>
                        </div>
                        <p class="text-sm text-gray-200">Appointments</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-2">
                            <i class="fas fa-prescription-bottle-alt text-2xl text-white"></i>
                        </div>
                        <p class="text-sm text-gray-200">Prescriptions</p>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/2 mt-8 lg:mt-0">
                <div class="relative">
                    <!-- Desktop Multi-Monitor Setup Mockup -->
                    <div class="animate-float">
                        <!-- Main Monitor -->
                        <div class="bg-gray-900 rounded-t-2xl p-4 shadow-2xl mx-auto max-w-lg transform">
                            <!-- Monitor Bezel -->
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                                <!-- Browser Chrome -->
                                <div class="bg-gray-100 px-4 py-2 flex items-center space-x-2">
                                    <div class="flex space-x-1">
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    </div>
                                    <div class="flex-1 bg-white rounded px-3 py-1 text-xs text-gray-600">bokodmedical.com/dashboard</div>
                                </div>
                                
                                <!-- Desktop Interface -->
                                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-sm font-bold text-gray-900">School Health Portal v2.1</h3>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                            <span class="text-xs text-gray-600">Live School Data</span>
                                        </div>
                                    </div>
                                    
                                    <!-- School Stats Grid Layout -->
                                    <div class="grid grid-cols-2 gap-2 mb-3">
                                        <div class="bg-white rounded p-2 text-center border">
                                            <div class="text-lg font-bold text-blue-600">{{ number_format($stats['total_patients'] ?? 847) }}</div>
                                            <div class="text-xs text-gray-600">Students Enrolled</div>
                                        </div>
                                        <div class="bg-white rounded p-2 text-center border">
                                            <div class="text-lg font-bold text-green-600">{{ number_format($stats['total_appointments'] ?? 23) }}</div>
                                            <div class="text-xs text-gray-600">Health Visits Today</div>
                                        </div>
                                    </div>
                                    
                                    <!-- School Health Activities -->
                                    <div class="bg-white rounded-lg p-3 border">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-gray-700">Today's Health Activities</span>
                                            <span class="text-xs text-indigo-600">Live Updates</span>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-600">Nurse Johnson - Clinic</span>
                                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded">Available</span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-600">Temperature Checks</span>
                                                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded">12 Complete</span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-600">Immunization Records</span>
                                                <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded">5 Pending</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Monitor Stand -->
                            <div class="bg-gray-800 h-8 w-16 mx-auto rounded-b-lg"></div>
                            <div class="bg-gray-700 h-2 w-24 mx-auto rounded-full"></div>
                        </div>
                        
                        <!-- Secondary Monitor (Smaller) -->
                        <div class="absolute -right-8 top-4 hidden lg:block transform scale-75 opacity-75">
                            <div class="bg-gray-900 rounded-t-xl p-2 shadow-xl">
                                <div class="bg-white rounded-lg overflow-hidden">
                                    <div class="bg-gray-100 px-2 py-1 text-xs text-gray-600">Analytics Dashboard</div>
                                    <div class="bg-indigo-50 p-2">
                                        <div class="text-xs font-bold mb-1">Real-time Analytics</div>
                                        <div class="grid grid-cols-2 gap-1">
                                            <div class="bg-white rounded p-1 text-center">
                                                <div class="text-xs font-bold text-indigo-600">94%</div>
                                                <div class="text-xs text-gray-500">Efficiency</div>
                                            </div>
                                            <div class="bg-white rounded p-1 text-center">
                                                <div class="text-xs font-bold text-green-600">$12K</div>
                                                <div class="text-xs text-gray-500">Revenue</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-800 h-4 w-8 mx-auto rounded-b-md"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Desktop Power Indicators -->
                    <div class="absolute -bottom-4 left-1/2 transform -translate-x-1/2 hidden md:flex items-center space-x-4 bg-white bg-opacity-90 backdrop-blur-sm rounded-full px-4 py-2 shadow-lg">
                        <div class="flex items-center text-xs text-gray-600">
                            <i class="fas fa-microchip text-blue-500 mr-1"></i>
                            <span>High-Performance</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-600">
                            <i class="fas fa-expand-arrows-alt text-green-500 mr-1"></i>
                            <span>Multi-Screen</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-600">
                            <i class="fas fa-database text-purple-500 mr-1"></i>
                            <span>Local Storage</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision and Mission Section -->
<section class="section-padding bg-gradient-to-r from-blue-900 to-indigo-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4">Benguet State University - Bokod Campus</h2>
            <p class="text-lg text-blue-100">Guiding Principles of Academic Excellence</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Vision -->
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 border border-white border-opacity-20">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-eye text-blue-900 text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Vision</h3>
                </div>
                <p class="text-lg leading-relaxed text-blue-50">
                    A premier university in transformative education, innovative research, inclusive extension services, sustainable development, and stewardship of culture and the environment.
                </p>
            </div>
            
            <!-- Mission -->
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 border border-white border-opacity-20">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-green-400 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-bullseye text-blue-900 text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Mission</h3>
                </div>
                <p class="text-lg leading-relaxed text-blue-50">
                    Cultivate resilient and future-ready human capital through excellent teaching, responsive research, proactive and sustainable community engagements, strategic partnerships, and progressive leadership.
                </p>
            </div>
        </div>
        
        <!-- Campus Health Integration -->
        <div class="mt-12 text-center">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-6 max-w-4xl mx-auto border border-white border-opacity-20">
                <h4 class="text-xl font-bold mb-3">Campus Health Excellence</h4>
                <p class="text-blue-100">
                    Our campus clinic management system embodies BSU's mission by providing excellent healthcare services, conducting health research, engaging with the campus community proactively, and leading in innovative digital health solutions for educational institutions.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="section-padding bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4">Comprehensive Campus Health Management</h2>
            <p class="text-lg md:text-xl text-gray-600 max-w-4xl mx-auto px-4 sm:px-0">Designed specifically for university campus clinics and health centers to manage student wellness, track health records, and ensure campus-wide health compliance efficiently for BSU-Bokod Campus community.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Student Health Records -->
            <div class="feature-card bg-white rounded-xl shadow-lg p-6 md:p-8 text-center touch-manipulation">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <i class="fas fa-user-graduate text-2xl md:text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 md:mb-4">Student Health Records</h3>
                <p class="text-sm md:text-base text-gray-600 mb-4">Comprehensive student health profiles with immunization tracking, allergy alerts, and emergency contact management for school safety.</p>
                <ul class="text-xs md:text-sm text-gray-500 space-y-2">
                    <li>• Digital health files per student</li>
                    <li>• Immunization compliance tracking</li>
                    <li>• Allergy & medical alert system</li>
                    <li>• Parent/guardian notifications</li>
                </ul>
            </div>
            
            <!-- Health Visit Scheduling -->
            <div class="feature-card bg-white rounded-xl shadow-lg p-6 md:p-8 text-center touch-manipulation">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <i class="fas fa-calendar-check text-2xl md:text-3xl text-green-600"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 md:mb-4">Health Visit Scheduling</h3>
                <p class="text-sm md:text-base text-gray-600 mb-4">Streamlined scheduling for routine health screenings, nurse visits, and health assessments with automated parent notifications.</p>
                <ul class="text-xs md:text-sm text-gray-500 space-y-2">
                    <li>• Vision & hearing screening scheduling</li>
                    <li>• Automated parent notifications</li>
                    <li>• Mass health event coordination</li>
                    <li>• Grade-level health tracking</li>
                </ul>
            </div>
            
            <!-- Emergency Response System -->
            <div class="feature-card bg-white rounded-xl shadow-lg p-6 md:p-8 text-center touch-manipulation">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <i class="fas fa-ambulance text-2xl md:text-3xl text-red-600"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 md:mb-4">Emergency Response System</h3>
                <p class="text-sm md:text-base text-gray-600 mb-4">Quick access emergency protocols with instant parent contact, medical alert information, and emergency action plans for critical situations.</p>
                <ul class="text-xs md:text-sm text-gray-500 space-y-2">
                    <li>• One-click emergency contacts</li>
                    <li>• Medical alert notifications</li>
                    <li>• Emergency action plan templates</li>
                    <li>• Incident documentation system</li>
                </ul>
            </div>
            
            <!-- Medication Management -->
            <div class="feature-card bg-white rounded-xl shadow-lg p-6 md:p-8 text-center touch-manipulation">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <i class="fas fa-pills text-2xl md:text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 md:mb-4">Medication Management</h3>
                <p class="text-sm md:text-base text-gray-600 mb-4">Secure medication administration tracking for students requiring daily medications, inhalers, and emergency medications like EpiPens.</p>
                <ul class="text-xs md:text-sm text-gray-500 space-y-2">
                    <li>• Medication administration logs</li>
                    <li>• Prescription authorization tracking</li>
                    <li>• Emergency medication alerts</li>
                    <li>• Dosage & timing compliance</li>
                </ul>
            </div>
            
            <!-- Health Compliance Reports -->
            <div class="feature-card bg-white rounded-xl shadow-lg p-6 md:p-8 text-center touch-manipulation">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <i class="fas fa-clipboard-check text-2xl md:text-3xl text-orange-600"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 md:mb-4">Health Compliance Reports</h3>
                <p class="text-sm md:text-base text-gray-600 mb-4">Automated generation of state-required health reports, immunization compliance tracking, and health screening documentation.</p>
                <ul class="text-xs md:text-sm text-gray-500 space-y-2">
                    <li>• State compliance reporting</li>
                    <li>• Immunization gap analysis</li>
                    <li>• Health screening summaries</li>
                    <li>• Attendance & health correlations</li>
                </ul>
            </div>
            
            <!-- Parent Communication Portal -->
            <div class="feature-card bg-white rounded-xl shadow-lg p-6 md:p-8 text-center touch-manipulation">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <i class="fas fa-users text-2xl md:text-3xl text-teal-600"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 md:mb-4">Parent Communication Portal</h3>
                <p class="text-sm md:text-base text-gray-600 mb-4">Secure communication platform for health updates, immunization reminders, and health-related notifications to parents and guardians.</p>
                <ul class="text-xs md:text-sm text-gray-500 space-y-2">
                    <li>• Health status notifications</li>
                    <li>• Immunization reminders</li>
                    <li>• Health form distribution</li>
                    <li>• Two-way secure messaging</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Desktop Showcase Section -->
<section class="desktop-showcase section-padding scroll-animate">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4">Built for Desktop Excellence</h2>
            <p class="text-lg md:text-xl text-gray-600 max-w-4xl mx-auto px-4 sm:px-0">Experience the full power of desktop computing with our clinic management system designed for professional healthcare environments.</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center mb-16">
            <!-- Desktop Power Features -->
            <div class="space-y-6">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 touch-target">
                        <i class="fas fa-desktop text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Multi-Monitor Workflow</h3>
                        <p class="text-sm md:text-base text-gray-600">Utilize multiple screens for patient records, scheduling, and reporting simultaneously. Drag and drop between windows for maximum efficiency.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 touch-target">
                        <i class="fas fa-bolt text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">High-Performance Processing</h3>
                        <p class="text-sm md:text-base text-gray-600">Handle complex queries, generate detailed reports, and process large patient databases with desktop-class computing power.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 touch-target">
                        <i class="fas fa-keyboard text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Advanced Keyboard Shortcuts</h3>
                        <p class="text-sm md:text-base text-gray-600">Streamline your workflow with comprehensive keyboard shortcuts for all major functions. Navigate faster than ever before.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 touch-target">
                        <i class="fas fa-database text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Local Data Processing</h3>
                        <p class="text-sm md:text-base text-gray-600">Process sensitive patient data locally with full HIPAA compliance. No cloud dependency for critical operations.</p>
                    </div>
                </div>
            </div>
            
            <!-- System Requirements -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 border">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-microchip text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">System Requirements</h3>
                    <p class="text-sm md:text-base text-gray-600">Optimized for professional desktop environments</p>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm md:text-base font-medium text-gray-700">Operating System</span>
                        <span class="text-sm md:text-base text-gray-600">Windows 10/11, macOS 10.15+</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm md:text-base font-medium text-gray-700">RAM</span>
                        <span class="text-sm md:text-base text-gray-600">8GB+ (16GB recommended)</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm md:text-base font-medium text-gray-700">Storage</span>
                        <span class="text-sm md:text-base text-gray-600">10GB available space</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm md:text-base font-medium text-gray-700">Display</span>
                        <span class="text-sm md:text-base text-gray-600">1920x1080+ resolution</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm md:text-base font-medium text-gray-700">Network</span>
                        <span class="text-sm md:text-base text-gray-600">Broadband connection</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm md:text-base font-medium text-gray-700">Multi-Monitor</span>
                        <span class="text-sm md:text-base text-green-600 font-medium">✓ Supported</span>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-4">Free system compatibility check</p>
                        <button class="btn-primary btn-touch w-full py-3 rounded-lg font-medium text-white">
                            <i class="fas fa-download mr-2"></i>Download System Check
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Desktop Workflow Visualization -->
        <div class="text-center">
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-8">Professional Desktop Workflow</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                <div class="bg-white rounded-xl p-6 shadow-lg border">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-window-restore text-blue-600 text-xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2">Multi-Window Management</h4>
                    <p class="text-sm text-gray-600">Open multiple patient records, scheduling views, and reports simultaneously across your desktop setup.</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-lg border">
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-arrows-alt text-green-600 text-xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2">Drag & Drop Operations</h4>
                    <p class="text-sm text-gray-600">Seamlessly move appointments, transfer patient data, and reorganize schedules with intuitive desktop interactions.</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-lg border">
                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2">Advanced Task Management</h4>
                    <p class="text-sm text-gray-600">Handle complex healthcare workflows with powerful desktop tools designed for professional medical environments.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Interactive Demo Section -->
<section id="demo" class="section-padding bg-white scroll-animate">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">See It In Action</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Experience the power of our CMS with this interactive demonstration</p>
        </div>
        
        <div class="demo-container">
            <!-- Demo Navigation -->
            <div class="flex justify-center mb-8">
                <div class="bg-gray-100 rounded-lg p-2 flex space-x-2">
                    <button class="demo-tab active px-4 py-2 rounded-md font-medium transition-colors bg-indigo-600 text-white" data-tab="patients-demo">
                        <i class="fas fa-user-injured mr-2"></i>Patients
                    </button>
                    <button class="demo-tab px-4 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-gray-900" data-tab="appointments-demo">
                        <i class="fas fa-calendar-check mr-2"></i>Appointments
                    </button>
                    <button class="demo-tab px-4 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-gray-900" data-tab="prescriptions-demo">
                        <i class="fas fa-pills mr-2"></i>Prescriptions
                    </button>
                    <button class="demo-tab px-4 py-2 rounded-md font-medium transition-colors text-gray-600 hover:text-gray-900" data-tab="analytics-demo">
                        <i class="fas fa-chart-bar mr-2"></i>Analytics
                    </button>
                </div>
            </div>
            
            <!-- Demo Content -->
            <div class="demo-screen bg-white rounded-2xl shadow-2xl overflow-hidden max-w-5xl mx-auto">
                <!-- Patients Demo -->
                <div id="patients-demo" class="demo-content active p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Patient Management System</h3>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Live Demo</span>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold">Recent Patients</h4>
                                    <button class="text-indigo-600 hover:text-indigo-800 font-medium">View All</button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-4">JS</div>
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900">John Smith</h5>
                                            <p class="text-sm text-gray-600">Last visit: Today, 2:30 PM</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold mr-4">MG</div>
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900">Maria Garcia</h5>
                                            <p class="text-sm text-gray-600">Last visit: Yesterday, 10:15 AM</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Follow-up</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold mr-4">RJ</div>
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900">Robert Johnson</h5>
                                            <p class="text-sm text-gray-600">Last visit: 3 days ago</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Completed</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold mb-4">Quick Stats</h4>
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium">New Patients This Month</span>
                                            <span class="text-sm text-gray-600">23</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: 76%"></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium">Active Cases</span>
                                            <span class="text-sm text-gray-600">156</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: 92%"></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium">Follow-ups Pending</span>
                                            <span class="text-sm text-gray-600">8</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-orange-600 h-2 rounded-full" style="width: 34%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Appointments Demo -->
                <div id="appointments-demo" class="demo-content p-8 hidden">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Appointment Scheduler</h3>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">Interactive</span>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold mb-4">Today's Schedule</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center p-3 bg-white rounded-lg border-l-4 border-green-500">
                                        <div class="mr-4">
                                            <div class="text-lg font-bold text-green-600">09:00</div>
                                            <div class="text-sm text-gray-600">AM</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-medium">Dr. Martinez - Consultation</h5>
                                            <p class="text-sm text-gray-600">Patient: Sarah Wilson</p>
                                        </div>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Confirmed</span>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-white rounded-lg border-l-4 border-blue-500">
                                        <div class="mr-4">
                                            <div class="text-lg font-bold text-blue-600">11:30</div>
                                            <div class="text-sm text-gray-600">AM</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-medium">Dr. Chen - Follow-up</h5>
                                            <p class="text-sm text-gray-600">Patient: Michael Davis</p>
                                        </div>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">In Progress</span>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-white rounded-lg border-l-4 border-yellow-500">
                                        <div class="mr-4">
                                            <div class="text-lg font-bold text-yellow-600">14:00</div>
                                            <div class="text-sm text-gray-600">PM</div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-medium">Dr. Rodriguez - Check-up</h5>
                                            <p class="text-sm text-gray-600">Patient: Emma Thompson</p>
                                        </div>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold mb-4">Quick Actions</h4>
                                <div class="space-y-3">
                                    <button class="w-full bg-indigo-600 text-white p-3 rounded-lg hover:bg-indigo-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Schedule New Appointment
                                    </button>
                                    <button class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-calendar-check mr-2"></i>View Calendar
                                    </button>
                                    <button class="w-full bg-orange-600 text-white p-3 rounded-lg hover:bg-orange-700 transition-colors">
                                        <i class="fas fa-bell mr-2"></i>Send Reminders
                                    </button>
                                </div>
                                
                                <div class="mt-6">
                                    <h5 class="font-medium mb-2">Upcoming Week</h5>
                                    <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                        <div class="p-2 bg-gray-200 rounded">Mon<br><span class="text-xs text-gray-600">12</span></div>
                                        <div class="p-2 bg-blue-100 rounded">Tue<br><span class="text-xs text-blue-600">8</span></div>
                                        <div class="p-2 bg-green-100 rounded">Wed<br><span class="text-xs text-green-600">15</span></div>
                                        <div class="p-2 bg-yellow-100 rounded">Thu<br><span class="text-xs text-yellow-600">6</span></div>
                                        <div class="p-2 bg-purple-100 rounded">Fri<br><span class="text-xs text-purple-600">11</span></div>
                                        <div class="p-2 bg-gray-200 rounded">Sat<br><span class="text-xs text-gray-600">3</span></div>
                                        <div class="p-2 bg-gray-200 rounded">Sun<br><span class="text-xs text-gray-600">0</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Prescriptions Demo -->
                <div id="prescriptions-demo" class="demo-content p-8 hidden">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Digital Prescriptions</h3>
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">Smart System</span>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold mb-4">Recent Prescriptions</h4>
                                <div class="space-y-4">
                                    <div class="bg-white rounded-lg p-4 border">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h5 class="font-medium text-gray-900">Amoxicillin 500mg</h5>
                                                <p class="text-sm text-gray-600">Patient: John Smith</p>
                                            </div>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Dosage:</span>
                                                <p class="font-medium">1 capsule, 3x daily</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Duration:</span>
                                                <p class="font-medium">7 days</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Remaining:</span>
                                                <p class="font-medium">4 days</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white rounded-lg p-4 border">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h5 class="font-medium text-gray-900">Lisinopril 10mg</h5>
                                                <p class="text-sm text-gray-600">Patient: Maria Garcia</p>
                                            </div>
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Ongoing</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Dosage:</span>
                                                <p class="font-medium">1 tablet, daily</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Duration:</span>
                                                <p class="font-medium">30 days</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Refills:</span>
                                                <p class="font-medium">2 remaining</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold mb-4">Alerts & Reminders</h4>
                                <div class="space-y-3">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                            <span class="text-sm font-medium text-red-700">Drug Interaction Alert</span>
                                        </div>
                                        <p class="text-xs text-red-600 mt-1">Check compatibility with current medications</p>
                                    </div>
                                    
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                            <span class="text-sm font-medium text-yellow-700">Refill Reminder</span>
                                        </div>
                                        <p class="text-xs text-yellow-600 mt-1">3 prescriptions need refills this week</p>
                                    </div>
                                    
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <div class="flex items-center">
                                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                            <span class="text-sm font-medium text-blue-700">New Generic Available</span>
                                        </div>
                                        <p class="text-xs text-blue-600 mt-1">Cost-effective alternatives found</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Analytics Demo -->
                <div id="analytics-demo" class="demo-content p-8 hidden">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h3>
                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">Real-time</span>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold mb-4">Performance Metrics</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">Patient Satisfaction</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: 94%"></div>
                                            </div>
                                            <span class="text-sm text-green-600 font-medium">94%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">Average Wait Time</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: 78%"></div>
                                            </div>
                                            <span class="text-sm text-blue-600 font-medium">12 min</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">Treatment Success Rate</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: 88%"></div>
                                            </div>
                                            <span class="text-sm text-purple-600 font-medium">88%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">Appointment Adherence</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-orange-600 h-2 rounded-full" style="width: 82%"></div>
                                            </div>
                                            <span class="text-sm text-orange-600 font-medium">82%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="text-lg font-semibold mb-4">Monthly Trends</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-green-600">+23%</div>
                                        <div class="text-sm text-gray-600">New Patients</div>
                                        <div class="text-xs text-green-600 mt-1">↗ vs last month</div>
                                    </div>
                                    
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-blue-600">156</div>
                                        <div class="text-sm text-gray-600">Appointments</div>
                                        <div class="text-xs text-blue-600 mt-1">↗ +12% growth</div>
                                    </div>
                                    
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-purple-600">89%</div>
                                        <div class="text-sm text-gray-600">Efficiency</div>
                                        <div class="text-xs text-purple-600 mt-1">↗ +5% improved</div>
                                    </div>
                                    
                                    <div class="bg-white rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-orange-600">4.8</div>
                                        <div class="text-sm text-gray-600">Avg Rating</div>
                                        <div class="text-xs text-orange-600 mt-1">⭐ Excellent</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section id="stats" class="section-padding bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Trusted by Healthcare Professionals</h2>
            <p class="text-xl text-gray-600">See the impact our platform has made in the healthcare industry</p>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="stats-counter" data-count="{{ $stats['total_patients'] ?? 1247 }}">0</div>
                <p class="text-gray-600 text-lg font-medium">Patients Managed</p>
                <p class="text-sm text-gray-500">Across all healthcare facilities</p>
            </div>
            <div class="text-center">
                <div class="stats-counter" data-count="{{ $stats['total_appointments'] ?? 3456 }}">0</div>
                <p class="text-gray-600 text-lg font-medium">Appointments Scheduled</p>
                <p class="text-sm text-gray-500">This month alone</p>
            </div>
            <div class="text-center">
                <div class="stats-counter" data-count="{{ $stats['total_medicines'] ?? 892 }}">0</div>
                <p class="text-gray-600 text-lg font-medium">Medicines Catalogued</p>
                <p class="text-sm text-gray-500">In our comprehensive database</p>
            </div>
            <div class="text-center">
                <div class="stats-counter" data-count="{{ $stats['total_users'] ?? 45 }}">0</div>
                <p class="text-gray-600 text-lg font-medium">Healthcare Professionals</p>
                <p class="text-sm text-gray-500">Using our platform daily</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="section-padding bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">What Healthcare Professionals Say</h2>
            <p class="text-xl text-gray-600">Real feedback from real users who trust our platform daily</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user-nurse text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Nurse Maria Santos</h4>
                        <p class="text-gray-600 text-sm">School Nurse, Lincoln Elementary</p>
                    </div>
                </div>
                <div class="text-yellow-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic">"This system has revolutionized how I manage student health records. Tracking immunizations and managing medications is now so much easier, and parents love the automated notifications."</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Dr. Jennifer Cruz</h4>
                        <p class="text-gray-600 text-sm">Health Coordinator, Madison High School</p>
                    </div>
                </div>
                <div class="text-yellow-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic">"The emergency response features have been invaluable. Having instant access to student medical alerts and parent contact information has made our emergency procedures much more efficient."</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user-graduate text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">John Garcia</h4>
                        <p class="text-gray-600 text-sm">Principal, Roosevelt Middle School</p>
                    </div>
                </div>
                <div class="text-yellow-400 mb-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic">"The compliance reporting feature saves us hours during state inspections. All our health records and immunization data are perfectly organized and always audit-ready."</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="section-padding bg-white scroll-animate">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-xl text-gray-600">Get answers to common questions about our school health management system</p>
        </div>
        
        <div class="space-y-4">
            <div class="faq-item bg-gray-50 rounded-lg">
                <button class="faq-question w-full text-left p-6 focus:outline-none" type="button">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Is the system FERPA and HIPAA compliant?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                    </div>
                </button>
                <div class="faq-answer px-6 pb-6">
                    <p class="text-gray-600">Yes, our system is fully compliant with both FERPA (Family Educational Rights and Privacy Act) and HIPAA regulations. We implement robust security measures including end-to-end encryption, secure data storage, role-based access controls, and detailed audit trails to protect student health information and ensure compliance with all applicable privacy regulations.</p>
                </div>
            </div>
            
            <div class="faq-item bg-gray-50 rounded-lg">
                <button class="faq-question w-full text-left p-6 focus:outline-none" type="button">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Can I import existing student health records?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                    </div>
                </button>
                <div class="faq-answer px-6 pb-6">
                    <p class="text-gray-600">Absolutely! We provide comprehensive data migration services to help you transfer existing student health records, immunization histories, and medication information from your current system. Our team will work with your school to ensure a smooth transition with zero data loss while maintaining all compliance requirements.</p>
                </div>
            </div>
            
            <div class="faq-item bg-gray-50 rounded-lg">
                <button class="faq-question w-full text-left p-6 focus:outline-none" type="button">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">What kind of training and support do you provide?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                    </div>
                </button>
                <div class="faq-answer px-6 pb-6">
                    <p class="text-gray-600">We provide comprehensive training for school nurses and health coordinators including live virtual training sessions, video tutorials, and detailed documentation. Our support team understands the unique needs of school health programs and is available via email, phone, and live chat during school hours.</p>
                </div>
            </div>
            
            <div class="faq-item bg-gray-50 rounded-lg">
                <button class="faq-question w-full text-left p-6 focus:outline-none" type="button">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Does the system work with state reporting requirements?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                    </div>
                </button>
                <div class="faq-answer px-6 pb-6">
                    <p class="text-gray-600">Yes! Our system generates reports that comply with state health department requirements for immunization tracking, health screening documentation, and communicable disease reporting. We regularly update our reporting templates to match changing state regulations and can customize reports for specific state requirements.</p>
                </div>
            </div>
            
            <div class="faq-item bg-gray-50 rounded-lg">
                <button class="faq-question w-full text-left p-6 focus:outline-none" type="button">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">How does parent communication work?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                    </div>
                </button>
                <div class="faq-answer px-6 pb-6">
                    <p class="text-gray-600">Our parent communication portal allows secure messaging, automated notifications for health visits, immunization reminders, and health form distribution. Parents can update emergency contacts, submit health forms digitally, and receive real-time notifications about their child's health activities at school.</p>
                </div>
            </div>
            
            <div class="faq-item bg-gray-50 rounded-lg">
                <button class="faq-question w-full text-left p-6 focus:outline-none" type="button">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Can the system handle multiple schools or districts?</h3>
                        <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-200"></i>
                    </div>
                </button>
                <div class="faq-answer px-6 pb-6">
                    <p class="text-gray-600">Absolutely! Our system supports multi-school and district-wide implementations. You can manage health records across multiple schools while maintaining appropriate access controls. District administrators can generate consolidated reports while individual school nurses maintain their own student populations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Signup Section -->
<section class="section-padding bg-indigo-50 scroll-animate">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Stay Updated with Healthcare Innovation</h2>
            <p class="text-lg text-gray-600">Get the latest updates on healthcare technology, best practices, and system improvements delivered to your inbox.</p>
        </div>
        
        <form class="enhanced-form max-w-md mx-auto" id="newsletter-form">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="form-group flex-1">
                    <input type="email" class="form-input w-full px-4 py-3 rounded-lg border-2 focus:outline-none" placeholder="Enter your email address" required>
                </div>
                <button type="submit" class="btn-primary px-8 py-3 rounded-lg font-semibold text-white">
                    <i class="fas fa-paper-plane mr-2"></i>Subscribe
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-3">We respect your privacy. Unsubscribe at any time.</p>
        </form>
        
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div class="flex items-center justify-center">
                <i class="fas fa-envelope text-indigo-600 mr-2"></i>
                <span>Monthly newsletters</span>
            </div>
            <div class="flex items-center justify-center">
                <i class="fas fa-lightbulb text-indigo-600 mr-2"></i>
                <span>Healthcare tips & insights</span>
            </div>
            <div class="flex items-center justify-center">
                <i class="fas fa-rocket text-indigo-600 mr-2"></i>
                <span>Product updates & features</span>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section-padding gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-4">Ready to Transform Your Healthcare Practice?</h2>
        <p class="text-xl text-gray-100 mb-8">Join thousands of healthcare professionals who trust our platform for their daily operations.</p>
        
        <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0 justify-center">
            @auth
                <a href="{{ route('dashboard.index') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Access Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-rocket mr-2"></i>
                    Start Free Today
                </a>
                <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-white hover:text-indigo-600 transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </a>
            @endauth
        </div>
        
        <div class="mt-8 text-sm text-gray-200">
            <p>💡 No credit card required • ⚡ Get started in minutes • 🔒 HIPAA compliant</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer id="contact" class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center mb-4">
                    @if(config('app.favicon'))
                        <img src="{{ asset(config('app.favicon')) }}" alt="Bokod Medical CMS" class="h-8 w-8 mr-3">
                    @else
                        <i class="fas fa-hospital-alt text-2xl text-indigo-400 mr-3"></i>
                    @endif
                    <span class="text-xl font-bold">Bokod Medical CMS</span>
                </div>
                <p class="text-gray-300 mb-4 max-w-md">
                    Professional healthcare management system designed to streamline medical practices and improve patient care through modern technology.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold mb-4">Features</h3>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="#" class="hover:text-white transition-colors">Patient Management</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Appointments</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Prescriptions</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Medicine Inventory</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Reports</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Info</h3>
                <ul class="space-y-2 text-gray-300">
                    <li class="flex items-center">
                        <i class="fas fa-envelope mr-2 text-indigo-400"></i>
                        <a href="mailto:support@bokodmedical.com" class="hover:text-white transition-colors">support@bokodmedical.com</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-phone mr-2 text-indigo-400"></i>
                        <a href="tel:+63912345678" class="hover:text-white transition-colors">+63 912 345 678</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-indigo-400"></i>
                        <span>Bokod, Benguet, Philippines</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Bokod Medical CMS. All rights reserved. Built with ❤️ for healthcare professionals.</p>
        </div>
    </div>
</footer>
@endsection