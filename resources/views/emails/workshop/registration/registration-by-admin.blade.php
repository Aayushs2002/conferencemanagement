<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Workshop Registration Done</title>
</head>

<body>
    <div>
        <h3>Dear {{ $data['name'] }},</h3>
    </div>
    <br>
    <div>
        <p>We hope this message finds you well. We want to express my gratitude for your interest in attending workshop
            @foreach ($data['workshop'] as $workshop)
                {{ $workshop['name'] }}.
            @endforeach
        </p>
        <p>We feel pleasure to inform you that, your request has been done. Thank You.</p>
    </div>
    <br>
    @if ($data['type'] == 1)
        <div>
            <p><a href="{{ config('app.url') }}/login" target="_blank">Click here for login</a></p>
            <p>Email: {{ $data['email'] }}</p>
            <p>Password: {{ $data['password'] }}</p>
        </div>
        <br>
    @endif
    <div>
        <p>Best Regards,</p>
        <p>{{ $data['conference_name'] }}</p>
    </div>
</body>

</html>
