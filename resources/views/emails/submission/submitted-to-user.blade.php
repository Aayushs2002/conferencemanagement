<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Paper Submission Submitted</title>
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
            <p>Thank you for submitting your abstract titled “{{ $data['topic'] }}” for {{ $data['conferenceTheme'] }},
                scheduled from {{ $data['conferenceDate'] }}.</p>
            <p>Please note that your abstract is currently under review by our selection committee. We will be in touch
                once
                the review process is complete.</p>
            <p>If you have any questions in the meantime, feel free to contact us at
                {{ $data['conferenceEmail'] ?? $data['societyEmail'] }}.</p>
            <p>Thank you again for your submission!</p>
        @endif
        {{-- @dd($bodyContent) --}}
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>
        <p>{{ $data['conferenceName'] }}</p>
        </p>
        {{-- <p>{{ $data['societyEmail'] }}</p> --}}
    </div>
</body>

</html>
