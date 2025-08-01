<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Workshop Rejected</title>
</head>

<body>
    <div>
        <h3>Dear {{ $data['name'] }},</h3>
    </div>
    <br>
    <div>
        <p>I hope this message finds you well. I want to express my gratitude for your interest in organizing workshop
            of title "{{ $data['workshopTitle'] }}" and for taking the time to submit your application.</p>
        <p>We feel regret to inform you that, your application has been cancelled as "{{ $data['remarks'] }}". Hope such
            mistakes won't be repeated in future. Thank You.</p>
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>{{ $data['conference_name'] }}</p>
    </div>
</body>

</html>
