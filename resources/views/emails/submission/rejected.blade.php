<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Submission Rejected</title>
</head>

<body>
    <div>
        <h3>Dear {{ $data['namePrefix'] . ' ' . $data['presenter_name'] }},</h3>
    </div>
    <br>
    <div>
        <p>We hope this message finds you well. We want to express my gratitude for your interest in presenting your
            desired topic ({{ $data['topic'] }}) for our conference.</p>
        <p>We have reviewed your submission file and unfortunately we cannot accept your presentation.The reason for
            rejection is {{ $data['remarks'] }}.
            You may contact to administration for further discussion. Thank You.</p>
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>Scientific committee</p>
        {{-- <p>SANCON-ASPA 2025</p> --}}
    </div>
</body>

</html>
