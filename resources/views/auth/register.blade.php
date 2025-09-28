@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* BSU Official Color Palette */
        :root {
            --bsu-primary-green: #0F5132;
            --bsu-secondary-green: #198754;
            --bsu-light-green: #20c997;
            --bsu-accent-yellow: #FFD60A;
            --bsu-golden-yellow: #FFC107;
            --text-dark: #1a202c;
            --text-light: #6c757d;
            --success-color: #198754;
            --warning-color: #FFC107;
            --error-color: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bsu-primary-green) 0%, var(--bsu-secondary-green) 50%, var(--bsu-light-green) 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Background decoration - BSU themed */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 25% 25%, rgba(255, 214, 10, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(25, 135, 84, 0.1) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
            z-index: -2;
        }
        
        /* Floating shapes - BSU themed */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(25, 135, 84, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 214, 10, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            z-index: -1;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(-30px, -50px) rotate(120deg); }
            66% { transform: translate(30px, -20px) rotate(240deg); }
        }
        
        .card {
            border: none;
            box-shadow: 
                0 25px 50px rgba(0,0,0,0.25),
                0 10px 25px rgba(0,0,0,0.15),
                inset 0 1px 0 rgba(255,255,255,0.1);
            max-width: 1200px;
            margin: 1rem auto;
            border-radius: 1rem;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
            position: relative;
            z-index: 1;
        }
        
        .registration-header {
            background: linear-gradient(135deg, var(--bsu-primary-green) 0%, var(--bsu-secondary-green) 100%);
            color: white;
            padding: 1.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .registration-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(255, 214, 10, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 80% 80%, rgba(255, 214, 10, 0.05) 0%, transparent 30%);
            z-index: 0;
        }
        
        .registration-header h3,
        .registration-header p {
            position: relative;
            z-index: 1;
        }
        
        .registration-header h3 {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
        }
        
        .registration-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .registration-content {
            padding: 0 !important;
            background: white;
        }
        
        .card-body {
            padding: 0 !important;
        }
        
        .section-header {
            background: rgba(25, 135, 84, 0.05);
            padding: 0.5rem 1rem;
            margin: 0 -2rem 1rem -2rem;
            border-left: 4px solid var(--bsu-secondary-green);
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
        }
        
        .section-header i {
            margin-right: 0.5rem;
            color: var(--bsu-secondary-green);
        }
        
        .form-row, .row {
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem !important;
        }
        
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 2px solid #ced4da;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--bsu-secondary-green);
            box-shadow: 0 0 0 0.125rem rgba(25, 135, 84, 0.25);
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--bsu-secondary-green) 0%, var(--bsu-light-green) 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 0.25rem;
        }
        
        .info-note {
            background: rgba(25, 135, 84, 0.1);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: var(--bsu-primary-green);
            padding: 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            margin-top: 1rem;
        }
        
        .col-form {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        /* Multi-step form styles */
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 0.5rem;
            padding: 0.75rem;
        }
        
        .step {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 12px;
            background: #e9ecef;
            color: #6c757d;
            font-weight: bold;
            font-size: 1rem;
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .step.active {
            background: var(--bsu-secondary-green);
            color: white;
            box-shadow: 0 4px 15px rgba(25, 135, 84, 0.4);
        }
        
        .step.completed {
            background: var(--bsu-accent-yellow);
            color: var(--bsu-primary-green);
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(255, 214, 10, 0.4);
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -16px;
            width: 16px;
            height: 2px;
            background: #e9ecef;
            transform: translateY(-50%);
        }
        
        .step.completed:not(:last-child)::after {
            background: var(--bsu-accent-yellow);
        }
        
        .form-section {
            background: #ffffff;
            padding: 1rem;
            border-radius: 0.75rem;
            margin: 0.5rem;
            border: 1px solid #e3e6f0;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .form-section::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(135deg, var(--bsu-secondary-green) 0%, var(--bsu-accent-yellow) 100%);
            border-radius: 0.5rem 0 0 0.5rem;
        }
        
        .form-section h5 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            font-size: 1rem;
            padding-bottom: 0.25rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .form-section h5 i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }
        
        .btn-next, .btn-prev, #submitBtn {
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            min-width: 100px;
        }
        
        .btn-next {
            background: linear-gradient(135deg, var(--bsu-secondary-green) 0%, var(--bsu-light-green) 100%);
            border: none;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .btn-next::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 214, 10, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-next:hover::before {
            left: 100%;
        }
        
        #submitBtn {
            background: linear-gradient(135deg, var(--bsu-accent-yellow) 0%, var(--bsu-golden-yellow) 100%);
            border: none;
            color: var(--bsu-primary-green);
            font-weight: bold;
            position: relative;
            overflow: hidden;
        }
        
        #submitBtn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(15, 81, 50, 0.2), transparent);
            transition: left 0.5s;
        }
        
        #submitBtn:hover::before {
            left: 100%;
        }
        
        .btn-prev {
            border: 1px solid #6c757d;
            color: #6c757d;
            background: transparent;
        }
        
        .d-flex.justify-content-between {
            padding: 0.75rem 1rem !important;
            margin-top: 0.5rem;
            border-top: 1px solid #e9ecef;
        }
        
        /* Page decorations */
        .page-decoration {
            position: fixed;
            pointer-events: none;
            z-index: 0;
        }
        
        .decoration-top-left {
            top: 10%;
            left: 5%;
            font-size: 120px;
            color: rgba(255, 214, 10, 0.1);
            animation: pulse 4s ease-in-out infinite;
        }
        
        .decoration-bottom-right {
            bottom: 10%;
            right: 5%;
            font-size: 80px;
            color: rgba(25, 135, 84, 0.1);
            animation: pulse 3s ease-in-out infinite reverse;
        }
        
        .decoration-middle-left {
            top: 50%;
            left: 2%;
            font-size: 60px;
            color: rgba(255, 193, 7, 0.08);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.1; transform: scale(1.1); }
        }
        
        
        
        @media (max-width: 768px) {
            .registration-content {
                padding: 0;
            }
            .form-section {
                padding: 0.75rem;
                margin: 0.25rem;
            }
            .step {
                width: 30px;
                height: 30px;
                margin: 0 6px;
                font-size: 0.8rem;
            }
            .step:not(:last-child)::after {
                right: -12px;
                width: 12px;
            }
            .decoration-top-left,
            .decoration-bottom-right,
            .decoration-middle-left {
                display: none;
            }
            
        }
        
        /* Password Strength Indicator */
        .password-strength {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin: 0.5rem 0;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .password-strength-bar.weak {
            background: var(--error-color);
            width: 25%;
        }
        
        .password-strength-bar.fair {
            background: var(--bsu-golden-yellow);
            width: 50%;
        }
        
        .password-strength-bar.good {
            background: var(--bsu-secondary-green);
            width: 75%;
        }
        
        .password-strength-bar.strong {
            background: var(--bsu-primary-green);
            width: 100%;
        }
        
        /* Enhanced input groups */
        .enhanced-input {
            margin-bottom: 1rem;
        }
        
        .input-group .input-group-text {
            background: #f8f9fa;
            border-color: #ced4da;
        }
        
        /* Improved form transitions */
        .form-step {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease-in-out;
        }
        
        .form-step.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Better button styling - BSU themed */
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--bsu-secondary-green) 0%, var(--bsu-light-green) 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(25, 135, 84, 0.4);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, var(--bsu-secondary-green) 0%, var(--bsu-light-green) 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(25, 135, 84, 0.4);
        }
        
        .btn-next:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(25, 135, 84, 0.4);
        }
        
        #submitBtn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(255, 214, 10, 0.6);
        }
        
        /* Shake animation for validation errors */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* Smooth focus transitions */
        .form-control:focus {
            transform: translateY(-1px);
        }
        
        /* Valid/Invalid input states */
        .form-control.is-valid {
            border-color: var(--bsu-secondary-green);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73 2.63-2.54c.14-.13.37-.13.51 0l.64.62a.36.36 0 0 1 0 .52L3.2 7.9a.36.36 0 0 1-.52 0L.25 5.48a.36.36 0 0 1 0-.52l.64-.62a.36.36 0 0 1 .52 0l.89.91z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .form-control.is-valid:focus {
            border-color: var(--bsu-secondary-green);
            box-shadow: 0 0 0 0.125rem rgba(25, 135, 84, 0.25);
        }
        
        .form-control.is-invalid {
            border-color: var(--error-color);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 2.4 2.4M8.2 4.6l-2.4 2.4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .form-control.is-invalid:focus {
            border-color: var(--error-color);
            box-shadow: 0 0 0 0.125rem rgba(220, 53, 69, 0.25);
        }
        
        /* Real-time validation feedback */
        .invalid-feedback {
            display: none;
            font-size: 0.875rem;
            color: var(--error-color);
            margin-top: 0.25rem;
        }
        
        .invalid-feedback.d-block,
        .invalid-feedback[style*="block"] {
            display: block !important;
        }
    </style>
@stop

@section('auth_header', '')

@section('auth_body')
<!-- Page Decorations -->
<div class="page-decoration decoration-top-left">
    <i class="fas fa-stethoscope"></i>
</div>
<div class="page-decoration decoration-bottom-right">
    <i class="fas fa-heartbeat"></i>
</div>
<div class="page-decoration decoration-middle-left">
    <i class="fas fa-user-md"></i>
</div>

<div class="card-body">
    <div class="registration-header">
        <h3 class="mb-2">🎓 BSU Student Registration</h3>
        <p class="mb-0 opacity-90">Create your account to book health center appointments online</p>
    </div>
    
    <div class="registration-content">
        <!-- Progress Indicator -->
        <div class="step-indicator">
            <div class="step active" data-step="1">1</div>
            <div class="step" data-step="2">2</div>
            <div class="step" data-step="3">3</div>
            <div class="step" data-step="4">4</div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="post" id="registrationForm" novalidate>
            @csrf
            
            <!-- Step 1: Personal Information -->
            <div class="form-step active" id="step1">
                <div class="form-section">
                    <h5><i class="fas fa-user text-primary"></i> Personal Information</h5>
                    
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="Enter your full name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                               placeholder="Enter your email (e.g., name@example.com)" value="{{ old('email') }}" required>
                        <small class="text-muted">Gmail, Yahoo, and other providers accepted</small>
                        <div class="invalid-feedback" id="email-error"></div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                               value="{{ old('date_of_birth') }}" required max="{{ date('Y-m-d', strtotime('-13 years')) }}">
                        @error('date_of_birth')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                               placeholder="Enter phone number (e.g., 09123456789)" value="{{ old('phone_number') }}" required>
                        <small class="text-muted">Philippine mobile number format: 09XXXXXXXXX (11 digits)</small>
                        <div class="invalid-feedback" id="phone-error"></div>
                        @error('phone_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Civil Status <small>(Optional)</small></label>
                        <select name="civil_status" class="form-control @error('civil_status') is-invalid @enderror">
                            <option value="">Select status</option>
                            <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                        @error('civil_status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Home Address</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                  placeholder="Enter your home address" rows="3" required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Academic Information -->
            <div class="form-step" id="step2">
                <div class="form-section">
                    <h5><i class="fas fa-graduation-cap text-primary"></i> Academic Information</h5>
                    
                    <div class="row three-cols">
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label class="form-label">Student ID</label>
                                <input type="text" name="student_id" class="form-control @error('student_id') is-invalid @enderror" 
                                       placeholder="Enter student ID" value="{{ old('student_id') }}" required>
                                @error('student_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-8">
                            <div class="form-group">
                                <label class="form-label">Course/Program</label>
                                <input type="text" name="course" class="form-control @error('course') is-invalid @enderror" 
                                       placeholder="e.g., BS Computer Science" value="{{ old('course') }}" required>
                                @error('course')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="form-group">
                                <label class="form-label">Year Level</label>
                                <select name="year_level" class="form-control @error('year_level') is-invalid @enderror" required>
                                    <option value="">Select Year</option>
                                    <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                                    <option value="5th Year" {{ old('year_level') == '5th Year' ? 'selected' : '' }}>5th Year</option>
                                    <option value="Graduate" {{ old('year_level') == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                                </select>
                                @error('year_level')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Emergency Contact & Health Info -->
            <div class="form-step" id="step3">
                <div class="form-section">
                    <h5><i class="fas fa-exclamation-triangle text-warning"></i> Emergency Contact Information</h5>
                    
                    <div class="row three-cols">
                        <div class="col-md-4 col-sm-6">
                            <div class="enhanced-input">
                                <label class="form-label">Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                       placeholder="Full name" value="{{ old('emergency_contact_name') }}" required>
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="enhanced-input">
                                <label class="form-label">Relationship</label>
                                <input type="text" name="emergency_contact_relationship" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                       placeholder="e.g., Parent, Guardian" value="{{ old('emergency_contact_relationship') }}" required>
                                @error('emergency_contact_relationship')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="enhanced-input">
                                <label class="form-label">Contact Phone</label>
                                <div class="input-group">
                                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                           placeholder="Phone number (e.g., 09123456789)" value="{{ old('emergency_contact_phone') }}" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-phone"></span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Philippine mobile number format: 09XXXXXXXXX</small>
                                <div class="invalid-feedback" id="emergency-phone-error"></div>
                                @error('emergency_contact_phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="enhanced-input">
                        <label class="form-label">Emergency Contact Address</label>
                        <textarea name="emergency_contact_address" class="form-control @error('emergency_contact_address') is-invalid @enderror" 
                                  placeholder="Full address of emergency contact" rows="2" required>{{ old('emergency_contact_address') }}</textarea>
                        @error('emergency_contact_address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-section">
                    <h5><i class="fas fa-heartbeat text-info"></i> Health Information (Optional)</h5>
                    <p class="text-muted small mb-3">This information helps us provide better healthcare services.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="enhanced-input">
                                <label class="form-label text-muted mb-2">Height <small class="text-muted">(Optional)</small></label>
                                <div class="input-group">
                                    <input type="number" name="height" class="form-control @error('height') is-invalid @enderror" 
                                           placeholder="Enter height" value="{{ old('height') }}" min="50" max="250" step="0.1">
                                    <div class="input-group-append">
                                        <div class="input-group-text">cm</div>
                                    </div>
                                </div>
                                @error('height')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="enhanced-input">
                                <label class="form-label text-muted mb-2">Weight <small class="text-muted">(Optional)</small></label>
                                <div class="input-group">
                                    <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror" 
                                           placeholder="Enter weight" value="{{ old('weight') }}" min="20" max="300" step="0.1">
                                    <div class="input-group-append">
                                        <div class="input-group-text">kg</div>
                                    </div>
                                </div>
                                @error('weight')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Security & Terms -->
            <div class="form-step" id="step4">
                <div class="form-section">
                    <h5><i class="fas fa-lock text-primary"></i> Account Security</h5>
                    
                    <div class="enhanced-input">
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="password-strength" id="password-strength">
                            <div class="password-strength-bar" id="strength-bar"></div>
                        </div>
                        <small class="text-muted">Password must be at least 8 characters with letters and numbers</small>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="enhanced-input">
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                   placeholder="Confirm Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="password-match-error"></div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h5><i class="fas fa-file-contract text-primary"></i> Terms & Privacy</h5>
                    
                    <div class="enhanced-input">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="terms_agreement" id="agreeTerms" value="1" {{ old('terms_agreement') ? 'checked' : '' }} required>
                            <label class="custom-control-label" for="agreeTerms">
                                I agree to the <a href="#" data-toggle="modal" data-target="#termsModal" class="text-primary">Terms and Conditions</a>
                            </label>
                        </div>
                        @error('terms_agreement')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="enhanced-input">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="privacy_agreement" id="agreePrivacy" value="1" {{ old('privacy_agreement') ? 'checked' : '' }} required>
                            <label class="custom-control-label" for="agreePrivacy">
                                I agree to the <a href="#" data-toggle="modal" data-target="#privacyModal" class="text-primary">Privacy Policy</a>
                            </label>
                        </div>
                        @error('privacy_agreement')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info mt-3" style="background: rgba(25, 135, 84, 0.1); border: 1px solid rgba(25, 135, 84, 0.3); color: var(--bsu-primary-green);">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Registration Note:</strong> Your account will be reviewed by our admin team. You'll receive an email notification once approved.
                    </div>
                </div>
            </div>
            
            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-prev" id="prevBtn" style="display: none;">
                    <i class="fas fa-chevron-left mr-1"></i> Previous
                </button>
                <button type="button" class="btn btn-primary btn-next" id="nextBtn">
                    Next <i class="fas fa-chevron-right ml-1"></i>
                </button>
                <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                    <i class="fas fa-user-plus mr-1"></i> Create Account
                </button>
            </div>
            
        </form>
        
        <p class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-primary">
                <i class="fas fa-sign-in-alt mr-1"></i> Already have an account? Sign in
            </a>
        </p>
    </div>
</div>
@stop

@section('auth_footer')
    <div class="card-footer text-center">
        <p class="text-muted">
            <small>
                <i class="fas fa-graduation-cap mr-1"></i>
                BSU Health Portal - Student Registration
            </small>
        </p>
    </div>
@stop

@section('adminlte_js')
<script>
let currentStep = 1;
const totalSteps = 4;

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    setupFormValidation();
    setupPasswordStrength();
    setupStepNavigation();
    setupRealTimeValidation();
});

function initializeForm() {
    showStep(1);
    updateStepIndicator(1);
}

function setupStepNavigation() {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    nextBtn.addEventListener('click', function() {
        if (validateCurrentStep()) {
            currentStep++;
            showStep(currentStep);
            updateStepIndicator(currentStep);
            updateNavigationButtons();
        }
    });
    
    prevBtn.addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
        updateStepIndicator(currentStep);
        updateNavigationButtons();
    });
    
    // Enhanced submit handling
    const form = document.getElementById('registrationForm');
    form.addEventListener('submit', function(e) {
        if (!validateAllSteps()) {
            e.preventDefault();
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Creating Account...';
        
        // Add loading animation to form
        const formContainer = document.querySelector('.registration-content');
        formContainer.style.opacity = '0.7';
    });
}

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(s => {
        s.classList.remove('active');
        s.style.display = 'none';
    });
    
    // Show current step with animation
    const currentStepElement = document.getElementById(`step${step}`);
    currentStepElement.style.display = 'block';
    setTimeout(() => {
        currentStepElement.classList.add('active');
    }, 10);
}

function updateStepIndicator(step) {
    document.querySelectorAll('.step').forEach((s, index) => {
        s.classList.remove('active', 'completed');
        if (index + 1 < step) {
            s.classList.add('completed');
            s.innerHTML = '<i class="fas fa-check"></i>';
        } else if (index + 1 === step) {
            s.classList.add('active');
            s.innerHTML = index + 1;
        } else {
            s.innerHTML = index + 1;
        }
    });
}

function updateNavigationButtons() {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    prevBtn.style.display = currentStep === 1 ? 'none' : 'block';
    
    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
    } else {
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    }
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Additional validation for specific steps
    if (currentStep === 1) {
        const email = document.querySelector('input[name="email"]');
        if (email.value && !isValidEmail(email.value)) {
            email.classList.add('is-invalid');
            isValid = false;
        }
    }
    
    if (currentStep === 4) {
        const password = document.querySelector('input[name="password"]');
        const confirmPassword = document.querySelector('input[name="password_confirmation"]');
        
        if (password.value !== confirmPassword.value) {
            confirmPassword.classList.add('is-invalid');
            document.getElementById('password-match-error').textContent = 'Passwords do not match';
            document.getElementById('password-match-error').style.display = 'block';
            isValid = false;
        } else {
            confirmPassword.classList.remove('is-invalid');
            document.getElementById('password-match-error').style.display = 'none';
        }
    }
    
    if (!isValid) {
        // Shake animation for invalid form
        currentStepElement.style.animation = 'shake 0.5s';
        setTimeout(() => {
            currentStepElement.style.animation = '';
        }, 500);
    }
    
    return isValid;
}

function validateAllSteps() {
    let allValid = true;
    for (let i = 1; i <= totalSteps; i++) {
        const stepElement = document.getElementById(`step${i}`);
        const requiredFields = stepElement.querySelectorAll('input[required], select[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                allValid = false;
            }
        });
    }
    return allValid;
}

function setupFormValidation() {
    // Real-time validation
    document.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
}

// Real-time validation for phone and email
function setupRealTimeValidation() {
    // Email validation
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    
    if (emailInput && emailError) {
        emailInput.addEventListener('input', function() {
            const email = this.value.trim();
            if (email.length > 0) {
                if (isValidEmailFormat(email)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    emailError.style.display = 'none';
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    emailError.textContent = 'Please enter a valid email format (name@domain.com)';
                    emailError.style.display = 'block';
                }
            } else {
                this.classList.remove('is-invalid', 'is-valid');
                emailError.style.display = 'none';
            }
        });
    }
    
    // Phone number validation
    const phoneInput = document.getElementById('phone_number');
    const phoneError = document.getElementById('phone-error');
    
    if (phoneInput && phoneError) {
        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            if (phone.length > 0) {
                if (isValidPhoneFormat(phone)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    phoneError.style.display = 'none';
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    phoneError.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX - 11 digits)';
                    phoneError.style.display = 'block';
                }
            } else {
                this.classList.remove('is-invalid', 'is-valid');
                phoneError.style.display = 'none';
            }
        });
    }
    
    // Emergency contact phone validation
    const emergencyPhoneInput = document.getElementById('emergency_contact_phone');
    const emergencyPhoneError = document.getElementById('emergency-phone-error');
    
    if (emergencyPhoneInput && emergencyPhoneError) {
        emergencyPhoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            if (phone.length > 0) {
                if (isValidPhoneFormat(phone)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    emergencyPhoneError.style.display = 'none';
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    emergencyPhoneError.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX - 11 digits)';
                    emergencyPhoneError.style.display = 'block';
                }
            } else {
                this.classList.remove('is-invalid', 'is-valid');
                emergencyPhoneError.style.display = 'none';
            }
        });
    }
}

function setupPasswordStrength() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strength-bar');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        updatePasswordStrengthBar(strength);
    });
    
    // Password confirmation matching
    const confirmPassword = document.getElementById('password_confirmation');
    confirmPassword.addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirm = this.value;
        const errorElement = document.getElementById('password-match-error');
        
        if (confirm && password !== confirm) {
            this.classList.add('is-invalid');
            errorElement.textContent = 'Passwords do not match';
            errorElement.style.display = 'block';
        } else {
            this.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        }
    });
}

function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    return strength;
}

function updatePasswordStrengthBar(strength) {
    const strengthBar = document.getElementById('strength-bar');
    strengthBar.style.width = strength + '%';
    
    if (strength < 50) {
        strengthBar.style.backgroundColor = '#dc3545';
    } else if (strength < 75) {
        strengthBar.style.backgroundColor = '#ffc107';
    } else {
        strengthBar.style.backgroundColor = '#28a745';
    }
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Enhanced email validation matching backend rules
function isValidEmailFormat(email) {
    // Match the backend EmailValidationRule format
    const basicEmailRegex = /^[a-z0-9]+([._-][a-z0-9]+)*@[a-z0-9]+([.-][a-z0-9]+)*\.[a-z]{2,}$/i;
    
    if (!basicEmailRegex.test(email)) {
        return false;
    }
    
    const [local, domain] = email.split('@');
    
    // Check length limits
    if (local.length === 0 || local.length > 64 || domain.length < 4 || domain.length > 255) {
        return false;
    }
    
    // Check for invalid patterns
    if (local.startsWith('.') || local.endsWith('.') || local.includes('..')) {
        return false;
    }
    
    return true;
}

// Philippine phone number validation matching backend rules
function isValidPhoneFormat(phone) {
    // Remove all non-digit characters for validation
    const cleanNumber = phone.replace(/[^0-9]/g, '');
    
    // Check if it follows Philippine mobile format: 09XXXXXXXXX (exactly 11 digits)
    if (!/^09[0-9]{9}$/.test(cleanNumber)) {
        return false;
    }
    
    // Check for valid network prefixes (simplified version)
    const networkCode = cleanNumber.substring(2, 4);
    const validNetworkCodes = [
        '17', '05', '06', '15', '16', '26', '27', '35', '36', '37', '94', '95', '96', '97', // Globe/TM
        '07', '08', '09', '10', '11', '12', '13', '14', '18', '19', '20', '21', '22', '23', '28', '29', '30', '31', '32', '33', '34', '38', '39', '40', '41', '42', '43', '44', '89', '98', '99', // Smart/TNT/Sun
        '91', '92', '93' // DITO
    ];
    
    return validNetworkCodes.includes(networkCode);
}

// Add shake animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
`;
document.head.appendChild(style);
</script>
@stop

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Terms and Conditions</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>BSU Health Portal Terms of Use</h5>
                <p>By registering for the BSU Health Portal, you agree to:</p>
                <ul>
                    <li>Provide accurate and truthful information about yourself</li>
                    <li>Use this system only for legitimate health-related appointments</li>
                    <li>Respect appointment schedules and notify in advance of cancellations</li>
                    <li>Maintain confidentiality of your account credentials</li>
                    <li>Follow BSU health center policies and procedures</li>
                </ul>
                <p>Misuse of this system may result in account suspension and disciplinary action.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Privacy Policy</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Information Collection and Use</h5>
                <p>The BSU Health Center collects and uses your information to:</p>
                <ul>
                    <li>Provide medical and health services</li>
                    <li>Maintain accurate health records</li>
                    <li>Schedule and manage appointments</li>
                    <li>Contact you regarding health matters</li>
                    <li>Emergency contact purposes when necessary</li>
                </ul>
                <h5>Information Protection</h5>
                <p>Your health information is protected under medical confidentiality laws and BSU policies. Access is restricted to authorized healthcare personnel only.</p>
                <p>We do not share your personal health information with third parties without your explicit consent, except as required by law.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
