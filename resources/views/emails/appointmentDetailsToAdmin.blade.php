{{-- File: resources/views/emails/appointmentDetailsToAdmin.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
</head>
<body>
    <p>Dear Admin,</p>

    <p>Please find below the details of the upcoming appointment scheduled for {{ $enquiry->name }}:</p>
    <ul>
        <li><strong>Patient Name:</strong> {{ $enquiry->name }}</li>
        <li><strong>Date of Appointment:</strong> {{ $enquiry->date }}</li>
        <li><strong>Time of Appointment:</strong> {{  $enquiry->scheduleDayTimeSlot ? $enquiry->scheduleDayTimeSlot->start_from : "no time set" }}</li>
        <li><strong>Branch:</strong> {{ $enquiry->branch }}</li>
        <li><strong>Type of Service:</strong> {{ $enquiry->service->name }}</li>
        <li><strong>Contact Information:</strong> {{ $enquiry->phone }}, {{ $enquiry->email }}</li>
        <li><strong>Special Instructions:</strong> {{ $enquiry->message }}</li>
    </ul>

    <p>Please ensure that the necessary preparations are made to accommodate this appointment effectively. Should there be any changes or additional requirements, I will update you accordingly.</p>

    <p>Thank you for your attention to this matter.</p>

    <p>Best regards,</p>
    <p>[Your Name]<br>
    Customer Service Representative<br>
    Rident Dental Centers<br>
    customercare@ridentdentalcenters.com</p>
</body>
</html>
