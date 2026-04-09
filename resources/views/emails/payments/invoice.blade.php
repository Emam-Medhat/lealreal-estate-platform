<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .invoice-details {
            margin-bottom: 30px;
        }

        .property-summary {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            color: #D4AF37;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>إشعار تأكيد الدفع</h1>
            <p>شكراً لثقتكم بنا</p>
        </div>

        <div class="invoice-details">
            <p><strong>عزيزي {{ $payment->user->name }},</strong></p>
            <p>تم استلام دفعتك بنجاح. إليك ملخص المعاملة:</p>

            <ul>
                <li>رقم المعاملة: {{ $payment->transaction_id }}</li>
                <li>التاريخ: {{ $payment->processed_at->format('Y-m-d H:i') }}</li>
                <li>وسيلة الدفع: {{ $payment->method->name ?? 'البطاقة البنكية' }}</li>
            </ul>
        </div>

        <div class="property-summary">
            <h3>تفاصيل العقار:</h3>
            <p>{{ $payment->property->title }}</p>
            <p>{{ $payment->property->full_address }}</p>
        </div>

        <div class="total">
            الإجمالي المدفوع: {{ number_format($payment->amount) }} {{ $payment->currency }}
        </div>

        <div class="footer">
            هذه فاتورة آلية صادرة عن نظام العقارات. جميع الحقوق محفوظة &copy; {{ date('Y') }}
        </div>
    </div>
</body>

</html>