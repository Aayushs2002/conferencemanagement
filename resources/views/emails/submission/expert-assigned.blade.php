<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Submission Assigned For Review</title>
    <style>
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div>
        <h3>Dear {{ $data['namePrefix'] . ' ' . $data['name'] }},</h3>
    </div>
    <br>
    <div>
        <p>We hope this message finds you well. We want to inform you that a presentation submission for a topic
            ({{ $data['topic'] }}) has been assigned to you to review and make a decision for the request.</p>
        <p>Please check your dashboard for more details. Thank You.</p>
    </div>
    <br>
    <div>
        {{-- <a href="{{ route('submission.index') }}" class="button">Go to Submission</a> --}}
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
                <p>SAFOGCON 2025</p>

    </div>
</body>

</html>
