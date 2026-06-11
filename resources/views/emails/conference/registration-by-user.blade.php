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
    <h3>Dear {{ ($data['namePrefix'] ?? '') . ' ' . ($data['name'] ?? 'Participant') }},</h3>

    {{-- Intro --}}
    <p>
        Thank you for registering for the <strong>{{ $data['conference_name'] ?? 'Conference' }}</strong>
        (Theme: <em>{{ $data['conference_theme'] ?? 'N/A' }}</em>).
        @if (!empty($data['workshop']))
            We also acknowledge your interest in attending the
            @foreach ($data['workshop'] as $workshop)
                <strong>workshop: {{ $workshop['name'] }}</strong> <br>
            @endforeach
        @endif
    </p>

    {{-- Payment Status --}}
    @php
        $isPendingVerification = (($data['verified_status'] ?? 1) == 0)
            || strtolower($data['paymentType'] ?? '') == 'bank transfer';
    @endphp

    @if ($isPendingVerification)
        <p>
            We have received your registration details and your selected payment method is
            <strong>{{ $data['paymentType'] }}</strong>.
            Your registration is currently <strong>pending verification</strong>.
            @if (strtolower($data['paymentType'] ?? '') == 'bank transfer')
                Once your payment is reviewed and approved, you will receive a confirmation email.
            @else
                Once your registration details and documents are reviewed and approved, you will receive a confirmation email.
            @endif
        </p>
    @else
        <p>
            We are pleased to inform you that your registration has been
            <strong>successfully confirmed</strong>.
            Please keep this email as a reference for your records.
        </p>
    @endif

    {{-- Registration Details --}}
    @php
        $paymentCurrency = strtoupper($data['paymentCurrency'] ?? '');
        $resolvedCurrencySymbol = $data['currencySymbol'] ?? null;

        if (empty($resolvedCurrencySymbol)) {
            if ($paymentCurrency === 'INR') {
                $resolvedCurrencySymbol = 'INR';
            } elseif (($data['country'] ?? null) == 125) {
                $resolvedCurrencySymbol = 'Rs.';
            } else {
                $resolvedCurrencySymbol = '$';
            }
        }

        $isINR = $paymentCurrency === 'INR';
        $displayAmount = isset($data['displayAmount']) ? (float) $data['displayAmount'] : (float) ($data['amount'] ?? 0);
        $baseAmount = isset($data['amount']) ? (float) $data['amount'] : 0;
        $conversionRate = ($isINR && $displayAmount > 0 && $baseAmount > 0) ? ($displayAmount / $baseAmount) : 1;
    @endphp
    <h4>Registration Summary:</h4>
    <ul>
        <li><strong>Transaction ID:</strong> {{ $data['transactionId'] ?? 'N/A' }}</li>
        <li><strong>Payment Type:</strong> {{ $data['paymentType'] ?? 'N/A' }}</li>
        <li><strong>Amount Paid:</strong> {{ $resolvedCurrencySymbol }} {{ number_format($displayAmount, 2) }}
            @if ($isINR)
                <em>(USD ${{ number_format($baseAmount, 2) }} converted to INR)</em>
            @endif
        </li>
        <li><strong>Amount in Words:</strong> {{ $data['amountInWord'] ?? '' }}</li>
        <li><strong>Date:</strong> {{ $data['date'] ?? date('F j, Y') }}</li>
    </ul>

    {{-- Add-ons Section --}}
    @if (!empty($data['addons']))
        <h4>Selected Add-ons:</h4>
        <ul>
            @foreach ($data['addons'] as $addon)
                @php
                    $availabilityType = $addon['availability_type'] ?? 'both';
                    $totalAddonQty = 0;
                    $totalAddonAmount = 0;
                    
                    // Calculate based on availability type
                    if ($availabilityType === 'participant_only') {
                        // Only participant
                        $totalAddonQty = 1;
                        $totalAddonAmount = $addon['amount'] ?? 0;
                    } elseif ($availabilityType === 'accompany_only') {
                        // Only guests
                        if (!empty($data['accompany'])) {
                            $totalAddonQty = $data['accompany']['accompany_person'] ?? 0;
                            $guestAddonPrice = isset($addon['guest_amount']) && $addon['guest_amount'] > 0 ? $addon['guest_amount'] : ($addon['amount'] ?? 0);
                            $totalAddonAmount = $guestAddonPrice * $totalAddonQty;
                        }
                    } else {
                        // Both - original logic
                        $totalAddonQty = 1; // Main attendee
                        $totalAddonAmount = $addon['amount'] ?? 0; // Main amount
                        
                        if (!empty($data['accompany']) && ($addon['include_guest'] ?? false)) {
                            $accompanyCount = $data['accompany']['accompany_person'] ?? 0;
                            $totalAddonQty += $accompanyCount; // Add guests if included
                            $guestAddonPrice = isset($addon['guest_amount']) && $addon['guest_amount'] > 0 ? $addon['guest_amount'] : ($addon['amount'] ?? 0);
                            $totalAddonAmount += ($guestAddonPrice * $accompanyCount); // Add guest costs
                        }
                    }
                @endphp
                @if ($totalAddonQty > 0)
                    @php
                        $displayAddonAmount = $isINR ? ($totalAddonAmount * $conversionRate) : $totalAddonAmount;
                    @endphp
                    <li>{{ $addon['name'] ?? 'Addon' }} (x{{ $totalAddonQty }}) – {{ $resolvedCurrencySymbol }} {{ number_format((float) $displayAddonAmount, 2) }}
                        @if ($totalAddonQty > 1 || $availabilityType === 'accompany_only')
                            <em>(Includes accompanying persons)</em>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    @endif

    {{-- Workshop Section --}}
    @if (!empty($data['workshop']))
        <h4>Workshop Registration:</h4>
        <ul>
            @foreach ($data['workshop'] as $workshop)
                @php
                    $workshopAmount = (float) ($workshop['amount'] ?? 0);
                    $displayWorkshopAmount = $isINR ? ($workshopAmount * $conversionRate) : $workshopAmount;
                @endphp
                <li><strong>Workshop Name:</strong> {{ $workshop['name'] ?? 'Workshop' }}</li>
                <li><strong>Workshop Amount:</strong> {{ $resolvedCurrencySymbol }} {{ number_format($displayWorkshopAmount, 2) }}</li>
                <br>
            @endforeach
        </ul>
    @endif

    {{-- Closing --}}
    <p>
        If you have any questions or require assistance, please contact us at
        <strong>{{ $data['societyEmail'] ?? 'N/A' }}</strong> or call <strong>{{ $data['societyPhone'] ?? 'N/A' }}</strong>.
    </p>

    <br>
    <p>Best regards,</p>
    <p><strong>{{ $data['conference_name'] ?? 'Conference' }}</strong></p>
</body>

</html>
