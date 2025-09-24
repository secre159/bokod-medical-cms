@extends('adminlte::page')

@section('title', 'My Appointments | Bokod CMS')

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.min.css">
@endsection

@section('adminlte_js')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.all.min.js"></script>

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-calendar-alt mr-2"></i>My Appointments
                <small class="text-muted">Manage your medical appointments</small>
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Appointments</li>
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
            $upcomingAppointments = $patient->appointments()->upcoming()->active()->with('patient')->get();
            $pendingAppointments = $patient->appointments()->pendingApproval()->active()->get();
            $cancelledAppointments = $patient->appointments()->where('status', 'cancelled')->orderBy('appointment_date', 'desc')->take(5)->get();
        @endphp

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $upcomingAppointments->count() }}</h3>
                        <p>Upcoming Appointments</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pendingAppointments->count() }}</h3>
                        <p>Pending Approval</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $patient->appointments()->where('status', 'completed')->count() }}</h3>
                        <p>Completed Visits</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $patient->appointments()->where('status', 'cancelled')->count() }}</h3>
                        <p>Cancelled</p>
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
                        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#bookAppointmentModal">
                            <i class="fas fa-plus mr-2"></i>Request New Appointment
                        </button>
                        <a href="{{ route('patient.history') }}" class="btn btn-info btn-lg ml-2">
                            <i class="fas fa-history mr-2"></i>View Full History
                        </a>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-tachometer-alt mr-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-check mr-2"></i>Upcoming Appointments
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($upcomingAppointments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcomingAppointments as $appointment)
                                        <tr>
                                            <td>
                                                <strong>{{ $appointment->appointment_date->format('M d, Y') }}</strong>
                                                <br>
                                                <span class="text-muted">{{ $appointment->appointment_time->format('h:i A') }}</span>
                                                <br>
                                                <small class="text-info">{{ $appointment->appointment_date->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <span title="{{ $appointment->reason }}">
                                                    {{ Str::limit($appointment->reason, 60) }}
                                                </span>
                                                @if($appointment->notes)
                                                    <br><small class="text-muted">Notes: {{ Str::limit($appointment->notes, 40) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($appointment->status == 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="badge badge-primary">Completed</span>
                                                @elseif($appointment->approval_status == 'pending')
                                                    <span class="badge badge-warning">Pending Approval</span>
                                                @elseif($appointment->approval_status == 'approved')
                                                    <span class="badge badge-success">Confirmed</span>
                                                @else
                                                    <span class="badge badge-danger">Declined</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="viewAppointment({{ $appointment->appointment_id }})"
                                                            title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($appointment->status == 'active' && $appointment->approval_status != 'declined' && $appointment->appointment_date > now())
                                                        <button type="button" class="btn btn-sm btn-warning" 
                                                                onclick="rescheduleAppointment({{ $appointment->appointment_id }})"
                                                                title="Reschedule">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                onclick="cancelAppointment({{ $appointment->appointment_id }})"
                                                                title="Cancel">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No Upcoming Appointments</h4>
                                <p class="text-muted">You don't have any upcoming appointments scheduled.</p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bookAppointmentModal">
                                    <i class="fas fa-plus mr-2"></i>Schedule Your First Appointment
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Appointments -->
        @if($pendingAppointments->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock mr-2 text-warning"></i>Pending Approval
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Requested Date & Time</th>
                                        <th>Reason</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingAppointments as $appointment)
                                    <tr>
                                        <td>
                                            <strong>{{ $appointment->appointment_date->format('M d, Y') }}</strong>
                                            <br>
                                            <span class="text-muted">{{ $appointment->appointment_time->format('h:i A') }}</span>
                                        </td>
                                        <td>{{ Str::limit($appointment->reason, 50) }}</td>
                                        <td>{{ $appointment->created_at->diffForHumans() }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="viewAppointment({{ $appointment->appointment_id }})"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="cancelAppointment({{ $appointment->appointment_id }})"
                                                    title="Cancel Request">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Cancelled Appointments -->
        @if($cancelledAppointments->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-times-circle mr-2 text-danger"></i>Recently Cancelled
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Reason</th>
                                        <th>Cancelled Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cancelledAppointments as $appointment)
                                    <tr>
                                        <td>
                                            <strong>{{ $appointment->appointment_date->format('M d, Y') }}</strong>
                                            <br>
                                            <span class="text-muted">{{ $appointment->appointment_time->format('h:i A') }}</span>
                                        </td>
                                        <td>
                                            <span title="{{ $appointment->reason }}">
                                                {{ Str::limit($appointment->reason, 50) }}
                                            </span>
                                            @if($appointment->cancellation_reason)
                                                <br><small class="text-info"><i class="fas fa-info-circle mr-1"></i><strong>Cancellation Reason:</strong> {{ Str::limit($appointment->cancellation_reason, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">Cancelled</span>
                                            <br><small class="text-muted">{{ $appointment->cancelled_at ? $appointment->cancelled_at->diffForHumans() : $appointment->updated_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="viewAppointment({{ $appointment->appointment_id }})"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('patient.history') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-history mr-2"></i>View Full History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Book Appointment Modal -->
        <div class="modal fade" id="bookAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="bookAppointmentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookAppointmentModalLabel">
                            <i class="fas fa-calendar-plus mr-2"></i>Request New Appointment
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="appointmentForm">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Clinic Hours:</strong> Monday to Friday, 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM
                                <br>Appointments are not available on weekends and Philippine holidays.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="appointment_date">Preferred Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                                        <small class="text-muted">Monday to Friday only</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="appointment_time">Preferred Time <span class="text-danger">*</span></label>
                                        <select class="form-control" id="appointment_time" name="appointment_time" required>
                                            <option value="">Select time...</option>
                                            <option value="08:00">8:00 AM</option>
                                            <option value="08:30">8:30 AM</option>
                                            <option value="09:00">9:00 AM</option>
                                            <option value="09:30">9:30 AM</option>
                                            <option value="10:00">10:00 AM</option>
                                            <option value="10:30">10:30 AM</option>
                                            <option value="11:00">11:00 AM</option>
                                            <option value="11:30">11:30 AM</option>
                                            <option value="13:00">1:00 PM</option>
                                            <option value="13:30">1:30 PM</option>
                                            <option value="14:00">2:00 PM</option>
                                            <option value="14:30">2:30 PM</option>
                                            <option value="15:00">3:00 PM</option>
                                            <option value="15:30">3:30 PM</option>
                                            <option value="16:00">4:00 PM</option>
                                            <option value="16:30">4:30 PM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reason">Reason for Visit <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Please describe your reason for the visit..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="notes">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional information or special requests..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-2"></i>Submit Request
                            </button>
                        </div>
                    </form>
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
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endsection

@section('js')
<script>
// Global functions that can be called from HTML onclick attributes
function viewAppointment(appointmentId) {
    $.ajax({
        url: '/api/patient/appointments/' + appointmentId + '/details',
        method: 'GET',
        success: function(response) {
            const appointment = response.appointment;
            
            // Show details in professional modal
            const appointmentDetails = `
                <div class="appointment-details">
                    <div class="row mb-2">
                        <div class="col-4"><strong>Date:</strong></div>
                        <div class="col-8">${new Date(appointment.appointment_date).toLocaleDateString()}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Time:</strong></div>
                        <div class="col-8">${appointment.appointment_time}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Status:</strong></div>
                        <div class="col-8"><span class="badge badge-info">${appointment.status}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Approval:</strong></div>
                        <div class="col-8"><span class="badge badge-primary">${appointment.approval_status}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4"><strong>Reason:</strong></div>
                        <div class="col-8">${appointment.reason}</div>
                    </div>
                    ${appointment.notes ? `
                    <div class="row mb-2">
                        <div class="col-4"><strong>Notes:</strong></div>
                        <div class="col-8">${appointment.notes}</div>
                    </div>` : ''}
                    ${appointment.diagnosis ? `
                    <div class="row mb-2">
                        <div class="col-4"><strong>Diagnosis:</strong></div>
                        <div class="col-8">${appointment.diagnosis}</div>
                    </div>` : ''}
                </div>
            `;
            
            if (typeof modalAlert === 'function') {
                modalAlert(appointmentDetails, 'Appointment Details', 'info');
            } else {
                // Fallback to SweetAlert2 if modalAlert is not available
                Swal.fire({
                    title: 'Appointment Details',
                    html: appointmentDetails,
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            }
        },
        error: function(xhr) {
            if (typeof toastr !== 'undefined') {
                toastr.error(xhr.responseJSON?.error || 'Failed to load appointment details.');
            } else {
                alert('Failed to load appointment details.');
            }
        }
    });
}

function rescheduleAppointment(appointmentId) {
    console.log('Reschedule appointment clicked for ID:', appointmentId);
    
    // Check if SweetAlert2 is available
    if (typeof Swal === 'undefined') {
        alert('SweetAlert2 is not loaded. Please refresh the page and try again.');
        return;
    }
    
    Swal.fire({
        title: 'Reschedule Appointment',
        html: `
            <div class="text-left">
                <div class="form-group">
                    <label for="reschedule_date">New Date:</label>
                    <input type="date" id="reschedule_date" class="form-control" min="${new Date().toISOString().split('T')[0]}">
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
                    <label for="reschedule_reason">Reason for Rescheduling <span class="text-danger">*</span>:</label>
                    <textarea id="reschedule_reason" class="form-control" rows="3" placeholder="Please explain why you need to reschedule this appointment..." required></textarea>
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
            
            if (!reason || reason.trim() === '') {
                Swal.showValidationMessage('Please provide a reason for rescheduling');
                return false;
            }
            
            if (reason.trim().length < 10) {
                Swal.showValidationMessage('Please provide a more detailed reason (at least 10 characters)');
                return false;
            }
            
            return { date: date, time: time, reason: reason.trim() };
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
                url: `/api/patient/appointments/${appointmentId}/reschedule`,
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
                        location.reload(); // Refresh the page to show updated appointment
                    });
                },
                error: function(xhr) {
                    console.error('Reschedule error:', xhr);
                    const errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message || 'Failed to submit reschedule request. Please try again.';
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
}

function cancelAppointment(appointmentId) {
    // Check if SweetAlert2 is available
    if (typeof Swal === 'undefined') {
        const reason = prompt('Please provide a reason for cancellation (optional):');
        if (confirm('Are you sure you want to cancel this appointment? This action cannot be undone.')) {
            // Proceed with cancellation using simple confirm dialog
            $.ajax({
                url: '/api/patient/appointments/' + appointmentId,
                method: 'DELETE',
                data: {
                    cancellation_reason: reason || '',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Your appointment has been cancelled.');
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || 'Failed to cancel appointment. Please try again.');
                }
            });
        }
        return;
    }
    
    Swal.fire({
        title: 'Cancel Appointment',
        html: `
            <div class="text-left">
                <p class="mb-3">Are you sure you want to cancel this appointment? This action cannot be undone.</p>
                <div class="form-group">
                    <label for="cancellation_reason">Reason for Cancellation (Optional):</label>
                    <textarea id="cancellation_reason" class="form-control" rows="3" placeholder="Please explain why you need to cancel this appointment..."></textarea>
                    <small class="text-muted">This will help us improve our services and may be useful for rescheduling.</small>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Yes, Cancel Appointment',
        cancelButtonText: 'Keep Appointment',
        confirmButtonColor: '#dc3545',
        width: '500px',
        preConfirm: () => {
            const reason = document.getElementById('cancellation_reason').value;
            return { reason: reason.trim() };
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
            
            $.ajax({
                url: '/api/patient/appointments/' + appointmentId,
                method: 'DELETE',
                data: {
                    cancellation_reason: reason || '',
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
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Failed to cancel appointment. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Wait for jQuery and SweetAlert2 to be available
function initPatientAppointments() {
    if (typeof $ === 'undefined') {
        console.log('jQuery not loaded for patient appointments, waiting...');
        setTimeout(initPatientAppointments, 100);
        return;
    }
    
    if (typeof Swal === 'undefined') {
        console.log('SweetAlert2 not loaded for patient appointments, waiting...');
        setTimeout(initPatientAppointments, 100);
        return;
    }
    
    $(document).ready(function() {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        $('#appointment_date').attr('min', today);
        
        // Philippine holidays for 2024 and 2025
        const philippineHolidays = [
            '2024-01-01', '2024-04-09', '2024-04-10', '2024-05-01', '2024-06-12', '2024-08-26', '2024-11-30', '2024-12-25', '2024-12-30',
            '2025-01-01', '2025-04-17', '2025-04-18', '2025-05-01', '2025-06-12', '2025-08-25', '2025-11-30', '2025-12-25', '2025-12-30'
        ];
        
        // Validate date selection
        $('#appointment_date').on('change', function() {
            const selectedDate = new Date(this.value);
            const dayOfWeek = selectedDate.getDay();
            const dateStr = this.value;
            
            // Check if weekend
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                if (typeof modalError === 'function') {
                    modalError('Appointments are only available Monday through Friday. Please select a weekday.', 'Invalid Date Selection');
                } else {
                    alert('Appointments are only available Monday through Friday. Please select a weekday.');
                }
                this.value = '';
                return;
            }
            
            // Check if holiday
            if (philippineHolidays.includes(dateStr)) {
                if (typeof modalWarning === 'function') {
                    modalWarning('The selected date is a Philippine holiday. Please choose a different date.', 'Holiday Restriction');
                } else {
                    alert('The selected date is a Philippine holiday. Please choose a different date.');
                }
                this.value = '';
                return;
            }
        });
        
        // Submit appointment form
        $('#appointmentForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                appointment_date: $('#appointment_date').val(),
                appointment_time: $('#appointment_time').val(),
                reason: $('#reason').val(),
                notes: $('#notes').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            $.ajax({
                url: '/api/patient/appointments',
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#bookAppointmentModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Appointment request submitted successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        alert('Appointment request submitted successfully!');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.values(errors).forEach(error => {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(error[0]);
                            } else {
                                alert(error[0]);
                            }
                        });
                    } else {
                        const errorMessage = xhr.responseJSON?.error || 'An error occurred. Please try again.';
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    }
                }
            });
        });
    });
}

// Initialize when page loads
initPatientAppointments();
</script>
@endsection
