@extends('adminlte::page')

@section('title', 'Edit My Profile | Bokod CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">Edit My Profile</h1>
            <small class="text-muted">Update your contact information and emergency details</small>
        </div>
        <div>
            <a href="{{ route('patient.profile.show') }}" class="btn btn-info mr-2">
                <i class="fas fa-eye mr-1"></i> View Profile
            </a>
            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Include modal-based alerts instead of inline alerts --}}
    @include('components.modal-alerts')

    <form method="POST" action="{{ route('patient.profile.update') }}" id="patientProfileForm" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        
        {{-- Profile Picture Upload --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-camera mr-2"></i>
                    Profile Picture
                </h3>
                <div class="card-tools">
                    <span class="badge badge-success">You Can Edit</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="profile-picture-preview mb-3">
                                <x-user-avatar :user="$patient->user" width="150px" height="150px" class="img-thumbnail" />
                            </div>
                            <input type="file" class="form-control-file @error('profile_picture') is-invalid @enderror" 
                                   id="profile_picture" name="profile_picture" accept="image/*">
                            <small class="form-text text-muted">Max size: 5MB. Supported formats: JPEG, PNG, JPG, GIF, WebP. Images uploaded to ImgBB CDN.</small>
                            @error('profile_picture')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Profile Picture Guidelines:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Upload a clear photo of yourself</li>
                                <li>Square images work best (1:1 ratio)</li>
                                <li>Images will be uploaded securely to ImgBB</li>
                                <li>Your previous image will be replaced</li>
                                <li>Leave blank to keep current picture</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Personal Information (Read-Only) --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>
                    Personal Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">Read-Only - Contact Admin to Change</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="patient_name">Full Name</label>
                            <input type="text" id="patient_name" 
                                   class="form-control" 
                                   value="{{ $patient->patient_name }}" 
                                   readonly>
                            <small class="form-text text-muted">Contact administrator to change your name</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <input type="text" id="gender" 
                                   class="form-control" 
                                   value="{{ $patient->gender }}" 
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="text" id="date_of_birth" 
                                   class="form-control" 
                                   value="{{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'Not set' }}" 
                                   readonly>
                            @if($patient->date_of_birth)
                                <small class="form-text text-muted">Age: {{ $patient->age }} years</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="civil_status">Civil Status</label>
                            <input type="text" id="civil_status" 
                                   class="form-control" 
                                   value="{{ $patient->civil_status }}" 
                                   readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        {{-- Contact Information (Editable) --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-address-book mr-2"></i>
                    Contact Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-success">You Can Edit</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $patient->email) }}" required
                                   placeholder="your.email@example.com"
                                   maxlength="254"
                                   autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                This is your login email address
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" name="phone_number" id="phone_number" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   value="{{ old('phone_number', $patient->phone_number) }}"
                                   placeholder="+63 9XX XXX XXXX"
                                   pattern="[0-9+\s\(\)\-]+"
                                   title="Enter a valid phone number with at least 11 digits"
                                   maxlength="20"
                                   autocomplete="tel">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted phone-feedback">
                                Enter phone number (e.g., +639XX XXX XXXX or 09XX XXX XXXX)
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="Your complete address">{{ old('address', $patient->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="course">Course/Department</label>
                            <select name="course" id="course" class="form-control @error('course') is-invalid @enderror">
                                <option value="">Select your course/program</option>
                                @include('components.course-options', ['selected' => old('course', $patient->course)])
                            </select>
                            @error('course')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Update if you changed course or department
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Emergency Contact Information (Editable) --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-phone mr-2"></i>
                    Emergency Contact Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-success">You Can Edit</span>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Important:</strong> This information helps us contact someone in case of emergencies.
                    Please keep it up to date.
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="emergency_contact_name">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" 
                                   class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                   value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                                   placeholder="Full name">
                            @error('emergency_contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="emergency_contact_relationship">Relationship</label>
                            <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" 
                                   class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                   value="{{ old('emergency_contact_relationship', $patient->emergency_contact_relationship) }}"
                                   placeholder="e.g., Parent, Spouse, Sibling">
                            @error('emergency_contact_relationship')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="emergency_contact_phone">Emergency Contact Phone</label>
                            <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" 
                                   class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                   value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                                   placeholder="+63 9XX XXX XXXX"
                                   pattern="[0-9+\s\(\)\-]+"
                                   title="Enter a valid phone number with at least 11 digits"
                                   maxlength="20"
                                   autocomplete="tel">
                            @error('emergency_contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted phone-feedback">
                                Enter phone number (e.g., +639XX XXX XXXX or 09XX XXX XXXX)
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="emergency_contact_address">Emergency Contact Address</label>
                            <textarea name="emergency_contact_address" id="emergency_contact_address" rows="3"
                                      class="form-control @error('emergency_contact_address') is-invalid @enderror" 
                                      placeholder="Complete address of emergency contact">{{ old('emergency_contact_address', $patient->emergency_contact_address) }}</textarea>
                            @error('emergency_contact_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Medical Information (Read-Only) --}}
        @if($patient->height || $patient->weight || $patient->bmi || $patient->blood_pressure)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heartbeat mr-2"></i>
                    Medical Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-warning">Medical Staff Only</span>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-stethoscope mr-2"></i>
                    <strong>Note:</strong> Medical information can only be updated by medical staff during consultations.
                </div>
                
                <div class="row">
                    @if($patient->height)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Height</label>
                            <input type="text" class="form-control" 
                                   value="{{ $patient->height }} cm" readonly>
                        </div>
                    </div>
                    @endif
                    
                    @if($patient->weight)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Weight</label>
                            <input type="text" class="form-control" 
                                   value="{{ $patient->weight }} kg" readonly>
                        </div>
                    </div>
                    @endif
                    
                    @if($patient->bmi)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>BMI</label>
                            <input type="text" class="form-control" 
                                   value="{{ $patient->bmi }}" readonly>
                            @if($patient->bmi_status)
                                <small class="form-text text-muted">{{ $patient->bmi_status }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($patient->blood_pressure)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Blood Pressure</label>
                            <input type="text" class="form-control" 
                                   value="{{ $patient->blood_pressure }}" readonly>
                            @if($patient->bp_status)
                                <small class="form-text text-muted">{{ $patient->bp_status }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Form Actions --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Privacy Note:</strong> Your information is kept confidential and secure. 
                            Only authorized medical staff can access your complete medical records.
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i>
                            Update My Profile
                        </button>
                        <a href="{{ route('patient.profile.show') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times mr-2"></i>
                            Cancel Changes
                        </a>
                        
                        <button type="button" class="btn btn-info btn-lg ml-2" onclick="resetForm()">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    {{-- Password Change Form (Separate Form) --}}
    <div class="card" id="password-section">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-key mr-2"></i>
                Change Password
            </h3>
            <div class="card-tools">
                <span class="badge badge-success">You Can Change</span>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-shield-alt mr-2"></i>
                <strong>Security Tip:</strong> Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and symbols.
            </div>
            
            <form method="POST" action="{{ route('password.update') }}" id="passwordChangeForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="current_password">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" id="current_password" 
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                   required autocomplete="current-password"
                                   placeholder="Enter your current password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                   required autocomplete="new-password" minlength="8"
                                   placeholder="Enter new password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                                   required autocomplete="new-password" minlength="8"
                                   placeholder="Confirm new password">
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key mr-2"></i>
                            Change Password
                        </button>
                        <button type="button" class="btn btn-secondary ml-2" onclick="clearPasswordForm()">
                            <i class="fas fa-times mr-2"></i>
                            Clear Fields
                        </button>
                        
                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success mt-2">
                                <i class="fas fa-check-circle mr-2"></i>
                                Your password has been updated successfully!
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('adminlte_css')
    <style>
        /* Preloader fix */
        .preloader {
            z-index: 9999;
        }
        
        /* Hide preloader after 2 seconds via CSS as backup */
        body:not(.sidebar-mini-md):not(.sidebar-mini-xs) .preloader {
            animation: hidePreloader 2s forwards;
        }
        
        @keyframes hidePreloader {
            0% { opacity: 1; visibility: visible; }
            90% { opacity: 1; visibility: visible; }
            100% { opacity: 0; visibility: hidden; }
        }
    </style>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: 600;
            color: #495057;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .card-header .card-title {
            font-weight: 600;
            color: #495057;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .btn-lg {
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 6px;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }
        
        .badge {
            font-size: 0.75rem;
        }
        
        input[readonly], textarea[readonly] {
            background-color: #f8f9fa;
            opacity: 0.8;
        }
    </style>
@stop

@section('js')
    {{-- Ensure jQuery is loaded --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/enhanced-validation.js') }}"></script>
    <script src="{{ asset('js/profile-picture-preview.js') }}"></script>
    <script>
        // Immediate preloader hide to prevent stuck state
        $(window).on('load', function() {
            $('.preloader').fadeOut(300);
            $('.overlay').fadeOut(300);
            console.log('Preloader hidden on window load - Patient Profile Edit');
        });
        
        // Backup preloader hide
        setTimeout(function() {
            $('.preloader').fadeOut(500);
            $('.overlay').fadeOut(500);
            console.log('Backup preloader hide executed');
        }, 500);
    </script>
    <script>
        $(document).ready(function() {
            // DEBUG: Track page load and initial profile picture state
            const initialProfilePicture = $('.user-avatar img').attr('src');
            console.log('PATIENT PROFILE PAGE: Loaded', {
                initialProfilePictureUrl: initialProfilePicture,
                hasProfilePicture: initialProfilePicture ? true : false,
                userAvatarElements: $('.user-avatar').length,
                timestamp: new Date().toISOString(),
                pageUrl: window.location.href
            });
            
            // PRELOADER FIX: Force hide preloader after page load to prevent stuck preloader
            setTimeout(function() {
                $('.preloader').fadeOut(500);
                $('.overlay').fadeOut(500);
                console.log('Preloader forcefully hidden after timeout - Patient Profile Edit');
            }, 1000); // 1 second timeout
            
            // Setup error handling for failed AJAX requests to prevent stuck preloaders
            $(document).ajaxError(function(event, xhr, settings, error) {
                console.error('AJAX Error:', error, 'URL:', settings.url);
                
                // Hide any lingering preloaders on AJAX errors
                $('.preloader, .overlay').fadeOut(300);
                
                // Show user-friendly error message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Connection Error',
                        text: 'There was a problem loading the page. Please refresh if needed.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            });
            
            // Form validation
            $('#patientProfileForm').on('submit', function(e) {
                const email = $('#email').val();
                const hasProfilePicture = $('#profile_picture')[0].files.length > 0;
                const currentProfilePictureUrl = $('.user-avatar img').attr('src');
                
                console.log('PATIENT PROFILE FORM: Submission started', {
                    hasProfilePicture: hasProfilePicture,
                    currentProfilePictureUrl: currentProfilePictureUrl,
                    email: email,
                    formAction: $(this).attr('action'),
                    formMethod: $(this).attr('method'),
                    timestamp: new Date().toISOString()
                });
                
                if (!email || !email.includes('@')) {
                    e.preventDefault();
                    alert('Please provide a valid email address.');
                    $('#email').focus();
                    return false;
                }
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Updating Profile...').prop('disabled', true);
                
                console.log('PATIENT PROFILE FORM: Loading state activated', {
                    buttonText: originalText,
                    buttonDisabled: true
                });
                
                // Set session storage flag to indicate profile picture update is in progress
                if (hasProfilePicture) {
                    sessionStorage.setItem('profilePictureUpdated', 'true');
                    console.log('PATIENT PROFILE FORM: Session storage flag set for profile picture update');
                }
                
                // Re-enable button after 10 seconds as failsafe
                setTimeout(function() {
                    submitBtn.html(originalText).prop('disabled', false);
                    console.log('PATIENT PROFILE FORM: Button failsafe activated');
                }, 10000);
            });
            
            // Password change form validation
            $('#passwordChangeForm').on('submit', function(e) {
                const currentPassword = $('#current_password').val();
                const newPassword = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();
                
                if (!currentPassword || !newPassword || !confirmPassword) {
                    e.preventDefault();
                    alert('Please fill in all password fields.');
                    return false;
                }
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New password and confirmation password do not match.');
                    $('#password_confirmation').focus();
                    return false;
                }
                
                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('New password must be at least 8 characters long.');
                    $('#password').focus();
                    return false;
                }
                
                if (currentPassword === newPassword) {
                    e.preventDefault();
                    alert('New password must be different from your current password.');
                    $('#password').focus();
                    return false;
                }
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Changing Password...').prop('disabled', true);
                
                // Re-enable button after 10 seconds as failsafe
                setTimeout(function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }, 10000);
            });
            
            // Real-time password confirmation validation
            $('#password_confirmation').on('keyup', function() {
                const newPassword = $('#password').val();
                const confirmPassword = $(this).val();
                
                if (confirmPassword && newPassword !== confirmPassword) {
                    $(this).addClass('is-invalid').removeClass('is-valid');
                    if (!$(this).siblings('.invalid-feedback.password-mismatch').length) {
                        $(this).after('<div class="invalid-feedback password-mismatch">Passwords do not match</div>');
                    }
                } else if (confirmPassword && newPassword === confirmPassword) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $(this).siblings('.invalid-feedback.password-mismatch').remove();
                } else {
                    $(this).removeClass('is-invalid is-valid');
                    $(this).siblings('.invalid-feedback.password-mismatch').remove();
                }
            });
            
            // File input label update
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass('selected').html(fileName || 'Choose image file...');
            });
            
            // DEBUG: Watch for profile picture changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                        const target = mutation.target;
                        if (target.classList.contains('user-avatar') || target.closest('.user-avatar')) {
                            console.log('PATIENT PROFILE: Profile picture URL changed', {
                                oldSrc: mutation.oldValue,
                                newSrc: target.src,
                                element: target,
                                timestamp: new Date().toISOString()
                            });
                        }
                    }
                });
            });
            
            // Start observing profile picture changes
            $('.user-avatar img').each(function() {
                observer.observe(this, {
                    attributes: true,
                    attributeOldValue: true,
                    attributeFilter: ['src']
                });
            });
            
            // DEBUG: Check for success/error messages on page load
            const successAlerts = $('.alert-success');
            const errorAlerts = $('.alert-danger, .alert-error');
            if (successAlerts.length > 0) {
                console.log('PATIENT PROFILE: Success alerts found', {
                    count: successAlerts.length,
                    messages: successAlerts.map(function() { return $(this).text().trim(); }).get()
                });
            }
            if (errorAlerts.length > 0) {
                console.log('PATIENT PROFILE: Error alerts found', {
                    count: errorAlerts.length,
                    messages: errorAlerts.map(function() { return $(this).text().trim(); }).get()
                });
            }
            
            console.log('Patient profile edit form loaded successfully!');
        });
        
        // Reset form to original values
        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                location.reload();
            }
        }
        
        // Clear password form fields
        function clearPasswordForm() {
            if (confirm('Are you sure you want to clear all password fields?')) {
                $('#passwordChangeForm')[0].reset();
                $('#passwordChangeForm input').removeClass('is-valid is-invalid');
                $('#passwordChangeForm .invalid-feedback.password-mismatch').remove();
            }
        }
        
    </script>
@stop