<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 30px; background: #f9f9f9; }
        .appointment-details { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #007bff; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BOKOD Medical CMS</h1>
        </div>
        
        <div class="content">
            <h2>Appointment {{ ucfirst($type) }}</h2>
            
            <p>Dear {{ $patient->patient_name ?? 'Patient' }},</p>
            
            @if($type === 'rescheduled')
                <p>Your appointment has been rescheduled to a new date and time.</p>
            @elseif($type === 'approved')
                <p>Your appointment has been approved.</p>
            @elseif($type === 'cancelled')
                <p>Your appointment has been cancelled.</p>
            @else
                <p>This is an update regarding your appointment.</p>
            @endif
            
            <div class="appointment-details">
                <h3>Appointment Details</h3>
                <p><strong>Date:</strong> {{ $appointment->appointment_date->format('F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $appointment->appointment_time->format('g:i A') }}</p>
                <p><strong>Reason:</strong> {{ $appointment->reason ?? 'General consultation' }}</p>
            </div>
            
            <p>If you have any questions, please contact us.</p>
            
            <p>Thank you!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} BOKOD Medical CMS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>