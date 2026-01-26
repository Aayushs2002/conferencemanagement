<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Multiple Submissions Assigned For Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .credentials-box {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .credentials-box h3 {
            margin-top: 0;
            color: #007bff;
        }

        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-left: 3px solid #28a745;
        }

        .credential-label {
            font-weight: bold;
            color: #555;
        }

        .credential-value {
            font-size: 16px;
            color: #000;
            font-family: 'Courier New', monospace;
        }

        .submissions-list {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .submission-item {
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
            background-color: #f8f9fa;
        }

        .submission-title {
            font-weight: bold;
            color: #17a2b8;
            font-size: 16px;
        }

        .submission-details {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .count-badge {
            background-color: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }

        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .warning-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="container">
        <div>
            <h2>Dear {{ $data['namePrefix'] . ' ' . $data['name'] }},</h2>
        </div>
        
        <br>

        <div>
            <p>We hope this message finds you well.</p>
            
            <div class="alert-info">
                <strong>📋 Multiple Submissions Assigned</strong><br>
                You have been assigned <span class="count-badge">{{ $data['total_count'] }}</span> 
                presentation submission(s) for review and evaluation.
            </div>

            @if ($data['password_changed'])
                <div class="credentials-box">
                    <h3>🔐 Your Login Credentials</h3>
                    <p style="margin-bottom: 15px;">For security purposes, your password has been updated. Please use the following credentials to access your reviewer dashboard:</p>
                    
                    <div class="credential-item">
                        <span class="credential-label">Email:</span><br>
                        <span class="credential-value">{{ $data['email'] }}</span>
                    </div>
                    
                    <div class="credential-item">
                        <span class="credential-label">New Password:</span><br>
                        <span class="credential-value">{{ $data['password'] }}</span>
                    </div>

                    <div class="warning-note">
                        <strong>⚠️ Important Security Notice:</strong><br>
                        Please change your password after logging in for the first time. Keep your credentials secure and do not share them with anyone.
                    </div>
                </div>
            @endif

            <div class="submissions-list">
                <h3 style="color: #17a2b8; margin-top: 0;">📝 Submissions Assigned to You:</h3>
                
                @foreach ($data['submissions'] as $index => $submission)
                    <div class="submission-item">
                        <div class="submission-title">
                            {{ $index + 1 }}. {{ $submission['title'] }}
                        </div>
                        <div class="submission-details">
                            <strong>Presenter:</strong> {{ $submission['presenter'] }} | 
                            <strong>Type:</strong> {{ $submission['presentation_type'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($bodyContent)
                <div style="margin: 20px 0;">
                    {!! $bodyContent !!}
                </div>
            @else
                <p>Please review these submissions carefully and provide your expert evaluation. You can access all the submissions through your reviewer dashboard.</p>
            @endif

            <div style="margin: 30px 0; text-align: center;">
                <a href="{{ route('my-society.conference.submission.submissionReview', [$data['society_slug'] ?? 'society', $data['conference_slug'] ?? 'conference']) }}" class="button">
                    📋 Review Now
                </a>
                <p style="color: #6c757d; font-size: 14px; margin-top: 10px;">
                    Click the button above to access your reviewer dashboard and start reviewing the assigned submissions.
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>Best Regards,</strong></p>
            <p>{{ $data['conference_name'] }}</p>
            
            <p style="font-size: 12px; color: #6c757d; margin-top: 20px;">
                If you have any questions or need assistance, please don't hesitate to contact us.
            </p>
        </div>
    </div>
</body>

</html>
