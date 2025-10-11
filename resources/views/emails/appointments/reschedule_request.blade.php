@extends('emails.layouts.base')

@section('content')
    <h2>📋 Reschedule Request Received</h2>
    
    <p>Dear {{ $patient->patient_name ?? 'Patient' }},</p>
    
    <div class="info-box">
        <p><strong>Thank you!</strong> We have received your request to reschedule your appointment.</p>
    </div>
    
    <p>Here are the details of your reschedule request:</p>
    
    <table class="details-table">
        <tr>
            <th>Appointment ID</th>
            <td>#{{ $appointment->appointment_id }}</td>
        </tr>
        <tr>
            <th>Current Date</th>
            <td>{{ $appointment->appointment_date ? $appointment->appointment_date->format('F j, Y (l)') : 'Not set' }}</td>
        </tr>
        <tr>
            <th>Current Time</th>
            <td>{{ $appointment->appointment_time ? $appointment->appointment_time->format('g:i A') : 'Not set' }}</td>
        </tr>
        @if($appointment->requested_date && $appointment->requested_time)
        <tr>
            <th>Requested New Date</th>
            <td>{{ \Carbon\Carbon::parse($appointment->requested_date)->format('F j, Y (l)') }}</td>
        </tr>
        <tr>
            <th>Requested New Time</th>
            <td>{{ \Carbon\Carbon::parse($appointment->requested_time)->format('g:i A') }}</td>
        </tr>
        @endif
        <tr>
            <th>Reason</th>
            <td>{{ $appointment->reason ?? 'Not specified' }}</td>
        </tr>
        @if($appointment->reschedule_reason)
        <tr>
            <th>Reschedule Reason</th>
            <td>{{ $appointment->reschedule_reason }}</td>
        </tr>
        @endif
        <tr>
            <th>Request Status</th>
            <td><strong style="color: #ffa500;">Pending Review</strong></td>
        </tr>
    </table>
    
    <div class="warning-box">
        <h3>⏳ What Happens Next:</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Our staff will review your reschedule request</li>
            <li>We will check availability for your requested time slot</li>
            <li>You will receive an email confirmation once the request is processed</li>
            <li>If your requested time is not available, we will suggest alternative times</li>
            <li>Your original appointment remains active until the reschedule is confirmed</li>
        </ul>
    </div>
    
    <p>We will process your request as soon as possible and notify you of the outcome via email.</p>
    
    <p>If you have any urgent questions or need to cancel your appointment, please contact our clinic directly.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Best regards,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection