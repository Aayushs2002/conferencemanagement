<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Conference Registration</title>
</head>

<body>
    <div>
        <h3>Dear {{ $data['namePrefix'] . ' ' . $data['name'] }},</h3>
    </div>
    <br>
    <div>
        <p>I hope this message finds you well. We are delighted to confirm your registration for the conference
            {{ $data['conference_theme'] }}.</p>
        <p>Thank you for joining us. We look forward to your participation and an engaging experience at the event.</p>
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>SAFOGCON 2025</p>
    </div>
</body>

</html>
