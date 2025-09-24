@extends('emails.layouts.base')

@section('content')
    <h2>❌ Appointment Cancellation Notice</h2>
    
    <p>Dear {{ $patient->patient_name }},</p>
    
    <div class="alert-box">
        <p><strong>We regret to inform you</strong> that your appointment has been cancelled.</p>
    </div>
    
    <p>Here are the details of the cancelled appointment:</p>
    
    <table class="details-table">
        <tr>
            <th>Appointment ID</th>
            <td>#{{ $appointment->appointment_id }}</td>
        </tr>
        <tr>
            <th>Original Date</th>
            <td>{{ $appointment->appointment_date->format('F j, Y (l)') }}</td>
        </tr>
        <tr>
            <th>Original Time</th>
            <td>{{ $appointment->appointment_time->format('g:i A') }}</td>
        </tr>
        <tr>
            <th>Reason for Visit</th>
            <td>{{ $appointment->reason }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><strong style="color: #f56565;">Cancelled</strong></td>
        </tr>
        @if(isset($additionalData['cancellation_reason']) && $additionalData['cancellation_reason'])
        <tr>
            <th>Cancellation Reason</th>
            <td>{{ $additionalData['cancellation_reason'] }}</td>
        </tr>
        @endif
    </table>
    
    <div class="info-box">
        <h3>📞 Need to Reschedule?</h3>
        <p>If you would like to schedule a new appointment, please:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Contact our clinic directly</li>
            <li>Visit our patient portal to book online</li>
            <li>Call during business hours for immediate assistance</li>
        </ul>
    </div>
    
    <p>We apologize for any inconvenience this cancellation may have caused. We are committed to providing you with the best possible care and will do our best to accommodate you at the earliest available time.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Sincerely,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection