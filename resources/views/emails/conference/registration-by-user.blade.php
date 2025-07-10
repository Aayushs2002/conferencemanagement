<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Conference Registeration Accepted</title>
</head>

<body>
    <div>
        <h3>Dear {{ $data['namePrefix'] . ' ' . $data['name'] }},</h3>
    </div>
    <br>
    <div>
        <p>We hope this message finds you well.<br>
            We want to express my gratitude for your interest in attending conference (Theme:
            {{ $data['conference_theme'] }}) and for taking your time to register.</p>
        <p>We feel pleasure to inform you that, your registration has been accepted and you will receive pass attached
            with QR code later.<br>
            Please keep this mail safe for your reference.<br>
            Thank you.</p>
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>SAFOGCON 2025</p>

    </div>
</body>

</html>
