@extends('adminlte::page')

@section('title', 'My Profile | Bokod CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">My Profile</h1>
            <small class="text-muted">View your personal and contact information</small>
        </div>
        <div>
            <a href="{{ route('patient.profile.edit') }}" class="btn btn-primary mr-2">
                <i class="fas fa-edit mr-1"></i> Edit Profile
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

    <div class="row">
        <!-- Patient Profile Information ---->
        <div class="col-md-8">
            <!-- Personal Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>Personal Information
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-user"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Full Name</span>
                                    <span class="info-box-number">{{ $patient->patient_name }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-{{ $patient->gender === 'Male' ? 'mars' : ($patient->gender === 'Female' ? 'venus' : 'genderless') }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Gender</span>
                                    <span class="info-box-number">{{ $patient->gender ?: 'Not specified' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-birthday-cake"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Date of Birth</span>
                                    <span class="info-box-number">
                                        {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'Not provided' }}
                                    </span>
                                    @if($patient->date_of_birth)
                                        <span class="progress-description">Age: {{ $patient->age }} years old</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-heart"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Civil Status</span>
                                    <span class="info-box-number">{{ $patient->civil_status ?: 'Not specified' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Position/ID Number:</strong></label>
                                <p class="form-control-static">{{ $patient->position ?: 'Not provided' }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Course/Department:</strong></label>
                                <p class="form-control-static">{{ $patient->course ?: 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-address-book mr-2"></i>Contact Information
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><i class="fas fa-envelope mr-2 text-muted"></i>Email Address:</strong></label>
                                <p class="form-control-static">
                                    @if($patient->email)
                                        <a href="mailto:{{ $patient->email }}">{{ $patient->email }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><i class="fas fa-phone mr-2 text-muted"></i>Phone Number:</strong></label>
                                <p class="form-control-static">
                                    @if($patient->phone_number)
                                        <a href="tel:{{ $patient->phone_number }}">{{ $patient->phone_number }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong><i class="fas fa-map-marker-alt mr-2 text-muted"></i>Address:</strong></label>
                                <p class="form-control-static">{{ $patient->address ?: 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Information Card -->
            @if($patient->emergency_contact_name || $patient->emergency_contact_phone || $patient->emergency_contact_relationship || $patient->emergency_contact_address)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone mr-2"></i>Emergency Contact Information
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        @if($patient->emergency_contact_name)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><i class="fas fa-user mr-2 text-muted"></i>Name:</strong></label>
                                <p class="form-control-static">{{ $patient->emergency_contact_name }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($patient->emergency_contact_relationship)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><i class="fas fa-heart mr-2 text-muted"></i>Relationship:</strong></label>
                                <p class="form-control-static">{{ $patient->emergency_contact_relationship }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="row">
                        @if($patient->emergency_contact_phone)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><i class="fas fa-phone mr-2 text-muted"></i>Phone Number:</strong></label>
                                <p class="form-control-static">
                                    <a href="tel:{{ $patient->emergency_contact_phone }}">{{ $patient->emergency_contact_phone }}</a>
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($patient->emergency_contact_address)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><i class="fas fa-map-marker-alt mr-2 text-muted"></i>Address:</strong></label>
                                <p class="form-control-static">{{ $patient->emergency_contact_address }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Medical Information Card -->
            @if($patient->height || $patient->weight || $patient->bmi || $patient->blood_pressure)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heartbeat mr-2"></i>Medical Information
                    </h3>
                </div>
                
                <div class="card-body">
                    <!-- Height and Weight -->
                    <div class="row">
                        @if($patient->height)
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-ruler-vertical"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Height</span>
                                    <span class="info-box-number">{{ $patient->height }} cm</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($patient->weight)
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-weight-hanging"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Weight</span>
                                    <span class="info-box-number">{{ $patient->weight }} kg</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- BMI and Blood Pressure -->
                    <div class="row">
                        @if($patient->bmi)
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon 
                                    @if($patient->bmi_status == 'Normal weight')
                                        bg-success
                                    @elseif($patient->bmi_status == 'Underweight')
                                        bg-info
                                    @elseif($patient->bmi_status == 'Overweight')
                                        bg-warning
                                    @elseif($patient->bmi_status == 'Obese')
                                        bg-danger
                                    @else
                                        bg-secondary
                                    @endif">
                                    <i class="fas fa-calculator"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">BMI (Body Mass Index)</span>
                                    <span class="info-box-number">{{ $patient->bmi }}</span>
                                    @if($patient->bmi_status)
                                        <span class="progress-description">{{ $patient->bmi_status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($patient->blood_pressure || ($patient->systolic_bp && $patient->diastolic_bp))
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon 
                                    @if($patient->bp_status == 'Normal')
                                        bg-success
                                    @elseif($patient->bp_status == 'Elevated')
                                        bg-warning
                                    @elseif(str_contains($patient->bp_status, 'High Blood Pressure') || $patient->bp_status == 'Hypertensive Crisis')
                                        bg-danger
                                    @else
                                        bg-secondary
                                    @endif">
                                    <i class="fas fa-heartbeat"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Blood Pressure</span>
                                    <span class="info-box-number">
                                        @if($patient->blood_pressure)
                                            {{ $patient->blood_pressure }}
                                        @elseif($patient->systolic_bp && $patient->diastolic_bp)
                                            {{ $patient->systolic_bp }}/{{ $patient->diastolic_bp }}
                                        @endif
                                    </span>
                                    @if($patient->bp_status)
                                        <span class="progress-description">{{ $patient->bp_status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar with Quick Actions and Account Info -->
        <div class="col-md-4">
            <!-- Patient Avatar/Summary Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if($patient->user->profile_picture)
                            <img class="profile-user-img img-fluid img-circle mb-3"
                                 src="{{ asset('storage/' . $patient->user->profile_picture) }}"
                                 alt="{{ $patient->patient_name }}'s profile picture"
                                 style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #007bff;">
                        @else
                            <div class="user-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 32px;">
                                {{ strtoupper(substr($patient->patient_name, 0, 1)) }}
                            </div>
                        @endif
                        
                        <h3 class="profile-username text-center">{{ $patient->patient_name }}</h3>
                        
                        <p class="text-muted text-center">
                            Patient ID: {{ $patient->id }}
                            @if($patient->position)
                                <br>{{ $patient->position }}
                            @endif
                        </p>
                    </div>

                    <a href="{{ route('patient.profile.edit') }}" class="btn btn-primary btn-block mb-2">
                        <b><i class="fas fa-user-edit mr-2"></i>Edit My Profile</b>
                    </a>
                    <a href="{{ route('patient.profile.edit') }}#password-section" class="btn btn-warning btn-block">
                        <b><i class="fas fa-key mr-2"></i>Change Password</b>
                    </a>
                </div>
            </div>

            <!-- Account Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-cog mr-2"></i>Account Information
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Account Status:</strong></label>
                        <p class="form-control-static">
                            @if($patient->archived)
                                <span class="badge badge-danger">Archived</span>
                            @else
                                <span class="badge badge-success">Active</span>
                            @endif
                        </p>
                    </div>
                    
                    @if($patient->user)
                    <div class="form-group">
                        <label><strong>Login Status:</strong></label>
                        <p class="form-control-static">
                            @if($patient->user->status === 'active')
                                <span class="badge badge-success">Can Login</span>
                            @else
                                <span class="badge badge-secondary">Login Disabled</span>
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <div class="form-group">
                        <label><strong>Member Since:</strong></label>
                        <p class="form-control-static">{{ $patient->created_at->format('M d, Y') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label><strong>Last Updated:</strong></label>
                        <p class="form-control-static">{{ $patient->updated_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('dashboard.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('patient.profile.edit') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit mr-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('patient.profile.edit') }}#password-section" class="list-group-item list-group-item-action">
                            <i class="fas fa-key mr-2"></i>Change Password
                        </a>
                        <a href="{{ route('password.request') }}" class="list-group-item list-group-item-action text-warning">
                            <i class="fas fa-unlock mr-2"></i>Forgot Password?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .form-control-static {
            padding-top: 7px;
            padding-bottom: 7px;
            margin-bottom: 0;
            min-height: 34px;
        }
        
        .badge {
            font-size: 0.85em;
        }
        
        .info-box-number {
            font-size: 1.1rem;
        }
        
        .user-avatar {
            background: linear-gradient(45deg, #007bff, #0056b3);
            font-weight: bold;
        }
        
        .list-group-item-action:hover {
            background-color: #f8f9fa;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .card-header .card-title {
            font-weight: 600;
            color: #495057;
        }
    </style>
@stop