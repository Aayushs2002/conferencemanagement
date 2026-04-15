<?php
if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    $ssl = 'https';
} else {
    $ssl = 'http';
}

$app_url =
    $ssl .
    '://' .
    $_SERVER['HTTP_HOST'] .
    (dirname($_SERVER['SCRIPT_NAME']) == DIRECTORY_SEPARATOR ? '' : '/') .
    trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

header('Access-Control-Allow-Origin: *');
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sponsor Certificates</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Josefin Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
        }

        h3 {
            font-family: "Tangerine", cursive;
        }

        .superscript {
            font-size: 60%;
            vertical-align: super;
        }

        .certificate-page {
            page-break-after: always;
        }

        .certificate-page:last-child {
            page-break-after: auto;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var element = document.getElementById('container_content');

            var opt = {
                margin: 0,
                filename: 'sponsor-certificates_' + new Date().getTime() + '.pdf',
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 2,
                    width: 1700
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'landscape'
                }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                window.history.back();
            });
        });
    </script>
</head>

@php
    $startDate = \Carbon\Carbon::parse($conference->start_date);
    $endDate = \Carbon\Carbon::parse($conference->end_date);

    function formatDateWithSup($date)
    {
        $day = $date->format('j');
        $suffix = $date->format('S');

        return $day . '<span class="superscript">' . $suffix . '</span> ' . $date->format('F, Y');
    }

    function formatDayWithSup($date)
    {
        $day = $date->format('j');
        $suffix = $date->format('S');

        return $day . '<span class="superscript">' . $suffix . '</span>';
    }

    $dateDisplay = '';

    if ($startDate->isSameDay($endDate)) {
        $dateDisplay = formatDateWithSup($startDate);
    } elseif ($startDate->month === $endDate->month && $startDate->year === $endDate->year) {
        $dateDisplay = formatDayWithSup($startDate) . ' and ' . formatDateWithSup($endDate);
    } else {
        $dateDisplay =
            $startDate->format('j') .
            '<sup>' .
            $startDate->format('S') .
            '</sup> ' .
            $startDate->format('F') .
            ' and ' .
            formatDateWithSup($endDate);
    }

    $venueLocation = $conference->ConferenceVenueDetail
        ? $conference->ConferenceVenueDetail->venue_name
        : 'Conference Venue';

    $certificateBg = '';
    if ($conference->conferenceCertificate && $conference->conferenceCertificate->background_image) {
        $certificateBg =
            'background:url(' .
            asset(
                'storage/conference/conference/certificate/background/' .
                    $conference->conferenceCertificate->background_image,
            ) .
            ') no-repeat center top; background-size:100%;';
    } else {
        $certificateBg = 'background:url(\'frame_1.png\') no-repeat center top; background-size:100%;';
    }

    $showCpdPoints = $conference->conferenceSetting && $conference->conferenceSetting->cpd_points_required == 1;
@endphp

