@extends('emails.layouts.base')

@section('content')
    <h2>⏰ Appointment Reminder</h2>
    
    <p>Dear {{ $patient->patient_name }},</p>
    
    <div class="info-box">
        <p><strong>This is a friendly reminder</strong> that you have an upcoming appointment with us.</p>
    </div>
    
    <p>Please review your appointment details:</p>
    
    <table class="details-table">
        <tr>
            <th>Appointment ID</th>
            <td>#{{ $appointment->appointment_id }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <td>{{ $appointment->appointment_date->format('F j, Y (l)') }}</td>
        </tr>
        <tr>
            <th>Time</th>
            <td>{{ $appointment->appointment_time->format('g:i A') }}</td>
        </tr>
        <tr>
            <th>Reason</th>
            <td>{{ $appointment->reason }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><strong style="color: #667eea;">{{ ucfirst($appointment->approval_status) }}</strong></td>
        </tr>
        @if(isset($additionalData['days_until']))
        <tr>
            <th>Time Until Appointment</th>
            <td><strong>{{ $additionalData['days_until'] }} day(s)</strong></td>
        </tr>
        @endif
    </table>
    
    <div class="info-box">
        <h3>📋 Before Your Visit:</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Arrive 15 minutes early</strong> for check-in and paperwork</li>
            <li>Bring a valid government-issued ID</li>
            <li>Bring your insurance card (if applicable)</li>
            <li>Bring a list of current medications you're taking</li>
            <li>Prepare a list of questions or concerns you want to discuss</li>
            <li>Follow any pre-appointment instructions given previously</li>
        </ul>
    </div>
    
    <div class="alert-box">
        <h3>⚠️ Important Reminders:</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>If you need to cancel or reschedule, please contact us at least 24 hours in advance</li>
            <li>Please wear a face mask and follow all health protocols</li>
            <li>If you're feeling unwell or have COVID-19 symptoms, please call before your visit</li>
        </ul>
    </div>
    
    <p>We're looking forward to seeing you at your scheduled appointment. If you have any questions or need to make any changes, please contact our clinic as soon as possible.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>See you soon,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection