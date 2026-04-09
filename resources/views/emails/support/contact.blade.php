<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة دعم فني</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .field-value {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 5px;
            border-right: 3px solid #1e293b;
        }
        .message-field {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border-right: 3px solid #1e293b;
            white-space: pre-wrap;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 رسالة دعم فني جديدة</h1>
        </div>
        
        <div class="content">
            <div class="field">
                <div class="field-label">الاسم:</div>
                <div class="field-value">{{ $data['name'] }}</div>
            </div>
            
            <div class="field">
                <div class="field-label">البريد الإلكتروني:</div>
                <div class="field-value">{{ $data['email'] }}</div>
            </div>
            
            <div class="field">
                <div class="field-label">الموضوع:</div>
                <div class="field-value">{{ $data['subject'] }}</div>
            </div>
            
            @if(isset($data['report_type']) && $data['report_type'])
            <div class="field">
                <div class="field-label">نوع التقرير:</div>
                <div class="field-value">{{ $data['report_type'] }}</div>
            </div>
            @endif
            
            <div class="field">
                <div class="field-label">الرسالة:</div>
                <div class="message-field">{{ $data['message'] }}</div>
            </div>
            
            <div class="field">
                <div class="field-label">تاريخ الإرسال:</div>
                <div class="field-value">{{ now()->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>
        
        <div class="footer">
            <p>تم إرسال هذه الرسالة من نظام التقارير</p>
            <p>© {{ date('Y') }} جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>
