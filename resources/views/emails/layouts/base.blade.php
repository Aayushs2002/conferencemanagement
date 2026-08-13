<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
</head>

<body
    style="margin:0; padding:0; background-color:#f1f4fc; font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif; -webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color:#f1f4fc; padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:600px; background-color:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,10,38,0.06);">

                    {{-- Header --}}
                    <tr>
                        <td align="center" style="background-color:#1a237e; padding:28px 32px;">
                            <span
                                style="display:inline-block; color:#ffffff; font-size:22px; font-weight:600; letter-spacing:0.5px;">
                                {{ $brand ?? config('app.name') }}
                            </span>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:36px 40px 8px 40px;">
                            <h1 style="margin:0 0 18px 0; font-size:20px; font-weight:600; color:#000a26;">
                                @yield('heading')
                            </h1>
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Action --}}
                    @hasSection('actionUrl')
                        <tr>
                            <td align="center" style="padding:0 40px 28px 40px;">
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td align="center" style="background-color:#1a237e; border-radius:6px;">
                                            <a href="@yield('actionUrl')" target="_blank"
                                                style="display:inline-block; padding:14px 38px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none;">
                                                @yield('actionText')
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Footnote + copy-paste fallback --}}
                    <tr>
                        <td style="padding:0 40px 32px 40px;">
                            @yield('footnote')
                            @hasSection('actionUrl')
                                <hr style="border:none; border-top:1px solid #edf0f7; margin:24px 0;">
                                <p style="margin:0 0 8px 0; font-size:13px; line-height:20px; color:#6c757d;">
                                    Trouble with the button? Copy and paste this URL into your browser:
                                </p>
                                <p style="margin:0; font-size:13px; line-height:20px; word-break:break-all;">
                                    <a href="@yield('actionUrl')" style="color:#1a237e;">@yield('actionUrl')</a>
                                </p>
                            @endif
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="background-color:#f9f9f9; padding:20px 32px;">
                            <p style="margin:0; font-size:12px; line-height:18px; color:#6c757d;">
                                &copy; {{ date('Y') }} {{ $brand ?? config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
