<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Conference Registration Status</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    {{-- Greeting --}}
    <h3>Dear {{ $data['namePrefix'] . ' ' . $data['name'] }},</h3>

    {{-- Intro --}}
    <p>
        Thank you for registering for the <strong>{{ $data['conference_name'] }}</strong>
        (Theme: <em>{{ $data['conference_theme'] }}</em>).
        @if (!empty($data['workshop']))
            We also acknowledge your interest in attending the
            <strong>workshop: {{ $data['workshop']['name'] }}</strong>.
        @endif
    </p>

    {{-- Payment Status --}}
    @if (strtolower($data['paymentType']) == 'bank transfer')
        <p>
            We have received your registration details and your selected payment method is
            <strong>{{ $data['paymentType'] }}</strong>.
            Your registration is currently <strong>pending verification</strong>.
            Once your payment is reviewed and approved, you will receive a confirmation email
            along with your registration details.
        </p>
    @else
        <p>
            We are pleased to inform you that your registration has been
            <strong>successfully confirmed</strong>.
            Please keep this email as a reference for your records.
        </p>
    @endif

    {{-- Registration Details --}}
    <h4>Registration Summary:</h4>
    <ul>
        <li><strong>Transaction ID:</strong> {{ $data['transactionId'] }}</li>
        <li><strong>Payment Type:</strong> {{ $data['paymentType'] }}</li>
        <li><strong>Amount Paid:</strong> {{ $data['amount'] }} ({{ $data['amountInWord'] }})</li>
        <li><strong>Date:</strong> {{ $data['date'] }}</li>
    </ul>

    {{-- Add-ons Section --}}
    @if (!empty($data['addons']))
        <h4>Selected Add-ons:</h4>
        <ul>
            @foreach ($data['addons'] as $addon)
                <li>{{ $addon['name'] }} – {{ $addon['amount'] }}</li>
            @endforeach
        </ul>
    @endif

    {{-- Workshop Section --}}
    @if (!empty($data['workshop']))
        <h4>Workshop Registration:</h4>
        <ul>
            <li><strong>Workshop Name:</strong> {{ $data['workshop']['name'] }}</li>
            <li><strong>Workshop Amount:</strong> {{ $data['workshop']['amount'] }}</li>
        </ul>
    @endif

    {{-- Closing --}}
    <p>
        If you have any questions or require assistance, please contact us at
        <strong>{{ $data['societyEmail'] }}</strong> or call <strong>{{ $data['societyPhone'] }}</strong>.
    </p>

    <br>
    <p>Best regards,</p>
    <p><strong>{{ $data['conference_name'] }}</strong></p>
</body>

</html>
