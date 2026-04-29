 <?php
//--->get app url > start

if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    $ssl = 'https';
} else {
    $ssl = 'http';
}

$app_url =
    $ssl .
    '://' .
    $_SERVER['HTTP_HOST'] .
    //. $_SERVER["SERVER_NAME"]
    (dirname($_SERVER['SCRIPT_NAME']) == DIRECTORY_SEPARATOR ? '' : '/') .
    trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

//--->get app url > end

header('Access-Control-Allow-Origin: *');

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Workshop Certificate</title>
    <!--[CSS/JS Files - Start]-->
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
    </style>
    @if ($workshop->workshopCertificate && $workshop->workshopCertificate->custom_css)
        <style>
            {!! $workshop->workshopCertificate->custom_css !!}
        </style>
    @elseif ($conference->conferenceCertificate && $conference->conferenceCertificate->custom_css)
        <style>
            {!! $conference->conferenceCertificate->custom_css !!}
        </style>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 50%, #e8f0fe 100%);
            min-height: 100vh;
            margin: 0;
            font-family: "Josefin Sans", sans-serif;
            overflow-x: hidden;
        }
        .cert-page-wrap {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 9999;
            background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 50%, #e8f0fe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            box-sizing: border-box;
        }
        .cert-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            padding: 50px 60px;
            max-width: 520px;
            width: 100%;
            text-align: center;
        }
        .icon-circle {
            width: 90px; height: 90px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        .icon-circle.blue  { background: linear-gradient(135deg, #0c72b4, #1e90ff); }
        .icon-circle.green { background: linear-gradient(135deg, #28a745, #20c997); }
        .icon-circle i { font-size: 38px; color: #fff; }
        .cert-card h2 { font-size: 26px; font-weight: 700; color: #1a1a2e; margin: 0 0 10px; }
        .cert-card p  { font-size: 15px; color: #6c757d; margin: 0 0 28px; line-height: 1.7; }
        .conf-name    { font-weight: 700; color: #0c72b4; }
        .cert-divider { border: none; border-top: 1px solid #f0f0f0; margin: 24px 0; }
        .btn-download {
            background: linear-gradient(135deg, #0c72b4, #1e90ff);
            color: #fff; border: none; border-radius: 50px;
            padding: 14px 40px; font-size: 16px; font-weight: 600;
            cursor: pointer; transition: all .3s;
            display: inline-flex; align-items: center; gap: 10px;
            box-shadow: 0 6px 20px rgba(12,114,180,0.35);
            font-family: "Josefin Sans", sans-serif;
        }
        .btn-download:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(12,114,180,0.45); }
        .btn-redownload {
            background: transparent; color: #0c72b4;
            border: 2px solid #0c72b4; border-radius: 50px;
            padding: 11px 32px; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: all .3s;
            display: inline-flex; align-items: center; gap: 8px;
            font-family: "Josefin Sans", sans-serif;
        }
        .btn-redownload:hover { background: #0c72b4; color: #fff; }
        .spinner-wrap { margin: 0 auto 20px; }
        .spinner-ring {
            width: 60px; height: 60px;
            border: 5px solid #e9ecef;
            border-top-color: #0c72b4;
            border-radius: 50%;
            animation: cert-spin .8s linear infinite;
            margin: 0 auto;
        }
        @keyframes cert-spin { to { transform: rotate(360deg); } }
        .cert-state { display: none; }
        .cert-state.active { display: block; }
        .badge-label {
            display: inline-block;
            background: #e8f4fd; color: #0c72b4;
            border-radius: 50px; font-size: 12px;
            font-weight: 600; padding: 4px 14px;
            margin-bottom: 20px; letter-spacing: .5px;
            text-transform: uppercase;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            var isNormalUser = @json(auth()->check() ? (int) auth()->user()->type === 3 : false);

            function showState(id) {
                $('.cert-state').removeClass('active');
                $('#' + id).addClass('active');
            }

            function downloadCertificate() {
                showState('state_loading');

                var element = document.getElementById('container_content');
                var overlay = document.querySelector('.cert-page-wrap');

                // Hide the fixed overlay so only the certificate is visible to html2canvas
                overlay.style.display = 'none';

                var opt = {
                    margin: 0,
                    filename: 'workshop-certificate_' + new Date().getTime() + '.pdf',
                    image: { type: 'jpeg', quality: 1 },
                    html2canvas: {
                        scale: 2,
                        width: 1700,
                        useCORS: true,
                        allowTaint: true,
                        logging: false
                    },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
                };

                // Allow browser to repaint (remove overlay) before capture
                setTimeout(function() {
                    html2pdf().set(opt).from(element).save().then(function() {
                        overlay.style.display = '';
                        showState('state_success');
                    });
                }, 150);
            }

            $(document).on('click', '.btn_print', function(e) {
                e.preventDefault();
                downloadCertificate();
            });

            $(document).on('click', '.btn_redownload', function(e) {
                e.preventDefault();
                downloadCertificate();
            });

            if (isNormalUser) {
                downloadCertificate();
            }
        });
    </script>

</head>

@php
    // Format dates
    $startDate = \Carbon\Carbon::parse($workshop->start_date);
    $endDate = \Carbon\Carbon::parse($workshop->end_date);

    // Check if dates are same day, same month, or different
    if (!function_exists('formatDateWithSup')) {
        function formatDateWithSup($date)
        {
            $day = $date->format('j');
            $suffix = $date->format('S');

            return $day . '<span class="superscript">' . $suffix . '</span> ' . $date->format('F, Y');
        }
    }

    if (!function_exists('formatDayWithSup')) {
        function formatDayWithSup($date)
        {
            $day = $date->format('j');
            $suffix = $date->format('S');

            return $day . '<span class="superscript">' . $suffix . '</span>';
        }
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

    // Get venue location
    $venueLocation = $workshop->WorkshopVenueDetail
        ? $workshop->WorkshopVenueDetail->venue_address
        : 'Workshop Venue';

    // Get certificate background - prioritize workshop certificate, fallback to conference certificate
    $certificateBg = '';
    if ($workshop->workshopCertificate && $workshop->workshopCertificate->background_image) {
        $certificateBg =
            'background:url(' .
            asset('storage/workshop/certificate/background/' . $workshop->workshopCertificate->background_image) .
            ') no-repeat center top; background-size:100%;';
    } elseif ($conference->conferenceCertificate && $conference->conferenceCertificate->background_image) {
        $certificateBg =
            'background:url(' .
            asset('storage/conference/conference/certificate/background/' . $conference->conferenceCertificate->background_image) .
            ') no-repeat center top; background-size:100%;';
    } else {
        $certificateBg = 'background:url(\'frame_1.png\') no-repeat center top; background-size:100%;';
    }

    // Check if CPD points should be shown
    $showCpdPoints = $conference->conferenceSetting && $conference->conferenceSetting->cpd_points_required == 1;

    // User-type based download behavior
    $userType = auth()->check() ? (int) auth()->user()->type : null;
@endphp

<body>

    {{-- ===== Visible UI Card ===== --}}
    <div class="cert-page-wrap">
        <div class="cert-card">

            {{-- Idle state: admin / super-admin only --}}
            @if (in_array($userType, [1, 2], true))
            <div id="state_idle" class="cert-state active">
                <div class="icon-circle blue">
                    <i class="fa fa-file-pdf-o"></i>
                </div>
                <span class="badge-label">Certificate Ready</span>
                <h2>Download Certificate</h2>
                <p>
                    Click the button below to save the certificate for<br>
                    <span class="conf-name">{{ $workshop->workshop_title }}</span><br>
                    as a PDF file.
                </p>
                <hr class="cert-divider">
                <button class="btn_print btn-download">
                    <i class="fa fa-download"></i> Download Certificate
                </button>
            </div>
            @endif

            {{-- Loading / generating state --}}
            <div id="state_loading" class="cert-state {{ $userType === 3 ? 'active' : '' }}">
                <div class="spinner-wrap">
                    <div class="spinner-ring"></div>
                </div>
                <h2 style="margin-top:20px;">Generating Certificate&hellip;</h2>
                <p>Please wait while your certificate is being prepared.<br>Do not close this tab.</p>
            </div>

            {{-- Success state --}}
            <div id="state_success" class="cert-state">
                <div class="icon-circle green">
                    <i class="fa fa-check"></i>
                </div>
                <span class="badge-label" style="background:#e6f9f0; color:#28a745;">Downloaded</span>
                <h2>Download Successful!</h2>
                <p>
                    Your certificate for<br>
                    <span class="conf-name">{{ $workshop->workshop_title }}</span><br>
                    has been saved to your device.
                </p>
                <hr class="cert-divider">
                <button class="btn_redownload">
                    <i class="fa fa-refresh"></i> Download Again
                </button>
            </div>

        </div>
    </div>

    {{-- Certificate markup — in normal document flow so images/backgrounds load fully --}}
    <div class="container_content" id="container_content">
        <div class="invoice-box">

            <table width="1300" border="0" cellspacing="0" cellpadding="0"
                style="font-size:18px; {{ $certificateBg }} padding-bottom:0px;" class="certificate">

                <tr class="ctrl_tr_1">
                    <td>
                        <table width="1600" border="0" cellspacing="0" cellpadding="0"
                            style="text-align:center; height:180px; margin-top:63px;">
                            <tr>
                                <td width="1670"
                                    style="text-align:center; font-size:60px; font-weight:bold; color:red;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="ctrl_tr_2">
                    <td align="center">
                        <table width="1670" border="0" cellspacing="0" cellpadding="0"
                            style="text-align:center; margin-top:10px;">
                            <tr>
                                <td width="500">&nbsp;</td>
                                @if ($conference->conferenceCertificate && ($conference->conferenceCertificate->include_title ?? 1))

                                <td width="550" style="text-align:left; font-size:80px; font-weight:bold;color:red;">
                                    {{ $conference->abbreviation ?? 'WORKSHOP' }}</td>
                                <td width="450" style="text-align:left;">&nbsp;</td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="ctrl_tr_3">
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

                <tr class="ctrl_tr_4">
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="padding-top:0px; text-align:center; line-height:0px;">
                            <tr>
                                <td width="348">&nbsp;</td>
                                <td width="700">
                                    <h1
                                        style="text-transform:uppercase; font-size:48px; background-color:#0c72b4; width:auto; height:68px; line-height:50px; padding:12px 0px; margin:5px 0px; overflow:hidden; color:#fff;">
                                        Certificate of Appreciation</h1>
                                </td>
                                <td width="355">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="ctrl_tr_5">
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="padding-top:40px; text-align:center; line-height:40px;">
                            <tr>
                                <td width="300">&nbsp;</td>
                                <td width="1098" style="margin-top: 13px;">
                                    <h2 style="font-weight:500; font-size:60px; padding-top:28px;">This Certificate has
                                        been awarded </h2>
                                </td>
                                <td width="300">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="ctrl_tr_6">
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="text-align:center; line-height:0px;">
                            <tr>
                                <td width="315">&nbsp;</td>
                                <td width="780">
                                    <h6 style="font-size:40px; margin:0px; padding:10px 0px;">to</h6>
                                    <h3 style="font-weight:500; font-size:80px; margin-bottom:15px; color:red;">
                                        {{ $registrantName }}</h3>
                                </td>
                                <td width="315">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="ctrl_tr_7">
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="text-align:center; font-size:22px; line-height:32px;">
                            <tr>
                                <td width="160">&nbsp;</td>
                                <td width="1410">
                                    <h1 style="line-height:60px; margin-bottom:10px; font-weight:bold;">for
                                        Participating as {{ in_array(strtoupper(substr($registrantType, 0, 1)), ['A','E','I','O','U']) ? 'an' : 'a' }}
                                        {{ $registrantType }} in <br />
                                        <small style="font-weight:400;"><b
                                                style="font-size:35px; font-weight:400; line-height:40px; margin:0px 0px; color:red;">{{ $workshop->workshop_title }}</b>
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

                @php
                    // Combine conference signatures with workshop signature
                    $allSignatures = [];
                    $selectedConferenceSignatures = $workshop->workshopCertificate?->selected_conference_signatures;
                    $hasSpecificConferenceSignatureSelection = is_array($selectedConferenceSignatures);
                    $includeConferenceSignatures =
                        !$workshop->workshopCertificate ||
                        (int) ($workshop->workshopCertificate->include_conference_signatures ?? 1) === 1;
                    
                    // First add conference signatures if available
                    if ($includeConferenceSignatures && $conference->conferenceCertificate && !empty($conference->conferenceCertificate->signature)) {
                        $conferenceSignatures = collect($conference->conferenceCertificate->signature)
                            ->sortBy(function ($signature, $index) {
                                return $signature['order'] ?? ($index + 1);
                            })
                            ->values();

                        if ($hasSpecificConferenceSignatureSelection) {
                            $conferenceSignatures = $conferenceSignatures
                                ->filter(function ($signature) use ($selectedConferenceSignatures) {
                                    return in_array($signature['fileName'] ?? null, $selectedConferenceSignatures ?? []);
                                })
                                ->values();
                        }

                        $allSignatures = $conferenceSignatures->all();
                    }
                    
                    // Then add workshop signature if available
                    if ($workshop->workshopCertificate && $workshop->workshopCertificate->signature_image) {
                        $allSignatures[] = [
                            'image' => $workshop->workshopCertificate->signature_image,
                            'name' => $workshop->workshopCertificate->signature_name ?? '',
                            'designation' => $workshop->workshopCertificate->signature_designation ?? '',
                            'is_workshop' => true
                        ];
                    }
                @endphp

                @if (!empty($allSignatures))
                    <tr class="ctrl_tr_8">
                        <td>
                            <table width="1600" border="0" cellspacing="0" cellpadding="0"
                                style="margin-top:5px; margin-bottom:55px;">
                                <tbody>
                                    <tr>
                                        @php
                                            $signatureCount = count($allSignatures);
                                            $paddingLeft = 150;
                                            $cellWidth = 300;
                                        @endphp

                                        @foreach ($allSignatures as $index => $signature)
                                            <td
                                                style="padding:0px {{ $index < $signatureCount - 1 ? '50px' : '15px' }} 0px {{ $index === 0 ? $paddingLeft . 'px' : '10px' }}; text-align:center;">
                                                <table width="{{ $cellWidth }}" border="0" cellspacing="0"
                                                    cellpadding="0">
                                                    <tbody>
                                                        <tr>
                                                            <td width="{{ $cellWidth }}" align="center">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="{{ $cellWidth }}"  align="center"            
                                                                style="border-bottom: #000 2px dotted;">
                                                                <img src="{{ isset($signature['is_workshop']) ? asset('storage/workshop/certificate/signature/' . $signature['image']) : asset('storage/conference/conference/certificate/signature/' . $signature['fileName']) }}"
                                                                    alt="" style="height: 75px" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="{{ $cellWidth }}" align="center">
                                                                <span
                                                                    style="text-align: center; line-height: 35px; padding: 12px 0px; font-size: 22px;">
                                                                    <b>{{ $signature['name'] ?? '' }}</b><br />
                                                                    ({{ $signature['designation'] ?? '' }})
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
</body>

</html>
