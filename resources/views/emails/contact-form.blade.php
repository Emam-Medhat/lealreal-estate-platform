<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        .value {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Real Estate Pro</h1>
        <p>New Contact Form Message</p>
    </div>

    <div class="content">
        <div class="field">
            <div class="label">Name:</div>
            <div class="value">{{ $contactData['name'] }}</div>
        </div>

        <div class="field">
            <div class="label">Email:</div>
            <div class="value">{{ $contactData['email'] }}</div>
        </div>

        @if(!empty($contactData['phone']))
        <div class="field">
            <div class="label">Phone:</div>
            <div class="value">{{ $contactData['phone'] }}</div>
        </div>
        @endif

        <div class="field">
            <div class="label">Subject:</div>
            <div class="value">{{ $contactData['subject'] }}</div>
        </div>

        <div class="field">
            <div class="label">Message:</div>
            <div class="value">{{ nl2br(e($contactData['message'])) }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This message was sent from the Real Estate Pro contact form.</p>
        <p>{{ now()->format('F j, Y, g:i a') }}</p>
    </div>
</body>
</html>
