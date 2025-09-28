@extends('adminlte::page')

@section('title', 'My Medical History | Bokod CMS')

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-history mr-2"></i>My Medical History
                <small class="text-muted">View your complete medical records</small>
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Medical History</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @if(!auth()->user()->patient)
        <div class="alert alert-warning">
            <h4><i class="icon fas fa-exclamation-triangle"></i> Profile Incomplete!</h4>
            Your patient profile is not set up yet. Please contact the administrator to complete your registration.
        </div>
    @else
        @php
            $patient = auth()->user()->patient;
            $allAppointments = $patient->appointments()->with(['prescriptions.medicine'])->latest('appointment_date')->get();
            $completedAppointments = $allAppointments->where('status', 'completed');
            $cancelledAppointments = $allAppointments->where('status', 'cancelled');
            $totalPrescriptions = $patient->prescriptions ? $patient->prescriptions->count() : 0;
        @endphp

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $completedAppointments->count() }}</h3>
                        <p>Completed Visits</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $allAppointments->count() }}</h3>
                        <p>Total Appointments</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $totalPrescriptions }}</h3>
                        <p>Total Prescriptions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $cancelledAppointments->count() }}</h3>
                        <p>Cancelled Visits</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('patient.appointments') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-plus mr-2"></i>My Appointments
                        </a>
                        <button type="button" class="btn btn-info btn-lg ml-2" onclick="exportHistory()">
                            <i class="fas fa-download mr-2"></i>Export History
                        </button>
                        <button type="button" class="btn btn-success btn-lg ml-2" onclick="printHistory()">
                            <i class="fas fa-print mr-2"></i>Print Records
                        </button>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-tachometer-alt mr-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter mr-2"></i>Filter Records
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterYear">Year</label>
                                    <select class="form-control" id="filterYear">
                                        <option value="">All Years</option>
                                        @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterStatus">Status</label>
                                    <select class="form-control" id="filterStatus">
                                        <option value="">All Statuses</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="active">Active</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterType">View Type</label>
                                    <select class="form-control" id="filterType">
                                        <option value="all">All Records</option>
                                        <option value="appointments">Appointments Only</option>
                                        <option value="prescriptions">Prescriptions Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                            <i class="fas fa-search mr-2"></i>Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="clearFilters()">
                                            <i class="fas fa-times mr-2"></i>Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical History Timeline -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-timeline mr-2"></i>Medical History Timeline
                        </h3>
                    </div>
                    <div class="card-body" id="historyContent">
                        @if($allAppointments->count() > 0)
                            <div class="timeline">
                                @php
                                    $currentYear = null;
                                @endphp
                                @foreach($allAppointments as $appointment)
                                    @php
                                        $appointmentYear = $appointment->appointment_date->format('Y');
                                    @endphp
                                    
                                    @if($currentYear !== $appointmentYear)
                                        @php $currentYear = $appointmentYear; @endphp
                                        <div class="time-label">
                                            <span class="bg-blue">{{ $appointmentYear }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="appointment-record" data-status="{{ $appointment->status }}" data-year="{{ $appointmentYear }}">
                                        @if($appointment->status == 'completed')
                                            <i class="fas fa-check-circle bg-success"></i>
                                        @elseif($appointment->status == 'cancelled')
                                            <i class="fas fa-times-circle bg-danger"></i>
                                        @else
                                            <i class="fas fa-calendar bg-info"></i>
                                        @endif
                                        
                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="fas fa-clock"></i> 
                                                {{ $appointment->appointment_date->format('M d, Y') }} at 
                                                {{ $appointment->appointment_time->format('h:i A') }}
                                            </span>
                                            <h3 class="timeline-header">
                                                @if($appointment->status == 'completed')
                                                    <i class="fas fa-check-circle text-success mr-2"></i>Visit Completed
                                                @elseif($appointment->status == 'cancelled')
                                                    <i class="fas fa-times-circle text-danger mr-2"></i>Appointment Cancelled
                                                @else
                                                    <i class="fas fa-calendar text-info mr-2"></i>Scheduled Appointment
                                                @endif
                                                
                                                <span class="float-right">
                                                    @if($appointment->approval_status == 'approved')
                                                        <span class="badge badge-success">Approved</span>
                                                    @elseif($appointment->approval_status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-danger">Declined</span>
                                                    @endif
                                                </span>
                                            </h3>
                                            
                                            <div class="timeline-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h5><strong>Reason for Visit:</strong></h5>
                                                        <p>{{ $appointment->reason }}</p>
                                                        
                                                        @if($appointment->notes)
                                                            <h5><strong>Notes:</strong></h5>
                                                            <p>{{ $appointment->notes }}</p>
                                                        @endif
                                                        
                                                        @if($appointment->diagnosis)
                                                            <h5><strong>Diagnosis:</strong></h5>
                                                            <p>{{ $appointment->diagnosis }}</p>
                                                        @endif
                                                        
                                                        @if($appointment->treatment_notes)
                                                            <h5><strong>Treatment:</strong></h5>
                                                            <p>{{ $appointment->treatment_notes }}</p>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <div class="info-box bg-light">
                                                            <span class="info-box-icon bg-info">
                                                                <i class="fas fa-info"></i>
                                                            </span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Appointment ID</span>
                                                                <span class="info-box-number">{{ $appointment->appointment_id }}</span>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-info" style="width: 100%"></div>
                                                                </div>
                                                                <span class="progress-description">
                                                                    Status: {{ ucfirst($appointment->status) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Prescriptions for this appointment -->
                                                @if($appointment->prescriptions && $appointment->prescriptions->count() > 0)
                                                    <div class="mt-3">
                                                        <h5><strong><i class="fas fa-pills mr-2"></i>Prescriptions:</strong></h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Medicine</th>
                                                                        <th>Dosage</th>
                                                                        <th>Instructions</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($appointment->prescriptions as $prescription)
                                                                    <tr>
                                                                        <td>{{ $prescription->medicine->name ?? 'N/A' }}</td>
                                                                        <td>{{ $prescription->dosage }}</td>
                                                                        <td>{{ $prescription->instructions }}</td>
                                                                        <td>
                                                                            @if($prescription->status == 'active')
                                                                                <span class="badge badge-success">Active</span>
                                                                            @elseif($prescription->status == 'completed')
                                                                                <span class="badge badge-info">Completed</span>
                                                                            @else
                                                                                <span class="badge badge-warning">{{ ucfirst($prescription->status) }}</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="timeline-footer">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-plus mr-1"></i>
                                                    Created: {{ $appointment->created_at->format('M d, Y h:i A') }}
                                                    @if($appointment->updated_at != $appointment->created_at)
                                                        | <i class="fas fa-edit mr-1"></i>
                                                        Updated: {{ $appointment->updated_at->format('M d, Y h:i A') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <div>
                                    <i class="fas fa-flag bg-gray"></i>
                                    <div class="timeline-item">
                                        <div class="timeline-body">
                                            <div class="text-center">
                                                <i class="fas fa-user-plus fa-2x text-muted mb-2"></i>
                                                <p class="text-muted">Patient registration completed</p>
                                                <small class="text-muted">{{ $patient->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Medical History Found</h4>
                                <p class="text-muted">You haven't had any appointments yet. Start by booking your first appointment.</p>
                                <a href="{{ route('patient.appointments') }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i>Book Your First Appointment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Summary Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-md mr-2"></i>Patient Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <x-user-avatar 
                                        :user="auth()->user()" 
                                        size="large" 
                                        width="100px" 
                                        height="100px"
                                        class="profile-user-img img-fluid img-circle" />
                                    <h3 class="profile-username">{{ $patient->patient_name }}</h3>
                                    <p class="text-muted">Patient ID: {{ $patient->id }}</p>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong><i class="fas fa-envelope mr-1"></i> Email:</strong>
                                        <p class="text-muted">{{ $patient->email }}</p>
                                        
                                        <strong><i class="fas fa-phone mr-1"></i> Phone:</strong>
                                        <p class="text-muted">{{ $patient->phone_number }}</p>
                                        
                                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Address:</strong>
                                        <p class="text-muted">{{ $patient->address ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong><i class="fas fa-birthday-cake mr-1"></i> Date of Birth:</strong>
                                        <p class="text-muted">
                                            @if($patient->date_of_birth)
                                                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') }}
                                                ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} years old)
                                            @else
                                                Not provided
                                            @endif
                                        </p>
                                        
                                        <strong><i class="fas fa-venus-mars mr-1"></i> Gender:</strong>
                                        <p class="text-muted">{{ ucfirst($patient->gender ?? 'Not provided') }}</p>
                                        
                                        <strong><i class="fas fa-user-plus mr-1"></i> Member Since:</strong>
                                        <p class="text-muted">{{ $patient->created_at->format('M d, Y') }}</p>
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
    
    .timeline > div {
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
        padding: 10px;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    }
    
    .timeline-item:before {
        content: '';
        position: absolute;
        left: -15px;
        top: 10px;
        width: 0;
        height: 0;
        border-top: 10px solid transparent;
        border-bottom: 10px solid transparent;
        border-right: 15px solid #fff;
    }
    
    .time-label > span {
        font-weight: 600;
        padding: 5px 10px;
        display: inline-block;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .timeline > div > .fas,
    .timeline > div > .fab {
        position: absolute;
        left: 15px;
        top: 15px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        line-height: 20px;
        text-align: center;
        font-size: 12px;
        color: #fff;
    }
    
    .profile-user-img {
        width: 100px;
        height: 100px;
        border: 3px solid #adb5bd;
        margin: 0 auto;
        padding: 3px;
    }
    
    @media print {
        .btn, .card-tools, .breadcrumb, .content-header {
            display: none !important;
        }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('Patient history loaded');
});

function applyFilters() {
    const year = $('#filterYear').val();
    const status = $('#filterStatus').val();
    const type = $('#filterType').val();
    
    $('.appointment-record').hide();
    
    $('.appointment-record').each(function() {
        const record = $(this);
        const recordYear = record.data('year').toString();
        const recordStatus = record.data('status');
        
        let showRecord = true;
        
        if (year && recordYear !== year) {
            showRecord = false;
        }
        
        if (status && recordStatus !== status) {
            showRecord = false;
        }
        
        if (showRecord) {
            record.show();
        }
    });
    
    // Show/hide year labels based on visible records
    $('.time-label').each(function() {
        const yearLabel = $(this);
        const nextRecords = yearLabel.nextUntil('.time-label', '.appointment-record:visible');
        
        if (nextRecords.length === 0) {
            yearLabel.hide();
        } else {
            yearLabel.show();
        }
    });
}

function clearFilters() {
    $('#filterYear, #filterStatus, #filterType').val('');
    $('.appointment-record, .time-label').show();
}

function exportHistory() {
    // Implement export functionality
    alert('Export functionality will be implemented here');
}

function printHistory() {
    window.print();
}
</script>
@endsection