@extends('adminlte::page')

@section('title', 'Patient Profile | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Patient Profile</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                <li class="breadcrumb-item active">{{ $patient->patient_name }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    {{-- Include modal-based alerts instead of inline alerts --}}
    @include('components.modal-alerts')

    <div class="row">
        <!-- Patient Profile Information -->
        <div class="col-md-8">
            <!-- Personal Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>Personal Information
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Back to List
                        </a>
                        @if(!$patient->archived)
                            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit mr-1"></i>Edit Profile
                            </a>
                        @endif
                    </div>
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
                                <label><strong>ID/Position Number:</strong></label>
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heartbeat mr-2"></i>Medical Information
                    </h3>
                </div>
                
                <div class="card-body">
                    <!-- Height and Weight -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-ruler-vertical"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Height</span>
                                    <span class="info-box-number">
                                        {{ $patient->height ? $patient->height . ' cm' : 'Not recorded' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-weight-hanging"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Weight</span>
                                    <span class="info-box-number">
                                        {{ $patient->weight ? $patient->weight . ' kg' : 'Not recorded' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- BMI -->
                    <div class="row">
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
                                    <span class="info-box-number">
                                        {{ $patient->bmi ?: 'Not calculated' }}
                                    </span>
                                    @if($patient->bmi_status)
                                        <span class="progress-description
                                            @if($patient->bmi_status == 'Normal weight')
                                                text-success
                                            @elseif($patient->bmi_status == 'Underweight')
                                                text-info
                                            @elseif($patient->bmi_status == 'Overweight')
                                                text-warning
                                            @elseif($patient->bmi_status == 'Obese')
                                                text-danger
                                            @endif">
                                            <strong>{{ $patient->bmi_status }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Blood Pressure -->
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
                                        @else
                                            Not recorded
                                        @endif
                                    </span>
                                    @if($patient->bp_status)
                                        <span class="progress-description
                                            @if($patient->bp_status == 'Normal')
                                                text-success
                                            @elseif($patient->bp_status == 'Elevated')
                                                text-warning
                                            @elseif(str_contains($patient->bp_status, 'High Blood Pressure') || $patient->bp_status == 'Hypertensive Crisis')
                                                text-danger
                                            @endif">
                                            <strong>{{ $patient->bp_status }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($patient->systolic_bp && $patient->diastolic_bp)
                    <!-- Individual BP Components -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><i class="fas fa-arrow-up mr-2 text-muted"></i>Systolic Pressure:</strong></label>
                                <p class="form-control-static">{{ $patient->systolic_bp }} mmHg</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong><i class="fas fa-arrow-down mr-2 text-muted"></i>Diastolic Pressure:</strong></label>
                                <p class="form-control-static">{{ $patient->diastolic_bp }} mmHg</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Account Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>Account History
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Patient Since:</strong></label>
                                <p class="form-control-static">{{ $patient->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Last Updated:</strong></label>
                                <p class="form-control-static">{{ $patient->updated_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
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
                        </div>
                        
                        @if($patient->user)
                        <div class="col-md-6">
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
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Quick Actions and Statistics -->
        <div class="col-md-4">
            <!-- Patient Avatar/Summary Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="user-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                            {{ strtoupper(substr($patient->patient_name, 0, 1)) }}
                        </div>
                        
                        <h3 class="profile-username text-center">{{ $patient->patient_name }}</h3>
                        
                        <p class="text-muted text-center">
                            Patient ID: {{ $patient->id }}
                            @if($patient->position)
                                <br>{{ $patient->position }}
                            @endif
                        </p>
                    </div>

                    @if($patient->email || $patient->phone_number)
                    <ul class="list-group list-group-unbordered mb-3">
                        @if($patient->email)
                        <li class="list-group-item">
                            <b>Email</b> 
                            <a class="float-right" href="mailto:{{ $patient->email }}">{{ $patient->email }}</a>
                        </li>
                        @endif
                        
                        @if($patient->phone_number)
                        <li class="list-group-item">
                            <b>Phone</b> 
                            <a class="float-right" href="tel:{{ $patient->phone_number }}">{{ $patient->phone_number }}</a>
                        </li>
                        @endif
                    </ul>
                    @endif

                    @if(!$patient->archived)
                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-edit mr-1"></i>Edit Profile
                            </a>
                            @if($patient->user)
                            <form action="{{ route('patients.resetPassword', $patient) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to reset this patient\'s password? A new password will be generated and sent to their email address.')">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-key mr-1"></i>Reset Password
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('appointments.create') }}?patient_id={{ $patient->id }}" class="btn btn-success btn-block btn-sm mb-2">
                        <i class="fas fa-calendar-plus mr-1"></i>Schedule Appointment
                    </a>
                    
                    <a href="{{ route('prescriptions.create') }}?patient_id={{ $patient->id }}&from=patient" class="btn btn-info btn-block btn-sm mb-2">
                        <i class="fas fa-prescription-bottle-alt mr-1"></i>Create Prescription
                    </a>
                    
                    <a href="{{ route('appointments.index') }}?search={{ $patient->patient_name }}" class="btn btn-secondary btn-block btn-sm mb-2">
                        <i class="fas fa-history mr-1"></i>View Appointments
                    </a>
                    
                    <a href="{{ route('prescriptions.index') }}?search={{ $patient->patient_name }}" class="btn btn-secondary btn-block btn-sm mb-2">
                        <i class="fas fa-pills mr-1"></i>View Prescriptions
                    </a>

                    <div class="dropdown-divider"></div>
                    
                    @if(!$patient->archived)
                        <button type="button" class="btn btn-warning btn-block btn-sm" onclick="archivePatient()">
                            <i class="fas fa-archive mr-1"></i>Archive Patient
                        </button>
                    @else
                        <button type="button" class="btn btn-success btn-block btn-sm" onclick="restorePatient()">
                            <i class="fas fa-undo mr-1"></i>Restore Patient
                        </button>
                    @endif
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>Patient Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Appointments</span>
                            <span class="info-box-number">{{ $patient->appointments->count() }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-prescription-bottle-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Prescriptions</span>
                            <span class="info-box-number">{{ $patient->prescriptions->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
<style>
.user-avatar {
    width: 80px;
    height: 80px;
    font-size: 32px;
}

.form-control-static {
    padding-top: 7px;
    padding-bottom: 7px;
    margin-bottom: 0;
    min-height: 34px;
}

.info-box-number {
    font-size: 1.1rem;
}

.card-outline {
    border-width: 2px;
}
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Archive patient
function archivePatient() {
    if (confirm('Are you sure you want to archive this patient? This will also disable their login access.')) {
        // Create a form dynamically to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("patients.destroy", $patient) }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Restore patient
function restorePatient() {
    if (confirm('Are you sure you want to restore this patient? This will re-enable their account.')) {
        // Create a form dynamically to submit the delete request (which toggles archive status)
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("patients.destroy", $patient) }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection