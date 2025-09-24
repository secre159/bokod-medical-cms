@extends('adminlte::page')

@section('title', 'Patient Dashboard | Bokod CMS')

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                Welcome, {{ auth()->user()->name }}!
                <small class="text-muted">Patient Portal</small>
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    @if(!$patient)
        <div class="alert alert-warning">
            <h4><i class="icon fas fa-exclamation-triangle"></i> Profile Incomplete!</h4>
            Your patient profile is not set up yet. Please contact the administrator to complete your registration.
        </div>
    @else
        <!-- Patient Overview Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $patient->appointments ? $patient->appointments()->active()->count() : 0 }}</h3>
                        <p>Active Appointments</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <a href="#upcoming-appointments" class="small-box-footer">
                        View Details <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $patient->appointments ? $patient->appointments()->where('status', 'completed')->count() : 0 }}</h3>
                        <p>Completed Visits</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="#appointment-history" class="small-box-footer">
                        View History <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $patient->appointments ? $patient->appointments()->pendingApproval()->count() : 0 }}</h3>
                        <p>Pending Approval</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('patient.appointments') }}#pending-appointments" class="small-box-footer">
                        View Pending <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $patient->prescriptions()->active()->count() ?? 0 }}</h3>
                        <p>Active Prescriptions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <a href="{{ route('patient.prescriptions') }}" class="small-box-footer">
                        View Prescriptions <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications & Alerts -->
        <div class="row">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bell mr-2"></i>Important Notifications
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            // Simplified notifications - avoid complex queries that may cause errors
                            $notifications = collect([]); // Empty collection for now
                            // TODO: Implement proper notification system with database tables
                        @endphp
                        
                        @if($notifications->count() > 0)
                            <div class="timeline timeline-inverse">
                                @foreach($notifications as $notification)
                                    <div class="time-label">
                                        <span class="bg-{{ $notification->priority === 'urgent' ? 'danger' : ($notification->priority === 'high' ? 'warning' : 'info') }}">
                                            {{ $notification->created_at->format('M d') }}
                                        </span>
                                    </div>
                                    <div>
                                        <i class="{{ $notification->icon }} bg-{{ $notification->priority === 'urgent' ? 'danger' : ($notification->priority === 'high' ? 'warning' : 'info') }}"></i>
                                        <div class="timeline-item">
                                            <h3 class="timeline-header">
                                                <strong>{{ $notification->title }}</strong>
                                                @if($notification->priority === 'urgent')
                                                    <span class="badge badge-danger ml-2">URGENT</span>
                                                @elseif($notification->priority === 'high')
                                                    <span class="badge badge-warning ml-2">HIGH</span>
                                                @endif
                                            </h3>
                                            <div class="timeline-body">
                                                {{ $notification->message }}
                                            </div>
                                            <div class="timeline-footer">
                                                <button class="btn btn-sm btn-primary" onclick="markNotificationRead({{ $notification->id }})">
                                                    <i class="fas fa-check mr-1"></i>Mark as Read
                                                </button>
                                                @if($notification->type === 'appointment_reminder')
                                                    <a href="{{ route('patient.appointments') }}" class="btn btn-sm btn-info ml-2">
                                                        <i class="fas fa-calendar mr-1"></i>View Appointments
                                                    </a>
                                                @elseif($notification->type === 'medication_reminder')
                                                    <a href="{{ route('patient.prescriptions') }}" class="btn btn-sm btn-success ml-2">
                                                        <i class="fas fa-pills mr-1"></i>View Prescriptions
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-sm btn-outline-primary" onclick="viewAllNotifications()">
                                    <i class="fas fa-list mr-1"></i>View All Notifications
                                </a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No new notifications</p>
                                <small class="text-muted">We'll notify you about important updates here</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Upcoming Appointments -->
            <div class="col-md-8">
                <div class="card" id="upcoming-appointments">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar mr-2"></i>Upcoming Appointments</h3>
                        <div class="card-tools">
                            <a href="{{ route('patient.appointments') }}#bookAppointmentModal" class="btn btn-success btn-sm" onclick="if(typeof $ !== 'undefined') { $('#bookAppointmentModal').modal('show'); return false; } else { return true; }">
                                <i class="fas fa-plus mr-1"></i>Request Appointment
                            </a>
                        </div>
                    </div>
                    <div class="card-body {{ $patient->appointments()->upcoming()->count() <= 2 ? 'compact-appointments' : '' }}">
                        @php
                            $upcomingAppointments = $patient->appointments()->upcoming()->with('patient')->take(5)->get();
                        @endphp
                        
                        @if($upcomingAppointments->count() > 0)
                            @if($upcomingAppointments->count() <= 2)
                                <!-- Compact card layout for 1-2 appointments -->
                                <div class="appointments-compact">
                                    @foreach($upcomingAppointments as $appointment)
                                        <div class="appointment-card mb-3 p-3 border rounded">
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="date-display text-center">
                                                        <div class="date-day">{{ $appointment->appointment_date->format('d') }}</div>
                                                        <div class="date-month">{{ $appointment->appointment_date->format('M Y') }}</div>
                                                        <small class="text-muted">{{ $appointment->appointment_date->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <div class="time-display">
                                                        <i class="fas fa-clock text-info"></i>
                                                        <div class="font-weight-bold">{{ $appointment->appointment_time->format('h:i A') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="reason-display">
                                                        <strong>Reason:</strong><br>
                                                        <span class="text-muted">{{ Str::limit($appointment->reason, 60) }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <div class="status-display">
                                                        @if($appointment->approval_status == 'pending')
                                                            <span class="badge badge-warning badge-lg">Pending Approval</span>
                                                        @elseif($appointment->approval_status == 'approved')
                                                            <span class="badge badge-success badge-lg">Confirmed</span>
                                                        @else
                                                            <span class="badge badge-danger badge-lg">Declined</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Table layout for 3+ appointments -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Reason</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($upcomingAppointments as $appointment)
                                            <tr>
                                                <td>
                                                    <strong>{{ $appointment->appointment_date->format('M d, Y') }}</strong>
                                                    <br><small class="text-muted">{{ $appointment->appointment_date->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    {{ $appointment->appointment_time->format('h:i A') }}
                                                </td>
                                                <td>{{ Str::limit($appointment->reason, 40) }}</td>
                                                <td>
                                                    @if($appointment->approval_status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($appointment->approval_status == 'approved')
                                                        <span class="badge badge-success">Confirmed</span>
                                                    @else
                                                        <span class="badge badge-danger">Declined</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            
                            <!-- View All Appointments Button -->
                            <div class="text-center mt-3">
                                <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-calendar-alt mr-1"></i>View All Appointments
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No upcoming appointments scheduled</p>
                                <a href="{{ route('patient.appointments') }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i>Schedule Your First Appointment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Health Summary -->
            <div class="col-md-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-heartbeat mr-2"></i>Health Summary
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-success">
                                        <i class="fas fa-caret-up"></i> 92%
                                    </span>
                                    <h5 class="description-header">Health Score</h5>
                                    <span class="description-text">EXCELLENT</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-info">
                                        <i class="fas fa-calendar"></i> {{ $patient->appointments()->where('status', 'completed')->count() }}
                                    </span>
                                    <h5 class="description-header">Total Visits</h5>
                                    <span class="description-text">ALL TIME</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($patient->height || $patient->weight || $patient->blood_pressure)
                        <hr>
                        <div class="row">
                            @if($patient->height && $patient->weight)
                            <div class="col-12">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-weight"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">BMI Status</span>
                                        <span class="info-box-number">{{ $patient->bmi ?? 'N/A' }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: 70%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $patient->bmi_status ?? 'Normal' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($patient->blood_pressure)
                            <div class="col-12 mt-2">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-heartbeat"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Blood Pressure</span>
                                        <span class="info-box-number">{{ $patient->blood_pressure }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" style="width: 60%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $patient->bp_status ?? 'Normal' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @else
                        <hr>
                        <div class="text-center">
                            <i class="fas fa-stethoscope fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No vital signs recorded yet</p>
                            <small class="text-muted">Data will appear after your next visit</small>
                        </div>
                        @endif
                        
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header text-warning">{{ $patient->appointments()->where('approval_status', 'pending')->count() }}</h5>
                                    <span class="description-text">PENDING</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header text-success">{{ $patient->appointments()->active()->count() }}</h5>
                                    <span class="description-text">ACTIVE</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Patient Profile Summary -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                        <div class="profile-user-img img-fluid img-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; font-size: 2rem; font-weight: bold; margin: 0 auto;">
                            {{ strtoupper(substr($patient->patient_name, 0, 1)) }}
                        </div>
                                 onerror="this.src='{{ asset('vendor/adminlte/dist/img/user4-128x128.jpg') }}'; console.log('Dashboard profile image failed to load');">
                        </div>
                        
                        <h3 class="profile-username text-center">{{ $patient->patient_name }}</h3>
                        
                        <p class="text-muted text-center">Patient ID: {{ $patient->id }}</p>
                        
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b><i class="fas fa-envelope mr-2"></i>Email</b>
                                <span class="float-right">{{ $patient->email }}</span>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-phone mr-2"></i>Phone</b>
                                <span class="float-right">{{ $patient->phone_number }}</span>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-birthday-cake mr-2"></i>Age</b>
                                <span class="float-right">
                                    @if($patient->date_of_birth)
                                        {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} years
                                    @else
                                        Not set
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-venus-mars mr-2"></i>Gender</b>
                                <span class="float-right">{{ ucfirst($patient->gender ?? 'Not set') }}</span>
                            </li>
                            @if($patient->emergency_contact_name)
                            <li class="list-group-item">
                                <b><i class="fas fa-user-friends mr-2"></i>Emergency Contact</b>
                                <span class="float-right">{{ $patient->emergency_contact_name }}</span>
                            </li>
                            @endif
                            @if($patient->emergency_contact_phone)
                            <li class="list-group-item">
                                <b><i class="fas fa-phone-alt mr-2"></i>Emergency Phone</b>
                                <span class="float-right">
                                    <a href="tel:{{ $patient->emergency_contact_phone }}">{{ $patient->emergency_contact_phone }}</a>
                                </span>
                            </li>
                            @endif
                        </ul>
                        
                        <a href="{{ route('patient.profile.edit') }}" class="btn btn-primary btn-block mb-2">
                            <b><i class="fas fa-user-edit mr-2"></i>Update Profile</b>
                        </a>
                        <a href="{{ route('patient.profile.edit') }}#password-section" class="btn btn-warning btn-block mb-2">
                            <b><i class="fas fa-key mr-2"></i>Change Password</b>
                        </a>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Forgot your password? 
                            <a href="{{ route('password.request') }}" class="text-primary">Reset it here</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-plus"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Book Appointment</span>
                                        <span class="info-box-number">
                                            <a href="{{ route('patient.appointments') }}" class="btn btn-sm btn-info">Schedule Now</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-history"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Medical History</span>
                                        <span class="info-box-number">
                                            <a href="{{ route('patient.history') }}" class="btn btn-sm btn-success">View Records</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-prescription-bottle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Prescriptions</span>
                                        <span class="info-box-number">
                                            <a href="{{ route('patient.prescriptions') }}" class="btn btn-sm btn-warning">View All</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-comments"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Messages</span>
                                        <span class="info-box-number">
                                            <a href="{{ route('patient.messages.index') }}" class="btn btn-sm btn-primary">View Chat</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <div class="card" id="appointment-history">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Recent Activity</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $recentAppointments = $patient->appointments ? $patient->appointments()->latest('appointment_date')->take(5)->get() : collect();
                        @endphp
                        
                        @if($recentAppointments->count() > 0)
                            <div class="timeline timeline-enhanced">
                                @foreach($recentAppointments as $index => $appointment)
                                <div class="time-label">
                                    <span class="bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'primary') }}">
                                        {{ $appointment->appointment_date->format('M d') }}
                                    </span>
                                </div>
                                <div class="timeline-event" data-appointment-id="{{ $appointment->appointment_id }}">
                                    <i class="fas fa-{{ $appointment->status == 'completed' ? 'check-circle' : ($appointment->status == 'cancelled' ? 'times-circle' : 'calendar') }} bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'info') }}"></i>
                                    <div class="timeline-item enhanced-timeline-item">
                                        <div class="timeline-header-row">
                                            <span class="time"><i class="fas fa-clock text-muted mr-1"></i> {{ $appointment->appointment_time->format('h:i A') }}</span>
                                            <div class="timeline-status-badges">
                                                @if($appointment->approval_status == 'pending')
                                                    <span class="badge badge-warning badge-sm">Pending Approval</span>
                                                @elseif($appointment->approval_status == 'approved')
                                                    <span class="badge badge-success badge-sm">Approved</span>
                                                @elseif($appointment->approval_status == 'rejected')
                                                    <span class="badge badge-danger badge-sm">Rejected</span>
                                                @endif
                                                <span class="badge badge-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'secondary' : 'info') }} badge-sm ml-1">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="timeline-header">
                                            @if($appointment->status == 'completed')
                                                <i class="fas fa-user-md text-success mr-2"></i>Medical Visit Completed
                                            @elseif($appointment->status == 'cancelled')
                                                <i class="fas fa-calendar-times text-danger mr-2"></i>Appointment Cancelled
                                            @else
                                                <i class="fas fa-calendar-check text-info mr-2"></i>Upcoming Appointment
                                            @endif
                                        </h3>
                                        <div class="timeline-body">
                                            <div class="appointment-reason">
                                                <strong>Reason:</strong> {{ Str::limit($appointment->reason, 100) }}
                                                @if(strlen($appointment->reason) > 100)
                                                    <a href="#" class="text-primary read-more-btn" data-appointment-id="{{ $appointment->appointment_id }}" data-reason="{{ addslashes($appointment->reason) }}">...read more</a>
                                                @endif
                                            </div>
                                            <div class="appointment-meta mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-plus mr-1"></i>Scheduled: {{ $appointment->created_at->format('M d, Y h:i A') }} 
                                                    @if($appointment->created_at->diffInDays() < 7)
                                                        ({{ $appointment->created_at->diffForHumans() }})
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="timeline-footer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="timeline-actions">
                                                    @if($appointment->status == 'active' && $appointment->appointment_date >= now())
                                                        <button class="btn btn-outline-primary btn-sm mr-1 reschedule-btn" data-appointment-id="{{ $appointment->appointment_id }}" title="Reschedule this appointment">
                                                            <i class="fas fa-calendar-alt mr-1"></i>Reschedule
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm mr-1 cancel-btn" data-appointment-id="{{ $appointment->appointment_id }}" title="Cancel this appointment">
                                                            <i class="fas fa-times mr-1"></i>Cancel
                                                        </button>
                                                    @elseif($appointment->status == 'completed')
                                                        <button class="btn btn-outline-info btn-sm mr-1 view-details-btn" data-appointment-id="{{ $appointment->appointment_id }}" title="View appointment details">
                                                            <i class="fas fa-eye mr-1"></i>View Details
                                                        </button>
                                                    @endif
                                                    @if($appointment->status == 'active' && $appointment->appointment_date < now())
                                                        <small class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Past due - contact clinic</small>
                                                    @endif
                                                </div>
                                                <div class="timeline-date">
                                                    <small class="text-muted">{{ $appointment->appointment_date->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                <div class="timeline-end">
                                    <i class="fas fa-history bg-gray"></i>
                                    <div class="timeline-end-label">
                                        <small class="text-muted">End of recent activity</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No recent activity</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Emergency Information Quick Access -->
        <div class="row">
            <div class="col-12">
                <div class="card card-danger collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Emergency Information
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-phone-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-white">Emergency Contact</span>
                                        <span class="info-box-number text-white">
                                            @if($patient->emergency_contact_name)
                                                {{ $patient->emergency_contact_name }}
                                                <br><small>{{ $patient->emergency_contact_phone ?? 'No phone' }}</small>
                                            @else
                                                Not Set
                                            @endif
                                        </span>
                                        @if($patient->emergency_contact_phone)
                                        <div class="mt-2">
                                            <a href="tel:{{ $patient->emergency_contact_phone }}" class="btn btn-sm btn-light">
                                                <i class="fas fa-phone mr-1"></i>Call Now
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-allergies"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-dark">Allergies</span>
                                        <span class="info-box-number text-dark">
                                            @if($patient->user->allergies)
                                                {{ Str::limit($patient->user->allergies, 30) }}
                                            @else
                                                None Recorded
                                            @endif
                                        </span>
                                        @if($patient->user->allergies)
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-dark" onclick="showAllergies()">
                                                <i class="fas fa-eye mr-1"></i>View All
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-file-medical"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text text-white">Medical ID</span>
                                        <span class="info-box-number text-white">
                                            {{ $patient->id }}
                                            <br><small>{{ $patient->patient_name }}</small>
                                        </span>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-light" onclick="generateMedicalCard()">
                                                <i class="fas fa-id-card mr-1"></i>Medical Card
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($patient->user->medical_history)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info-circle"></i> Medical History Summary</h5>
                                    {{ Str::limit($patient->user->medical_history, 200) }}
                                    @if(strlen($patient->user->medical_history) > 200)
                                        <br><a href="#" onclick="showFullMedicalHistory()" class="alert-link">Read more...</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Important:</strong> In case of emergency, show this information to medical personnel. 
                                    Keep your emergency contact information up to date.
                                    <div class="mt-2">
                                        <a href="{{ route('patient.profile.edit') }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit mr-1"></i>Update Emergency Info
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('css')
<style>
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    
    .profile-user-img {
        width: 100px;
        height: 100px;
        border: 3px solid #adb5bd;
        margin: 0 auto;
        padding: 3px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
    
    .profile-user-img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .timeline {
        position: relative;
        margin: 0 0 30px 0;
        padding: 0;
        list-style: none;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        left: 25px;
        height: 100%;
        width: 4px;
        background: #ddd;
    }
    
    .timeline > li {
        position: relative;
        margin-right: 10px;
        margin-bottom: 15px;
    }
    
    .timeline-item {
        background: #fff;
        border-radius: 3px;
        margin-top: 0;
        color: #444;
        margin-left: 60px;
        margin-right: 15px;
        padding: 0;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    }
    
    /* Compact appointments styling */
    .appointments-compact {
        margin-bottom: 0;
    }
    
    .compact-appointments {
        padding: 15px !important;
        min-height: auto !important;
    }
    
    .appointment-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef !important;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .appointment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #007bff !important;
    }
    
    .date-display {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border-radius: 8px;
        padding: 10px;
    }
    
    .date-day {
        font-size: 24px;
        font-weight: bold;
        line-height: 1;
    }
    
    .date-month {
        font-size: 12px;
        opacity: 0.9;
        margin-bottom: 2px;
    }
    
    .time-display {
        padding: 10px;
    }
    
    .time-display i {
        font-size: 18px;
        margin-bottom: 5px;
    }
    
    .reason-display {
        padding: 10px 0;
    }
    
    .status-display {
        padding: 10px;
    }
    
    .badge-lg {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    /* Enhanced Timeline Styles */
    .timeline-enhanced {
        position: relative;
        padding: 0;
    }
    
    .timeline-enhanced:before {
        content: '';
        position: absolute;
        top: 0;
        left: 25px;
        height: 100%;
        width: 3px;
        background: linear-gradient(to bottom, #007bff 0%, #28a745 50%, #6c757d 100%);
        border-radius: 2px;
    }
    
    .timeline-event {
        position: relative;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .timeline-event:hover {
        transform: translateX(2px);
    }
    
    .enhanced-timeline-item {
        background: #fff;
        border-radius: 8px;
        margin-left: 60px;
        margin-right: 15px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
    }
    
    .enhanced-timeline-item:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        border-left-color: #28a745;
    }
    
    .timeline-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .timeline-status-badges {
        display: flex;
        gap: 5px;
    }
    
    .badge-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .appointment-reason {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 10px;
        border-left: 3px solid #007bff;
    }
    
    .appointment-meta {
        border-top: 1px solid #e9ecef;
        padding-top: 8px;
    }
    
    .timeline-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .timeline-actions .btn {
        transition: all 0.2s ease;
        font-size: 0.85rem;
    }
    
        .timeline-actions .btn:hover {
            transform: translateY(-1px);
        }
        
        /* SIDEBAR COLLAPSE CSS FIX */
        .sidebar-collapse .main-sidebar {
            width: 4.6rem !important;
        }
        
        .sidebar-collapse .main-sidebar .nav-sidebar .nav-link {
            width: calc(4.6rem - 1rem) !important;
        }
        
        .sidebar-collapse .main-sidebar .nav-sidebar .nav-link p {
            display: none !important;
        }
        
        .sidebar-collapse .main-sidebar .brand-link {
            width: 4.6rem !important;
        }
        
        .sidebar-collapse .main-sidebar .brand-link .brand-text {
            display: none !important;
        }
        
        .sidebar-collapse .content-wrapper,
        .sidebar-collapse .main-footer {
            margin-left: 4.6rem !important;
        }
        
        /* Mobile sidebar behavior */
        @media (max-width: 991.98px) {
            .main-sidebar {
                position: fixed !important;
                z-index: 1037;
                left: -250px;
                transition: left 0.3s ease-in-out;
            }
            
            .sidebar-open .main-sidebar {
                left: 0 !important;
            }
            
            .sidebar-open::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1036;
            }
        }
        
        /* Ensure hamburger menu is visible */
        .navbar-nav .nav-link[data-widget="pushmenu"] {
            display: block !important;
        }
    
    .timeline-end {
        position: relative;
        margin-bottom: 0;
        text-align: center;
    }
    
    .timeline-end-label {
        margin-left: 60px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 20px;
        display: inline-block;
    }
    
    /* Status-based styling */
    .enhanced-timeline-item[data-status="completed"] {
        border-left-color: #28a745;
        background: linear-gradient(135deg, #fff 0%, #f8fff8 100%);
    }
    
    .enhanced-timeline-item[data-status="cancelled"] {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #fff 0%, #fff8f8 100%);
    }
    
    .enhanced-timeline-item[data-status="active"] {
        border-left-color: #007bff;
        background: linear-gradient(135deg, #fff 0%, #f8fbff 100%);
    }
    
    /* Mobile responsiveness for appointment cards */
    @media (max-width: 768px) {
        .appointment-card .row {
            flex-direction: column;
            text-align: center;
        }
        
        .appointment-card .col-md-3,
        .appointment-card .col-md-2,
        .appointment-card .col-md-4 {
            margin-bottom: 10px;
        }
        
        .date-display {
            display: inline-block;
            margin-bottom: 10px;
        }
        
        /* Mobile timeline adjustments */
        .enhanced-timeline-item {
            margin-left: 40px;
            margin-right: 10px;
            padding: 12px;
        }
        
        .timeline-header-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .timeline-actions {
            flex-direction: column;
            gap: 5px;
            width: 100%;
        }
        
        .timeline-actions .btn {
            width: 100%;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('js')
<script>
// Define global functions FIRST before any document ready events

// Appointment management functions - Define in global scope
window.rescheduleAppointment = function(appointmentId) {
    console.log('rescheduleAppointment called with ID:', appointmentId);
    
    // Check if SweetAlert is available
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert not available, using fallback');
        if (confirm('Reschedule appointment ID ' + appointmentId + '? This will redirect you to the appointments page.')) {
            window.location.href = '{{ route("patient.appointments") }}';
        }
        return;
    }
    
    Swal.fire({
        title: 'Reschedule Appointment',
        html: `
            <div class="text-left">
                <div class="form-group">
                    <label for="reschedule_date">New Date:</label>
                    <input type="date" id="reschedule_date" class="form-control" min="` + new Date().toISOString().split('T')[0] + `">
                </div>
                <div class="form-group">
                    <label for="reschedule_time">Preferred Time:</label>
                    <select id="reschedule_time" class="form-control">
                        <option value="08:00">8:00 AM</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="reschedule_reason">Reason for Rescheduling:</label>
                    <textarea id="reschedule_reason" class="form-control" rows="3" placeholder="Please provide a reason for rescheduling..."></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-calendar-alt"></i> Request Reschedule',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#007bff',
        width: '500px',
        preConfirm: () => {
            const date = document.getElementById('reschedule_date').value;
            const time = document.getElementById('reschedule_time').value;
            const reason = document.getElementById('reschedule_reason').value;
            
            if (!date) {
                Swal.showValidationMessage('Please select a date');
                return false;
            }
            
            if (!reason.trim()) {
                Swal.showValidationMessage('Please provide a reason for rescheduling');
                return false;
            }
            
            return { date: date, time: time, reason: reason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { date, time, reason } = result.value;
            
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Submitting your reschedule request',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Make AJAX request to reschedule
            $.ajax({
                url: '{{ route("patient.api.reschedule", ["appointment" => "__ID__"]) }}'.replace('__ID__', appointmentId),
                method: 'POST',
                data: {
                    appointment_date: date,
                    appointment_time: time,
                    reason: reason,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your reschedule request has been submitted and is pending approval.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Failed to submit reschedule request. Please try again.';
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
};

window.cancelAppointment = function(appointmentId) {
    console.log('cancelAppointment called with ID:', appointmentId);
    
    // Check if SweetAlert is available
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert not available, using fallback');
        if (confirm('Cancel appointment ID ' + appointmentId + '? This action cannot be undone.')) {
            // Simple AJAX call without SweetAlert
            $.ajax({
                url: '{{ route("patient.api.cancelWithReason", ["appointment" => "__ID__"]) }}'.replace('__ID__', appointmentId),
                method: 'POST',
                data: {
                    reason: 'Cancelled from dashboard',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    alert('Appointment cancelled successfully.');
                    location.reload();
                },
                error: function() {
                    alert('Failed to cancel appointment. Please try again.');
                }
            });
        }
        return;
    }
    
    Swal.fire({
        title: 'Cancel Appointment',
        text: 'Are you sure you want to cancel this appointment?',
        html: `
            <div class="text-left mt-3">
                <div class="form-group">
                    <label for="cancel_reason">Reason for Cancellation:</label>
                    <textarea id="cancel_reason" class="form-control" rows="3" placeholder="Please provide a reason for cancelling..."></textarea>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Yes, Cancel Appointment',
        cancelButtonText: 'No, Keep Appointment',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        width: '500px',
        preConfirm: () => {
            const reason = document.getElementById('cancel_reason').value;
            
            if (!reason.trim()) {
                Swal.showValidationMessage('Please provide a reason for cancelling');
                return false;
            }
            
            return { reason: reason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { reason } = result.value;
            
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Cancelling your appointment',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Make AJAX request to cancel
            $.ajax({
                url: '{{ route("patient.api.cancelWithReason", ["appointment" => "__ID__"]) }}'.replace('__ID__', appointmentId),
                method: 'POST',
                data: {
                    reason: reason,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Cancelled!',
                        text: 'Your appointment has been cancelled successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Failed to cancel appointment. Please try again.';
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
};

// Enhanced timeline functions - Define in global scope
window.showFullReason = function(appointmentId, reason) {
    console.log('showFullReason called with ID:', appointmentId);
    Swal.fire({
        title: 'Appointment Details',
        html: `
            <div class="text-left">
                <h6 class="text-muted mb-3">Appointment ID: ${appointmentId}</h6>
                <div class="appointment-reason-full">
                    <strong>Full Reason:</strong><br>
                    <div class="mt-2 p-3 bg-light rounded">
                        ${reason.replace(/\n/g, '<br>')}
                    </div>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Close',
        confirmButtonColor: '#007bff',
        width: '500px'
    });
};

window.viewAppointmentDetails = function(appointmentId) {
    console.log('viewAppointmentDetails called with ID:', appointmentId);
    
    // Check if SweetAlert is available
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert not available, using fallback');
        alert('View details feature requires SweetAlert. Redirecting to appointments page.');
        window.location.href = '{{ route("patient.appointments") }}';
        return;
    }
    
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching appointment details',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Fetch appointment details
    $.ajax({
        url: '{{ route("patient.api.details", ["appointment" => "__ID__"]) }}'.replace('__ID__', appointmentId),
        method: 'GET',
        success: function(response) {
            if (response.appointment) {
                const appointment = response.appointment;
                const prescriptions = appointment.prescriptions || [];
                
                let prescriptionsList = '';
                if (prescriptions.length > 0) {
                    prescriptionsList = '<h6 class="mt-3 mb-2">Prescriptions:</h6><div class="list-group">';
                    prescriptions.forEach(prescription => {
                        prescriptionsList += `
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${prescription.medicine ? prescription.medicine.name : 'Medicine'}</h6>
                                    <small class="text-muted">${prescription.status || 'N/A'}</small>
                                </div>
                                <p class="mb-1"><strong>Dosage:</strong> ${prescription.dosage || 'N/A'}</p>
                                <p class="mb-1"><strong>Instructions:</strong> ${prescription.instructions || 'N/A'}</p>
                                <small class="text-muted">Qty: ${prescription.quantity || 'N/A'}</small>
                            </div>
                        `;
                    });
                    prescriptionsList += '</div>';
                } else {
                    prescriptionsList = '<p class="text-muted mt-3"><em>No prescriptions for this appointment.</em></p>';
                }
                
                Swal.fire({
                    title: 'Appointment Details',
                    html: `
                        <div class="text-left">
                            <div class="appointment-details-card">
                                <h6 class="text-muted mb-3">Appointment #${appointment.appointment_id}</h6>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Date:</strong><br>
                                        ${new Date(appointment.appointment_date).toLocaleDateString('en-US', {
                                            weekday: 'long',
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        })}
                                    </div>
                                    <div class="col-6">
                                        <strong>Time:</strong><br>
                                        ${appointment.appointment_time}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <strong>Status:</strong> 
                                    <span class="badge badge-${appointment.status === 'completed' ? 'success' : (appointment.status === 'cancelled' ? 'danger' : 'info')}">
                                        ${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}
                                    </span>
                                    <span class="badge badge-${appointment.approval_status === 'approved' ? 'success' : (appointment.approval_status === 'pending' ? 'warning' : 'danger')} ml-2">
                                        ${appointment.approval_status.charAt(0).toUpperCase() + appointment.approval_status.slice(1)}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <strong>Reason for Visit:</strong><br>
                                    <div class="p-2 bg-light rounded mt-1">
                                        ${appointment.reason || 'No reason provided'}
                                    </div>
                                </div>
                                ${appointment.notes ? `
                                    <div class="mb-3">
                                        <strong>Notes:</strong><br>
                                        <div class="p-2 bg-light rounded mt-1">
                                            ${appointment.notes.replace(/\n/g, '<br>')}
                                        </div>
                                    </div>
                                ` : ''}
                                ${prescriptionsList}
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#007bff',
                    width: '600px',
                    showCancelButton: false
                });
            } else {
                throw new Error('No appointment data received');
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Failed to load appointment details. Please try again.';
            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
};

// Now start the document ready functions
$(document).ready(function() {
    // Initialize any patient dashboard specific JavaScript
    console.log('Patient dashboard loaded');
    console.log('Functions defined:', typeof window.rescheduleAppointment, typeof window.cancelAppointment);
    
    // Force hide preloader after page load
    setTimeout(function() {
        $('.preloader').fadeOut(500);
        $('.overlay').fadeOut(500);
        // Don't force remove sidebar-collapse - let AdminLTE handle it
        console.log('Preloader forcefully hidden after timeout');
    }, 2000); // 2 second timeout
    
    // Optimize dashboard loading
    optimizeDashboardLoading();
    
    // SIDEBAR FIX: Force enable sidebar toggle functionality after AdminLTE loads
    setTimeout(function() {
        fixSidebarToggle();
    }, 1000);
    
    // Disable auto-refresh notifications to prevent API errors
    // TODO: Re-enable when notification system is properly implemented
    // setInterval(function() {
    //     refreshNotifications();
    // }, 300000); // 5 minutes
    
    // Debug function availability
    console.log('Available functions check:');
    console.log('- rescheduleAppointment:', typeof window.rescheduleAppointment);
    console.log('- cancelAppointment:', typeof window.cancelAppointment);
    console.log('- viewAppointmentDetails:', typeof window.viewAppointmentDetails);
    console.log('- showFullReason:', typeof window.showFullReason);
    console.log('- jQuery available:', typeof $);
    console.log('- SweetAlert available:', typeof Swal);
    
    // Apply status-based styling to timeline items
    $('.timeline-event').each(function() {
        const appointmentId = $(this).data('appointment-id');
        const timelineItem = $(this).find('.enhanced-timeline-item');
        
        console.log('Processing timeline event for appointment:', appointmentId);
        
        // Get status from badges
        const statusBadge = $(this).find('.timeline-status-badges .badge').last().text().toLowerCase().trim();
        timelineItem.attr('data-status', statusBadge);
        
        // Add hover effects
        $(this).on('mouseenter', function() {
            $(this).find('.enhanced-timeline-item').addClass('shadow-lg');
        }).on('mouseleave', function() {
            $(this).find('.enhanced-timeline-item').removeClass('shadow-lg');
        });
    });
    
    // Fix button click events using specific classes and data attributes
    $(document).on('click', '.reschedule-btn', function(e) {
        e.preventDefault();
        const $button = $(this);
        const appointmentId = $button.data('appointment-id');
        
        // Visual feedback
        $button.html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...');
        
        console.log('Reschedule button clicked for appointment ID:', appointmentId);
        
        // Reset button after a short delay if function fails to load
        setTimeout(function() {
            $button.html('<i class="fas fa-calendar-alt mr-1"></i>Reschedule');
        }, 5000);
        
        try {
            if (typeof window.rescheduleAppointment === 'function') {
                window.rescheduleAppointment(appointmentId);
            } else {
                console.error('rescheduleAppointment function not available');
                alert('Reschedule function not loaded. Please refresh the page and try again.');
            }
        } catch (error) {
            console.error('Error calling rescheduleAppointment:', error);
            alert('Error: ' + error.message);
        }
    });
    
    $(document).on('click', '.cancel-btn', function(e) {
        e.preventDefault();
        const $button = $(this);
        const appointmentId = $button.data('appointment-id');
        
        // Visual feedback
        $button.html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...');
        
        console.log('Cancel button clicked for appointment ID:', appointmentId);
        
        // Reset button after a short delay if function fails to load
        setTimeout(function() {
            $button.html('<i class="fas fa-times mr-1"></i>Cancel');
        }, 5000);
        
        try {
            if (typeof window.cancelAppointment === 'function') {
                window.cancelAppointment(appointmentId);
            } else {
                console.error('cancelAppointment function not available');
                alert('Cancel function not loaded. Please refresh the page and try again.');
            }
        } catch (error) {
            console.error('Error calling cancelAppointment:', error);
            alert('Error: ' + error.message);
        }
    });
    
    $(document).on('click', '.view-details-btn', function(e) {
        e.preventDefault();
        const appointmentId = $(this).data('appointment-id');
        console.log('View details button clicked for appointment ID:', appointmentId);
        window.viewAppointmentDetails(appointmentId);
    });
    
    $(document).on('click', '.read-more-btn', function(e) {
        e.preventDefault();
        const appointmentId = $(this).data('appointment-id');
        const reason = $(this).data('reason');
        console.log('Read more button clicked for appointment ID:', appointmentId);
        window.showFullReason(appointmentId, reason);
    });
    
    // Animate timeline items on scroll (if visible)
    if (typeof IntersectionObserver !== 'undefined') {
        const timelineObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.timeline-event').forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            timelineObserver.observe(item);
        });
    }
    
    // Add test button functionality (remove in production)
    if (window.location.search.includes('debug=1')) {
        $('body').append('<div style="position:fixed;top:10px;right:10px;z-index:9999;"><button class="btn btn-warning btn-sm" onclick="console.log(\'Functions check:\', typeof window.rescheduleAppointment, typeof window.cancelAppointment)">Debug Functions</button></div>');
    }
    
    console.log('Patient dashboard timeline initialization complete');
    
    // Test if functions are accessible
    setTimeout(function() {
        console.log('Post-initialization function check:');
        console.log('- window.rescheduleAppointment:', typeof window.rescheduleAppointment);
        console.log('- window.cancelAppointment:', typeof window.cancelAppointment);
        console.log('- window.viewAppointmentDetails:', typeof window.viewAppointmentDetails);
        console.log('- window.showFullReason:', typeof window.showFullReason);
    }, 1000);
});

// Dashboard loading optimization
function optimizeDashboardLoading() {
    try {
        // Check for jQuery and AdminLTE availability
        if (typeof $ === 'undefined') {
            console.warn('jQuery not loaded, dashboard may have loading issues');
            return;
        }
        
        // Ensure all dashboard components are visible
        $('.content-wrapper').css('opacity', '1');
        $('.main-header').css('opacity', '1');
        
        // Initialize cards with lazy loading
        $('.card').each(function() {
            $(this).css('opacity', '1');
        });
        
        // Setup error handling for failed AJAX requests
        $(document).ajaxError(function(event, xhr, settings, error) {
            console.error('AJAX Error:', error, 'URL:', settings.url);
            
            // Hide any lingering preloaders on AJAX errors
            $('.preloader, .overlay').fadeOut(300);
            
            // Show user-friendly error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Connection Error',
                    text: 'There was a problem loading some dashboard data. Please refresh the page.',
                    icon: 'warning',
                    confirmButtonText: 'Refresh Page',
                    showCancelButton: true,
                    cancelButtonText: 'Continue'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            }
        });
        
        console.log('Dashboard loading optimization applied');
    } catch (e) {
        console.error('Error during dashboard optimization:', e);
        // Fallback: force hide preloader
        $('.preloader').hide();
    }
}

function markNotificationRead(notificationId) {
    // For now, just hide the notification visually
    // TODO: Implement proper notification system with backend routes
    $('[data-notification-id="' + notificationId + '"]').fadeOut(300, function() {
        $(this).remove();
        
        // Check if there are no more notifications
        if ($('.timeline .timeline-item').length === 0) {
            location.reload(); // Refresh to show "no notifications" message
        }
    });
    
    // Show success message without making API call
    if (typeof toastr !== 'undefined') {
        toastr.success('Notification marked as read');
    } else {
        console.log('Notification marked as read');
    }
}

function viewAllNotifications() {
    // Use modal alert instead of native alert to avoid conflicts
    if (typeof modalAlert !== 'undefined') {
        modalAlert('View all notifications feature coming soon!', 'Coming Soon');
    } else if (typeof Swal !== 'undefined') {
        Swal.fire('Coming Soon', 'View all notifications feature coming soon!', 'info');
    } else {
        alert('View all notifications feature coming soon!');
    }
}

function refreshNotifications() {
    // Disable automatic refresh for now to prevent API errors
    // TODO: Implement proper notification API endpoints
    console.log('Notification refresh disabled - implement proper notification system');
    return false;
}

// Emergency Information Functions
function showAllergies() {
    const allergies = @json(isset($patient->user->allergies) ? $patient->user->allergies : 'None recorded');
    
    Swal.fire({
        title: 'Allergy Information',
        html: '<div class="text-left"><strong>Known Allergies:</strong><br><br>' + String(allergies).replace(/\n/g, '<br>') + '</div>',
        icon: 'warning',
        confirmButtonText: 'Close',
        confirmButtonColor: '#ffc107',
        width: '500px'
    });
}

function showFullMedicalHistory() {
    const medicalHistory = @json(isset($patient->user->medical_history) ? $patient->user->medical_history : 'No medical history recorded');
    
    Swal.fire({
        title: 'Complete Medical History',
        html: '<div class="text-left">' + String(medicalHistory).replace(/\n/g, '<br>') + '</div>',
        icon: 'info',
        confirmButtonText: 'Close',
        confirmButtonColor: '#17a2b8',
        width: '600px'
    });
}

function generateMedicalCard() {
    Swal.fire({
        title: 'Medical Emergency Card',
        html: `
            <div class="medical-card" style="border: 2px solid #dc3545; padding: 20px; background: #fff; text-align: left;">
                <h4 style="color: #dc3545; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-plus-circle"></i> MEDICAL EMERGENCY CARD
                </h4>
                <div style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 10px;">
                    <strong>Patient Name:</strong> {{ $patient->patient_name }}<br>
                    <strong>Medical ID:</strong> {{ $patient->id }}<br>
                    <strong>Date of Birth:</strong> {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'Not set' }}<br>
                    <strong>Gender:</strong> {{ ucfirst($patient->gender ?? 'Not set') }}
                </div>
                <div style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 10px;">
                    <strong style="color: #dc3545;">Emergency Contact:</strong><br>
                    {{ $patient->emergency_contact_name ?? 'Not set' }}<br>
                    {{ $patient->emergency_contact_phone ?? 'No phone' }}
                </div>
                <div style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 10px;">
                    <strong style="color: #ffc107;">Allergies:</strong><br>
                    {{ Str::limit($patient->user->allergies ?? 'None recorded', 100) }}
                </div>
                <div style="font-size: 12px; color: #666; text-align: center; margin-top: 15px;">
                    Generated: ` + new Date().toLocaleDateString() + `<br>
                    Bokod Medical Center - Patient Management System
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-print"></i> Print Card',
        cancelButtonText: 'Close',
        confirmButtonColor: '#dc3545',
        width: '500px',
        showCloseButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.print();
        }
    });
}

// SIDEBAR TOGGLE FIX
function fixSidebarToggle() {
    console.log('Applying sidebar toggle fix...');
    
    // Remove any existing click handlers on the pushmenu button
    $('[data-widget="pushmenu"]').off('click');
    
    // Add our own click handler
    $(document).on('click', '[data-widget="pushmenu"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Sidebar toggle clicked');
        
        const $body = $('body');
        
        if ($(window).width() >= 992) {
            // Desktop: Toggle collapse
            if ($body.hasClass('sidebar-collapse')) {
                $body.removeClass('sidebar-collapse');
                localStorage.setItem('adminlte_sidebar_collapse', 'false');
                console.log('Sidebar expanded');
            } else {
                $body.addClass('sidebar-collapse');
                localStorage.setItem('adminlte_sidebar_collapse', 'true');
                console.log('Sidebar collapsed');
            }
        } else {
            // Mobile: Toggle open/close
            if ($body.hasClass('sidebar-open')) {
                $body.removeClass('sidebar-open');
                console.log('Mobile sidebar closed');
            } else {
                $body.addClass('sidebar-open');
                console.log('Mobile sidebar opened');
            }
        }
        
        return false;
    });
    
    // Initialize sidebar state for desktop
    if ($(window).width() >= 992) {
        const savedState = localStorage.getItem('adminlte_sidebar_collapse');
        console.log('Saved sidebar state:', savedState);
        
        if (savedState === 'true') {
            $('body').addClass('sidebar-collapse');
        } else if (savedState === 'false') {
            $('body').removeClass('sidebar-collapse');
        } else {
            // Default to collapsed for better UX
            $('body').addClass('sidebar-collapse');
            localStorage.setItem('adminlte_sidebar_collapse', 'true');
        }
    } else {
        // Mobile: ensure sidebar is closed initially
        $('body').removeClass('sidebar-open');
    }
    
    // Handle window resize
    $(window).on('resize.sidebar', function() {
        if ($(window).width() >= 992) {
            // Desktop mode
            $('body').removeClass('sidebar-open');
            const savedState = localStorage.getItem('adminlte_sidebar_collapse');
            if (savedState === 'true') {
                $('body').addClass('sidebar-collapse');
            } else {
                $('body').removeClass('sidebar-collapse');
            }
        } else {
            // Mobile mode
            $('body').removeClass('sidebar-collapse sidebar-open');
        }
    });
    
    // Mobile: Close sidebar when clicking outside on mobile
    $(document).on('click', function(e) {
        if ($(window).width() < 992 && $('body').hasClass('sidebar-open')) {
            const $target = $(e.target);
            if (!$target.closest('.main-sidebar').length && !$target.closest('[data-widget="pushmenu"]').length) {
                $('body').removeClass('sidebar-open');
                console.log('Mobile sidebar closed by outside click');
            }
        }
    });
    
    console.log('Sidebar toggle fix applied successfully');
}

</script>
@endsection
