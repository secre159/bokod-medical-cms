@extends('emails.layouts.base')

@section('content')
    <h2>💊 New Prescription Issued</h2>
    
    <p>Dear {{ $patient->patient_name }},</p>
    
    <div class="success-box">
        <p><strong>A new prescription has been issued for you</strong> following your recent visit.</p>
    </div>
    
    <h3>📋 Prescription Details</h3>
    <table class="details-table">
        <tr>
            <th>Prescription ID</th>
            <td>#{{ $prescription->prescription_id }}</td>
        </tr>
        <tr>
            <th>Date Issued</th>
            <td>{{ $prescription->created_at->format('F j, Y') }}</td>
        </tr>
        <tr>
            <th>Prescribed By</th>
            <td>{{ $prescription->prescribed_by ?? 'BOKOD CMS Medical Team' }}</td>
        </tr>
        @if($prescription->diagnosis)
        <tr>
            <th>Diagnosis</th>
            <td>{{ $prescription->diagnosis }}</td>
        </tr>
        @endif
    </table>
    
    <div class="info-box">
        <h3>💊 Prescribed Medications</h3>
        @if(isset($additionalData['medicines']) && count($additionalData['medicines']) > 0)
        <table class="details-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Quantity</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($additionalData['medicines'] as $medicine)
                <tr>
                    <td><strong>{{ $medicine['medicine_name'] }}</strong></td>
                    <td>{{ $medicine['dosage'] ?? 'As directed' }}</td>
                    <td>{{ $medicine['quantity'] ?? 'N/A' }}</td>
                    <td>{{ $medicine['instructions'] ?? 'Take as prescribed' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>Please refer to your physical prescription for medication details.</p>
        @endif
    </div>
    
    <div class="alert-box">
        <h3>⚠️ Important Medication Guidelines</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Follow dosage instructions exactly</strong> as prescribed</li>
            <li>Take medications at the same time each day</li>
            <li>Complete the full course of treatment, even if you feel better</li>
            <li>Do not share your medications with others</li>
            <li>Store medications properly according to label instructions</li>
            <li>Contact us immediately if you experience severe side effects</li>
        </ul>
    </div>
    
    @if(isset($additionalData['pharmacy_instructions']))
    <div class="info-box">
        <h3>🏥 Pharmacy Information</h3>
        <p>{{ $additionalData['pharmacy_instructions'] }}</p>
    </div>
    @endif
    
    <div class="info-box">
        <h3>📞 Need Help?</h3>
        <p>If you have questions about your prescription or medications:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Contact our clinic during business hours</li>
            <li>Speak with your pharmacist when picking up medications</li>
            <li>Schedule a follow-up appointment if needed</li>
            <li>Call emergency services if you experience severe reactions</li>
        </ul>
    </div>
    
    <h3>🏥 Next Steps</h3>
    <ol style="margin: 15px 0; padding-left: 20px;">
        <li>Pick up your prescription from the pharmacy</li>
        <li>Read all medication labels and instructions carefully</li>
        <li>Start taking medications as prescribed</li>
        <li>Schedule any recommended follow-up appointments</li>
        <li>Monitor your symptoms and response to treatment</li>
    </ol>
    
    <p>Your health and recovery are our top priority. Please don't hesitate to contact us if you have any concerns about your prescription or treatment plan.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Take care of yourself,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection