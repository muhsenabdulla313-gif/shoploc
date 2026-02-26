<!DOCTYPE html>
<html>
<head>
    <title>Contact Form Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        .content {
            padding: 20px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            display: block;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Message</h2>
        </div>
        <div class="content">
            <div class="field">
                <span class="label">Name:</span>
                <span class="value">{{ $name }}</span>
            </div>
            <div class="field">
                <span class="label">Email:</span>
                <span class="value">{{ $email }}</span>
            </div>
            <div class="field">
                <span class="label">Subject:</span>
                <span class="value">{{ $subject }}</span>
            </div>
            <div class="field">
                <span class="label">Message:</span>
                <span class="value">{{ $message }}</span>
            </div>
        </div>
        <div class="footer">
            <p>This email was sent from the contact form on your website.</p>
        </div>
    </div>
</body>
</html>