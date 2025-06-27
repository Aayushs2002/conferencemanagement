<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Untitled Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Archivo:ital,wght@0,300;0,500;0,700;1,300;1,500;1,700&family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: "Barlow", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
        }
    </style>
</head>

<body>
    @foreach ($registrants as $registrant)
        <div style="width:1280px; height:auto;">

            <div style="width:550px; float:left !important; margin:20px;">
                <div
                    style="font-size:18px; background:url({{ asset('storage/workshop/pass/' . $passSetting->image) }}) no-repeat center top #66b7ef;  background-size:100%; height:auto; overflow:hidden; padding:20px 0px 0px;">

                    <ul style="width:100%; margin:0px; float:left; padding:0px; padding-top:0px; padding-bottom:20px;">


                        <li
                            style="float:left; padding:30px 0px 0px; width:100%; letter-spacing:-0.3px; text-align:center; display:inline; font-size:50px; line-height:40px; color:#fff; font-weight:700;text-shadow:-1px -1px 0 #000;">
                            SANCON-ASPA 2025
                        </li>


                    </ul>

                    <ul style="width:100%; margin:0px; float:left; padding:0px; padding-bottom:20px;">

                        <li style="float:left; padding-left:60px; text-align:center; display:inline;">
                            <img src="{{ asset('pass/SAN.png') }}" width="80" alt="" />
                        </li>

                        <li style="float:right; padding-right:60px;  text-align:center; display:inline;">
                            <img src="{{ asset('pass/ASPA.png') }}" width="80" alt="" />
                        </li>

                    </ul>

                    <div
                        style="padding:30px 0px 0px;  font-size:20px; font-size:20px; text-align:center; font-weight:normal; line-height:22px;">


                        <small
                            style="font-size:18px; font-weight:500; letter-spacing:-0.02em; color:#000; padding-top:40px;">Venue:
                            {{ $registrant->workshop->venue }}
                        </small>
                        <p
                            style="line-height:30PX; color:white; margin:0px; padding:2px 0px 6px; font-size:16px; font-weight:500;">
                            @if ($registrant->workshop->start_date == $registrant->workshop->end_date)
                                {{ \Carbon\Carbon::parse($registrant->workshop->start_date)->format('jS F, Y') }},
                            @else
                                {{ \Carbon\Carbon::parse($registrant->workshop->start_date)->format('jS') }}
                                -
                                {{ \Carbon\Carbon::parse($registrant->workshop->end_date)->format('jS F, Y') }},
                            @endif Kathmandu, Nepal<br />
                        </p>

                        <h6
                            style="font-size:24px; background:#fff;  margin:5px 0px; line-height:30px; font-weight:500; padding:2px 0px; background-color:rgba(255, 255, 255, 0.1);">
                        </h6>

                        <h1
                            style="font-size:24px;text-transform:capitalize; letter-spacing:-0.01em; background:red;  margin:25px auto 0px; width:180px; color:#fff; border-radius:10px; height:30px; padding:5px 0px;">
                            @if (\Carbon\Carbon::parse($registrant->workshop->start_date)->lt(\Carbon\Carbon::parse('2025-04-04')))
                                Pre-congress
                            @elseif (\Carbon\Carbon::parse($registrant->workshop->start_date)->gt(\Carbon\Carbon::parse('2025-04-05')))
                                Post-congress
                            @endif
                        </h1>
                        @php
                            $wordCount = str_word_count($registrant->workshop->workshop_title);
                        @endphp

                        @if ($wordCount > 5)
                            <h3 style="padding:0px 20px; height:80px; line-height:36px;">
                                {{ $registrant->workshop->workshop_title }}</h3>
                        @else
                            <h2 style="padding:0px 20px; height:80px; font-size:36px; line-height:36px;">
                                {{ $registrant->workshop->workshop_title }}</h2>
                        @endif

                        <h1
                            style="font-size:34px;text-transform:capitalize; letter-spacing:-0.02em; background:#fff; margin:25px auto 30px; width:470px; border-radius:10px; height:30px; padding:15px 0px;">
                            {{ $registrant->user->fullName($registrant->user) }}

                        </h1>
                    </div>
                    <div style="width:510px; padding:0px 20px 10px; text-align:center; float:left;">

                        <div
                            style="padding:5px; font-size:13px; border-radius:5px; height:138px; width:120px; margin:10px auto 15px; overflow:hidden; background:#fff;">
                            {!! QrCode::size(120)->generate(config('app.url') . 'workshop/participant/profile/' . $registrant->token) !!}
                            {{-- <img src="data:image/png;base64, {!! base64_encode(
                            QrCode::size(200)->generate(config('app.url') . '/participant/profile/' . $participant->token),
                        ) !!} "> --}}
                            <br />Serial No:

                        </div>



                    </div>


                    <div style="background-color:green; height:auto; float:left; width:100%; overflow:hidden;">
                        <h1
                            style="color:#fff; font-size:40px; padding:0px 30px 8px; margin:0px;  weight:bold; text-align:center;">
                            {{ $registrant->registrant_type == 1 ? 'Participant' : 'Instructor' }}</h1>
                    </div>
                    <div style="width:92%; font-size:15px; padding:105px 25px 48px; color:#fff; float:left;">

                    </div>

                </div>
            </div>




        </div>
    @endforeach

</body>

</html>
