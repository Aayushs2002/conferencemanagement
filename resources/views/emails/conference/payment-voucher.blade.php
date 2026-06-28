<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payment Receipt</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7fb;
            margin: 0; 
            padding: 20px;
        }

        .receipt {
            max-width: 650px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* HEADER */
        /* .header-table {
            width: 100%;
            background-color: rgba(192, 57, 43, 0.2);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
        } */

        .header-table td {
            vertical-align: middle;
            padding: 20px;
        }

        .header-table img {
            width: 90px;
        }

        .header-text h1 {
            font-size: 22px;
            margin: 0;
            color: white;
        }

        .header-text p {
            font-size: 14px;
            color: white;
            margin: 4px 0;
        }

        /* TITLE SECTION */
        .title-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .title-table td {
            padding: 5px;
        }

        .title h2 {
            font-size: 24px;
            border-bottom: 3px solid rgba(192, 57, 43);
            border-left: 1px solid rgba(192, 57, 43, 0.5);
            border-right: 1px solid rgba(192, 57, 43, 0.5);
            border-top: 1px solid rgba(192, 57, 43, 0.5);
            display: inline-block;
            color: rgba(192, 57, 43);
            margin: 0;
            height: 42px;
            border-radius: 5px;
            padding: 3px 10px;
            line-height: 30px;
        }

        /* INFO GRID USING TABLE */
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 15px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 0;
        }

        .info-table .right {
            text-align: right;
        }

        /* ITEMS TABLE */
        .items {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
            font-size: 15px;
        }

        .items th,
        .items td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }

        .items th {
            background-color: #f9f9f9;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #c0392b;
            margin-top: 20px;
        }

        .in-words {
            font-style: italic;
            color: #333;
            font-size: 15px;
            text-align: center;
        }

        .signature {
            margin-top: 20px;
            text-align: center;
        }

        .signature img {
            height: 80px;
        }

        .signature p {
            margin-top: 5px;
            font-size: 14px;
            color: #333;
        }

        @media (max-width: 600px) {

            .title-table,
            .info-table {
                font-size: 13px;
            }

            .title h2 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="receipt">

        <!-- Header -->
        <table class="header-table"
            style=" width: 100%;background-color:{{ $data['primaryColor'] ?? '#eeb6b9' }};
            opacity:0.9;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;">
            <tr>
                <td style="width:100px;">
                    @if (!empty($data['societyLogo']))
                        <img src="{{ public_path('storage/society/logo/' . $data['societyLogo']) }}" alt="Logo">
                    @endif
                </td>
                <td>
                    <div class="header-text">
                        <h1>{{ $data['societyName'] }}</h1>
                        <p>{{ $data['societyAddress'] }}</p>
                        <p>Phone: {{ $data['societyPhone'] }} | Email: {{ $data['email'] }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Title -->
        <table class="title-table">
            <tr>
                <td style="width:150px;"><strong>Receipt No:</strong> {{ $data['transactionId'] }}</td>
                <td style="text-align:center;">
                    <div class="title">
                        <h2>{{ !empty($data['is_unpaid']) ? 'Payment Voucher' : 'Payment Receipt' }}</h2>
                    </div>
                </td>
                <td style="width:180px; text-align:right;"><strong>Date:</strong> {{ $data['date'] }}</td>
            </tr>
        </table>

        <!-- Info Table -->
        <table class="info-table">
            <tr>
                <td><strong>Received From:</strong> {{ $data['namePrefix'] }} {{ $data['name'] }}</td>
                <td class="right"><strong>Payment Method:</strong> {{ $data['paymentType'] }}</td>
            </tr>
            @if (!empty($data['is_unpaid']))
                <tr>
                    <td colspan="2"><strong>Payment Status:</strong> Credit</td>
                </tr>
            @endif
            <tr>
                <td colspan="2"><strong>Purpose:</strong> {{ $data['conference_name'] }}</td>
            </tr>
        </table>

        <!-- Items -->
        @php
            $paymentCurrency = strtoupper($data['paymentCurrency'] ?? '');
            $currencySymbol = $data['currencySymbol'] ?? null;

            if (empty($currencySymbol)) {
                if ($paymentCurrency === 'INR') {
                    $currencySymbol = 'INR';
                } elseif (($data['country'] ?? null) == 125) {
                    $currencySymbol = 'Rs.';
                } else {
                    $currencySymbol = '$';
                }
            }

            $isINR = $paymentCurrency === 'INR';
            $displayAmount = isset($data['displayAmount']) ? (float) $data['displayAmount'] : (float) ($data['amount'] ?? 0);
            $baseAmount = isset($data['amount']) ? (float) $data['amount'] : 0;
            $conversionRate = 1;

            // Keep line-item math aligned with total for INR mails.
            if ($isINR && $displayAmount > 0 && $baseAmount > 0) {
                $conversionRate = $displayAmount / $baseAmount;
            }
        @endphp
        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount {{ $currencySymbol }}</th>
                </tr>
            </thead>
            <tbody>
                
                @if ($data['conferenceAmount'])
                    <tr>
                        <td>Conference Registration Fee</td>
                        <td>{{ $currencySymbol }}{{ $isINR ? number_format($data['conferenceAmount'] * $conversionRate, 2) : $data['conferenceAmount'] }}</td>
                    </tr>
                @endif
                @if (!empty($data['accompany']))
                    <tr>
                        <td>Accompany Person X {{ $data['accompany']['accompany_person'] }}</td>
                        <td>{{ $currencySymbol }}{{ $isINR ? number_format($data['accompany']['amount'] * $data['accompany']['accompany_person'] * $conversionRate, 2) : ($data['accompany']['amount'] * $data['accompany']['accompany_person']) }}
                        </td>
                    </tr>
                @endif
                @if (!empty($data['addons']))
                    @foreach ($data['addons'] as $addon)
                        @php
                            $availabilityType = $addon['availability_type'] ?? 'both';
                        @endphp
                        
                        @if ($availabilityType === 'participant_only')
                            {{-- Only show participant line --}}
                            <tr>
                                <td>{{ $addon['name'] }}</td>
                                <td>{{ $currencySymbol }}{{ $isINR ? number_format($addon['amount'] * $conversionRate, 2) : $addon['amount'] }}</td>
                            </tr>
                        @elseif ($availabilityType === 'accompany_only')
                            {{-- Only show guest line --}}
                            @if (!empty($data['accompany']))
                                @php
                                    $guestAddonPrice = isset($addon['guest_amount']) && $addon['guest_amount'] > 0 ? $addon['guest_amount'] : $addon['amount'];
                                @endphp
                                <tr>
                                    <td>{{ $addon['name'] }} X {{ $data['accompany']['accompany_person'] }}</td>
                                    <td>{{ $currencySymbol }}{{ $isINR ? number_format($guestAddonPrice * $data['accompany']['accompany_person'] * $conversionRate, 2) : ($guestAddonPrice * $data['accompany']['accompany_person']) }}</td>
                                </tr>
                            @endif
                        @else
                            {{-- Both - show participant line --}}
                            @if ($addon['amount'] > 0)
                                <tr>
                                    <td>{{ $addon['name'] }} (Main Attendee)</td>
                                    <td>{{ $currencySymbol }}{{ $isINR ? number_format($addon['amount'] * $conversionRate, 2) : $addon['amount'] }}</td>
                                </tr>
                            @endif
                            {{-- Show guest line if include_guest is true --}}
                            @if (!empty($data['accompany']) && $addon['include_guest'])
                                @php
                                    $guestAddonPrice = isset($addon['guest_amount']) && $addon['guest_amount'] > 0 ? $addon['guest_amount'] : $addon['amount'];
                                @endphp
                                <tr>
                                    <td>{{ $addon['name'] }} (Accompanying Persons) X {{ $data['accompany']['accompany_person'] }}</td>
                                    <td>{{ $currencySymbol }}{{ $isINR ? number_format($guestAddonPrice * $data['accompany']['accompany_person'] * $conversionRate, 2) : ($guestAddonPrice * $data['accompany']['accompany_person']) }}</td>
                                </tr>
                            @endif
                        @endif
                    @endforeach
                @endif
                {{-- @dd($data) --}}
                {{-- @dd($data['workshop']) --}}
                @if (!empty($data['workshop']))
                    @foreach ($data['workshop'] as $workshop)
                        <tr>
                            <td>{{ $workshop['name'] }}</td>
                            <td>{{ $currencySymbol }}{{ $isINR ? number_format($workshop['amount'] * $conversionRate, 2) : $workshop['amount'] }}</td>
                        </tr>
                    @endforeach
                @endif
                @if (!empty($data['serviceCharge'])) 
                    <tr>
                        <td>Service Charge (3.5%)</td>
                        <td>{{ $currencySymbol }}{{ $isINR ? number_format($data['serviceCharge'] * $conversionRate, 2) : $data['serviceCharge'] }}</td>
                    </tr>
                @endif
                @if (!empty($data['is_unpaid']))
                    <tr>
                        <td>Credit Amount</td>
                        <td>
                            <b class="total">-{{ $currencySymbol }}{{ number_format(abs((float) ($data['due_or_credit_amount'] ?? $data['amount'] ?? 0)), 2) }}</b>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>Total Amount</td>
                        <td><b class="total">{{ $currencySymbol }}{{ $isINR ? number_format($displayAmount, 2) : $data['amount'] }}</b></td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2" class="in-words">
                        <b>In words:</b> {{ $data['amountInWord'] }}/-
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Signature -->
        <div class="signature">
            @if (!empty($data['signature']))
                <img src="{{ public_path('storage/conference/voucher/signature/' . $data['signature']) }}" alt="Signature">
            @endif
            <p>
                <b> {{ $data['signatureName'] }}</b> <br />
                {{ $data['conference_name'] }} - Registration Committee Chair
            </p>
        </div>

    </div>

</body>

</html>
