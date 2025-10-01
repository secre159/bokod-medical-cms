@extends('adminlte::page')

@section('title', 'Dashboard - BOKOD CMS')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 text-dark">Dashboard</h1>
        <div class="text-right">
            <div class="current-datetime">
                <div class="current-time text-primary font-weight-bold" id="current-time" style="font-size: 1.1em;">
                    {{ \App\Helpers\TimezoneHelper::now()->format('g:i:s A') }}
                </div>
                <div class="current-date text-muted" style="font-size: 0.9em;">
                    <i class="fas fa-calendar-alt mr-1"></i>{{ \App\Helpers\TimezoneHelper::now()->format('l, F j, Y') }}
                </div>
                <div class="timezone-info text-muted" style="font-size: 0.75em;">
                    <i class="fas fa-globe-asia mr-1"></i>Philippine Time (GMT+8)
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    {{-- Welcome Card --}}
    <div class="card bg-gradient-primary text-white mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="card-title mb-2">Welcome back, {{ Auth::user()->display_name ?? Auth::user()->name }}! 👋</h2>
                    <p class="card-text mb-0 opacity-75">Here's what's happening in your clinic today</p>
                </div>
                <div class="col-auto">
                    <i class="fas fa-heartbeat fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Row --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info position-relative">
                <div class="inner">
                    <h3>{{ $stats['total_patients'] }}</h3>
                    <p>Total Patients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users position-relative">
                        @if($stats['total_patients'] > 0)
                            <span class="notification-dot-info" style="display: block;"></span>
                        @endif
                    </i>
                </div>
                <a href="{{ route('patients.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
                @if($stats['total_patients'] > 0)
                    <span class="badge badge-info badge-pill position-absolute" style="top: 8px; right: 8px; font-size: 10px; min-width: 18px; height: 18px; line-height: 18px; z-index: 20;">{{ $stats['total_patients'] }}</span>
                @endif
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success position-relative">
                <div class="inner">
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p>Active Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check position-relative">
                        @if($stats['total_users'] > 0)
                            <span class="notification-dot-success" style="display: block;"></span>
                        @endif
                    </i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
                @if($stats['total_users'] > 0)
                    <span class="badge badge-success badge-pill position-absolute" style="top: 8px; right: 8px; font-size: 10px; min-width: 18px; height: 18px; line-height: 18px; z-index: 20;">{{ $stats['total_users'] }}</span>
                @endif
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning position-relative">
                <div class="inner">
                    <h3>{{ $stats['appointments_today'] }}</h3>
                    <p>Appointments Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check position-relative">
                        @if($stats['appointments_today'] > 0)
                            <span class="notification-dot-warning" style="display: block;"></span>
                        @endif
                    </i>
                </div>
                <a href="{{ route('appointments.index', ['date_filter' => 'today']) }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
                @if($stats['appointments_today'] > 0)
                    <span class="badge badge-warning badge-pill position-absolute" style="top: 8px; right: 8px; font-size: 10px; min-width: 18px; height: 18px; line-height: 18px; z-index: 20;">{{ $stats['appointments_today'] }}</span>
                @endif
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger position-relative">
                <div class="inner">
                    <h3>{{ $stats['pending_approvals'] }}</h3>
                    <p>Pending Approvals</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock position-relative">
                        @if($stats['pending_approvals'] > 0)
                            <span class="notification-dot-danger" style="display: block;"></span>
                        @endif
                    </i>
                </div>
                <a href="{{ route('appointments.index', ['approval' => 'pending']) }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
                @if($stats['pending_approvals'] > 0)
                    <span class="badge badge-danger badge-pill position-absolute pulse-badge" style="top: 8px; right: 8px; font-size: 10px; min-width: 18px; height: 18px; line-height: 18px; z-index: 20;">{{ $stats['pending_approvals'] }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Fast Loading Secondary Stats --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <a href="{{ route('appointments.index', ['date_filter' => 'upcoming']) }}" class="info-box-link position-relative">
                <div class="info-box">
                    <span class="info-box-icon bg-info position-relative">
                        <i class="fas fa-calendar-day"></i>
                        <span class="notification-dot-info async-dot" id="tomorrow-dot" style="display: none;"></span>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tomorrow's Appointments</span>
                        <span class="info-box-number">Loading...</span>
                    </div>
                </div>
                <span class="badge badge-info badge-pill position-absolute async-badge" id="tomorrow-badge" style="top: 3px; right: 3px; font-size: 9px; min-width: 16px; height: 16px; line-height: 16px; display: none; z-index: 25;">0</span>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="{{ route('prescriptions.index', ['status' => 'active']) }}" class="info-box-link position-relative">
                <div class="info-box">
                    <span class="info-box-icon bg-success position-relative">
                        <i class="fas fa-prescription-bottle-alt"></i>
                        <span class="notification-dot-success async-dot" id="prescriptions-dot" style="display: none;"></span>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Prescriptions</span>
                        <span class="info-box-number">Loading...</span>
                    </div>
                </div>
                <span class="badge badge-success badge-pill position-absolute async-badge" id="prescriptions-badge" style="top: 3px; right: 3px; font-size: 9px; min-width: 16px; height: 16px; line-height: 16px; display: none; z-index: 25;">0</span>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="{{ route('medicines.stock') }}" class="info-box-link position-relative">
                <div class="info-box">
                    <span class="info-box-icon bg-warning position-relative">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="notification-dot-warning async-dot" id="lowstock-dot" style="display: none;"></span>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Low Stock Medicines</span>
                        <span class="info-box-number">Loading...</span>
                    </div>
                </div>
                <span class="badge badge-warning badge-pill position-absolute async-badge pulse-badge-warning" id="lowstock-badge" style="top: 3px; right: 3px; font-size: 9px; min-width: 16px; height: 16px; line-height: 16px; display: none; z-index: 25;">0</span>
            </a>
        </div>
        <div class="col-lg-3 col-6">
            <a href="{{ route('prescriptions.index', ['status' => 'expired']) }}" class="info-box-link position-relative">
                <div class="info-box">
                    <span class="info-box-icon bg-danger position-relative">
                        <i class="fas fa-hourglass-end"></i>
                        <span class="notification-dot-danger async-dot" id="expiring-dot" style="display: none;"></span>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Expiring Prescriptions</span>
                        <span class="info-box-number">Loading...</span>
                    </div>
                </div>
                <span class="badge badge-danger badge-pill position-absolute async-badge pulse-badge" id="expiring-badge" style="top: 3px; right: 3px; font-size: 9px; min-width: 16px; height: 16px; line-height: 16px; display: none; z-index: 25;">0</span>
            </a>
        </div>
    </div>

    {{-- Quick Actions & Upcoming Appointments --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('patients.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus mb-2"></i><br>
                                Add Patient
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('appointments.index') }}" class="btn btn-success btn-block">
                                <i class="fas fa-calendar-plus mb-2"></i><br>
                                New Appointment
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('medicines.index') }}" class="btn btn-warning btn-block position-relative" id="medicines-button">
                                <i class="fas fa-pills mb-2 position-relative">
                                    <span class="notification-dot" id="medicines-stock-dot" style="display: none;"></span>
                                </i><br>
                                Manage Medicines
                                <span class="badge badge-danger badge-pill position-absolute" id="medicines-stock-badge" style="top: 8px; right: 8px; display: none;">0</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('admin.messages.index') }}" class="btn btn-purple btn-block position-relative" id="messages-button">
                                <i class="fas fa-comments mb-2 position-relative">
                                    <span class="notification-dot" id="unread-dot" style="display: none;"></span>
                                </i><br>
                                Messages
                                <span class="badge badge-danger badge-pill position-absolute" id="unread-messages-badge" style="top: 8px; right: 8px; display: none;">0</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('reports.patients') }}" class="btn btn-info btn-block">
                                <i class="fas fa-chart-bar mb-2"></i><br>
                                View Reports
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('registrations.index') }}" class="btn btn-secondary btn-block position-relative" id="registrations-button">
                                <i class="fas fa-user-check mb-2 position-relative">
                                    <span class="notification-dot" id="pending-registrations-dot" style="display: none;"></span>
                                </i><br>
                                Registration Approvals
                                <span class="badge badge-warning badge-pill position-absolute" id="pending-registrations-badge" style="top: 8px; right: 8px; display: none;">0</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Pending Registrations Widget --}}
            @if($stats['pending_registrations'] > 0)
            <div class="card mb-3 border-left-warning" style="border-left: 5px solid #ffc107;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-clock mr-2 text-warning position-relative">
                            <span class="notification-pulse-warning"></span>
                        </i>
                        Pending Registrations
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-warning pulse-badge-warning">{{ $stats['pending_registrations'] }}</span>
                    </div>
                </div>
                <div class="card-body text-center py-3">
                    <p class="mb-2">{{ $stats['pending_registrations'] }} student{{ $stats['pending_registrations'] > 1 ? 's' : '' }} waiting for approval</p>
                    <a href="{{ route('registrations.index') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-check-circle mr-1"></i>Review Applications
                    </a>
                </div>
            </div>
            @endif
            
            {{-- New Messages Widget --}}
            @if($stats['recent_unread_messages']->count() > 0)
            <div class="card mb-3 border-left-danger" style="border-left: 5px solid #dc3545;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-envelope mr-2 text-primary position-relative">
                            <span class="notification-pulse"></span>
                        </i>
                        New Messages
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-danger pulse-badge">{{ $stats['recent_unread_messages']->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($stats['recent_unread_messages'] as $message)
                            <li class="list-group-item py-2">
                                <a href="{{ route('admin.messages.index', ['conversation' => $message->conversation->id]) }}" class="text-decoration-none message-notification-link">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <x-user-avatar :user="$message->sender" size="thumbnail" width="24px" height="24px" class="rounded-circle mr-2" />
                                                <strong class="small">{{ $message->sender->name }}</strong>
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
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-comments mr-1"></i>View All Messages
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            {{-- Upcoming Appointments Widget --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Upcoming Appointments
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">Next 3 Days</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($stats['upcoming_appointments']->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($stats['upcoming_appointments'] as $appointment)
                                <li class="list-group-item py-2 p-0">
                                    <a href="{{ route('appointments.show', $appointment->appointment_id) }}" class="text-decoration-none d-block p-2 appointment-link">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong class="d-block text-dark">{{ $appointment->patient->patient_name }}</strong>
                                                <small class="text-muted">
                                                    {{ $appointment->appointment_date->format('M j') }} at {{ $appointment->appointment_time->format('g:i A') }}
                                                </small>
                                            </div>
                                            <div class="text-right">
                                                @php
                                                    $appointmentDateTime = \Carbon\Carbon::parse($appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->appointment_time->format('H:i:s'));
                                                    $now = \App\Helpers\TimezoneHelper::now();
                                                    $isOverdue = $appointmentDateTime->lt($now);
                                                    
                                                    // Use Philippine timezone for date comparisons
                                                    $today = \App\Helpers\TimezoneHelper::now()->toDateString();
                                                    $tomorrow = \App\Helpers\TimezoneHelper::now()->addDay()->toDateString();
                                                    $appointmentDate = $appointment->appointment_date->toDateString();
                                                @endphp
                                                
                                                @if($isOverdue)
                                                    <span class="badge badge-danger badge-sm">Overdue</span>
                                                @elseif($appointmentDate == $today)
                                                    <span class="badge badge-info badge-sm">Today</span>
                                                @elseif($appointmentDate == $tomorrow)
                                                    <span class="badge badge-primary badge-sm">Tomorrow</span>
                                                @elseif($appointment->approval_status == 'pending')
                                                    <span class="badge badge-warning badge-sm">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer text-center">
                            <a href="{{ route('appointments.index', ['date_filter' => 'upcoming']) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-calendar mr-1"></i>View All Upcoming
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No upcoming appointments</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i>Schedule Appointment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <style>
        .small-box h3 {
            font-size: 2.2rem;
            font-weight: bold;
        }
        
        /* Small box styling and positioning */
        .small-box {
            border-radius: 0.25rem;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            overflow: hidden;
            position: relative;
        }
        
        .small-box .icon {
            position: absolute;
            top: auto;
            right: 15px;
            bottom: 15px;
            font-size: 3rem;
            color: rgba(255,255,255,0.15);
            overflow: hidden;
        }
        
        .small-box .icon i {
            position: relative;
        }
        
        .quick-action-btn {
            height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        /* Info boxes styling */
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border-radius: .25rem;
            background-color: #fff;
            display: flex;
            margin-bottom: 1rem;
            min-height: 80px;
            padding: .5rem;
            position: relative;
            width: 100%;
        }
        
        .info-box .info-box-icon {
            border-radius: .25rem;
            align-items: center;
            display: flex;
            font-size: 1.875rem;
            justify-content: center;
            text-align: center;
            width: 70px;
        }
        
        .info-box .info-box-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.8;
            margin-left: .5rem;
            padding: 0 10px;
        }
        
        .info-box .info-box-number {
            display: block;
            margin-top: .25rem;
            font-weight: 700;
            font-size: 1.125rem;
        }
        
        .info-box .info-box-text {
            display: block;
            font-size: .875rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Info box link styling */
        .info-box-link {
            color: inherit;
            text-decoration: none;
            display: block;
            position: relative;
        }
        
        .info-box-link:hover {
            color: inherit;
            text-decoration: none;
        }
        
        .info-box-link .info-box {
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
        }
        
        .info-box-link:hover .info-box {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,.15);
        }
        
        /* Async notification dots for info boxes */
        .info-box-icon .async-dot {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            border: 1px solid white;
            z-index: 15;
        }
        
        /* Appointment link styling */
        .appointment-link {
            color: inherit;
            transition: background-color 0.2s ease;
        }
        
        .appointment-link:hover {
            color: inherit;
            text-decoration: none;
            background-color: #f8f9fc;
        }
        
        .appointment-link:hover .text-dark {
            color: #495057 !important;
        }
        
        /* Upcoming appointments styling */
        .list-group-item {
            border-left: 0;
            border-right: 0;
            border-radius: 0;
            padding: 8px 15px;
        }
        
        .list-group-item:first-child {
            border-top: 0;
        }
        
        .list-group-item:last-child {
            border-bottom: 0;
        }
        
        .badge-sm {
            font-size: 0.7em;
            padding: 0.2em 0.4em;
        }
        
        /* Purple button for messages */
        .btn-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .btn-purple:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        /* Unread messages badge animation */
        #unread-messages-badge {
            animation: pulse 2s ease-in-out infinite;
            min-width: 20px;
            height: 20px;
            line-height: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        
        /* Notification dots for different colors - positioned within icons */
        .notification-dot, .notification-dot-danger {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 6px;
            height: 6px;
            background-color: #dc3545;
            border-radius: 50%;
            border: 1px solid white;
            animation: pulse-dot 1.5s ease-in-out infinite;
            z-index: 10;
        }
        
        .notification-dot-info {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 6px;
            height: 6px;
            background-color: #17a2b8;
            border-radius: 50%;
            border: 1px solid white;
            animation: pulse-dot-info 1.5s ease-in-out infinite;
            z-index: 10;
        }
        
        .notification-dot-success {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 6px;
            height: 6px;
            background-color: #28a745;
            border-radius: 50%;
            border: 1px solid white;
            animation: pulse-dot-success 1.5s ease-in-out infinite;
            z-index: 10;
        }
        
        .notification-dot-warning {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 6px;
            height: 6px;
            background-color: #ffc107;
            border-radius: 50%;
            border: 1px solid white;
            animation: pulse-dot-warning 1.5s ease-in-out infinite;
            z-index: 10;
        }
        
        /* Pulsing notification on envelope icon */
        .notification-pulse {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 10px;
            height: 10px;
            background-color: #dc3545;
            border-radius: 50%;
            animation: pulse-ring 2s ease-in-out infinite;
        }
        
        /* Warning pulse notification */
        .notification-pulse-warning {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 10px;
            height: 10px;
            background-color: #ffc107;
            border-radius: 50%;
            animation: pulse-ring-warning 2s ease-in-out infinite;
        }
        
        .notification-pulse::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border: 1px solid #dc3545;
            border-radius: 50%;
            animation: pulse-ring 2s ease-in-out infinite;
            animation-delay: 0.5s;
        }
        
        /* Enhanced pulse animation for badges */
        .pulse-badge {
            animation: pulse-badge 2s ease-in-out infinite;
        }
        
        .pulse-badge-warning {
            animation: pulse-badge-warning 2s ease-in-out infinite;
        }
        
        /* Messages button glow effect when unread */
        .messages-button-unread {
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.5) !important;
            border: 1px solid rgba(220, 53, 69, 0.3) !important;
        }
        
        /* Registrations button glow effect when pending */
        .registrations-button-pending {
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
            border: 1px solid rgba(255, 193, 7, 0.3) !important;
        }
        
        /* Medicines button glow effect when low stock */
        .medicines-button-low-stock {
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.5) !important;
            border: 1px solid rgba(220, 53, 69, 0.3) !important;
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1); 
                opacity: 1; 
            }
            50% { 
                transform: scale(1.1); 
                opacity: 0.8; 
            }
        }
        
        /* Current time display styling */
        .current-datetime {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 8px 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .current-time {
            font-family: 'Courier New', 'Monaco', monospace;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .timezone-info {
            opacity: 0.8;
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
        
        @keyframes pulse-dot-info {
            0%, 100% { 
                transform: scale(1); 
                opacity: 1; 
            }
            50% { 
                transform: scale(1.3); 
                opacity: 0.7; 
            }
        }
        
        @keyframes pulse-dot-success {
            0%, 100% { 
                transform: scale(1); 
                opacity: 1; 
            }
            50% { 
                transform: scale(1.3); 
                opacity: 0.7; 
            }
        }
        
        @keyframes pulse-dot-warning {
            0%, 100% { 
                transform: scale(1); 
                opacity: 1; 
            }
            50% { 
                transform: scale(1.3); 
                opacity: 0.7; 
            }
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        @keyframes pulse-badge {
            0%, 100% { 
                transform: scale(1); 
                box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
            }
            50% { 
                transform: scale(1.1); 
                box-shadow: 0 0 15px rgba(220, 53, 69, 0.8);
            }
        }
        
        @keyframes pulse-badge-warning {
            0%, 100% { 
                transform: scale(1); 
                box-shadow: 0 0 5px rgba(255, 193, 7, 0.5);
            }
            50% { 
                transform: scale(1.1); 
                box-shadow: 0 0 15px rgba(255, 193, 7, 0.8);
            }
        }
        
        @keyframes pulse-ring-warning {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        /* Message notification styling */
        .message-notification-link {
            color: inherit;
            transition: background-color 0.2s ease;
        }
        
        .message-notification-link:hover {
            color: inherit;
            text-decoration: none;
        }
        
        .list-group-item:hover {
            background-color: #f8f9fc;
        }
        
        .message-notification-link .list-group-item {
            border: none;
            transition: all 0.2s ease;
        }
        
        .message-notification-link:hover .list-group-item {
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* New message animation */
        .new-message-notification {
            animation: fadeInSlide 0.5s ease-out;
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
        
        /* Responsive improvements */
        @media (max-width: 767px) {
            .small-box h3 {
                font-size: 1.6rem;
            }
            
            .info-box {
                margin-bottom: 0.5rem;
            }
            
            .col-md-4 .card {
                margin-bottom: 1rem;
            }
        }
    </style>
@stop

@section('js')
    <script>
        console.log('BOKOD CMS Dashboard loaded successfully!');
        
        // Load secondary stats asynchronously for better performance
        $(document).ready(function() {
            console.log('Dashboard document ready - initializing...');
            
            // Only load if async flag is set
            @if(isset($stats['load_async']) && $stats['load_async'])
                console.log('🔄 Async loading enabled - starting secondary stats...');
                loadSecondaryStatsReal();
            @else
                console.log('⚠️ Async loading disabled - stats will not be loaded');
            @endif
            
            // Load unread messages count
            loadUnreadMessagesCount();
            
            // Load pending registrations count
            loadPendingRegistrationsCount();
            
            // Start polling for new messages
            startMessagePolling();
            
            console.log('Dashboard initialization complete.');
        });
        
        function loadSecondaryStats() {
            // Fallback function with mock data
            setTimeout(function() {
                // Update the loading placeholders with mock data
                $('.info-box-content').each(function(index) {
                    const $number = $(this).find('.info-box-number');
                    if ($number.text() === 'Loading...') {
                        const mockData = [5, 12, 3, 2]; // Mock data for demo
                        $number.text(mockData[index] || 0);
                        $number.removeClass('text-muted').addClass('text-dark');
                    }
                });
                
                console.log('Secondary stats loaded asynchronously (fallback)');
            }, 1000); // 1 second delay to show the loading effect
        }
        
        // Real AJAX implementation for loading secondary stats
        function loadSecondaryStatsReal() {
            console.log('Starting AJAX request for secondary stats...');
            
            $.ajax({
                url: '{{ route("dashboard.async-stats") }}',
                method: 'GET',
                beforeSend: function() {
                    console.log('AJAX request sent to: {{ route("dashboard.async-stats") }}');
                },
                success: function(data) {
                    console.log('AJAX response received:', data);
                    
                    // Check if elements exist before updating
                    const tomorrowElement = $('.info-box-content:eq(0) .info-box-number');
                    const prescriptionsElement = $('.info-box-content:eq(1) .info-box-number');
                    const lowStockElement = $('.info-box-content:eq(2) .info-box-number');
                    const expiringElement = $('.info-box-content:eq(3) .info-box-number');
                    
                    console.log('Found elements:', {
                        'tomorrow': tomorrowElement.length,
                        'prescriptions': prescriptionsElement.length,
                        'lowStock': lowStockElement.length,
                        'expiring': expiringElement.length
                    });
                    
                    // Update tomorrow appointments
                    if (tomorrowElement.length > 0) {
                        tomorrowElement.text(data.appointments_tomorrow);
                        updateNotificationIndicators('tomorrow', data.appointments_tomorrow);
                        console.log('Updated tomorrow appointments to:', data.appointments_tomorrow);
                    }
                    
                    // Update active prescriptions
                    if (prescriptionsElement.length > 0) {
                        prescriptionsElement.text(data.active_prescriptions);
                        updateNotificationIndicators('prescriptions', data.active_prescriptions);
                        console.log('Updated active prescriptions to:', data.active_prescriptions);
                    }
                    
                    // Update low stock medicines
                    if (lowStockElement.length > 0) {
                        lowStockElement.text(data.low_stock_medicines);
                        updateNotificationIndicators('lowstock', data.low_stock_medicines);
                        
                        // Also update medicines quick action button
                        updateMedicinesButtonNotification(data.low_stock_medicines);
                        
                        console.log('Updated low stock medicines to:', data.low_stock_medicines);
                    }
                    
                    // Update expiring prescriptions
                    if (expiringElement.length > 0) {
                        expiringElement.text(data.expiring_prescriptions);
                        updateNotificationIndicators('expiring', data.expiring_prescriptions);
                        console.log('Updated expiring prescriptions to:', data.expiring_prescriptions);
                    }
                    
                    console.log('✅ Secondary stats loaded successfully via AJAX');
                },
                error: function(xhr, status, error) {
                    console.log('❌ AJAX Error:', {
                        'status': xhr.status,
                        'statusText': xhr.statusText,
                        'responseText': xhr.responseText,
                        'error': error
                    });
                    console.log('Failed to load secondary stats - using fallback');
                    // Use fallback function on error
                    loadSecondaryStats();
                }
            });
        }
        
        // Poll for new messages every 30 seconds
        let messagePollingInterval;
        let lastMessageCount = {{ $stats['recent_unread_messages']->count() }};
        
        function startMessagePolling() {
            // Poll every 30 seconds
            messagePollingInterval = setInterval(function() {
                loadRecentMessages();
            }, 30000);
        }
        
        function loadRecentMessages() {
            $.ajax({
                url: '{{ route("dashboard.recent-messages") }}',
                method: 'GET',
                success: function(data) {
                    const currentCount = data.messages.length;
                    const unreadCount = data.unread_count;
                    
                    // Update unread messages badge and indicators
                    const badge = $('#unread-messages-badge');
                    const dot = $('#unread-dot');
                    const messagesButton = $('#messages-button');
                    
                    if (unreadCount > 0) {
                        badge.text(unreadCount > 99 ? '99+' : unreadCount).show();
                        dot.show();
                        messagesButton.addClass('messages-button-unread');
                        
                        // Also update quick actions messages button badge
                        const quickActionBadge = messagesButton.find('.badge');
                        if (quickActionBadge.length > 0) {
                            quickActionBadge.text(unreadCount > 99 ? '99+' : unreadCount).show();
                        }
                        
                        console.log('Unread messages:', unreadCount);
                    } else {
                        badge.hide();
                        dot.hide();
                        messagesButton.removeClass('messages-button-unread');
                        
                        // Hide quick actions messages button badge
                        const quickActionBadge = messagesButton.find('.badge');
                        if (quickActionBadge.length > 0) {
                            quickActionBadge.hide();
                        }
                    }
                    // Update messages widget
                    updateMessagesWidget(data.messages);
                    
                    // Show notification if new messages arrived
                    if (currentCount > lastMessageCount && lastMessageCount >= 0) {
                        showNewMessageNotification(currentCount - lastMessageCount);
                    }
                    
                    lastMessageCount = currentCount;
                },
                error: function() {
                    console.log('Failed to load recent messages');
                }
            });
        }
        
        function updateMessagesWidget(messages) {
            const messagesContainer = $('.col-md-4');
            const existingWidget = messagesContainer.find('.card').first();
            
            if (messages.length === 0) {
                // Remove messages widget if no messages
                if (existingWidget.find('.fa-envelope').length > 0) {
                    existingWidget.remove();
                }
                return;
            }
            
            // Build new messages HTML
            let messagesHtml = '';
            messages.forEach(function(message) {
                // Use the avatar_url provided by the API (includes initials fallback)
                const avatarHtml = `<img src="${message.sender.avatar_url}" class="rounded-circle mr-2" width="24" height="24" alt="${message.sender.name}">`;
                
                    
                const messageText = message.message || '[File attachment]';
                const truncatedMessage = messageText.length > 50 ? messageText.substring(0, 50) + '...' : messageText;
                
                const urgentBadge = message.priority === 'urgent' ? '<br><span class="badge badge-warning badge-sm mt-1">Urgent</span>' : '';
                
                messagesHtml += `
                    <li class="list-group-item py-2">
                        <a href="{{ route('admin.messages.index') }}?conversation=${message.conversation.id}" class="text-decoration-none message-notification-link">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        ${avatarHtml}
                                        <strong class="small">${message.sender.name}</strong>
                                    </div>
                                    <p class="mb-0 small text-muted" style="font-size: 0.8rem;">
                                        ${truncatedMessage}
                                    </p>
                                </div>
                                <div class="text-right ml-2">
                                    <small class="text-muted" style="font-size: 0.75rem;">${getTimeAgo(message.created_at)}</small>
                                    ${urgentBadge}
                                </div>
                            </div>
                        </a>
                    </li>
                `;
            });
            
            const widgetHtml = `
                <div class="card mb-3 new-message-notification border-left-danger" style="border-left: 5px solid #dc3545;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-envelope mr-2 text-primary position-relative">
                                <span class="notification-pulse"></span>
                            </i>
                            New Messages
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-danger pulse-badge">${messages.length}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            ${messagesHtml}
                        </ul>
                        <div class="card-footer text-center">
                            <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-comments mr-1"></i>View All Messages
                            </a>
                        </div>
                    </div>
                </div>
            `;
            
            // Replace or insert the widget
            if (existingWidget.find('.fa-envelope').length > 0) {
                existingWidget.replaceWith(widgetHtml);
            } else {
                messagesContainer.prepend(widgetHtml);
            }
        }
        
        function showNewMessageNotification(count) {
            // Show browser notification if supported
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('BOKOD CMS', {
                    body: `You have ${count} new message${count > 1 ? 's' : ''}`,
                    icon: '/favicon.ico',
                    tag: 'new-message'
                });
            }
            
            // Visual indication
            const messagesWidget = $('.col-md-4 .card').first();
            if (messagesWidget.length > 0) {
                messagesWidget.addClass('new-message-notification');
                setTimeout(function() {
                    messagesWidget.removeClass('new-message-notification');
                }, 500);
            }
        }
        
        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) {
                return 'just now';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `${minutes} min ago`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `${hours} hr ago`;
            } else {
                const days = Math.floor(diffInSeconds / 86400);
                return `${days} day${days > 1 ? 's' : ''} ago`;
            }
        }
        
        // Request notification permission on page load
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
        
        // Clean up polling on page unload
        window.addEventListener('beforeunload', function() {
            if (messagePollingInterval) {
                clearInterval(messagePollingInterval);
            }
        });
        
        // Load unread messages count for admin
        function loadUnreadMessagesCount() {
            $.ajax({
                url: '{{ route("admin.messages.unreadCount") }}',
                method: 'GET',
                success: function(data) {
                    const count = data.unread_count || 0;
                    const badge = $('#unread-messages-badge');
                    const dot = $('#unread-dot');
                    const messagesButton = $('#messages-button');
                    
                    if (count > 0) {
                        badge.text(count > 99 ? '99+' : count).show();
                        dot.show();
                        messagesButton.addClass('messages-button-unread');
                        console.log('Unread messages:', count);
                    } else {
                        badge.hide();
                        dot.hide();
                        messagesButton.removeClass('messages-button-unread');
                    }
                },
                error: function() {
                    console.log('Failed to load unread messages count');
                    // Hide badge on error
                    $('#unread-messages-badge').hide();
                }
            });
        }
        
        // Function to update notification indicators for async stats
        function updateNotificationIndicators(type, count) {
            const dot = $('#' + type + '-dot');
            const badge = $('#' + type + '-badge');
            
            if (count > 0) {
                dot.show();
                badge.text(count > 99 ? '99+' : count).show();
            } else {
                dot.hide();
                badge.hide();
            }
        }
        
        // Function to update medicines button notification
        function updateMedicinesButtonNotification(count) {
            const dot = $('#medicines-stock-dot');
            const badge = $('#medicines-stock-badge');
            const button = $('#medicines-button');
            
            if (count > 0) {
                dot.show();
                badge.text(count > 99 ? '99+' : count).show();
                button.addClass('medicines-button-low-stock');
            } else {
                dot.hide();
                badge.hide();
                button.removeClass('medicines-button-low-stock');
            }
        }
        
        // Load pending registrations count
        function loadPendingRegistrationsCount() {
            $.ajax({
                url: '{{ route("registrations.pendingCount") }}',
                method: 'GET',
                success: function(data) {
                    const count = data.pending_count || 0;
                    const badge = $('#pending-registrations-badge');
                    const dot = $('#pending-registrations-dot');
                    const registrationsButton = $('#registrations-button');
                    
                    if (count > 0) {
                        badge.text(count > 99 ? '99+' : count).show();
                        dot.show();
                        registrationsButton.addClass('registrations-button-pending');
                        
                        // Also update quick actions registrations button badge
                        const quickActionBadge = registrationsButton.find('.badge');
                        if (quickActionBadge.length > 0) {
                            quickActionBadge.text(count > 99 ? '99+' : count).show();
                        }
                        
                        console.log('Pending registrations:', count);
                    } else {
                        badge.hide();
                        dot.hide();
                        registrationsButton.removeClass('registrations-button-pending');
                        
                        // Hide quick actions registrations button badge
                        const quickActionBadge = registrationsButton.find('.badge');
                        if (quickActionBadge.length > 0) {
                            quickActionBadge.hide();
                        }
                    }
                },
                error: function() {
                    console.log('Failed to load pending registrations count');
                    // Hide badge on error
                    $('#pending-registrations-badge').hide();
                }
            });
        }
        
        // Real-time Philippine time clock
        function updateCurrentTime() {
            // Get current time directly in Philippine timezone
            const timeString = new Date().toLocaleString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
                timeZone: 'Asia/Manila'
            });
            
            // Update the time display
            $('#current-time').text(timeString);
        }
        
        // Initialize real-time clock
        $(document).ready(function() {
            updateCurrentTime(); // Update immediately
            setInterval(updateCurrentTime, 1000); // Update every second
        });
    </script>
@stop
