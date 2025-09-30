@extends('adminlte::page')

@section('title', 'Patient Dashboard | Bokod CMS')

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Patient dashboard time display styling */
        .current-datetime {
            background: rgba(40, 167, 69, 0.1);
            border-radius: 8px;
            padding: 8px 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .current-time {
            font-family: 'Courier New', 'Monaco', monospace;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .timezone-info {
            opacity: 0.8;
        }
        
        /* Responsive time display */
        @media (max-width: 768px) {
            .current-datetime {
                margin-bottom: 0.5rem !important;
            }
            
            .current-time {
                font-size: 1em !important;
            }
            
            .current-date {
                font-size: 0.8em !important;
            }
            
            .timezone-info {
                font-size: 0.65em !important;
            }
        }
    </style>
@endsection

@section('content_header')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0">
                Welcome, {{ auth()->user()->name }}!
                <small class="text-muted">Patient Portal</small>
            </h1>
        </div>
        <div class="col-sm-6 text-right">
            <div class="current-datetime mb-2">
                <div class="current-time text-success font-weight-bold" id="patient-current-time" style="font-size: 1.1em;">
                    {{ \App\Helpers\TimezoneHelper::now()->format('g:i:s A') }}
                </div>
                <div class="current-date text-muted" style="font-size: 0.85em;">
                    <i class="fas fa-calendar-alt mr-1"></i>{{ \App\Helpers\TimezoneHelper::now()->format('l, F j, Y') }}
                </div>
                <div class="timezone-info text-muted" style="font-size: 0.7em;">
                    <i class="fas fa-globe-asia mr-1"></i>Philippine Time
                </div>
            </div>
            <ol class="breadcrumb float-sm-right mb-0" style="background: transparent; padding: 0;">
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
                    <a href="{{ route('patient.appointments') }}" class="small-box-footer">
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
                    <a href="{{ route('patient.history') }}" class="small-box-footer">
                        View History <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $patient->appointments ? $patient->appointments()->pendingApproval()->active()->count() : 0 }}</h3>
                        <p>Pending Approval</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('patient.appointments') }}" class="small-box-footer">
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

        <div class="row">
            <!-- Upcoming Appointments -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar mr-2"></i>Upcoming Appointments</h3>
                        <div class="card-tools">
                            <a href="{{ route('patient.appointments') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i>Request Appointment
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $upcomingAppointments = $patient->appointments ? $patient->appointments()->upcoming()->active()->take(5)->get() : collect();
                        @endphp
                        
                        @if($upcomingAppointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
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
                                            <td>{{ $appointment->appointment_time->format('h:i A') }}</td>
                                            <td>{{ Str::limit($appointment->reason, 40) }}</td>
                                            <td>
                                                @if($appointment->status == 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="badge badge-primary">Completed</span>
                                                @elseif($appointment->approval_status == 'pending')
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

            <!-- Patient Profile Summary -->
            <div class="col-md-4">
                {{-- New Messages Widget --}}
                @if($recentUnreadMessages->count() > 0)
                <div class="card mb-3 border-left-primary" style="border-left: 5px solid #007bff;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-envelope mr-2 text-primary position-relative">
                                <span class="notification-pulse-patient"></span>
                            </i>
                            New Messages
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-primary pulse-badge-patient">{{ $recentUnreadMessages->count() }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($recentUnreadMessages as $message)
                                <li class="list-group-item py-2">
                                    <a href="{{ route('patient.messages.index', ['conversation' => $message->conversation->id]) }}" class="text-decoration-none patient-message-notification-link">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <x-user-avatar :user="$message->sender" size="thumbnail" width="24px" height="24px" class="rounded-circle mr-2" />
                                                    <strong class="small">{{ $message->sender->name }}</strong>
                                                    @if($message->sender->role === 'admin')
                                                        <span class="badge badge-success badge-sm ml-1" style="font-size: 0.6em;">Staff</span>
                                                    @endif
                                                </div>
                                                <p class="mb-0 small text-muted" style="font-size: 0.8rem;">
                                                    {{ Str::limit($message->message ?: '[File attachment]', 50) }}
                                                </p>
                                            </div>
                                            <div class="text-right ml-2">
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $message->created_at->diffForHumans() }}</small>
                                                @if($message->priority === 'urgent')
                                                    <br><span class="badge badge-warning badge-sm mt-1">Urgent</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer text-center">
                            <a href="{{ route('patient.messages.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-comments mr-1"></i>View All Messages
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                {{-- Patient Profile Card --}}
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center profile-picture-container">
                            <x-user-avatar :user="$patient->user" class="profile-user-img img-fluid img-circle" width="100px" height="100px" style="border: 3px solid #adb5bd; margin: 0 auto;" />
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
                        </ul>
                        
                        <a href="{{ route('patient.profile.edit') }}" class="btn btn-primary btn-block">
                            <b><i class="fas fa-user-edit mr-2"></i>Update Profile</b>
                        </a>
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
                    <span class="info-box-icon bg-primary position-relative" id="patient-messages-icon">
                        <i class="fas fa-comments"></i>
                        @if($unreadMessagesCount > 0)
                            <span class="notification-dot" id="patient-unread-dot"></span>
                        @endif
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">
                            Messages
                            @if($unreadMessagesCount > 0)
                                <span class="badge badge-danger badge-pill ml-1" id="patient-unread-count">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>
                            @endif
                        </span>
                        <span class="info-box-number">
                            <a href="{{ route('patient.messages.index') }}" class="btn btn-sm btn-primary position-relative" id="patient-messages-button">
                                View Chat
                                @if($unreadMessagesCount > 0)
                                    <span class="badge badge-light badge-pill position-absolute" style="top: -8px; right: -8px; font-size: 0.7em;">{{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}</span>
                                @endif
                            </a>
                        </span>
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
    }
    
    /* Patient Message Notifications */
    .notification-dot {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 10px;
        height: 10px;
        background-color: #dc3545;
        border-radius: 50%;
        border: 2px solid white;
        animation: pulse-dot 1.5s ease-in-out infinite;
        z-index: 10;
    }
    
    .notification-pulse-patient {
        position: absolute;
        top: -3px;
        right: -3px;
        width: 10px;
        height: 10px;
        background-color: #007bff;
        border-radius: 50%;
        animation: pulse-ring-patient 2s ease-in-out infinite;
    }
    
    .notification-pulse-patient::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border: 1px solid #007bff;
        border-radius: 50%;
        animation: pulse-ring-patient 2s ease-in-out infinite;
        animation-delay: 0.5s;
    }
    
    .pulse-badge-patient {
        animation: pulse-badge-patient 2s ease-in-out infinite;
    }
    
    .patient-message-notification-link {
        color: inherit;
        transition: background-color 0.2s ease;
    }
    
    .patient-message-notification-link:hover {
        color: inherit;
        text-decoration: none;
    }
    
    .patient-message-notification-link .list-group-item {
        border: none;
        transition: all 0.2s ease;
    }
    
    .patient-message-notification-link:hover .list-group-item {
        transform: translateX(2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* New message animation */
    .new-patient-message-notification {
        animation: fadeInSlide 0.5s ease-out;
    }
    
    @keyframes pulse-dot {
        0%, 100% { 
            transform: scale(1); 
            opacity: 1; 
        }
        50% { 
            transform: scale(1.3); 
            opacity: 0.7; 
        }
    }
    
    @keyframes pulse-ring-patient {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        100% {
            transform: scale(2);
            opacity: 0;
        }
    }
    
    @keyframes pulse-badge-patient {
        0%, 100% { 
            transform: scale(1); 
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        50% { 
            transform: scale(1.1); 
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.8);
        }
    }
    
    @keyframes fadeInSlide {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Initialize patient dashboard
    console.log('Patient dashboard loaded');
    
    
    // Start polling for new messages
    startPatientMessagePolling();
    
    // PRELOADER FIX: Force hide preloader after page load to prevent stuck preloader
    setTimeout(function() {
        $('.preloader').fadeOut(500);
        $('.overlay').fadeOut(500);
        console.log('Preloader forcefully hidden after timeout');
    }, 2000); // 2 second timeout
    
    // Setup error handling for failed AJAX requests to prevent stuck preloaders
    $(document).ajaxError(function(event, xhr, settings, error) {
        console.error('AJAX Error:', error, 'URL:', settings.url);
        
        // Hide any lingering preloaders on AJAX errors
        $('.preloader, .overlay').fadeOut(300);
        
        // Show user-friendly error message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Connection Error',
                text: 'There was a problem loading dashboard data. Please refresh the page.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        }
    });
    
    // Poll for new messages every 30 seconds
    let patientMessagePollingInterval;
    let lastPatientMessageCount = {{ $recentUnreadMessages->count() }};
    
    function startPatientMessagePolling() {
        // Poll every 30 seconds
        patientMessagePollingInterval = setInterval(function() {
            loadPatientRecentMessages();
        }, 30000);
    }
    
    function loadPatientRecentMessages() {
        $.ajax({
            url: '{{ route("patient.messages.unreadCount") }}',
            method: 'GET',
            success: function(data) {
                const unreadCount = data.unread_count || 0;
                
                // Update all indicators
                updatePatientMessageIndicators(unreadCount);
                
                // Show notification if new messages arrived
                if (unreadCount > lastPatientMessageCount && lastPatientMessageCount >= 0) {
                    showPatientNewMessageNotification(unreadCount - lastPatientMessageCount);
                }
                
                lastPatientMessageCount = unreadCount;
            },
            error: function() {
                console.log('Failed to load patient messages count');
            }
        });
    }
    
    function updatePatientMessageIndicators(count) {
        const dot = $('#patient-unread-dot');
        const countBadge = $('#patient-unread-count');
        const buttonBadge = $('#patient-messages-button .badge');
        
        if (count > 0) {
            const displayCount = count > 99 ? '99+' : count;
            
            // Show/update dot
            if (dot.length === 0) {
                $('#patient-messages-icon').append('<span class="notification-dot" id="patient-unread-dot"></span>');
            } else {
                dot.show();
            }
            
            // Show/update count badge in messages text
            if (countBadge.length === 0) {
                $('.info-box-text').append('<span class="badge badge-danger badge-pill ml-1" id="patient-unread-count">' + displayCount + '</span>');
            } else {
                countBadge.text(displayCount).show();
            }
            
            // Show/update button badge
            if (buttonBadge.length === 0) {
                $('#patient-messages-button').append('<span class="badge badge-light badge-pill position-absolute" style="top: -8px; right: -8px; font-size: 0.7em;">' + displayCount + '</span>');
            } else {
                buttonBadge.text(displayCount).show();
            }
        } else {
            // Hide all indicators
            dot.hide();
            countBadge.hide();
            buttonBadge.hide();
        }
    }
    
    function showPatientNewMessageNotification(count) {
        // Show browser notification if supported
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('BOKOD CMS - New Message', {
                body: `You have ${count} new message${count > 1 ? 's' : ''} from medical staff`,
                icon: '/favicon.ico',
                tag: 'patient-new-message'
            });
        }
        
        // Visual indication on messages widget
        const messagesWidget = $('.col-md-4 .card').first();
        if (messagesWidget.length > 0) {
            messagesWidget.addClass('new-patient-message-notification');
            setTimeout(function() {
                messagesWidget.removeClass('new-patient-message-notification');
            }, 500);
        }
    }
    
    // Request notification permission on page load
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    // Real-time Philippine time clock for patient dashboard
    function updatePatientCurrentTime() {
        // Get current time directly in Philippine timezone
        const timeString = new Date().toLocaleString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
            timeZone: 'Asia/Manila'
        });
        
        // Update the time display
        $('#patient-current-time').text(timeString);
    }
    
    // Initialize real-time clock for patient
    updatePatientCurrentTime(); // Update immediately
    setInterval(updatePatientCurrentTime, 1000); // Update every second
    
    // Clean up polling on page unload
    window.addEventListener('beforeunload', function() {
        if (patientMessagePollingInterval) {
            clearInterval(patientMessagePollingInterval);
        }
    });
});
</script>
@endsection
