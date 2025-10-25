<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BSU-Bokod Campus Clinic</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-green-800 to-green-900">
    <!-- Header -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex items-center">
                        @if(config('app.favicon'))
                            <img src="{{ asset(config('app.favicon')) }}" alt="BSU-Bokod Campus Clinic" class="h-8 w-8 mr-3">
                        @else
                            <i class="fas fa-hospital-alt text-2xl text-green-700 mr-3"></i>
                        @endif
                        <span class="text-xl font-bold text-gray-900">BSU-Bokod Campus Clinic</span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="text-green-700 hover:text-green-900 px-3 py-2 text-sm font-medium transition-colors">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium transition-colors">Sign In</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-md text-sm font-semibold bg-yellow-400 text-green-800 border border-yellow-300 hover:bg-yellow-300 transition-colors">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <div class="h-16"></div>

    <!-- Vision and Mission Section -->
    <section class="py-20 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                @php($leftLogo = env('LANDING_LOGO_LEFT') ?: asset('images/cat.jpg'))
                @php($centerLogo = env('LANDING_LOGO_CENTER') ?: asset('images/cms.png'))
                @php($rightLogo = env('LANDING_LOGO_RIGHT') ?: asset('images/bsu-bokod.jpg'))
                <!-- Top Logos Row -->
                <div class="flex items-center justify-center gap-6 mb-6">
                    <!-- Left Logo -->
                    <div class="w-16 h-16 rounded-full ring-4 ring-yellow-300 bg-white p-1 shadow-md overflow-hidden">
                        <img src="{{ $leftLogo }}" alt="College of Applied Technology" class="w-full h-full rounded-full object-contain" onerror="this.style.display='none'">
                    </div>
                    <!-- Center/System Logo (smaller to avoid blur from favicon upscaling) -->
                    @if($centerLogo)
                        <div class="w-20 h-20 rounded-full ring-4 ring-green-500 bg-white p-1 shadow-lg overflow-hidden">
                            <img src="{{ $centerLogo }}" alt="System Logo" class="w-full h-full rounded-full object-contain">
                        </div>
                    @else
                        <div class="w-20 h-20 rounded-full bg-green-700 flex items-center justify-center text-white text-lg font-bold ring-4 ring-green-500 shadow-lg">LOGO</div>
                    @endif
                    <!-- Right Logo -->
                    <div class="w-16 h-16 rounded-full ring-4 ring-yellow-300 bg-white p-1 shadow-md overflow-hidden">
                        <img src="{{ $rightLogo }}" alt="BSU Bokod" class="w-full h-full rounded-full object-contain" onerror="this.style.display='none'">
                    </div>
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Benguet State University - Bokod Campus</h2>
                <p class="text-lg text-green-100">Guiding Principles of Academic Excellence</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 border border-white border-opacity-20">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-eye text-green-900 text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold">Vision</h3>
                    </div>
                    <p class="text-lg leading-relaxed text-green-50">
                        A premier university in transformative education, innovative research, inclusive extension services, sustainable development, and stewardship of culture and the environment.
                    </p>
                </div>
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 border border-white border-opacity-20">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-bullseye text-green-900 text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold">Mission</h3>
                    </div>
                    <p class="text-lg leading-relaxed text-green-50">
                        Cultivate resilient and future-ready human capital through excellent teaching, responsive research, proactive and sustainable community engagements, strategic partnerships, and progressive leadership.
                    </p>
                </div>
            </div>
            <div class="mt-12 text-center">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-6 max-w-4xl mx-auto border border-white border-opacity-20">
                    <h4 class="text-xl font-bold mb-3">Campus Health Excellence</h4>
                    <p class="text-green-100">
                        Our campus clinic embodies BSU's mission by providing excellent healthcare services, conducting health research, engaging with the campus community proactively, and leading in innovative health solutions for educational institutions.
                    </p>
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
                    <span class="text-green-800">Our Campus</span> Location
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Located at the heart of Benguet State University, BOKOD CMS serves the entire campus community
                </p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="relative">
                    <div class="bg-white rounded-2xl shadow-2xl p-4 border border-gray-100">
                        <div class="w-full h-80 rounded-xl overflow-hidden relative">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3827.8945674829845!2d120.83368906545476!3d16.519621391181005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTbCsDMxJzEwLjYiTiAxMjDCsDUwJzAxLjMiRQ!5e0!3m2!1sen!2sph!4v1642012345678!5m2!1sen!2sph&q=16.519621391181005,120.83368906545476" width="100%" height="320" style="border:0; border-radius: 12px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Benguet State University Campus Location"></iframe>
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-green-800 px-3 py-2 rounded-lg text-sm font-bold shadow-lg border border-yellow-200">
                                <i class="fas fa-map-marker-alt text-yellow-600 mr-1"></i>
                                BSU Campus
                            </div>
                            <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-sm text-gray-700 px-3 py-1 rounded-lg text-xs shadow-lg border border-gray-200">
                                16.5196°N, 120.8337°E
                            </div>
                        </div>
                        <div class="mt-4 flex justify-center space-x-3">
                            <a href="https://www.google.com/maps?q=16.519621391181005,120.83368906545476" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors shadow-md">
                                <i class="fas fa-external-link-alt mr-2"></i>Open in Google Maps
                            </a>
                            <a href="https://www.google.com/maps/dir//16.519621391181005,120.83368906545476" target="_blank" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-green-800 text-sm font-medium rounded-lg hover:bg-yellow-600 transition-colors shadow-md">
                                <i class="fas fa-route mr-2"></i>Get Directions
                            </a>
                        </div>
                    </div>
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-yellow-400 rounded-full opacity-60"></div>
                    <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-green-600 rounded-full opacity-60"></div>
                </div>
                <div class="space-y-8">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">📍 Campus Healthcare Hub</h3>
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
                            <div class="flex items-start space-x-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-signs text-green-700 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-2">Navigation</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="https://www.google.com/maps?q=16.519621391181005,120.83368906545476" target="_blank" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full hover:bg-green-200 transition-colors">
                                            <i class="fab fa-google mr-1"></i>Google Maps
                                        </a>
                                        <a href="https://waze.com/ul?q=16.519621391181005,120.83368906545476" target="_blank" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors">
                                            <i class="fas fa-route mr-1"></i>Waze
                                        </a>
                                        <a href="geo:16.519621391181005,120.83368906545476" class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full hover:bg-yellow-200 transition-colors">
                                            <i class="fas fa-location-arrow mr-1"></i>Mobile GPS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-center mb-4">
                @if(config('app.favicon'))
                    <img src="{{ asset(config('app.favicon')) }}" alt="BSU-Bokod Campus Clinic" class="h-8 w-8 mr-3">
                @else
                    <i class="fas fa-hospital-alt text-2xl text-green-400 mr-3"></i>
                @endif
                <span class="text-lg font-semibold">BSU-Bokod Campus Clinic</span>
            </div>
            <p class="text-gray-300 max-w-2xl">
                Professional campus health management system for Benguet State University - Bokod Campus.
            </p>
            <div class="border-t border-gray-800 mt-6 pt-6 text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} BSU-Bokod Campus Clinic. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
</body>
</html>
