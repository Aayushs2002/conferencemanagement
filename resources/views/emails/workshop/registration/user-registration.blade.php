<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Workshop Registration Received</title>
</head>

<body>
    <div>
        <h3>Dear {{ $data['name'] }},</h3>
    </div>
    <br>
    <div>
        <p>We hope this message finds you well. We want to express my gratitude for registering to the workshop
            {{ $data['workshop']['name'] }}.</p>
        @if (strtolower($data['paymentType']) == 'bank transfer')
            <p>
                We have received your registration details and your selected payment method is
                <strong>{{ $data['paymentType'] }}</strong>.
                Your registration is currently <strong>pending verification</strong>.
                Once your payment is reviewed and approved, you will receive a confirmation email.
            </p>
        @else
            <p>
                We are pleased to inform you that your registration has been
                <strong>successfully confirmed</strong>.
                Please keep this email as a reference for your records.
            </p>
        @endif
        <p>Thank You.</p>
    </div>
    <br>
    <div>
        <p>Best Regards,</p>
        <p>{{ $data['conference_name'] }}</p>
    </div>
</body>

</html>
