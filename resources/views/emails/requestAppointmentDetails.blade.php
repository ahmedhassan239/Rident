<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Confirmation</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0; padding: 20px;
            background-color: #f4f4f4;
            color: #333;
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
            padding-left: 0;
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
            <img src="https://ridentdentalcenters.com/assets/images/rident_light_logo.webp" alt="Rident Dental Centers Logo" style="width: 150px;">
        </div>
        <h1>Appointment Confirmation</h1>
        <p>Dear {{ $enquiry->name }},</p>
        <p>Thank you for scheduling your appointment with us. This email is to confirm your upcoming visit details:</p>
        <ul>
            <li><strong>Patient Name:</strong> {{ $enquiry->name }}</li>
            <li><strong>Appointment Date:</strong> {{ $enquiry->date }}</li>
            <li><strong>Appointment Time:</strong> {{  $enquiry->scheduleDayTimeSlot ? $enquiry->scheduleDayTimeSlot->start_from : "no time set" }}</li>
            <li><strong>Services:</strong> {{ $enquiry->service->name }}</li>
            <li><strong>Branch:</strong> {{ $enquiry->branch }}</li>
        </ul>
        <p>Please arrive at least 15 minutes early to complete any necessary paperwork.</p>
        <p>If you need to reschedule or cancel your appointment, please contact us at least 24 hours before.</p>
        <p>For any queries or further assistance, please contact our office at <a href="tel:+201143886655">+201143886655</a>, WhatsApp at <a href="https://wa.me/201117432229">+201117432229</a>, or email us at <a href="mailto:customercare@ridentdentalcenters.com">customercare@ridentdentalcenters.com</a>.</p>
        <div class="footer">
            <p>We look forward to seeing you and are committed to providing you with the highest quality care.</p>
            <p>Warm regards,</p>
            <p>Rident Dental Centers</p>
            <p><a href="https://www.ridentdentalcenters.com">www.ridentdentalcenters.com</a></p>
        </div>
    </div>
</body>
</html>
