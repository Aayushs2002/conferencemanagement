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
        @if ($bodyContent)
            {!! $bodyContent !!}
        @else
            <p>We hope this message finds you well. We want to inform you that a presentation submission for a topic
                ({{ $data['topic'] }}) has been assigned to you to review and make a decision for the request.</p>
            <p>Please check your dashboard for more details. Thank You.</p>
        @endif
    </div>
    <br>
    <div style="text-align: center; margin: 20px 0;">
        <a href="{{ route('my-society.conference.submission.submissionReview', [$data['society_slug'] ?? 'society', $data['conference_slug'] ?? 'conference']) }}" class="button">
            📋 Review Now
        </a>
        <p style="color: #6c757d; font-size: 14px; margin-top: 10px;">
            Click the button above to access your reviewer dashboard.
        </p>
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>{{ $data['conference_name'] }}</p>

    </div>
</body>

</html>
