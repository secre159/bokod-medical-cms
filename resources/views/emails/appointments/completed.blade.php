@extends('emails.layouts.base')

@section('content')
    <h2>✅ Thank You for Your Visit - Follow-up Information</h2>
    
    <p>Dear {{ $patient->patient_name }},</p>
    
    <div class="success-box">
        <p><strong>Thank you for visiting us!</strong> We hope you had a positive experience with our medical team.</p>
    </div>
    
    <p>Your recent appointment has been completed. Here are the details:</p>
    
    <table class="details-table">
        <tr>
            <th>Appointment ID</th>
            <td>#{{ $appointment->appointment_id }}</td>
        </tr>
        <tr>
            <th>Visit Date</th>
            <td>{{ $appointment->appointment_date->format('F j, Y (l)') }}</td>
        </tr>
        <tr>
            <th>Visit Time</th>
            <td>{{ $appointment->appointment_time->format('g:i A') }}</td>
        </tr>
        <tr>
            <th>Reason for Visit</th>
            <td>{{ $appointment->reason }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td><strong style="color: #48bb78;">Completed</strong></td>
        </tr>
    </table>
    
    <div class="info-box">
        <h3>🩺 Post-Visit Care Instructions</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Follow any specific instructions given by your healthcare provider</li>
            <li>Take prescribed medications as directed</li>
            <li>Monitor your symptoms and keep track of any changes</li>
            <li>Schedule any recommended follow-up appointments</li>
            <li>Contact us immediately if you experience any concerning symptoms</li>
        </ul>
    </div>
    
    @if(isset($additionalData['prescriptions']) && count($additionalData['prescriptions']) > 0)
    <div class="info-box">
        <h3>💊 Prescription Summary</h3>
        <p>You have been prescribed the following medications:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            @foreach($additionalData['prescriptions'] as $prescription)
            <li>{{ $prescription['medicine_name'] }} - {{ $prescription['dosage'] }} ({{ $prescription['instructions'] }})</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="info-box">
        <h3>📞 Need Support?</h3>
        <p>If you have any questions about your visit, treatment, or medications, please don't hesitate to contact us:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>For medical emergencies, call your local emergency number</li>
            <li>For non-urgent questions, contact our clinic during business hours</li>
            <li>You can also access your patient portal for medical records and appointments</li>
        </ul>
    </div>
    
    <p>Your health and well-being are our top priority. We're here to support you on your healthcare journey.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>With care,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection