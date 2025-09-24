@extends('adminlte::page')

@section('title', 'Edit Appointment | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Edit Appointment</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    {{-- Include modal-based alerts instead of inline alerts --}}
    @include('components.modal-alerts')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit mr-2"></i>Edit Appointment Details
            </h3>
            <div class="card-tools">
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back to List
                </a>
                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye mr-1"></i>View Details
                </a>
            </div>
        </div>
        
        <form action="{{ route('appointments.update', $appointment) }}" method="POST" id="appointmentForm">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Current Status Info -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Current Appointment Status</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Status:</strong><br>
                                    <span class="badge badge-{{ $appointment->status === 'active' ? 'primary' : ($appointment->status === 'completed' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Approval:</strong><br>
                                    <span class="badge badge-{{ $appointment->approval_status === 'approved' ? 'success' : ($appointment->approval_status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($appointment->approval_status) }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Created:</strong><br>
                                    {{ $appointment->created_at->format('M d, Y g:i A') }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Last Updated:</strong><br>
                                    {{ $appointment->updated_at->format('M d, Y g:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Patient Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="patient_id" class="required">Patient</label>
                            <select name="patient_id" id="patient_id" class="form-control select2" required>
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" 
                                            {{ (old('patient_id', $appointment->patient_id) == $patient->id) ? 'selected' : '' }}
                                            data-email="{{ $patient->email }}" 
                                            data-phone="{{ $patient->phone_number }}"
                                            data-address="{{ $patient->address }}">
                                        {{ $patient->patient_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Select the patient for this appointment
                            </small>
                        </div>
                    </div>

                    <!-- Appointment Date -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="appointment_date" class="required">Appointment Date</label>
                            <input type="date" name="appointment_date" id="appointment_date" 
                                   class="form-control" 
                                   value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}" 
                                   required min="{{ date('Y-m-d') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Select date for the appointment (Monday-Friday only)
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Appointment Time -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="appointment_time" class="required">Appointment Time</label>
                            <select name="appointment_time" id="appointment_time" class="form-control" required>
                                <option value="">Select Time...</option>
                                <!-- Morning Session: 8:00 AM - 12:00 PM -->
                                <optgroup label="Morning Session (8:00 AM - 12:00 PM)">
                                    <option value="08:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '08:00' ? 'selected' : '' }}>8:00 AM</option>
                                    <option value="08:15" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '08:15' ? 'selected' : '' }}>8:15 AM</option>
                                    <option value="08:30" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '08:30' ? 'selected' : '' }}>8:30 AM</option>
                                    <option value="08:45" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '08:45' ? 'selected' : '' }}>8:45 AM</option>
                                    <option value="09:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                    <option value="09:15" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '09:15' ? 'selected' : '' }}>9:15 AM</option>
                                    <option value="09:30" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '09:30' ? 'selected' : '' }}>9:30 AM</option>
                                    <option value="09:45" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '09:45' ? 'selected' : '' }}>9:45 AM</option>
                                    <option value="10:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                    <option value="10:15" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '10:15' ? 'selected' : '' }}>10:15 AM</option>
                                    <option value="10:30" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '10:30' ? 'selected' : '' }}>10:30 AM</option>
                                    <option value="10:45" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '10:45' ? 'selected' : '' }}>10:45 AM</option>
                                    <option value="11:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                    <option value="11:15" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '11:15' ? 'selected' : '' }}>11:15 AM</option>
                                    <option value="11:30" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '11:30' ? 'selected' : '' }}>11:30 AM</option>
                                    <option value="11:45" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '11:45' ? 'selected' : '' }}>11:45 AM</option>
                                </optgroup>
                                <!-- Afternoon Session: 1:00 PM - 5:00 PM -->
                                <optgroup label="Afternoon Session (1:00 PM - 5:00 PM)">
                                    <option value="13:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                    <option value="13:15" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '13:15' ? 'selected' : '' }}>1:15 PM</option>
                                    <option value="13:30" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '13:30' ? 'selected' : '' }}>1:30 PM</option>
                                    <option value="13:45" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '13:45' ? 'selected' : '' }}>1:45 PM</option>
                                    <option value="14:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                    <option value="14:15" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '14:15' ? 'selected' : '' }}>2:15 PM</option>
                                    <option value="14:30" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '14:30' ? 'selected' : '' }}>2:30 PM</option>
                                    <option value="14:45" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '14:45' ? 'selected' : '' }}>2:45 PM</option>
                                    <option value="15:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                    <option value="15:15" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '15:15' ? 'selected' : '' }}>3:15 PM</option>
                                    <option value="15:30" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '15:30' ? 'selected' : '' }}>3:30 PM</option>
                                    <option value="15:45" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '15:45' ? 'selected' : '' }}>3:45 PM</option>
                                    <option value="16:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                    <option value="16:15" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '16:15' ? 'selected' : '' }}>4:15 PM</option>
                                    <option value="16:30" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '16:30' ? 'selected' : '' }}>4:30 PM</option>
                                    <option value="16:45" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '16:45' ? 'selected' : '' }}>4:45 PM</option>
                                    <option value="17:00" {{ old('appointment_time', $appointment->appointment_time->format('H:i')) == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                                </optgroup>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-clock mr-1"></i>School hours: 8:00 AM - 12:00 PM and 1:00 PM - 5:00 PM (Monday to Friday)
                            </small>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="required">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active" {{ old('status', $appointment->status) == 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                                <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>Current status of the appointment
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Reason for Appointment -->
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="reason" class="required">Reason for Appointment</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" required
                                      maxlength="500" placeholder="Describe the reason for this appointment...">{{ old('reason', $appointment->reason) }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span id="reasonCounter">0/500</span> characters used
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Notes (if exists) -->
                @if($appointment->notes)
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      maxlength="1000" placeholder="Additional notes...">{{ old('notes', $appointment->notes) }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                Additional notes or special instructions
                            </small>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Patient Information Display -->
                <div id="patientInfo" class="row" style="display: {{ $appointment->patient ? 'block' : 'none' }};">
                    <div class="col-12">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user mr-2"></i>Patient Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Email:</strong><br>
                                        <span id="patientEmail" class="text-muted">{{ $appointment->patient->email ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Phone:</strong><br>
                                        <span id="patientPhone" class="text-muted">{{ $appointment->patient->phone_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Address:</strong><br>
                                        <span id="patientAddress" class="text-muted">{{ $appointment->patient->address ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conflict Checker Results -->
                <div id="conflictAlert" class="alert alert-warning" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Time Conflict!</strong>
                    <span id="conflictMessage"></span>
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-save mr-2"></i>Update Appointment
                        </button>
                        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-info">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                        @if($appointment->canCancelAppointment())
                            <button type="button" class="btn btn-warning" onclick="cancelAppointment()">
                                <i class="fas fa-ban mr-2"></i>Cancel Appointment
                            </button>
                        @endif
                        @if($appointment->canCompleteAppointment())
                            <button type="button" class="btn btn-primary" onclick="completeAppointment()">
                                <i class="fas fa-check-circle mr-2"></i>Mark Complete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
<style>
    .required::after {
        content: " *";
        color: red;
        font-weight: bold;
    }

    .select2-container .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        line-height: 26px;
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#patient_id').select2({
        theme: 'bootstrap4',
        placeholder: '-- Select Patient --',
        allowClear: true
    });

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#appointment_date').attr('min', today);

    // Character counter for reason
    $('#reason').on('input', function() {
        const length = $(this).val().length;
        $('#reasonCounter').text(length + '/500');
        
        if (length > 450) {
            $('#reasonCounter').addClass('text-warning');
        } else {
            $('#reasonCounter').removeClass('text-warning');
        }
    });

    // Patient selection change
    $('#patient_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        
        if ($(this).val()) {
            $('#patientEmail').text(selectedOption.data('email') || '-');
            $('#patientPhone').text(selectedOption.data('phone') || '-');
            $('#patientAddress').text(selectedOption.data('address') || '-');
            $('#patientInfo').show();
        } else {
            $('#patientInfo').hide();
        }
    });

    // Philippine school hours validation
    $('#appointment_time').on('change', function() {
        const time = $(this).val();
        if (time) {
            const [hours, minutes] = time.split(':');
            const totalMinutes = parseInt(hours) * 60 + parseInt(minutes);
            
            // Morning: 8:00 AM (480 min) to 12:00 PM (720 min)
            // Afternoon: 1:00 PM (780 min) to 5:00 PM (1020 min)
            const morningStart = 8 * 60;
            const morningEnd = 12 * 60;
            const afternoonStart = 13 * 60;
            const afternoonEnd = 17 * 60;
            
            const isValidTime = (totalMinutes >= morningStart && totalMinutes < morningEnd) ||
                               (totalMinutes >= afternoonStart && totalMinutes <= afternoonEnd);
            
            if (!isValidTime) {
                MessageModal.warning('Please select a time during school hours: 8:00 AM - 12:00 PM or 1:00 PM - 5:00 PM');
                $(this).focus();
            }
        }
    });

    // Philippine holidays (same as in other parts of the system)
    const philippineHolidays = [
        '2024-01-01', '2024-04-09', '2024-04-10', '2024-05-01', '2024-06-12', '2024-08-26', '2024-11-30', '2024-12-25', '2024-12-30',
        '2025-01-01', '2025-04-17', '2025-04-18', '2025-05-01', '2025-06-12', '2025-08-25', '2025-11-30', '2025-12-25', '2025-12-30'
    ];

    // Date validation
    $('#appointment_date').on('change', function() {
        const selectedDate = new Date(this.value);
        const dayOfWeek = selectedDate.getDay();
        const dateStr = this.value;
        
        // Check if weekend
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            MessageModal.warning('Appointments can only be scheduled Monday through Friday. Please select a weekday.');
            this.value = '';
            return;
        }
        
        // Check if holiday
        if (philippineHolidays.includes(dateStr)) {
            MessageModal.warning('The selected date is a Philippine holiday. Please choose a different date.');
            this.value = '';
            return;
        }
    });

    // Initialize character counter
    $('#reason').trigger('input');

    // Initialize patient info if patient is already selected
    if ($('#patient_id').val()) {
        $('#patient_id').trigger('change');
    }
});

function cancelAppointment() {
    MessageModal.confirm(
        'Are you sure you want to cancel this appointment?',
        function() {
            // Proceed with cancellation
            $.ajax({
                url: '/appointments/{{ $appointment->appointment_id }}/cancel',
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        MessageModal.success('Appointment cancelled successfully!');
                        $('#status').val('cancelled');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        MessageModal.error('Failed to cancel appointment: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    MessageModal.error('Failed to cancel appointment. Please try again.');
                }
            });
        },
        null,
        {
            title: 'Cancel Appointment',
            confirmText: 'Cancel Appointment',
            confirmClass: 'btn-danger'
        }
    );
}

function completeAppointment() {
    MessageModal.confirm(
        'Are you sure you want to mark this appointment as completed?',
        function() {
            // Proceed with completion
            $.ajax({
                url: '/appointments/{{ $appointment->appointment_id }}/complete',
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        MessageModal.success('Appointment marked as completed successfully!');
                        $('#status').val('completed');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        MessageModal.error('Failed to complete appointment: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    MessageModal.error('Failed to complete appointment. Please try again.');
                }
            });
        },
        null,
        {
            title: 'Complete Appointment',
            confirmText: 'Mark as Complete',
            confirmClass: 'btn-success'
        }
    );
}
</script>
@endsection