<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BOKOD CMS' }}</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .content h2 {
            color: #2d3748;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .content h3 {
            color: #4a5568;
            font-size: 18px;
            margin-bottom: 15px;
            margin-top: 25px;
            font-weight: 600;
        }
        
        .content p {
            margin-bottom: 16px;
            color: #4a5568;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .info-box {
            background-color: #edf2f7;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .alert-box {
            background-color: #fed7d7;
            border-left: 4px solid #f56565;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .success-box {
            background-color: #c6f6d5;
            border-left: 4px solid #48bb78;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 0;
            transition: all 0.3s ease;
        }
        
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f7fafc;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .details-table th,
        .details-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .details-table th {
            background-color: #edf2f7;
            font-weight: 600;
            color: #2d3748;
        }
        
        .details-table td {
            color: #4a5568;
        }
        
        .footer {
            background-color: #2d3748;
            color: #a0aec0;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        
        .footer p {
            margin-bottom: 10px;
        }
        
        .footer a {
            color: #81e6d9;
            text-decoration: none;
        }
        
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .content {
                padding: 25px 20px;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $headerTitle ?? 'BOKOD CMS' }}</h1>
            <p>{{ $headerSubtitle ?? 'Comprehensive Medical System' }}</p>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>BOKOD Comprehensive Medical System</strong></p>
            <p>This email was sent from BOKOD CMS. Please do not reply to this email.</p>
            <p>If you have any questions, please contact our support team.</p>
            
            @php
                try {
                    $footerUrls = app('App\Services\UrlService')::getEmailFooterUrls();
                } catch (\Exception $e) {
                    $footerUrls = [
                        'portal' => '#',
                        'password_reset' => '#',
                        'support' => '#',
                    ];
                }
            @endphp
            
            <div style="margin: 20px 0; border-top: 1px solid #4a5568; padding-top: 15px;">
                <p style="margin-bottom: 10px;">Quick Links:</p>
                <div style="display: inline-block; margin: 0;">
                    <a href="{{ $footerUrls['portal'] }}" style="color: #81e6d9; margin-right: 15px;">Patient Portal</a>
                    <a href="{{ $footerUrls['password_reset'] }}" style="color: #81e6d9; margin-right: 15px;">Reset Password</a>
                    <a href="{{ $footerUrls['support'] }}" style="color: #81e6d9;">Support</a>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <small>© {{ date('Y') }} BOKOD CMS. All rights reserved.</small>
            </div>
        </div>
    </div>
</body>
</html>