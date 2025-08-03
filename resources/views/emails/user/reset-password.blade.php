<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SANCON Conference login password reset.</title>
</head>

<body>
    <div>
        <h3>Dear, {{ $data['receiverName'] }}</h3>
    </div>
    <br>
    <div>
        <p>We hope this message finds you well. We want to inform you that your dashboard password has been reset.</p>
        <p>Following are new details to login into conference dashboard. Thank You.</p>
    </div>
    <br>
    <div>
        <p><a href="{{ config('app.url') }}/login" target="_blank">Click here for login</a></p>
        <p>Email: {{ $data['loginEmail'] }}</p>
        <p>Password: {{ $data['generatedPassword'] }}</p>
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>{{ $data['conference_name'] }}</p>
    </div>
</body>

</html>