<body>
    <div class="text-center" style="padding:20px;">
        <input type="button" id="rep" value="Download" class="btn btn-info btn_print">
    </div>
    <div class="container_content" id="container_content">
        @foreach ($sponsors as $sponsor)
            <div class="certificate-page">
                <div class="invoice-box">
                    <table width="1300" border="0" cellspacing="0" cellpadding="0"
                        style="font-size:18px; {{ $certificateBg }} padding-bottom:0px;">

                        <tr>
                            <td>
                                <table width="1600" border="0" cellspacing="0" cellpadding="0"
                                    style="text-align:center; height:180px; margin-top:60px;">
                                    <tr>
                                        <td width="1670"
                                            style="text-align:center; font-size:60px; font-weight:bold; color:red;">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td align="center">
                                <table width="1670" border="0" cellspacing="0" cellpadding="0"
                                    style="text-align:center; margin-top:10px;">
                                    <tr>
                                        <td width="500">&nbsp;</td>

                                        <td width="550"
                                            style="text-align:left; font-size:80px; font-weight:bold;color:red;">
                                            {{ $conference->abbreviation ?? 'CONFERENCE' }}</td>

                                        <td width="450" style="text-align:left;">&nbsp;
                                        </td>

                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>

                                <table width="1698" border="0" cellspacing="0" cellpadding="0"
                                    style="padding-top:0px; text-align:center; line-height:0px;">
                                    <tr>

                                        <td width="248">&nbsp;</td>
                                        <td width="900">
                                            @if ($conference->conference_theme)
                                                <h1
                                                    style="font-size:35px; width:auto; height:45px; line-height:50px; padding: 0px; margin:5px 0px;">
                                                    "{{ $conference->conference_theme }}"</h1>
                                            @endif
                                        </td>
                                        <td width="255">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>

                                <table width="1698" border="0" cellspacing="0" cellpadding="0"
                                    style="padding-top:0px; text-align:center; line-height:0px;">
                                    <tr>

                                        <td width="348">&nbsp;</td>
                                        <td width="700">
                                            <h1
                                                style="text-transform:uppercase; font-size:48px; background-color:#0c72b4; width:auto; height:60px; line-height:50px; padding:12px 0px; margin:5px 0px; overflow:hidden; color:#fff;">
                                                Certificate of Appreciation</h1>
                                        </td>
                                        <td width="355">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td>

                                <table width="1698" border="0" cellspacing="0" cellpadding="0"
                                    style="padding-top:40px; text-align:center; line-height:40px;">
                                    <tr>

                                        <td width="300">&nbsp;</td>
                                        <td width="1098" style="margin-top: 13px;">

                                            <h2 style="font-weight:500; font-size:60px; padding-top:28px;">This
                                                Certificate has been awarded </h2>
                                        </td>
                                        <td width="300">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="1698" border="0" cellspacing="0" cellpadding="0"
                                    style="text-align:center; line-height:0px;">
                                    <tr>
                                        <td width="315">&nbsp;</td>
                                        <td width="780">
                                            <h6 style="font-size:40px; margin:0px; padding:10px 0px;">to</h6>
                                            <h3 style="font-weight:500; font-size:80px; margin-bottom:15px; color:red;">
                                                {{ $sponsor->name }}</h3>
                                        </td>
                                        <td width="315">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="1698" border="0" cellspacing="0" cellpadding="0"
                                    style="text-align:center; font-size:22px; line-height:32px;">
                                    <tr>
                                        <td width="160">&nbsp;</td>
                                        <td width="1410">
                                            <h1 style="line-height:60px; margin-bottom:10px; font-weight:bold;">
                                                Contribution as a
                                                {{ $sponsor->category?->category_name ?? 'Sponsor' }} in <br />
                                                <small style="font-weight:400;"><b
                                                        style="font-size:35px; font-weight:400; line-height:40px; margin:0px 0px; color:red;">{{ $conference->conference_name }}</b>
                                                    <br />held on {!! $dateDisplay !!}, {{ $venueLocation }}</small><br />
                                                @if ($showCpdPoints)
                                                    <i
                                                        style="font-weight:bold; font-size:20px; margin:0px; display:block; height:60px; padding:0px">NMC
                                                        CPD Point Awarded</i>
                                                @endif
                                            </h1>
                                        <td width="100">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        @if ($conference->conferenceCertificate && !empty($conference->conferenceCertificate->signature))
                            <tr>
                                <td>
                                    <table width="1600" border="0" cellspacing="0" cellpadding="0"
                                        style="margin-top:5px; margin-bottom:50px;">
                                        <tbody>
                                            <tr>
                                                @php
                                                    $signatures = $conference->conferenceCertificate->signature;
                                                    $signatureCount = count($signatures);
                                                    $paddingLeft = 150;
                                                    $cellWidth = 300;
                                                @endphp

                                                @foreach ($signatures as $index => $signature)
                                                    <td
                                                        style="padding:0px {{ $index < $signatureCount - 1 ? '50px' : '15px' }} 0px {{ $index === 0 ? $paddingLeft . 'px' : '10px' }}; text-align:center;">
                                                        <table width="{{ $cellWidth }}" border="0" cellspacing="0"
                                                            cellpadding="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td width="{{ $cellWidth }}" align="center">
                                                                        <img src="{{ asset('storage/conference/conference/certificate/signature/' . $signature['fileName']) }}"
                                                                            alt="{{ $signature['name'] }}"
                                                                            style="height:75px; max-width:100%; object-fit:contain;">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="{{ $cellWidth }}"
                                                                        style="border-bottom:#000 2px dotted;">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="{{ $cellWidth }}" align="center">
                                                                        <span
                                                                            style="text-align:center; line-height:35px; padding:12px 0px; font-size:22px;">
                                                                            <b>{{ $signature['name'] }}</b><br />
                                                                            ({{ $signature['designation'] }})
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
