{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RECEIPT</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Arial", sans-serif;
            background-color: #f4f7fb;
            padding: 20px;
        }

        .receipt {
            max-width: 700px;
            margin: 40px auto;
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        .header td {
            padding: 20px;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-right: 20px;
        }

        .header-content h1 {
            font-size: 24px;
            color: black;
            margin-bottom: 5px;
        }

        .header-content p {
            font-size: 14px;
            color: black;
        }

        .Title {
            text-align: center;
            padding-top: 20px;
        }

        .info-container {
            margin-top: 30px;
        }

        .info-header td {
            padding: 5px;
            font-size: 14px;
            border-bottom: 2px solid #ddd;
        }

        .info-box {
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .info-item {
            padding: 5px 0;
            border-bottom: 1px dashed #ddd;
            font-size: 14px;
            line-height: 2;
        }

        .info-item span {
            font-weight: bold;
            color: #333;
        }

        .info-amount {
            font-size: 18px;
            font-weight: bold;
            text-align: left;
            margin-top: 20px;
            color: #d9534f;
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            line-height: 1.5;
        }

        .footer td {
            padding: 5px;
        }

        .designation-section {
            text-align: start;
        }

        .main-table td {
            border-bottom: 2px dotted #ddd;
            padding: 4px;

        }

        .signature {
            width: 60px;
            height: 40px;
            display: block;
            margin-left: 70px;
        }

  
    </style>
</head>

<body>
 
    <div class="receipt">
        <table class="header"
            style="background-color: {{ $data['primaryColor'] ?? '#eeb6b9' }}; opacity:0.9; border-radius: 10px; text-align: center;">

            <tr>
                <td rowspan="1"><img src="{{ public_path('storage/society/logo/' . $data['societyLogo']) }}"
                        alt="Logo" class="logo" />
                </td>
                <td style="color:white;">
                    <h2 style="margin-bottom: 10px;">{{ $data['societyName'] }}</h2>
                    <p class="address">{{ $data['societyAddress'] }}</p>
                    <p class="contact">Phone: {{ $data['societyPhone'] }}, Email: {{ $data['email'] }}</p>
                </td>
            </tr>
        </table>

        <div class="Title">
            <h2>Payment Receipt</h2>
        </div>

        <div class="info-container">
            <table class="info-header">
                <tr>
                    <td><strong>Purpose:</strong> {{ $data['conference_name'] }} Conference Registration</td>
                    <td>
                        <p style="text-align: right;"><strong>Date:</strong> {{ $data['date'] }}</p>
                    </td>
                </tr>
            </table>
            <div class="info-container">
                <table style="width: 100%;" class="main-table">
                    <tr class="info-item">
                        <td><span>Receipt No:</span></td>

                        <td colspan="4">
                            <p style="text-align: right;"> 
                                {{ $data['transactionId'] }}
                            </p>
                        </td>

                    </tr>
                    <tr class="info-item">
                        <td><span>Received From:</span></td>


                        <td colspan="4">
                            <p style="text-align: right;">
                                {{ $data['namePrefix'] }} {{ $data['name'] }}
                            </p>
                        </td>
                    </tr>
                    <tr class="info-item">
                        <td><span>Amount:</span> </td>

                        <td colspan="4">
                            @if (isset($data['country']))
                                <p style="text-align: right;">{{ $data['country'] == 125 ? 'Rs.' : 'USD' }}
                                    {{ $data['amountInWord'] }}</p>
                            @else
                                <p style="text-align: right;">
                        
                                    {{ $data['country'] == 125 ? 'Rs.' : 'USD' }}
                                    {{ $data['amountInWord'] }}</p>
                            @endif
                        </td>
                    </tr>
                    <tr class="info-item">
                        <td><span>Payment Method:</span> </td>


                        <td colspan="4">
                            <p style="text-align: right;">{{ $data['paymentType'] }}</p>
                        </td>
                    </tr>
                </table>
                <div class="info-amount">
                    @if (isset($data['country']))
                        {{ $data['country'] == 125 ? 'Rs.' : 'USD' }} {{ $data['amount'] }}
                    @else
                        {{ $data['country'] == 125 ? 'Rs.' : 'USD' }} {{ $data['amount'] }}
                    @endif
                </div>
            </div>

            <table class="footer">
                <tr>
                    <td class="designation-section">
                        <p>
                            <img class="signature"
                                src="{{ public_path('storage/conference/voucher/signature/' . $data['signature']) }}"><br>
                        <p style="text-align: center;">
                            {{ $data['signatureName'] }} <br />
                            {{ $data['conference_name'] }} <br />
                            Registration Committee Chair
                        </p>
                        </p>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html> --}}
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
                    <img src="{{ public_path('storage/society/logo/' . $data['societyLogo']) }}" alt="Logo">
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
                        <h2>Payment Receipt</h2>
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
            <tr>
                <td colspan="2"><strong>Purpose:</strong> {{ $data['conference_name'] }}</td>
            </tr>
        </table>

        <!-- Items -->
        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount {{ $data['country'] == 125 ? 'Rs.' : 'USD' }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Conference Registration Fee</td>
                    <td>{{ $data['country'] == 125 ? 'Rs.' : '$' }}{{ $data['conferenceAmount'] }}</td>
                </tr>
                @if (!empty($data['accompany']))
                    <tr>
                        <td>Accompany Person X {{ $data['accompany']['accompany_person'] }}</td>
                        <td>{{ $data['country'] == 125 ? 'Rs.' : '$' }}{{ $data['accompany']['amount'] * $data['accompany']['accompany_person'] }}
                        </td>
                    </tr>
                @endif
                @if (!empty($data['addons']))
                    @foreach ($data['addons'] as $addon)
                    {{-- @dd($addon) --}}
                        <tr>
                            <td>{{ $addon['name'] }} X
                                {{ $data['accompany'] == null ? '1' : $data['accompany']['accompany_person'] + 1 }}
                            </td>
                            <td>{{ $data['country'] == 125 ? 'Rs.' : '$' }}{{ $data['accompany'] == null ? $addon['amount'] * 1 : $addon['amount'] * ($data['accompany']['accompany_person'] + 1) }}
                            </td>
                        </tr>
                    @endforeach
                @endif
                {{-- @dd($data) --}}
                @if (!empty($data['workshop']))
                    <tr>
                        <td>{{ $data['workshop']['name'] }}</td>
                        <td>{{ $data['country'] == 125 ? 'Rs.' : '$' }}{{ $data['workshop']['amount'] }}</td>
                    </tr>
                @endif
                @if (!empty($data['serviceCharge']))
                    <tr>
                        <td>Service Charge (3.5%)</td>
                        <td>{{ $data['country'] == 125 ? 'Rs.' : '$' }}{{ $data['serviceCharge'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Total Amount</td>
                    <td><b class="total">{{ $data['country'] == 125 ? 'Rs.' : '$' }}{{ $data['amount'] }}</b></td>
                </tr>
                <tr>
                    <td colspan="2" class="in-words">
                        <b>In words:</b> {{ $data['amountInWord'] }}/-
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Signature -->
        <div class="signature">
            <img src="{{ public_path('storage/conference/voucher/signature/' . $data['signature']) }}" alt="Signature">
            <p>
                <b> {{ $data['signatureName'] }}</b> <br />
                {{ $data['conference_name'] }} - Registration Committee Chair
            </p>
        </div>

    </div>

</body>

</html>
