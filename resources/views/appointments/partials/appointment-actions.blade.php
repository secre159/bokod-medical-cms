@if($appointment->canApproveAppointment())
    <button type="button" class="btn btn-success approve-appointment" data-id="{{ $appointment->appointment_id }}">
        <i class="fas fa-check mr-1"></i>Approve
    </button>
    <button type="button" class="btn btn-danger reject-appointment" data-id="{{ $appointment->appointment_id }}">
        <i class="fas fa-times mr-1"></i>Reject
    </button>
@endif

@if($appointment->canEditAppointment())
    <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-primary">
        <i class="fas fa-edit mr-1"></i>Edit
    </a>
@endif

@if($appointment->canCompleteAppointment())
    <button type="button" class="btn btn-info complete-appointment" data-id="{{ $appointment->appointment_id }}">
        <i class="fas fa-check-circle mr-1"></i>Mark Complete
    </button>
@endif

@if($appointment->canCancelAppointment())
    <button type="button" class="btn btn-warning cancel-appointment" data-id="{{ $appointment->appointment_id }}">
        <i class="fas fa-ban mr-1"></i>Cancel
    </button>
@endif

<div class="btn-group">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-ellipsis-h mr-1"></i>More
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route('appointments.show', $appointment) }}">
            <i class="fas fa-eye mr-2"></i>View Full Details
        </a>
        @if($appointment->patient)
            <a class="dropdown-item" href="{{ route('patients.show', $appointment->patient) }}">
                <i class="fas fa-user mr-2"></i>View Patient
            </a>
        @endif
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('prescriptions.create') }}?patient_id={{ $appointment->patient_id }}&from=appointment">
            <i class="fas fa-prescription-bottle-alt mr-2"></i>Create Prescription
        </a>
        <div class="dropdown-divider"></div>
        @if($appointment->canRescheduleAppointment())
            <button class="dropdown-item text-info reschedule-appointment" data-id="{{ $appointment->appointment_id }}">
                <i class="fas fa-calendar-alt mr-2"></i>Reschedule
            </button>
        @endif
        @if($appointment->status !== App\Models\Appointment::STATUS_CANCELLED)
            <button class="dropdown-item text-danger delete-appointment" data-id="{{ $appointment->appointment_id }}">
                <i class="fas fa-trash mr-2"></i>Delete
            </button>
        @endif
    </div>
</div>