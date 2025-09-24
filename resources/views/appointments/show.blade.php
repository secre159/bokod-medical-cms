@extends('adminlte::page')

@section('title', 'Appointment Details | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Appointment Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
                <li class="breadcrumb-item active">View Details</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    {{-- Include modal-based alerts instead of inline alerts --}}
    @include('components.modal-alerts')

    <div class="row">
        <!-- Main Appointment Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>Appointment Information
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('appointments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Back to List
                        </a>
                        @if($appointment->status === 'active' && !$appointment->isOverdue())
                            <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Status Badges -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap">
                                <div class="mr-3 mb-2">
                                    <strong>Status:</strong>
                                    <span class="badge badge-lg badge-{{ $appointment->status === 'active' ? 'primary' : ($appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                        @if($appointment->status === 'cancelled')
                                            <i class="fas fa-times mr-1"></i>
                                        @elseif($appointment->status === 'completed')
                                            <i class="fas fa-check mr-1"></i>
                                        @elseif($appointment->status === 'active')
                                            <i class="fas fa-calendar mr-1"></i>
                                        @endif
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                                <div class="mr-3 mb-2">
                                    <strong>Approval:</strong>
                                    <span class="badge badge-lg badge-{{ $appointment->approval_status === 'approved' ? 'success' : ($appointment->approval_status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($appointment->approval_status) }}
                                    </span>
                                </div>
                                @if($appointment->reschedule_status !== 'none')
                                    <div class="mr-3 mb-2">
                                        <strong>Reschedule:</strong>
                                        <span class="badge badge-lg badge-{{ $appointment->reschedule_status === 'pending' ? 'warning' : ($appointment->reschedule_status === 'approved' ? 'info' : 'dark') }}">
                                            {{ ucfirst($appointment->reschedule_status) }}
                                        </span>
                                    </div>
                                @endif
                                @if($appointment->isOverdue() && $appointment->status !== 'completed')
                                    <div class="mr-3 mb-2">
                                        <span class="badge badge-lg badge-danger">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Appointment Date</span>
                                    <span class="info-box-number">{{ $appointment->appointment_date->format('M d, Y') }}</span>
                                    <span class="progress-description">{{ $appointment->appointment_date->format('l') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Appointment Time</span>
                                    <span class="info-box-number">{{ $appointment->appointment_time->format('g:i A') }}</span>
                                    <span class="progress-description">30-minute appointment</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reason Section -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-comment-medical mr-2"></i>Reason for Appointment</h3>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $appointment->reason }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section (if exists) -->
                    @if($appointment->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            @php
                                $isRescheduled = strpos($appointment->notes, 'Rescheduled by patient') !== false;
                                $cardColor = $isRescheduled ? 'warning' : 'secondary';
                                $iconClass = $isRescheduled ? 'fas fa-calendar-alt' : 'fas fa-sticky-note';
                                $cardTitle = $isRescheduled ? 'Appointment History & Reschedule Details' : 'Notes';
                            @endphp
                            <div class="card card-outline card-{{ $cardColor }}">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="{{ $iconClass }} mr-2"></i>{{ $cardTitle }}</h3>
                                    @if($isRescheduled)
                                        <div class="card-tools">
                                            <span class="badge badge-warning">Reschedule Request</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($isRescheduled)
                                        <div class="alert alert-warning">
                                            <h5><i class="fas fa-exclamation-triangle mr-2"></i>Patient Reschedule Request</h5>
                                            <p class="mb-0">This appointment has been rescheduled by the patient. See details below:</p>
                                        </div>
                                    @endif
                                    <div style="white-space: pre-wrap; font-family: monospace; background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid {{ $isRescheduled ? '#ffc107' : '#6c757d' }};">
                                        {{ $appointment->notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Reschedule Information (if exists) -->
                    @if($appointment->reschedule_status !== 'none' && $appointment->requested_date)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Reschedule Request</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Requested Date:</strong><br>
                                            {{ $appointment->requested_date ? \Carbon\Carbon::parse($appointment->requested_date)->format('M d, Y') : '-' }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Requested Time:</strong><br>
                                            {{ $appointment->requested_time ? \Carbon\Carbon::parse($appointment->requested_time)->format('g:i A') : '-' }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Status:</strong><br>
                                            <span class="badge badge-{{ $appointment->reschedule_status === 'pending' ? 'warning' : ($appointment->reschedule_status === 'approved' ? 'success' : 'danger') }}">
                                                {{ ucfirst($appointment->reschedule_status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($appointment->reschedule_reason)
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <strong>Reason for Reschedule:</strong><br>
                                                <p class="mb-0 text-muted">{{ $appointment->reschedule_reason }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Cancellation Information (if exists) -->
                    @if($appointment->status === 'cancelled')
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-danger">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-times-circle mr-2"></i>Cancellation Information</h3>
                                    <div class="card-tools">
                                        <span class="badge badge-danger">Cancelled</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong><i class="fas fa-calendar-times mr-2"></i>Cancelled Date:</strong><br>
                                            <span class="text-muted">{{ $appointment->cancelled_at ? $appointment->cancelled_at->format('M d, Y g:i A') : $appointment->updated_at->format('M d, Y g:i A') }}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong><i class="fas fa-user mr-2"></i>Cancelled By:</strong><br>
                                            <span class="text-muted">
                                                @if(strpos($appointment->notes, 'Cancelled by patient') !== false)
                                                    Patient ({{ $appointment->patient->patient_name }})
                                                @else
                                                    Administrator
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    @if($appointment->cancellation_reason)
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="alert alert-light border-left-danger">
                                                    <strong><i class="fas fa-comment-dots mr-2"></i>Reason for Cancellation:</strong><br>
                                                    <p class="mb-0 mt-2">{{ $appointment->cancellation_reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-light">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-history mr-2"></i>Appointment History</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Created:</strong><br>
                                            <span class="text-muted">{{ $appointment->created_at->format('M d, Y g:i A') }}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Last Updated:</strong><br>
                                            <span class="text-muted">{{ $appointment->updated_at->format('M d, Y g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Information Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>Patient Information
                    </h3>
                </div>
                <div class="card-body">
                    @if($appointment->patient)
                        <div class="text-center mb-3">
                            <div class="user-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                                {{ strtoupper(substr($appointment->patient->patient_name, 0, 1)) }}
                            </div>
                            <h4 class="mt-2 mb-0">{{ $appointment->patient->patient_name }}</h4>
                            <p class="text-muted small">Patient ID: {{ $appointment->patient->id }}</p>
                        </div>

                        <div class="border-top pt-3">
                            <div class="mb-3">
                                <strong><i class="fas fa-envelope mr-2 text-muted"></i>Email:</strong><br>
                                <span class="text-muted">{{ $appointment->patient->email ?: 'Not provided' }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <strong><i class="fas fa-phone mr-2 text-muted"></i>Phone:</strong><br>
                                <span class="text-muted">{{ $appointment->patient->phone_number ?: 'Not provided' }}</span>
                            </div>
                            
                            @if($appointment->patient->address)
                            <div class="mb-3">
                                <strong><i class="fas fa-map-marker-alt mr-2 text-muted"></i>Address:</strong><br>
                                <span class="text-muted">{{ $appointment->patient->address }}</span>
                            </div>
                            @endif

                            @if($appointment->patient->date_of_birth)
                            <div class="mb-3">
                                <strong><i class="fas fa-birthday-cake mr-2 text-muted"></i>Date of Birth:</strong><br>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($appointment->patient->date_of_birth)->format('M d, Y') }}</span>
                            </div>
                            @endif

                            @if($appointment->patient->gender)
                            <div class="mb-3">
                                <strong><i class="fas fa-user mr-2 text-muted"></i>Gender:</strong><br>
                                <span class="text-muted">{{ ucfirst($appointment->patient->gender) }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Patient Actions -->
                        <div class="border-top pt-3">
                            <a href="{{ route('patients.show', $appointment->patient) }}" class="btn btn-outline-primary btn-block btn-sm">
                                <i class="fas fa-eye mr-1"></i>View Patient Profile
                            </a>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p>Patient information not available</p>
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
                    @if($appointment->status === 'active' && !$appointment->isOverdue())
                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-primary btn-block btn-sm mb-2">
                            <i class="fas fa-edit mr-1"></i>Edit Appointment
                        </a>
                        
                        <button type="button" class="btn btn-success btn-block btn-sm mb-2" onclick="completeAppointment()">
                            <i class="fas fa-check mr-1"></i>Mark as Completed
                        </button>
                        
                        <button type="button" class="btn btn-warning btn-block btn-sm mb-2" onclick="rescheduleAppointment()">
                            <i class="fas fa-calendar-alt mr-1"></i>Request Reschedule
                        </button>
                        
                        <button type="button" class="btn btn-secondary btn-block btn-sm mb-2" onclick="cancelAppointment()">
                            <i class="fas fa-times mr-1"></i>Cancel Appointment
                        </button>
                        
                        @if($appointment->approval_status === 'pending')
                            <div class="btn-group d-flex mb-2" role="group">
                                <button type="button" class="btn btn-success btn-sm" onclick="approveAppointment()">
                                    <i class="fas fa-thumbs-up mr-1"></i>Approve
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectAppointment()">
                                    <i class="fas fa-thumbs-down mr-1"></i>Reject
                                </button>
                            </div>
                        @endif
                        
                        @if($appointment->reschedule_status === 'pending')
                            <div class="btn-group d-flex mb-2" role="group">
                                <button type="button" class="btn btn-success btn-sm" onclick="approveReschedule()">
                                    <i class="fas fa-calendar-check mr-1"></i>Approve Reschedule
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectReschedule()">
                                    <i class="fas fa-calendar-times mr-1"></i>Reject Reschedule
                                </button>
                            </div>
                        @endif
                        
                        <button type="button" class="btn btn-danger btn-block btn-sm" onclick="deleteAppointment()">
                            <i class="fas fa-trash mr-1"></i>Delete Permanently
                        </button>
                    @elseif($appointment->status === 'cancelled')
                        <button type="button" class="btn btn-danger btn-block btn-sm" onclick="deleteAppointment()">
                            <i class="fas fa-trash mr-1"></i>Delete Permanently
                        </button>
                    @elseif($appointment->status === 'completed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-2"></i>
                            This appointment has been completed.
                        </div>
                        <button type="button" class="btn btn-danger btn-block btn-sm" onclick="deleteAppointment()">
                            <i class="fas fa-trash mr-1"></i>Delete Permanently
                        </button>
                    @endif

                    <a href="{{ route('appointments.calendar') }}" class="btn btn-info btn-block btn-sm">
                        <i class="fas fa-calendar mr-1"></i>View Calendar
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
<style>
.user-avatar {
    width: 60px;
    height: 60px;
    font-size: 24px;
}
.badge-lg {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}
.card-outline {
    border-width: 2px;
}
.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}
.alert-light {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
}
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Complete appointment
function completeAppointment() {
    MessageModal.confirm('Are you sure you want to mark this appointment as completed?', function() {
        $.ajax({
            url: '{{ route("appointments.complete", $appointment) }}',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    MessageModal.success(response.message, { autoDismiss: 2000 });
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    MessageModal.error(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                MessageModal.error(message);
            }
        });
    });
}

// Cancel appointment
function cancelAppointment() {
    MessageModal.confirm('Are you sure you want to cancel this appointment?', function() {
        $.ajax({
            url: '{{ route("appointments.cancel", $appointment) }}',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    MessageModal.success(response.message, { autoDismiss: 2000 });
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    MessageModal.error(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                MessageModal.error(message);
            }
        });
    });
}

// Approve appointment
function approveAppointment() {
    MessageModal.confirm('Are you sure you want to approve this appointment?', function() {
        $.ajax({
            url: '{{ route("appointments.approve", $appointment) }}',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                MessageModal.success('Appointment approved successfully!', { autoDismiss: 2000 });
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                MessageModal.error(message);
            }
        });
    });
}

// Reject appointment
function rejectAppointment() {
    MessageModal.confirm('Are you sure you want to reject this appointment?', function() {
        $.ajax({
            url: '{{ route("appointments.reject", $appointment) }}',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                MessageModal.success('Appointment rejected successfully!', { autoDismiss: 2000 });
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                MessageModal.error(message);
            }
        });
    });
}

// Delete appointment permanently
function deleteAppointment() {
    MessageModal.confirm('Are you sure you want to permanently delete this appointment? This action cannot be undone.', function() {
        $.ajax({
            url: '{{ route("appointments.delete", $appointment) }}',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    MessageModal.success(response.message, { autoDismiss: 2000 });
                    setTimeout(function() {
                        window.location.href = '{{ route("appointments.index") }}';
                    }, 2000);
                } else {
                    MessageModal.error(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                MessageModal.error(message);
            }
        });
    });
}

// Reschedule appointment (placeholder - would need a modal)
function rescheduleAppointment() {
    // This would typically open a modal for reschedule request
    // For now, redirect to edit page
    window.location.href = '{{ route("appointments.edit", $appointment) }}';
}

// Approve reschedule request
function approveReschedule() {
    MessageModal.confirm('Are you sure you want to approve this reschedule request? The appointment will be moved to the requested date and time.', function() {
        $.ajax({
            url: '{{ route("appointments.approveReschedule", $appointment) }}',
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    MessageModal.success(response.message, function() {
                        location.reload();
                    });
                } else {
                    MessageModal.error(response.message || 'Failed to approve reschedule request');
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Network error occurred';
                MessageModal.error('Error: ' + errorMessage);
            }
        });
    });
}

// Reject reschedule request
function rejectReschedule() {
    MessageModal.prompt('Please provide a reason for rejecting this reschedule request (optional):', '', function(rejectionReason) {
        $.ajax({
            url: '{{ route("appointments.rejectReschedule", $appointment) }}',
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                rejection_reason: rejectionReason
            },
            success: function(response) {
                if (response.success) {
                    MessageModal.success(response.message, function() {
                        location.reload();
                    });
                } else {
                    MessageModal.error(response.message || 'Failed to reject reschedule request');
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Network error occurred';
                MessageModal.error('Error: ' + errorMessage);
            }
        });
    });
}

// All alerts now use MessageModal instead of inline alerts
</script>
@endsection