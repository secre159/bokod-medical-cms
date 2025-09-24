@extends('emails.layouts.base')

@section('content')
    <h2>🎉 Welcome to BOKOD CMS Patient Portal</h2>
    
    <p>Dear {{ $patient->patient_name }},</p>
    
    <div class="success-box">
        <p><strong>Welcome!</strong> Your patient account has been successfully created in our BOKOD CMS system.</p>
    </div>
    
    <p>We're excited to have you as part of our healthcare community. Your patient portal account will help you manage your healthcare more efficiently.</p>
    
    <h3>👤 Your Account Details</h3>
    <table class="details-table">
        <tr>
            <th>Patient ID</th>
            <td>#{{ $patient->id }}</td>
        </tr>
        <tr>
            <th>Full Name</th>
            <td>{{ $patient->patient_name }}</td>
        </tr>
        <tr>
            <th>Email Address</th>
            <td>{{ $patient->email }}</td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td>{{ $patient->phone_number ?? 'Not provided' }}</td>
        </tr>
        @if($patient->date_of_birth)
        <tr>
            <th>Date of Birth</th>
            <td>{{ $patient->date_of_birth->format('F j, Y') }}</td>
        </tr>
        @endif
    </table>
    
    @if($temporaryPassword)
    <div class="alert-box">
        <h3>🔐 Your Secure Login Credentials</h3>
        <p><strong>Important:</strong> We've generated a secure password for your account. Please keep this information confidential:</p>
        
        <div style="background: #f8f9fa; border: 2px solid #e9ecef; padding: 20px; border-radius: 8px; margin: 15px 0;">
            <table style="width: 100%; margin: 0;">
                <tr>
                    <td style="font-weight: bold; padding: 8px 0; color: #495057;">Email Address:</td>
                    <td style="padding: 8px 0; font-family: monospace; background: #e9ecef; padding: 4px 8px; border-radius: 4px;">{{ $patient->email }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 8px 0; color: #495057;">Secure Password:</td>
                    <td style="padding: 8px 0;"><code style="background: #fff; color: #e74c3c; font-weight: bold; padding: 8px 12px; border-radius: 4px; font-size: 16px; letter-spacing: 1px; border: 2px solid #e74c3c;">{{ $temporaryPassword }}</code></td>
                </tr>
            </table>
        </div>
        
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0;">
            <p style="margin: 0; color: #856404;"><strong>⚠️ Security Instructions:</strong></p>
            <ul style="margin: 10px 0; padding-left: 20px; color: #856404;">
                <li>Change your password immediately after your first login</li>
                <li>Do not share your password with anyone</li>
                <li>Use a strong, unique password that you haven't used elsewhere</li>
                <li>Log out completely when using shared computers</li>
            </ul>
        </div>
        
        <p style="font-size: 14px; color: #6c757d; margin-top: 15px;">
            <em>This password was automatically generated using secure methods and sent only to this email address. If you did not request this account, please contact our support team immediately.</em>
        </p>
    </div>
    @endif
    
    <div class="info-box">
        <h3>🌟 What You Can Do with Your Patient Portal</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Schedule Appointments:</strong> Book, reschedule, or cancel appointments online</li>
            <li><strong>View Medical History:</strong> Access your visit records and medical history</li>
            <li><strong>Prescription Management:</strong> View current prescriptions and medication history</li>
            <li><strong>Health Records:</strong> Track your vital signs, BMI, and other health metrics</li>
            <li><strong>Secure Messaging:</strong> Communicate with your healthcare providers</li>
            <li><strong>Test Results:</strong> View lab results and medical reports</li>
        </ul>
    </div>
    
    @if($portalUrl)
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $portalUrl }}" class="button">Access Patient Portal</a>
    </div>
    @endif
    
    <div class="info-box">
        <h3>📞 Need Help?</h3>
        <p>If you need assistance with your account or have any questions:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Contact our support team during business hours</li>
            <li>Visit our clinic for in-person assistance</li>
            <li>Check our FAQ section in the patient portal</li>
        </ul>
    </div>
    
    <h3>🏥 Next Steps</h3>
    <ol style="margin: 15px 0; padding-left: 20px;">
        <li><strong>Log into your patient portal</strong> using the credentials provided above</li>
        <li><strong>Change your password immediately</strong> - Use a strong, memorable password</li>
        <li><strong>Complete your health profile</strong> with any missing information</li>
        <li><strong>Review and update</strong> your contact information</li>
        <li><strong>Schedule your next appointment</strong> if needed</li>
        <li><strong>Explore all the features</strong> available to you in the portal</li>
    </ol>
    
    <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #155724;"><strong>✅ First Login Checklist:</strong></p>
        <ul style="margin: 10px 0; padding-left: 20px; color: #155724;">
            <li>✓ Change your temporary password to something secure</li>
            <li>✓ Verify your personal information is correct</li>
            <li>✓ Add any missing medical information (allergies, medications, etc.)</li>
            <li>✓ Set up appointment reminders if desired</li>
        </ul>
    </div>
    
    <p>We're committed to providing you with the highest quality healthcare and making your experience as convenient as possible. Your patient portal is just one of the many ways we're working to improve your healthcare journey.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Welcome to the BOKOD CMS family!</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection