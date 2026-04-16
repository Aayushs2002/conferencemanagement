



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
        @foreach ($sponsors as $sponsor)
            <div style="width:550px; float:left !important; margin:20px;">
                <div
                    style="font-size:18px; background:url({{ asset('storage/conference/conference/pass/' . $passSetting->image) }}) no-repeat center top #6dc3fe;  background-size:100%; height:auto; overflow:hidden; padding:150px 0px 0px;">
                    <div
                        style="padding:160px 0px 0px;  font-size:20px; font-size:20px; text-align:center; font-weight:normal; line-height:22px;">
                        <small
                            style="font-size:18px; font-weight:500; color:white; letter-spacing:-0.01em;  padding-top:40px;">Venue:
                            {{ $sponsor->conference->ConferenceVenueDetail->venue_name . ', ' . $sponsor->conference->ConferenceVenueDetail->venue_address }}</small>
                        <p
                            style="line-height:30PX; color:white; margin:0px; padding:2px 0px 6px; font-size:16px; font-weight:500;">
                            {{-- 13<sup>th</sup> - 14<sup>th</sup> Feb, 2026  --}}
                            @if ($sponsor->conference->start_date == $sponsor->conference->end_date)
                                {{ \Carbon\Carbon::parse($sponsor->conference->start_date)->format('jS F, Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($sponsor->conference->start_date)->format('jS') }}
                                -
                                {{ \Carbon\Carbon::parse($sponsor->conference->end_date)->format('jS F, Y') }}
                            @endif
                            <br />
                        </p>

                        <h6
                            style="font-size:24px; background:#fff;  margin:5px 0px; line-height:30px; font-weight:500; padding:2px 0px; background-color:{{ $passSetting->border_color ?? '#00aeef' }};">
                        </h6>
                        <h1
                            style="font-size:34px;text-transform:capitalize; letter-spacing:-0.02em; background:#fff; margin:15px auto 5px; width:470px; border-radius:10px; height:30px; padding:22px 0px;">
                            {{ $sponsor->name }}
                        </h1>
                    </div>
                    <div style="width:510px; padding:0px 20px 10px; text-align:center; float:left;">

                        <div
                            style="padding:5px; font-size:10px; border-radius:5px; height:110px; width:100px; margin:10px auto 5px; overflow:hidden; background:#fff;">
                            {!! QrCode::size(100)->generate(config('app.url') . '/sponsor/profile/' . $sponsor->token) !!}

                            <br />Serial No: {{ $sponsor->registration_id }}

                        </div>

 
 
                    </div>

                    <div style="background-color:#ff08c9; height:auto; float:left; width:100%; overflow:hidden;">
                        <h1
                            style="color:#fff;  font-size:40px; padding:0px 30px 8px; margin:0px; weight:bold; text-align:center;">
                            Sponsor<small style="text-align:right; font-size:22px; margin-left:5px;">(
                                {{ $sponsor->category->category_name }})</small>
                        </h1>
                    </div>
                    <div style="width:92%; font-size:15px; padding:12px 25px; color:#fff; float:left;">
                        <p style="text-align:center; text-shadow:1px 1px 1px #000; "><b>Hosted by:</b><br />
                            {{ $sponsor->conference->society->users->where('type', 2)->value('f_name') }}
                            (<span
                                style="text-transform:uppercase;">{{ $sponsor->conference->society->abbreviation }}</span>)
                        </p>
                        <p
                            style="text-align:right; margin:10px 0px 12px 0px; color:#fff; text-shadow:1px 1px 1px #000; font-weight:bold; font-size:14px;">
                            <small>Participation: @if ($sponsor->conference->start_date == $sponsor->conference->end_date)
                                    {{ \Carbon\Carbon::parse($sponsor->conference->start_date)->format('jS F, Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($sponsor->conference->start_date)->format('jS') }}
                                    -
                                    {{ \Carbon\Carbon::parse($sponsor->conference->end_date)->format('jS F, Y') }}
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
