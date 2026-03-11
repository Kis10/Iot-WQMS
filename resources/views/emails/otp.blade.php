<!DOCTYPE html>
<html>
<head>
    <style>
        .container { font-family: 'Outfit', sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .otp-code { font-size: 32px; font-weight: 800; color: #2563eb; text-align: center; letter-spacing: 5px; margin: 30px 0; }
        .footer { font-size: 12px; color: #64748b; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #0D1A63;">Account Verification</h2>
        </div>
        <p>Hello,</p>
        <p>Thank you for registering with <strong>{{ config('app.name') }}</strong>. To complete your registration and secure your account, please use the verification code below:</p>
        
        <div class="otp-code">
            {{ $otp }}
        </div>
        
        <p>This code will expire in 10 minutes. If you did not request this, please ignore this email.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
