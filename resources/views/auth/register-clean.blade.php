<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <style>
        /* Landing page matching styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #FDFDFC;
            color: #1b1b18;
            line-height: 1.5;
            padding: 1.5rem;
        }
        
        .container {
            max-width: 56rem;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .header {
            width: 100%;
            text-align: right;
            margin-bottom: 1.5rem;
        }
        
        .header a {
            display: inline-block;
            padding: 0.375rem 1.25rem;
            color: #1b1b18;
            text-decoration: none;
            border: 1px solid transparent;
            border-radius: 0.125rem;
            font-size: 0.875rem;
            transition: border-color 0.15s;
        }
        
        .header a:hover {
            border-color: #19140035;
        }
        
        .main-card {
            width: 100%;
            max-width: 48rem;
            background: white;
            box-shadow: inset 0px 0px 0px 1px rgba(26, 26, 0, 0.16);
            border-radius: 0.5rem;
            padding: 2rem;
        }
        
        .title {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .title h1 {
            font-size: 1.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1b1b18;
        }
        
        .title p {
            color: #706f6c;
            margin-bottom: 1.5rem;
        }
        
        .section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1b1b18;
            margin-bottom: 1rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .form-grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .form-grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background: white;
            color: #1b1b18;
            font-size: 1rem;
            transition: all 0.15s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #f53003;
            box-shadow: 0 0 0 2px rgba(245, 48, 3, 0.25);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 6rem;
        }
        
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
        
        .error-message {
            font-size: 0.875rem;
            color: #dc2626;
            margin-top: 0.5rem;
        }
        
        .help-text {
            font-size: 0.875rem;
            color: #706f6c;
            margin-top: 0.25rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .checkbox-input {
            width: 1rem;
            height: 1rem;
            margin-top: 0.125rem;
        }
        
        .checkbox-label {
            font-size: 0.875rem;
            color: #374151;
        }
        
        .submit-button {
            width: 100%;
            max-width: 12rem;
            margin: 0 auto;
            padding: 0.75rem 1.5rem;
            background: #f53003;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.15s;
        }
        
        .submit-button:hover {
            background: #146c43;
        }
        
        .footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #706f6c;
            font-size: 0.875rem;
        }
        
        .footer a {
            color: #f53003;
            text-decoration: underline;
            text-underline-offset: 4px;
        }
        
        .footer a:hover {
            text-decoration: none;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-grid-2,
            .form-grid-3 {
                grid-template-columns: 1fr;
            }
            
            .main-card {
                padding: 1.5rem;
            }
            
            .title h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="{{ route('login') }}">Log in</a>
        </div>
        
        <!-- Main Content -->
        <div class="main-card">
            <div class="title">
                <h1>BSU Student Registration</h1>
                <p>Create your account to book health center appointments online</p>
            </div>
            
            <!-- Error Message -->
            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ route('register.submit') }}" method="post">
                @csrf
                
                <!-- Personal Information -->
                <div class="section">
                    <h2 class="section-title">Personal Information</h2>
                    
                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" 
                                placeholder="Enter your full name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" 
                                placeholder="Enter your email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-input @error('phone_number') border-red-500 @enderror" 
                                placeholder="09123456789" value="{{ old('phone_number') }}" required>
                            <div class="help-text">Philippine mobile number format: 09XXXXXXXXX</div>
                            @error('phone_number')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-input @error('date_of_birth') border-red-500 @enderror" 
                                value="{{ old('date_of_birth') }}" required max="{{ date('Y-m-d', strtotime('-13 years')) }}">
                            @error('date_of_birth')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-input form-select @error('gender') border-red-500 @enderror" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Civil Status</label>
                            <select name="civil_status" class="form-input form-select">
                                <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Home Address</label>
                        <textarea name="address" class="form-input form-textarea @error('address') border-red-500 @enderror" 
                            placeholder="Enter your home address" required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Academic Information -->
                <div class="section">
                    <h2 class="section-title">Academic Information</h2>
                    
                    <div class="form-grid form-grid-3">
                        <div class="form-group">
                            <label class="form-label">Student ID</label>
                            <input type="text" name="student_id" class="form-input @error('student_id') border-red-500 @enderror" 
                                placeholder="Enter student ID" value="{{ old('student_id') }}" required>
                            @error('student_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Course/Program</label>
                            <select name="course" class="form-input form-select @error('course') border-red-500 @enderror" required>
                                <option value="">Select your course/program</option>
                                @include('components.course-options', ['selected' => old('course')])
                            </select>
                            @error('course')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Year Level</label>
                            <select name="year_level" class="form-input form-select @error('year_level') border-red-500 @enderror" required>
                                <option value="">Select Year</option>
                                <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                                <option value="5th Year" {{ old('year_level') == '5th Year' ? 'selected' : '' }}>5th Year</option>
                            </select>
                            @error('year_level')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Emergency Contact -->
                <div class="section">
                    <h2 class="section-title">Emergency Contact Information</h2>
                    
                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-input @error('emergency_contact_name') border-red-500 @enderror" 
                                placeholder="Full name" value="{{ old('emergency_contact_name') }}" required>
                            @error('emergency_contact_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Relationship</label>
                            <input type="text" name="emergency_contact_relationship" class="form-input @error('emergency_contact_relationship') border-red-500 @enderror" 
                                placeholder="e.g., Parent, Guardian" value="{{ old('emergency_contact_relationship') }}" required>
                            @error('emergency_contact_relationship')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Contact Phone</label>
                            <input type="tel" name="emergency_contact_phone" class="form-input @error('emergency_contact_phone') border-red-500 @enderror" 
                                placeholder="09123456789" value="{{ old('emergency_contact_phone') }}" required>
                            <div class="help-text">Philippine mobile number format: 09XXXXXXXXX</div>
                            @error('emergency_contact_phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Height (Optional)</label>
                            <input type="number" name="height" class="form-input @error('height') border-red-500 @enderror" 
                                placeholder="Height in cm" value="{{ old('height') }}" min="50" max="250" step="0.1">
                            @error('height')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Address</label>
                        <textarea name="emergency_contact_address" class="form-input form-textarea @error('emergency_contact_address') border-red-500 @enderror" 
                            placeholder="Full address of emergency contact" required>{{ old('emergency_contact_address') }}</textarea>
                        @error('emergency_contact_address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Account Security -->
                <div class="section">
                    <h2 class="section-title">Account Security</h2>
                    
                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-input @error('password') border-red-500 @enderror" 
                                placeholder="Password" required>
                            <div class="help-text">Password must be at least 8 characters with letters and numbers</div>
                            @error('password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-input" 
                                placeholder="Confirm Password" required>
                        </div>
                    </div>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="section">
                    <div class="checkbox-group">
                        <input type="checkbox" name="terms_agreement" id="terms" value="1" class="checkbox-input @error('terms_agreement') border-red-500 @enderror" 
                            required {{ old('terms_agreement') ? 'checked' : '' }}>
                        <label for="terms" class="checkbox-label">
                            I agree to the <a href="#" class="text-[#f53003] underline">Terms and Conditions</a>
                        </label>
                    </div>
                    @error('terms_agreement')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="privacy_agreement" id="privacy" value="1" class="checkbox-input @error('privacy_agreement') border-red-500 @enderror" 
                            required {{ old('privacy_agreement') ? 'checked' : '' }}>
                        <label for="privacy" class="checkbox-label">
                            I agree to the <a href="#" class="text-[#f53003] underline">Privacy Policy</a>
                        </label>
                    </div>
                    @error('privacy_agreement')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <button type="submit" class="submit-button">
                            Register Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div style="margin-bottom: 0.5rem;">
                Bokod Medical CMS - Clinic Management System
            </div>
            <div>
                <a href="{{ route('login') }}">Already have an account? Sign in here</a>
            </div>
        </div>
    </div>
    
    <script>
        // Phone validation
        function isValidPhoneFormat(phone) {
            const cleanNumber = phone.replace(/[^0-9]/g, '');
            return /^09[0-9]{9}$/.test(cleanNumber);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const phoneInputs = document.querySelectorAll('input[name="phone_number"], input[name="emergency_contact_phone"]');
            
            phoneInputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    const phone = this.value.trim();
                    if (phone.length > 0) {
                        if (isValidPhoneFormat(phone)) {
                            this.style.borderColor = '#10b981';
                        } else {
                            this.style.borderColor = '#ef4444';
                        }
                    } else {
                        this.style.borderColor = '#d1d5db';
                    }
                });
            });
        });
    </script>
</body>
</html>