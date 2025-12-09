@extends('adminlte::page')

@section('title', 'Add New Patient - BOKOD CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">Add New Patient</h1>
            <small class="text-muted">Register a new patient in the system</small>
        </div>
        <a href="{{ route('patients.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Patients
        </a>
    </div>
@stop

@section('content')
    {{-- Include modal-based alerts instead of inline alerts --}}
    @include('components.modal-alerts')

    <form method="POST" action="{{ route('patients.store') }}" id="patientForm">
        @csrf
        
        {{-- Personal Information --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>
                    Personal Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="first_name" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" 
                                   class="form-control @error('middle_name') is-invalid @enderror" 
                                   value="{{ old('middle_name') }}">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="last_name" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="gender">Gender <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" 
                                    class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                   class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   value="{{ old('date_of_birth') }}" 
                                   min="{{ now()->subYears(80)->format('Y-m-d') }}" 
                                   max="{{ now()->subYears(16)->format('Y-m-d') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>For college students, faculty, staff, and family members (ages 16-80)
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="civil_status">Civil Status <span class="text-danger">*</span></label>
                            <select name="civil_status" id="civil_status" 
                                    class="form-control @error('civil_status') is-invalid @enderror" required>
                                <option value="">Select Status</option>
                                <option value="Single" {{ old('civil_status') === 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') === 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Divorced" {{ old('civil_status') === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Widowed" {{ old('civil_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                            @error('civil_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="position">ID/Position Number</label>
                            <input type="text" name="position" id="position" 
                                   class="form-control @error('position') is-invalid @enderror" 
                                   value="{{ old('position') }}" 
                                   placeholder="e.g., Student ID, Employee ID">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="course">Course/Department</label>
                            <select name="course" id="course" class="form-control @error('course') is-invalid @enderror">
                                <option value="">Select course/program</option>
                                @include('components.course-options', ['selected' => old('course')])
                            </select>
                            @error('course')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-address-book mr-2"></i>
                    Contact Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required
                                   placeholder="patient@example.com"
                                   maxlength="254"
                                   autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                This will be used for login credentials and notifications
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" name="phone_number" id="phone_number" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   value="{{ old('phone_number') }}"
                                   placeholder="+63 9XX XXX XXXX"
                                   pattern="[0-9+\s()-]+"
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="Complete address">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Emergency Contact Information --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-phone mr-2"></i>
                    Emergency Contact Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-secondary">Optional</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="emergency_contact_name">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" 
                                   class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                   value="{{ old('emergency_contact_name') }}"
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
                                   value="{{ old('emergency_contact_relationship') }}"
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
                                   value="{{ old('emergency_contact_phone') }}"
                                   placeholder="+63 9XX XXX XXXX"
                                   pattern="[0-9+\s()-]+"
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
                                      placeholder="Complete address of emergency contact">{{ old('emergency_contact_address') }}</textarea>
                            @error('emergency_contact_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Medical Information --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heartbeat mr-2"></i>
                    Basic Medical Information
                </h3>
                <div class="card-tools">
                    <span class="badge badge-secondary">Optional</span>
                </div>
            </div>
            <div class="card-body">
                <!-- Height and Weight for BMI Calculation -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="height">Height (cm)</label>
                            <input type="number" name="height" id="height" step="0.1" min="50" max="250"
                                   class="form-control @error('height') is-invalid @enderror" 
                                   value="{{ old('height') }}"
                                   placeholder="e.g., 170.5">
                            @error('height')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Height in centimeters</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="number" name="weight" id="weight" step="0.1" min="10" max="300"
                                   class="form-control @error('weight') is-invalid @enderror" 
                                   value="{{ old('weight') }}"
                                   placeholder="e.g., 65.5">
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Weight in kilograms</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="bmi">BMI (Body Mass Index)</label>
                            <div class="input-group">
                                <input type="number" name="bmi" id="bmi" step="0.01" min="10" max="50"
                                       class="form-control @error('bmi') is-invalid @enderror" 
                                       value="{{ old('bmi') }}"
                                       placeholder="e.g., 22.5" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="calculateBMI">
                                        <i class="fas fa-calculator"></i>
                                    </button>
                                </div>
                            </div>
                            @error('bmi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="bmiStatus" class="mt-1"></div>
                            <small class="form-text text-muted">Auto-calculated from height & weight</small>
                        </div>
                    </div>
                </div>
                
                <!-- Blood Pressure -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="systolic_bp">Systolic BP</label>
                            <input type="number" name="systolic_bp" id="systolic_bp" min="60" max="250"
                                   class="form-control @error('systolic_bp') is-invalid @enderror" 
                                   value="{{ old('systolic_bp') }}"
                                   placeholder="e.g., 120">
                            @error('systolic_bp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Upper number</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="diastolic_bp">Diastolic BP</label>
                            <input type="number" name="diastolic_bp" id="diastolic_bp" min="40" max="150"
                                   class="form-control @error('diastolic_bp') is-invalid @enderror" 
                                   value="{{ old('diastolic_bp') }}"
                                   placeholder="e.g., 80">
                            @error('diastolic_bp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Lower number</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="blood_pressure">Blood Pressure</label>
                            <div class="input-group">
                                <input type="text" name="blood_pressure" id="blood_pressure" 
                                       class="form-control @error('blood_pressure') is-invalid @enderror" 
                                       value="{{ old('blood_pressure') }}"
                                       placeholder="e.g., 120/80" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="formatBP">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                            @error('blood_pressure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="bpStatus" class="mt-1"></div>
                            <small class="form-text text-muted">Auto-formatted from systolic/diastolic</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> A user account will be automatically created for this patient 
                            with the email address provided. A secure random password will be generated and 
                            <strong>sent to their email address</strong> along with login instructions. 
                            The patient can change this password after their first login.
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create Patient Account
                        </button>
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        
                        <button type="button" class="btn btn-info btn-lg ml-2" onclick="clearForm()">
                            <i class="fas fa-eraser mr-2"></i>
                            Clear Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
        
        .bmi-status, .bp-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 4px;
        }
        
        .status-normal {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/enhanced-validation.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Auto-calculate age when date of birth is entered
            $('#date_of_birth').on('change', function() {
                const dob = new Date($(this).val());
                const today = new Date();
                const age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
                
                if (age >= 0 && age <= 150) {
                    console.log('Patient age: ' + age + ' years');
                    
                    // Show age feedback
                    let ageText = $(this).siblings('.form-text.age-display');
                    if (ageText.length === 0) {
                        ageText = $('<small class="form-text age-display"></small>');
                        $(this).after(ageText);
                    }
                    
                    if (age < 16) {
                        ageText.html('<i class="fas fa-exclamation-triangle text-warning mr-1"></i>Age: ' + age + ' years - Minimum age for college clinic is 16 years')
                               .removeClass('text-success text-muted')
                               .addClass('text-warning');
                        $(this).addClass('is-invalid');
                    } else if (age > 80) {
                        ageText.html('<i class="fas fa-exclamation-triangle text-warning mr-1"></i>Age: ' + age + ' years - Maximum age for college clinic is 80 years')
                               .removeClass('text-success text-muted')
                               .addClass('text-warning');
                        $(this).addClass('is-invalid');
                    } else {
                        ageText.html('<i class="fas fa-check-circle text-success mr-1"></i>Age: ' + age + ' years - Appropriate for college clinic')
                               .removeClass('text-warning text-muted')
                               .addClass('text-success');
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                } else {
                    $(this).siblings('.form-text.age-display').remove();
                }
            });
            
            // BMI Calculation Functions
            function calculateBMI() {
                const height = parseFloat($('#height').val());
                const weight = parseFloat($('#weight').val());
                
                if (height > 0 && weight > 0) {
                    const heightInMeters = height / 100;
                    const bmi = (weight / (heightInMeters * heightInMeters)).toFixed(1);
                    $('#bmi').val(bmi);
                    updateBMIStatus(bmi);
                } else {
                    $('#bmi').val('');
                    $('#bmiStatus').html('');
                }
            }
            
            function updateBMIStatus(bmi) {
                let status = '';
                let className = '';
                
                if (bmi < 18.5) {
                    status = 'Underweight';
                    className = 'status-info';
                } else if (bmi >= 18.5 && bmi < 25) {
                    status = 'Normal weight';
                    className = 'status-normal';
                } else if (bmi >= 25 && bmi < 30) {
                    status = 'Overweight';
                    className = 'status-warning';
                } else if (bmi >= 30) {
                    status = 'Obese';
                    className = 'status-danger';
                }
                
                $('#bmiStatus').html(`<span class="bmi-status ${className}">${status}</span>`);
            }
            
            // Blood Pressure Functions
            function formatBloodPressure() {
                const systolic = $('#systolic_bp').val();
                const diastolic = $('#diastolic_bp').val();
                
                if (systolic && diastolic) {
                    const bp = `${systolic}/${diastolic}`;
                    $('#blood_pressure').val(bp);
                    updateBPStatus(parseInt(systolic), parseInt(diastolic));
                } else {
                    $('#blood_pressure').val('');
                    $('#bpStatus').html('');
                }
            }
            
            function updateBPStatus(systolic, diastolic) {
                let status = '';
                let className = '';
                
                if (systolic < 120 && diastolic < 80) {
                    status = 'Normal';
                    className = 'status-normal';
                } else if ((systolic >= 120 && systolic <= 129) && diastolic < 80) {
                    status = 'Elevated';
                    className = 'status-warning';
                } else if ((systolic >= 130 && systolic <= 139) || (diastolic >= 80 && diastolic <= 89)) {
                    status = 'High Blood Pressure Stage 1';
                    className = 'status-danger';
                } else if (systolic >= 140 || diastolic >= 90) {
                    status = 'High Blood Pressure Stage 2';
                    className = 'status-danger';
                } else if (systolic > 180 || diastolic > 120) {
                    status = 'Hypertensive Crisis';
                    className = 'status-danger';
                }
                
                $('#bpStatus').html(`<span class="bp-status ${className}">${status}</span>`);
            }
            
            // Event Listeners
            $('#height, #weight').on('input', calculateBMI);
            $('#calculateBMI').on('click', calculateBMI);
            
            $('#systolic_bp, #diastolic_bp').on('input', formatBloodPressure);
            $('#formatBP').on('click', formatBloodPressure);
            
            // Form validation
            $('#patientForm').on('submit', function(e) {
                const email = $('#email').val();
                const name = $('#patient_name').val();
                
                if (!email || !name) {
                    e.preventDefault();
                    alert('Please fill in all required fields (marked with *)');
                    return false;
                }
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Creating Patient...').prop('disabled', true);
                
                // Re-enable button after 10 seconds as failsafe
                setTimeout(function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }, 10000);
            });
            
            console.log('Patient create form loaded successfully!');
        });
        
        // Clear form function
        function clearForm() {
            if (confirm('Are you sure you want to clear all form data?')) {
                document.getElementById('patientForm').reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
            }
        }
    </script>
@stop