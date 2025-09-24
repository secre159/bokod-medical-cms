@extends('emails.layouts.base')

@section('content')
    <h2>❌ Appointment Request Not Approved</h2>
    
    <p>Dear {{ $patient->patient_name }},</p>
    
    <div class="alert-box">
        <p><strong>We regret to inform you</strong> that your appointment request could not be approved at this time.</p>
    </div>
    
    <p>Here are the details of your appointment request:</p>
    
    <table class="details-table">
        <tr>
            <th>Appointment ID</th>
            <td>#{{ $appointment->appointment_id }}</td>
        </tr>
        <tr>
            <th>Requested Date</th>
            <td>{{ $appointment->appointment_date->format('F j, Y (l)') }}</td>
        </tr>
        <tr>
            <th>Requested Time</th>
            <td>{{ $appointment->appointment_time->format('g:i A') }}</td>
        </tr>
        <tr>
            <th>Reason for Visit</th>
            <td>{{ $appointment->reason }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><strong style="color: #f56565;">Not Approved</strong></td>
        </tr>
        @if(isset($additionalData['rejection_reason']) && $additionalData['rejection_reason'])
        <tr>
            <th>Reason for Rejection</th>
            <td>{{ $additionalData['rejection_reason'] }}</td>
        </tr>
        @endif
    </table>
    
    <div class="info-box">
        <h3>📞 Alternative Options</h3>
        <p>We understand this may be disappointing. Here are some alternative options:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Submit a new appointment request for a different date and time</li>
            <li>Contact our clinic directly to discuss available appointment slots</li>
            <li>Consider scheduling a telemedicine consultation if appropriate</li>
            <li>Ask about our walk-in clinic hours for non-urgent matters</li>
        </ul>
    </div>
    
    <div class="success-box">
        <h3>💡 Scheduling Tips</h3>
        <p>To increase the likelihood of approval for future appointments:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Book appointments well in advance</li>
            <li>Choose from multiple preferred time slots</li>
            <li>Be flexible with your scheduling when possible</li>
            <li>Provide clear and detailed reasons for your visit</li>
        </ul>
    </div>
    
    <p>We apologize for any inconvenience and appreciate your understanding. Our goal is to provide quality care to all patients, and sometimes this requires careful scheduling coordination.</p>
    
    <p>Please don't hesitate to reach out if you have any questions or if you'd like assistance with rescheduling.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Thank you for your patience,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection