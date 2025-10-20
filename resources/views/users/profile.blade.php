<x-layouts.app :title="__('My Profile')">
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-700 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold">
                                {{ $user->initials() }}
                            </div>
                            <div>
                                <h1 class="text-4xl font-bold mb-2">{{ $user->name }}</h1>
                                <p class="text-purple-100">{{ $user->role }} • {{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-4">
                            <a href="{{ route('dashboard') }}" 
                               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-all duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                                </svg>
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Update Profile Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Information</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update your account profile information and email address.</p>
                    </div>
                    <form method="POST" action="{{ route('users.profile.update') }}" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label for="profile_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Full Name
                                </label>
                                <input type="text" name="name" id="profile_name" value="{{ old('name', $user->name) }}" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="profile_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email Address
                                </label>
                                <input type="email" name="email" id="profile_email" value="{{ old('email', $user->email) }}" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="profile_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Phone Number
                                </label>
                                <input type="text" name="phone" id="profile_phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Change Password</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update your password to keep your account secure.</p>
                    </div>
                    <form method="POST" action="{{ route('users.password.update') }}" class="p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Current Password
                                </label>
                                <input type="password" name="current_password" id="current_password" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('current_password') border-red-500 @enderror">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    New Password
                                </label>
                                <input type="password" name="password" id="password" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum 8 characters</p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Confirm New Password
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Account Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt>
                            <dd class="text-lg text-gray-900 dark:text-white font-semibold">{{ $user->role }}</dd>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="text-lg text-gray-900 dark:text-white font-semibold">{{ ucfirst($user->status) }}</dd>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                            <dd class="text-lg text-gray-900 dark:text-white font-semibold">{{ $user->created_at->format('M Y') }}</dd>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Appointments</dt>
                            <dd class="text-lg text-gray-900 dark:text-white font-semibold">{{ number_format($user->appointments->count()) }}</dd>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prescriptions</dt>
                            <dd class="text-lg text-gray-900 dark:text-white font-semibold">{{ number_format($user->prescriptions->count()) }}</dd>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="text-lg text-gray-900 dark:text-white font-semibold">{{ $user->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Permissions -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Your Permissions</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Based on your {{ $user->role }} role</p>
                </div>
                <div class="p-6">
                    @php
                        $permissions = [
                            'Admin' => [
                                'Full system access',
                                'User management',
                                'All reports and analytics',
                                'System configuration',
                                'Data backup and restore',
                                'Audit logs access'
                            ],
                            'Doctor' => [
                                'Patient management',
                                'Appointment scheduling',
                                'Create prescriptions',
                                'Medical reports access',
                                'Patient history review',
                                'Treatment planning'
                            ],
                            'Nurse' => [
                                'Patient care assistance',
                                'Appointment support',
                                'Basic patient data access',
                                'Treatment support',
                                'Vital signs recording',
                                'Care coordination'
                            ],
                            'Pharmacist' => [
                                'Medicine inventory management',
                                'Prescription fulfillment',
                                'Stock management',
                                'Medicine reports',
                                'Drug interaction checks',
                                'Inventory ordering'
                            ],
                            'Receptionist' => [
                                'Appointment scheduling',
                                'Patient check-in/check-out',
                                'Basic patient information',
                                'Front desk operations',
                                'Call management',
                                'Visitor management'
                            ]
                        ];
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($permissions[$user->role] ?? [] as $permission)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300">{{ $permission }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>