<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'KoboPoint' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .logo-container {
            margin-bottom: 10px;
        }
        .logo {
            height: 50px;
            width: auto;
        }
        .logo-placeholder {
            width: 50px;
            height: 50px;
            background-color: #ffffff;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .tagline {
            color: #ffffff;
            font-size: 14px;
            margin-top: 8px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
        .footer-link {
            color: #3b82f6;
            text-decoration: none;
        }
        .footer-link:hover {
            text-decoration: underline;
        }
        .security-note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .otp-code {
            background-color: #f3f4f6;
            border: 2px dashed #3b82f6;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 4px;
            color: #1e3a8a;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-box {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
        }
        .info-value {
            color: #111827;
            text-align: right;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        .table td {
            color: #111827;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            .otp-code {
                font-size: 24px;
                letter-spacing: 2px;
            }
        }
    </style>
</head>
<body>
    <div style="padding: 20px;">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="logo-container">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="KoboPoint" class="logo">
                    @elseif(file_exists(public_path('images/logo.svg')))
                        <img src="{{ asset('images/logo.svg') }}" alt="KoboPoint" class="logo">
                    @else
                        <div class="logo-placeholder">K</div>
                    @endif
                </div>
                <div class="tagline">Smart Payments. Simple Life.</div>
            </div>

            <!-- Body -->
            <div class="email-body">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p style="margin: 0 0 10px 0;">
                    <strong>Need Help?</strong><br>
                    Email us at <a href="mailto:support@kobopoint.com" class="footer-link">support@kobopoint.com</a>
                </p>
                <div class="security-note" style="margin: 20px 0;">
                    <strong>⚠️ Security Notice:</strong> If you did not initiate this request, please contact support immediately.
                </div>
                <p style="margin: 10px 0 0 0;">
                    © {{ date('Y') }} — KoboPoint Technologies Ltd<br>
                    <span style="color: #9ca3af;">All rights reserved</span>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

