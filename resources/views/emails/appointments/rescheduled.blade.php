@extends('emails.layouts.base')

@section('content')
    <h2>📅 Your Appointment Has Been Rescheduled</h2>
    
    <p>Dear {{ $patient->patient_name ?? 'Patient' }},</p>
    
    <div class="info-box">
        <p><strong>Important:</strong> Your appointment has been rescheduled to a new date and time.</p>
    </div>
    
    <p>Here are your updated appointment details:</p>
    
    <table class="details-table">
        <tr>
            <th>Appointment ID</th>
            <td>#{{ $appointment->appointment_id }}</td>
        </tr>
        <tr>
            <th>New Date</th>
            <td>{{ $appointment->appointment_date ? $appointment->appointment_date->format('F j, Y (l)') : 'Not set' }}</td>
        </tr>
        <tr>
            <th>New Time</th>
            <td>{{ $appointment->appointment_time ? $appointment->appointment_time->format('g:i A') : 'Not set' }}</td>
        </tr>
        <tr>
            <th>Reason</th>
            <td>{{ $appointment->reason ?? 'Not specified' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><strong style="color: #ffa500;">Rescheduled</strong></td>
        </tr>
    </table>
    
    <div class="warning-box">
        <h3>📝 Please Note:</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>This is your new appointment time - please update your calendar</li>
            <li>Arrive 15 minutes before your appointment time</li>
            <li>Bring a valid ID and any relevant medical documents</li>
            <li>If you need to make further changes, please contact us at least 24 hours in advance</li>
            <li>Follow all health protocols when visiting the clinic</li>
        </ul>
    </div>
    
    <p>If you have any questions about this change or need to make further adjustments, please contact our clinic immediately.</p>
    
    <p>We look forward to seeing you at your new appointment time!</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Best regards,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection