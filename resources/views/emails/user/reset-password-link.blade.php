@extends('emails.layouts.base')

@section('title', 'Reset your password')
@section('heading', 'Reset your password')

@section('content')
    <p style="margin:0 0 16px 0; font-size:15px; line-height:24px; color:#4a5568;">
        Hello{{ $name ? ' ' . $name : '' }},
    </p>
    <p style="margin:0 0 28px 0; font-size:15px; line-height:24px; color:#4a5568;">
        We received a request to reset the password for your {{ $appName }} account. Click the button below to choose a
        new one.
    </p>
@endsection

@section('actionUrl', $url)
@section('actionText', 'Reset Password')

@section('footnote')
    <p style="margin:0; font-size:14px; line-height:22px; color:#6c757d;">
        This link expires in <strong style="color:#000a26;">{{ $expiresIn }} minutes</strong>. If you did not request a
        password reset, you can safely ignore this email — your password will remain unchanged.
    </p>
@endsection
