{{-- <!DOCTYPE html
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
    @foreach ($registrants as $participant)

        <div style="width:1280px; height:auto;">
            <div style="width:550px; float:left !important; margin:20px;">
                <div
                    style="font-size:18px; background:url('{{ asset('storage/conference/conference/pass/' . $passSetting->image) }}') no-repeat center top #66b7ef;  background-size:100%; height:auto; overflow:hidden; padding:20px 0px 0px;">

                    <ul style="width:100%; margin:0px; float:left; padding:0px; padding-top:0px; padding-bottom:20px;">


                        <li
                            style="float:left; padding:30px 0px 0px; width:100%; letter-spacing:-0.3px; text-align:center; display:inline; font-size:50px; line-height:40px; color:#fff; font-weight:700;text-shadow:-1px -1px 0 #000;">
                            {{ $conference->conference_name }}
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
                            style="font-size:18px; font-weight:500; letter-spacing:-0.02em; color:#000; padding-top:40px;">"{{ $conference->conference_theme }}"</small>
                        <p
                            style="line-height:30PX; color:white; margin:0px; padding:2px 0px 6px; font-size:16px; font-weight:500;">
                            @if ($conference->start_date == $conference->end_date)
                                {{ \Carbon\Carbon::parse($conference->start_date)->format('jS F, Y') }},
                            @else
                                {{ \Carbon\Carbon::parse($conference->start_date)->format('jS') }}
                                -
                                {{ \Carbon\Carbon::parse($conference->end_date)->format('jS F, Y') }},
                            @endif Kathmandu, Nepal<br />
                        </p>

                        <h6
                            style="font-size:24px; background:#fff;  margin:5px 0px; line-height:30px; font-weight:500; padding:2px 0px; background-color:rgba(255, 255, 255, 0.1);">
                        </h6>



                        <h2
                            style="font-size:26px;text-transform:capitalize; letter-spacing:-0.02em; background:#fff; margin:25px auto 10px; width:470px; border-radius:10px; height:30px; padding:22px 0px;">
                            {{ $participant->user->userDetail->namePrefix->prefix ?? null }}
                            {{ $participant->user->fullName($participant->user) }}
                        </h2>

                    </div>
                    <div style="width:510px; padding:0px 20px 10px; text-align:center; float:left;">

                        <div
                            style="padding:5px; font-size:13px; border-radius:5px; height:138px; width:120px; margin:10px auto 15px; overflow:hidden; background:#fff;">
                            {!! QrCode::size(120)->generate(config('app.url') . '/participant/profile/' . $participant->token) !!}
                          
                            <br />Serial No:

                        </div>



                    </div>


                    <div style="background-color:red; height:auto; float:left; width:100%; overflow:hidden;">
                        <h1
                            style="color:#fff;  font-size:30px; padding:0px 30px 8px; margin:0px;height: 50px;  weight:bold; text-align:center;">
                            {{ $participant->designation }}
                        </h1>
                    </div>
                    <div style="width:92%; font-size:15px; padding:105px 25px 48px; color:#fff; float:left;">

                    </div>
                </div>
            </div>

        </div>
    @endforeach
</body>

</html> --}}
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
    <div style="width:1280px; height:auto;">
        @foreach ($registrants as $participant)
            <div style="width:550px; float:left !important; margin:20px;">
                <div
                    style="font-size:18px; background:url({{ asset('storage/conference/conference/pass/' . $passSetting->image) }}) no-repeat center top #6dc3fe;  background-size:100%; height:auto; overflow:hidden; padding:150px 0px 0px;">
                    <div
                        style="padding:160px 0px 0px;  font-size:20px; font-size:20px; text-align:center; font-weight:normal; line-height:22px;">
                        <small
                            style="font-size:18px; font-weight:500; color:white; letter-spacing:-0.01em;  padding-top:40px;">Venue:
                            {{ $conference->ConferenceVenueDetail->venue_name . ', ' . $conference->ConferenceVenueDetail->venue_address }}</small>
                        <p
                            style="line-height:30PX; color:white; margin:0px; padding:2px 0px 6px; font-size:16px; font-weight:500;">
                            {{-- 13<sup>th</sup> - 14<sup>th</sup> Feb, 2026  --}}
                            @if ($conference->start_date == $conference->end_date)
                                {{ \Carbon\Carbon::parse($conference->start_date)->format('jS F, Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($conference->start_date)->format('jS') }}
                                -
                                {{ \Carbon\Carbon::parse($conference->end_date)->format('jS F, Y') }}
                            @endif
                            <br />
                        </p>

                        <h6
                            style="font-size:24px; background:#fff;  margin:5px 0px; line-height:30px; font-weight:500; padding:2px 0px; background-color:rgba(0,174, 239, 0.4);">
                        </h6>
                        <h1
                            style="font-size:34px;text-transform:capitalize; letter-spacing:-0.02em; background:#fff; margin:15px auto 5px; width:470px; border-radius:10px; height:30px; padding:22px 0px;">
                            {{ $participant->user?->userDetail->namePrefix->prefix ?? null }}
                            {{ $participant->user?->fullName($participant->user) }}</h1>
                    </div>
                    <div style="width:510px; padding:0px 20px 10px; text-align:center; float:left;">

                        <div
                            style="padding:5px; font-size:12px; border-radius:5px; height:110px; width:100px; margin:10px auto 5px; overflow:hidden; background:#fff;">
                            {!! QrCode::size(100)->generate(config('app.url') . '/participant/profile/' . $participant->token) !!}
                            <br />Serial No: ORG001

                        </div>

 

                    </div>

                    <div
                        style="background-color:{{ $participant->designation_color ?? '#e31e26' }}; height:auto; float:left; width:100%; overflow:hidden;">
                        <h1
                            style="color:#fff;  font-size:40px; padding:0px 30px 8px; margin:0px; weight:bold; text-align:center;">
                            {{ $participant->designation }}
                        </h1>
                    </div> 
                    <div style="width:92%; font-size:15px; padding:12px 25px; color:#fff; float:left;">
                        <p style="text-align:center; text-shadow:1px 1px 1px #000; "><b>Hosted by:</b><br />
                             {{ $conference->society->users->where('type', 2)->value('f_name') }}
                            (<span
                                style="text-transform:uppercase;">{{ $conference->society->abbreviation }}</span>)
                        </p>
                        <p
                            style="text-align:right; margin:10px 0px 12px 0px; color:#fff; text-shadow:1px 1px 1px #000; font-weight:bold; font-size:14px;">
                            <small>Participation: @if ($conference->start_date == $conference->end_date)
                                    {{ \Carbon\Carbon::parse($conference->start_date)->format('jS F, Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($conference->start_date)->format('jS') }}
                                    -
                                    {{ \Carbon\Carbon::parse($conference->end_date)->format('jS F, Y') }}
                                @endif
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

</body>

</html>
