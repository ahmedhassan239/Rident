<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تأكيد الموعد</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0; padding: 20px;
            background-color: #f4f4f4;
            color: #333;
            direction: rtl; /* Right to left direction for Arabic */
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #0056b3;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        ul {
            padding-right: 0; /* Adjust padding for RTL */
            list-style: none;
        }
        li {
            margin-bottom: 10px;
            font-size: 16px;
        }
        a {
            color: #0056b3;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="https://ridentdentalcenters.com/assets/images/rident_light_logo.webp" alt="شعار مراكز رايدنت لطب الأسنان" style="width: 150px;">
        </div>
        <h1>تأكيد الموعد</h1>
        <p>عزيزي {{ $enquiry->name }},</p>
        <p>شكرا لك على تحديد موعدك معنا. هذه الرسالة لتأكيد تفاصيل زيارتك القادمة:</p>
        <ul>
            <li><strong>اسم المريض:</strong> {{ $enquiry->name }}</li>
            <li><strong>تاريخ الموعد:</strong> {{ $enquiry->date }}</li>
            <li><strong>التوقيت:</strong> {{  $enquiry->scheduleDayTimeSlot ? $enquiry->scheduleDayTimeSlot->start_from : "لا يوجد توقيت" }} </li> <!-- Make sure to insert time if available -->
            <li><strong>الخدمات:</strong> {{ $enquiry->service->name }}</li>
            <li><strong>الفرع:</strong> {{ $enquiry->branch }}</li>
        </ul>
        <p>يرجى الوصول قبل 15 دقيقة على الأقل لاستكمال أي أوراق ضرورية.</p>
        <p>إذا كنت بحاجة إلى إعادة جدولة أو إلغاء موعدك، يرجى الاتصال بنا قبل 24 ساعة على الأقل.</p>
        <p>لأية استفسارات أو مزيد من المساعدة، يرجى الاتصال بمكتبنا على <a href="tel:+201143886655">+201143886655</a>, واتساب على <a href="https://wa.me/201117432229">+201117432229</a>, أو مراسلتنا عبر البريد الإلكتروني على <a href="mailto:customercare@ridentdentalcenters.com">customercare@ridentdentalcenters.com</a>.</p>
        <div class="footer">
            <p>نحن نتطلع إلى رؤيتك ونلتزم بتقديم أعلى مستوى من الرعاية لك.</p>
            <p>تحياتنا،</p>
            <p>مراكز رايدنت لطب الأسنان</p>
            <p><a href="https://www.ridentdentalcenters.com">www.ridentdentalcenters.com</a></p>
        </div>
    </div>
</body>
</html>
