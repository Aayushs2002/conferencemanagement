<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendation to Change Presentation Category</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #7367f0; color: #fff; padding: 28px 32px; }
        .header h2 { margin: 0; font-size: 20px; }
        .header p { margin: 6px 0 0; font-size: 13px; opacity: 0.85; }
        .body { padding: 28px 32px; color: #333; font-size: 14px; line-height: 1.7; }
        .change-box { display: flex; align-items: center; justify-content: center; gap: 16px; margin: 24px 0; }
        .type-card { text-align: center; padding: 16px 24px; border-radius: 8px; min-width: 120px; }
        .type-card.current { background: #fff3cd; border: 1px solid #ffc107; }
        .type-card.requested { background: #d1e7dd; border: 1px solid #198754; }
        .type-card .label { font-size: 11px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 6px; color: #666; }
        .type-card .value { font-size: 16px; font-weight: 700; }
        .type-card.current .value { color: #856404; }
        .type-card.requested .value { color: #0f5132; }
        .arrow { font-size: 22px; color: #7367f0; }
        .action-btn { display: inline-block; background: #7367f0; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 14px; margin: 8px 0; }
        .info-box { background: #f0f4ff; border-left: 4px solid #7367f0; border-radius: 4px; padding: 14px 18px; margin: 18px 0; font-size: 13px; color: #444; }
        .footer { background: #f8f9fa; padding: 18px 32px; font-size: 12px; color: #888; text-align: center; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Recommendation: Change Presentation Category</h2>
            <p>{{ $data['conference_name'] }}</p>
        </div>
        <div class="body">
            <p>Dear {{ $data['namePrefix'] }} {{ $data['presenter_name'] }},</p>

            @if ($bodyContent)
                {!! $bodyContent !!}
            @else
                <p>
                    We are writing regarding your abstract submission titled <strong>"{{ $data['topic'] }}"</strong> for
                    <strong>{{ $data['conference_name'] }}</strong>.
                </p>
                <p>
                    After careful review, the organizing committee recommends changing the
                    <strong>Presentation Category</strong> of your submission:
                </p>

                <div class="change-box">
                    <div class="type-card current">
                        <div class="label">Current Category</div>
                        <div class="value">{{ $data['current_article_type'] }}</div>
                    </div>
                    <div class="arrow">&#8594;</div>
                    <div class="type-card requested">
                        <div class="label">Recommended Category</div>
                        <div class="value">{{ $data['requested_article_type'] }}</div>
                    </div>
                </div>

                <p>
                    Please review this recommendation and respond by clicking the button below.
                    You may choose to <strong>accept</strong> or <strong>decline</strong> this change.
                </p>
            @endif

            <div style="text-align: center; margin: 28px 0;">
                <a href="{{ $data['response_link'] }}" class="action-btn">Review &amp; Respond</a>
            </div>

            <div class="info-box">
                <strong>Please note:</strong> If you do not respond within <strong>24 hours</strong>,
                the category will remain unchanged. Accepting this recommendation will update
                your submission category in the conference system.
            </div>

            <p>If you have any questions, please contact the organizing committee.</p>
            <p>Best Regards,<br><strong>{{ $data['conference_name'] }}</strong></p>
        </div>
        <div class="footer">
            This is an automated email from the conference management system.
            Please do not reply directly to this email.
        </div>
    </div>
</body>
</html>
