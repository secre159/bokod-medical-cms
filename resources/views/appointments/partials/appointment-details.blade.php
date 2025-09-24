<div class="row">
    <div class="col-md-8">
        <table class="table table-borderless">
            <tr>
                <td><strong>Patient:</strong></td>
                <td>
                    {{ $appointment->patient ? $appointment->patient->patient_name : 'Unknown Patient' }}
                    @if($appointment->patient && $appointment->patient->email)
                        <br><small class="text-muted">{{ $appointment->patient->email }}</small>
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Date & Time:</strong></td>
                <td>
                    <i class="fas fa-calendar mr-1"></i>{{ $appointment->appointment_date->format('M d, Y') }}
                    <br>
                    <i class="fas fa-clock mr-1"></i>{{ $appointment->appointment_time->format('g:i A') }}
                    <small class="text-muted">({{ $appointment->appointment_time->format('H:i') }} - {{ $appointment->appointment_time->copy()->addMinutes(30)->format('H:i') }})</small>
                </td>
            </tr>
            <tr>
                <td><strong>Reason:</strong></td>
                <td>{{ $appointment->reason }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <span class="badge badge-{{ $appointment->status === 'active' ? 'primary' : ($appointment->status === 'completed' ? 'success' : 'secondary') }}">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Approval:</strong></td>
                <td>
                    <span class="badge badge-{{ $appointment->approval_status === 'approved' ? 'success' : ($appointment->approval_status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($appointment->approval_status) }}
                    </span>
                </td>
            </tr>
            @if($appointment->hasPendingReschedule())
            <tr>
                <td><strong>Reschedule:</strong></td>
                <td>
                    <span class="badge badge-warning">
                        <i class="fas fa-clock mr-1"></i>Reschedule Requested
                    </span>
                    @if($appointment->requested_date && $appointment->requested_time)
                        <br><small class="text-muted">
                            Requested: {{ $appointment->requested_date->format('M d, Y') }} at {{ $appointment->requested_time->format('g:i A') }}
                        </small>
                    @endif
                    @if($appointment->reschedule_reason)
                        <br><small class="text-muted">Reason: {{ $appointment->reschedule_reason }}</small>
                    @endif
                </td>
            </tr>
            @endif
        </table>
    </div>
    
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-info">
                <i class="fas fa-clock"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Duration</span>
                <span class="info-box-number">30 min</span>
            </div>
        </div>
        
        @if($appointment->isOverdue())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            This appointment is overdue.
        </div>
        @endif
        
        @if($appointment->isToday())
        <div class="alert alert-info">
            <i class="fas fa-calendar-day mr-2"></i>
            This appointment is today!
        </div>
        @endif
        
        @if($appointment->patient)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Contact</h5>
            </div>
            <div class="card-body py-2">
                @if($appointment->patient->phone_number)
                <div class="mb-2">
                    <i class="fas fa-phone text-muted mr-2"></i>
                    <a href="tel:{{ $appointment->patient->phone_number }}">{{ $appointment->patient->phone_number }}</a>
                </div>
                @endif
                @if($appointment->patient->email)
                <div class="mb-2">
                    <i class="fas fa-envelope text-muted mr-2"></i>
                    <a href="mailto:{{ $appointment->patient->email }}">{{ $appointment->patient->email }}</a>
                </div>
                @endif
                @if($appointment->patient->address)
                <div class="mb-0">
                    <i class="fas fa-map-marker-alt text-muted mr-2"></i>
                    <small>{{ $appointment->patient->address }}</small>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>