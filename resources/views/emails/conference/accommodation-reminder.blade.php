@extends('emails.layouts.base')

@section('title', 'Update your accommodation details')
@section('heading', 'Update your accommodation details')

@section('content')
    <p style="margin:0 0 16px 0; font-size:15px; line-height:24px; color:#4a5568;">
        Dear {{ $userName }},
    </p>
    <p style="margin:0 0 16px 0; font-size:15px; line-height:24px; color:#4a5568;">
        We noticed you haven't yet provided your accommodation and flight details for
        <strong style="color:#000a26;">{{ $conferenceName }}</strong>. As an international participant, this information
        helps us ensure a comfortable stay and a smooth arrival.
    </p>
    <p style="margin:0 0 10px 0; font-size:15px; line-height:24px; color:#000a26; font-weight:600;">
        Please update your:
    </p>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px 0;">
        @foreach (['Flight arrival and departure details', 'Hotel accommodation preferences', 'Airport pickup requirements'] as $item)
            <tr>
                <td valign="top" style="padding:0 10px 8px 0; font-size:15px; line-height:24px; color:#1a237e;">&bull;</td>
                <td style="padding:0 0 8px 0; font-size:15px; line-height:24px; color:#4a5568;">{{ $item }}</td>
            </tr>
        @endforeach
    </table>
@endsection

@section('actionUrl', $actionUrl)
@section('actionText', 'Update Details Now')

@section('footnote')
    <p style="margin:0; font-size:14px; line-height:22px; color:#6c757d;">
        Thank you for your cooperation.<br>
        Best regards,<br>
        <strong style="color:#000a26;">The {{ $brand }} Team</strong>
    </p>
@endsection
