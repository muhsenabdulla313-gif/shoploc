<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .content {
            padding: 20px 0;
        }
        .otp-code {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Welcome to ShopLock!</h1>
        </div>
        <div class="content">
            <p>Hello {{ $name }},</p>
            <p>Thank you for registering with ShopLock. Please use the OTP code below to complete your registration:</p>
            
            <div class="otp-code">{{ $otp }}</div>
            
            <p>This OTP is valid for 10 minutes. If you didn't request this registration, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; 2026 ShopLock. All rights reserved.</p>
        </div>
    </div>
</body>
</html>