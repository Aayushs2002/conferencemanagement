<!DOCTYPE html>
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

        /* .header {
            background-color: $data['primaryColor'];
            border-radius: 10px;
            text-align: center;
        } */

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

        /* table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        } */
    </style>
</head>

<body>
    {{-- @dd(asset('storage/society/logo/'. $data['societyLogo'])) --}}
    {{-- @dd($data) --}}
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
                        {{-- <td></td> --}}

                        <td colspan="4">
                            @if (isset($data['country']))
                                <p style="text-align: right;">{{ $data['country'] == 125 ? 'Rs.' : 'USD' }}
                                    {{ $data['amountInWord'] }}</p>
                            @else
                                <p style="text-align: right;">
                                    {{-- {{ $data['paymentType'] == 'FonePay' ? 'Rs.' : 'USD' }} --}}
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

</html>
