<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BSU Health Portal - Registration Approved</title>
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
            border-bottom: 2px solid #16a34a;
        }
        .logo {
            color: #16a34a;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .success-icon {
            font-size: 48px;
            color: #16a34a;
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
            <div class="success-icon">✅</div>
            <h1 style="color: #16a34a; margin: 0;">Registration Approved!</h1>
        </div>

        <div class="content">
            <p><strong>Dear {{ $user->name }},</strong></p>

            <p>Great news! Your registration for the BSU Health Portal has been <strong>approved</strong> by our admin team.</p>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #0ea5e9;">🎉 What's Next?</h3>
                <ul>
                    <li><strong>Login:</strong> Use your registered email and password</li>
                    <li><strong>Book Appointments:</strong> Schedule health center visits online</li>
                    <li><strong>Access Records:</strong> View your appointment history and prescriptions</li>
                    <li><strong>Stay Updated:</strong> Receive important health notifications</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/login') }}" class="btn">Login to Health Portal</a>
            </div>

            <div class="info-box">
                <h4 style="margin-top: 0;">📍 BSU Health Center Information</h4>
                <p><strong>Location:</strong> BSU Bokod Campus, Health Center<br>
                <strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM<br>
                <strong>Emergency:</strong> Contact campus security or call (074) 422-XXXX</p>
            </div>

            <p>Welcome to the BSU Health Portal! We're excited to serve your health and wellness needs.</p>
        </div>

        <div class="footer">
            <p>This email was sent to {{ $user->email }} because you registered for the BSU Health Portal.</p>
            <p>If you have any questions, please contact the BSU Health Center.</p>
            <p><strong>BSU Bokod Campus Health Center</strong><br>
            Benguet State University - Bokod</p>
        </div>
    </div>
</body>
</html>