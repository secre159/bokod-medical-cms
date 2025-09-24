@extends('emails.layouts.base')

@section('content')
    <h2>⏰ Medication Reminder</h2>
    
    <p>Dear {{ $patient->patient_name }},</p>
    
    <div class="info-box">
        <p><strong>This is a friendly reminder</strong> about your current prescription medications.</p>
    </div>
    
    <p>We want to ensure you're staying on track with your treatment plan. Here's a summary of your current medications:</p>
    
    <h3>💊 Your Current Medications</h3>
    @if(isset($additionalData['medicines']) && count($additionalData['medicines']) > 0)
    <table class="details-table">
        <thead>
            <tr>
                <th>Medicine</th>
                <th>Dosage</th>
                <th>Schedule</th>
                <th>Remaining</th>
            </tr>
        </thead>
        <tbody>
            @foreach($additionalData['medicines'] as $medicine)
            <tr>
                <td><strong>{{ $medicine['medicine_name'] }}</strong></td>
                <td>{{ $medicine['dosage'] ?? 'As directed' }}</td>
                <td>{{ $medicine['schedule'] ?? 'Take as prescribed' }}</td>
                <td>{{ $medicine['remaining_days'] ?? 'N/A' }} days</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="alert-box">
        <p>We don't have current medication details on file. Please contact us if you need assistance with your prescriptions.</p>
    </div>
    @endif
    
    <div class="alert-box">
        <h3>⚠️ Medication Adherence Tips</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Set daily alarms</strong> to remind you when to take medications</li>
            <li>Use a pill organizer to help track your medications</li>
            <li>Take medications at the same time each day</li>
            <li>Don't skip doses, even if you feel better</li>
            <li>Keep medications in their original containers</li>
            <li>Don't stop taking medications without consulting your doctor</li>
        </ul>
    </div>
    
    @if(isset($additionalData['low_supply_medicines']) && count($additionalData['low_supply_medicines']) > 0)
    <div class="alert-box">
        <h3>📦 Low Supply Alert</h3>
        <p><strong>The following medications may be running low:</strong></p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            @foreach($additionalData['low_supply_medicines'] as $medicine)
            <li>{{ $medicine['medicine_name'] }} - Only {{ $medicine['remaining_days'] }} days remaining</li>
            @endforeach
        </ul>
        <p>Please contact our clinic to request prescription refills before you run out.</p>
    </div>
    @endif
    
    <div class="success-box">
        <h3>📱 Helpful Tools</h3>
        <p>Consider using these tools to help manage your medications:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Smartphone apps for medication tracking</li>
            <li>Daily or weekly pill organizers</li>
            <li>Pharmacy automatic refill services</li>
            <li>Family member or caregiver support</li>
        </ul>
    </div>
    
    <div class="info-box">
        <h3>📞 Questions or Concerns?</h3>
        <p>If you have any questions about your medications or need assistance:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Side effects:</strong> Contact us immediately if you experience any concerning symptoms</li>
            <li><strong>Refills:</strong> Call our clinic to request prescription refills</li>
            <li><strong>Drug interactions:</strong> Always inform us of any new medications or supplements</li>
            <li><strong>Financial assistance:</strong> Ask about patient assistance programs if cost is a concern</li>
        </ul>
    </div>
    
    @if(isset($additionalData['next_appointment']))
    <div class="info-box">
        <h3>📅 Upcoming Appointment</h3>
        <p>Your next appointment is scheduled for <strong>{{ $additionalData['next_appointment'] }}</strong>. We'll review your medication progress during this visit.</p>
    </div>
    @endif
    
    <p>Remember, taking your medications as prescribed is crucial for your health and recovery. We're here to support you every step of the way.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Stay healthy,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection