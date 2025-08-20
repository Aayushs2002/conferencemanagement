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
    <title>Untitled Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tangerine:wght@400;700&display=swap" rel="stylesheet">
    <!--[CSS/JS Files - Start]-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">



    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>


    <script src="{{ asset('certificate/af.min.js') }}"></script>
    <style>
        body {
            font-family: "Josefin Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
        }

        h3 {
            font-family: "Tangerine", cursive;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            var element = document.getElementById('container_content');

            var opt = {
                margin: 0,
                filename: 'san-certificate_' + new Date().getTime() + '.pdf', // Unique filename
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

            // Automatically generate and download the PDF when the page loads
            html2pdf().set(opt).from(element).save().then(() => {
                // Optionally return back after download
                window.history.back(); // Takes the user back to the previous page
                // window.location.href = 'https://conference.san.org.np/';
            });
        });
    </script>
    {{-- <script type="text/javascript">
        $(document).ready(function($) {

            $(document).on('click', '.btn_print', function(event) {
                event.preventDefault();

                //credit : https://ekoopmans.github.io/html2pdf.js

                var element = document.getElementById('container_content');

                //easy
                //html2pdf().from(element).save();

                //custom file name
                //html2pdf().set({filename: 'code_with_mark_'+js.AutoCode()+'.pdf'}).from(element).save();


                //more custom settings
                var opt = {
                    margin: 0,
                    filename: 'san_' + js.AutoCode() + '.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 5
                    },
                    html2canvas: {
                        scale: 3,
                        width: 1700
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'a4',
                        orientation: 'l'
                    },

                };

                // New Promise-based usage:
                html2pdf().set(opt).from(element).save();


            });



        });
    </script> --}}


</head>

<body>
    <div class="text-center" style="padding:20px;">
        <input type="button" id="rep" value="Download" class="btn btn-info btn_print">
    </div>
    <div class="container_content" id="container_content">
        <div class="invoice-box">

            <table width="1400" border="0" cellspacing="0" cellpadding="0"
                style="
              font-size: 18px;
              background: url('{{ asset('certificate/deafult/frame.png') }}') no-repeat center top;
              background-size: 100%;
              padding-bottom: 40px;
            ">
                <tr>
                    <td>
                        <table width="1670" border="0" cellspacing="0" cellpadding="0"
                            style="text-align: center; margin-top: 80px">
                            <tr>
                                <td width="210">&nbsp;</td>
                                <td width="150">
                                    <img src="{{ asset('certificate/deafult/san.jpg') }}"
                                        style="
                          height: 130px;
                          width: 133px;
                          margin-left: 30px;
                          border-radius: 100%;
                          background: #fff;
                          padding: 6px;
                          border: 2px red solid;
                        "
                                        alt="" />
                                </td>
                                <td width="470"
                                    style="
                        text-align: left;
                        font-size: 60px;
                        font-weight: bold;
                        color: red;
                      ">
                                    SANCON-ASPA 2025
                                </td>

                                <td width="350" style="text-align: left">
                                    <img src="{{ asset('frontend/assets/images/logo/ASPN.png') }}" alt=""
                                        style="
                          height: 140px;
                          border-radius: 100%;
                          padding: 6px;
                          border: 2px red solid;
                        " />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="padding-top: 0px; text-align: center; line-height: 0px">
                            <tr>
                                <td width="248">&nbsp;</td>
                                <td width="900">
                                    <h1
                                        style="
                          font-size: 35px;
                          width: auto;
                          height: 45px;
                          line-height: 50px;
                          padding: 10px 0px;
                          margin: 5px 0px;
                        ">
                                        "Scaling new heights in Pediatric Anesthesia and beyond"
                                    </h1>
                                </td>
                                <td width="255">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="padding-top: 0px; text-align: center; line-height: 0px">
                            <tr>
                                <td width="348">&nbsp;</td>
                                <td width="700">
                                    <h1
                                        style="
                          text-transform: uppercase;
                          font-size: 40px;
                          background-color: #26268e;
                          width: auto;
                          height: 60px;
                          line-height: 50px;
                          padding: 10px 0px;
                          margin: 20px 0px;
                          overflow: hidden;
                          color: #fff;
                        ">
                                        certificate of Appreciation
                                    </h1>
                                </td>
                                <td width="355">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="padding-top: 10px; text-align: center; line-height: 0px">
                            <tr>
                                <td width="300">&nbsp;</td>
                                <td width="1098">
                                    <h2 style="font-weight: 500; font-size: 60px;padding-top:20px;">
                                        This Certificate has been awarded
                                    </h2>
                                </td>
                                <td width="300">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="text-align: center; line-height: 0px">
                            <tr>
                                <td width="215">&nbsp;</td>
                                <td width="980">
                                    <h6 style="font-size: 40px; margin: 0px; padding: 10px 0px">
                                        to
                                    </h6>
                                    <h3
                                        style="
                          font-weight: 500;
                          font-size: 80px;
                          margin-bottom: 15px;
                          color: red;
                        ">
                                        {{-- {{ $participant->user->namePrefix->prefix ?? null }}
                                        {{ $participant->user->fullname($participant, 'user') }} --}}
                                    </h3>
                                </td>
                                <td width="215">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="1698" border="0" cellspacing="0" cellpadding="0"
                            style="text-align: center; font-size: 22px; line-height: 32px">
                            <tr>
                                <td width="260">&nbsp;</td>
                                <td width="1210">
                                    <h1 style="line-height: 60px; margin-bottom: 0px">
                                        for participating as
                                        {{-- <strong>{{ $participant->registrant_type == 2 ? ' a Speaker' : 'an Attendee' }}</strong> --}}
                                        <strong>
                                            {{-- @if ($participant->registrant_type == 1)
                                                an Attendee
                                            @elseif ($participant->registrant_type == 3)
                                                a Session Chair
                                            @elseif (
                                                ($participant->registrant_type == 2 && $participant->user->userDetail->country->country_name != 'Nepal') ||
                                                    $participant->user->userDetail->member_type_id == 1)
                                                a Faculty
                                            @elseif(
                                                $participant->registrant_type == 2 &&
                                                    ($participant->user->userDetail->member_type_id == 2 || $participant->user->userDetail->member_type_id == 4))
                                                a Speaker
                                            @endif --}}

                                        </strong>
                                        in the <br />
                                        <small><b
                                                style="
                              font-size: 60px;
                              line-height: 80px;
                              margin: 20px 0px;
                              color: red;
                            ">SANCON-ASPA
                                                2025</b>
                                            <br />held on 4<sup>th</sup> and 5<sup>th</sup> April, 2025,
                                            Kathmandu,
                                            Nepal</small><br /><i
                                            style="font-size:20px; margin:0px; padding:0px">Accredited by NMC for CPD
                                            Points</i>
                                    </h1>
                                </td>

                                <td width="200"></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table width="1600" border="0" cellspacing="0" cellpadding="0"
                            style="margin-top: 10px; margin-bottom: 100px">
                            <tbody>
                                <tr>
                                    <td style="padding: 0px 0px 0px 50px; text-align: center">
                                        <table border="0" width="250" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr></tr>
                                                <tr>
                                                    <td width="250" align="center">
                                                        <img src="{{ asset('certificate/deafult/basu.png') }}"
                                                            alt="" style="height: 75px" />
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="250" style="border-bottom: #000 2px dotted">
                                                        &nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="250" align="center">
                                                        <span
                                                            style="
                                    text-align: center;
                                    line-height: 35px;
                                    padding: 12px 0px;
                                    font-size: 22px;
                                  "><b>Dr.
                                                                Bashu Dev Parajuli </b><br />(Organizing
                                                            Secretary)
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="padding: 0px 0px 0px 0px; text-align: center">
                                        <table border="0" width="240" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td width="260" align="center">
                                                        <img src="{{ asset('certificate/deafult/ponde.png') }}"
                                                            alt="" style="height: 75px" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="260" style="border-bottom: #000 2px dotted">
                                                        &nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="260" align="center">
                                                        <span
                                                            style="
                                    text-align: center;
                                    line-height: 35px;
                                    padding: 12px 0px;
                                    font-size: 22px;
                                  "><b>Dr.
                                                                Vrushali Ponde</b><br />
                                                            (ASPA Scientific chair)</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="padding: 0px 0px 0px 10px; text-align: center">
                                        <table width="250" border="0" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td width="260" align="center">
                                                        <img src="{{ asset('certificate/deafult/anil.png') }}"
                                                            alt="" style="height: 75px" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="260" style="border-bottom: #000 2px dotted">
                                                        &nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="260" align="center">
                                                        <span
                                                            style="
                                    text-align: center;
                                    line-height: 35px;
                                    padding: 12px 0px;
                                    font-size: 22px;
                                  "><b>Prof.
                                                                Dr. Anil Shrestha </b><br />
                                                            (Scientific Chairperson)</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="padding: 0px 0px 0px 10px; text-align: center">
                                        <table border="0" width="280" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td width="260" align="center">
                                                        <img src="{{ asset('certificate/deafult/aspa.png') }}"
                                                            alt="" style="height: 75px" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="260" style="border-bottom: #000 2px dotted">
                                                        &nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="260" align="center">
                                                        <span
                                                            style="
                                    text-align: center;
                                    line-height: 35px;
                                    padding: 12px 0px;
                                    font-size: 22px;
                                  "><b>Dr
                                                                Z Serpil Ustalar Ozgen</b><br />
                                                            (ASPA President)</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="padding: 0px 15px 0px 0px; text-align: center">
                                        <table width="310" border="0" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td width="260" align="center">
                                                        <img src="{{ asset('certificate/deafult/amir.png') }}"
                                                            alt="" style="height: 75px" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="260" style="border-bottom: #000 2px dotted">
                                                        &nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="260" align="center">
                                                        <span
                                                            style="
                                    text-align: center;
                                    line-height: 35px;
                                    padding: 12px 0px;
                                    font-size: 22px;
                                  "><b>Prof.
                                                                Dr. Amir Babu Shrestha </b><br />(Organizing
                                                            Chairperson)
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
