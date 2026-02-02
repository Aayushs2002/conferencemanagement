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
        <p>We hope this message finds you well.</p>
        
        @if (isset($data['password_changed']) && $data['password_changed'])
            <div style="background-color: #f8f9fa; border: 2px solid #007bff; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #007bff;">🔐 Your Login Credentials</h3>
                <p style="margin-bottom: 15px;">For security purposes, your password has been updated. Please use the following credentials to access your reviewer dashboard:</p>
                
                <div style="margin: 10px 0; padding: 10px; background-color: white; border-left: 3px solid #28a745;">
                    <span style="font-weight: bold; color: #555;">Email:</span><br>
                    <span style="font-size: 16px; color: #000; font-family: 'Courier New', monospace;">{{ $data['email'] }}</span>
                </div>
                
                <div style="margin: 10px 0; padding: 10px; background-color: white; border-left: 3px solid #28a745;">
                    <span style="font-weight: bold; color: #555;">New Password:</span><br>
                    <span style="font-size: 16px; color: #000; font-family: 'Courier New', monospace;">{{ $data['password'] }}</span>
                </div>

                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 15px 0;">
                    <strong>⚠️ Important Security Notice:</strong><br>
                    Please change your password after logging in for the first time. Keep your credentials secure and do not share them with anyone.
                </div>
            </div>
        @endif

        @if ($bodyContent)
            {!! $bodyContent !!}
        @else
            <p>We want to inform you that a presentation submission for the topic <strong>{{ $data['topic'] }}</strong> has been assigned to you to review and make a decision for the request.</p>
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
