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
            We feel pleasure to inform you that, you have been invited to conference (Theme:
            {{ $data['conference_theme'] }}).<br>
            Please keep this mail safe for your reference.<br>
            @if ($data['invitationType'] == 1)
                Below are your login details to access the dashboard of conference.<br>
                <div>
                    <p><a href="{{ config('app.url') }}/login" target="_blank">Click here for login</a></p>
                    <p>Email: {{ $data['email'] }}</p>
                    <p>Password: {{ $data['password'] }}</p>
                </div>
                <br>
            @endif
            @if ($data['is_invited'] == 1)
                <strong>Please click the link below to accept or decline this invitation:</strong><br>
                <a href="{{ $data['invitation_url'] }}"
                    style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;">
                    Accept/Decline Invitation
                </a><br>
                <strong>Note:</strong> You must accept this invitation before you can access conference features and
                submit accommodation details if you are an international participant.<br>
            @endif
            @if (!empty($data['is_unpaid']))
                <br>
                <strong>Your registration is currently marked as Unpaid.</strong><br>
                <strong>{{ ($data['due_or_credit_amount'] ?? 0) >= 0 ? 'Due Amount' : 'Credit Amount' }}:</strong>
                {{ ($data['country'] ?? 0) == 125 ? 'Rs.' : '$' }}{{ number_format(abs((float) ($data['due_or_credit_amount'] ?? 0)), 2) }}<br>
                <strong>Payment Link:</strong>
                <a href="{{ $data['payment_link'] ?? config('app.url') }}" target="_blank">Pay Now</a><br>
            @endif
            Thank you.
        </p>
    </div>
    <br>

    <div>
        <p>Best Regards,</p>
        <p>{{ $data['conference_name'] }}</p>
    </div>
</body>

</html>
