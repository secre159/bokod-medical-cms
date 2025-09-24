@extends('emails.layouts.base')

@section('content')
    <h2>🎉 Your Appointment Has Been Approved</h2>
    
    <p>Dear {{ $patient->patient_name }},</p>
    
    <div class="success-box">
        <p><strong>Great news!</strong> Your appointment has been approved and confirmed.</p>
    </div>
    
    <p>Here are your appointment details:</p>
    
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
            <td><strong style="color: #48bb78;">Approved</strong></td>
        </tr>
    </table>
    
    <div class="info-box">
        <h3>📋 Please Remember:</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Arrive 15 minutes before your appointment time</li>
            <li>Bring a valid ID and any relevant medical documents</li>
            <li>If you need to reschedule, please contact us at least 24 hours in advance</li>
            <li>Follow all health protocols when visiting the clinic</li>
        </ul>
    </div>
    
    <p>If you have any questions or need to make changes to your appointment, please contact our clinic immediately.</p>
    
    <p>We look forward to seeing you!</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Best regards,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection