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
        <p>We hope this message finds you well.<br>
            We feel pleasure to inform you that, you have been registered to conference (Theme:
            {{ $data['conference_theme'] }}).<br>
            Please keep this mail safe for your reference.<br>
            @if ($data['invitationType'] == 1)
                Below are your login details to access the dashboard of conference.<br>
            @endif
            Thank you.
        </p>
    </div>
    <br>
    @if ($data['invitationType'] == 1)
        <div>
            <p><a href="{{ config('app.url') }}/login" target="_blank">Click here for login</a></p>
            <p>Email: {{ $data['email'] }}</p>
            <p>Password: {{ $data['password'] }}</p>
        </div>
        <br>
    @endif
    <div>
        <p>Best Regards,</p>
        <p>SAFOGCON 2025</p>

    </div>
</body>

</html>
