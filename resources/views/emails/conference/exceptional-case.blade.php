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
        <p>I hope this message finds you well. We are delighted to confirm your registration for the conference
            {{ $data['conference_theme'] }}.</p>
        @if (!empty($data['is_unpaid']))
            <p>Your registration has been marked as <strong>Unpaid</strong>.</p>
            <p>
                <strong>{{ ($data['due_or_credit_amount'] ?? 0) >= 0 ? 'Due Amount' : 'Credit Amount' }}:</strong>
                {{ ($data['country'] ?? 0) == 125 ? 'Rs.' : '$' }}{{ number_format(abs((float) ($data['due_or_credit_amount'] ?? 0)), 2) }}
            </p>
            <p>
                Please use the payment link below to complete your conference payment:
                <a href="{{ $data['payment_link'] ?? config('app.url') }}" target="_blank">Pay Now</a>
            </p>
        @else
            <p>Thank you for joining us. We look forward to your participation and an engaging experience at the event.</p>
        @endif
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>{{ $data['conference_name'] }}</p>

    </div>
</body>

</html>
