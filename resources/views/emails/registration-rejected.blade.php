<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BSU Health Portal - Registration Status</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #dc2626;
        }
        .logo {
            color: #16a34a;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .warning-icon {
            font-size: 48px;
            color: #dc2626;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #16a34a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #15803d;
        }
        .info-box {
            background-color: #fef2f2;
            border: 1px solid #dc2626;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .help-box {
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">🏥 BSU Health Portal</div>
            <div class="warning-icon">⚠️</div>
            <h1 style="color: #dc2626; margin: 0;">Registration Status Update</h1>
        </div>

        <div class="content">
            <p><strong>Dear {{ $user->name }},</strong></p>

            <p>Thank you for your interest in registering for the BSU Health Portal. After reviewing your application, we were unable to approve your registration at this time.</p>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #dc2626;">Reason for Non-Approval:</h3>
                <p><strong>{{ $reason }}</strong></p>
            </div>

            <div class="help-box">
                <h3 style="margin-top: 0; color: #0ea5e9;">📞 Need Help?</h3>
                <p>If you believe this was an error or would like to reapply, please contact the BSU Health Center directly:</p>
                <ul>
                    <li><strong>Visit:</strong> BSU Bokod Campus, Health Center</li>
                    <li><strong>Phone:</strong> (074) 422-XXXX</li>
                    <li><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</li>
                    <li><strong>Email:</strong> health.center@bsu.edu.ph</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('register') }}" class="btn">Try Registering Again</a>
            </div>

            <div class="help-box">
                <h4 style="margin-top: 0;">💡 Tips for Successful Registration</h4>
                <ul>
                    <li>Use your official BSU email address (@bsu.edu.ph, @student.bsu.edu.ph)</li>
                    <li>Provide your correct Student ID number</li>
                    <li>Fill in all required information completely and accurately</li>
                    <li>Ensure your emergency contact information is current</li>
                </ul>
            </div>

            <p>We appreciate your understanding and look forward to serving your health needs at BSU.</p>
        </div>

        <div class="footer">
            <p>This email was sent to {{ $user->email }} regarding your BSU Health Portal registration.</p>
            <p>If you have any questions, please contact the BSU Health Center.</p>
            <p><strong>BSU Bokod Campus Health Center</strong><br>
            Benguet State University - Bokod</p>
        </div>
    </div>
</body>
</html>