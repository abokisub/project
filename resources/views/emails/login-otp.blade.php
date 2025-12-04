@extends('emails.layout')

@section('content')
    <h1 style="color: #111827; margin: 0 0 20px 0; font-size: 24px;">Hello {{ $firstName }},</h1>
    
    <p style="color: #374151; margin: 0 0 20px 0;">
        A login request was made to your KoboPoint account. Use the verification code below to continue.
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <p style="color: #6b7280; margin: 0 0 10px 0; font-size: 14px;">🔐 Login OTP:</p>
        <div class="otp-code">{{ $otpCode }}</div>
        <p style="color: #6b7280; margin: 10px 0 0 0; font-size: 14px;">
            Expires in <strong>{{ $expiresIn }} minutes</strong>.
        </p>
    </div>

    <div class="info-box">
        <p style="margin: 0 0 12px 0; font-weight: 600; color: #111827;">🔍 Login Attempt Details</p>
        <div class="info-row">
            <span class="info-label">IP Address:</span>
            <span class="info-value">{{ $ipAddress }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Location:</span>
            <span class="info-value">{{ $location }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Device/Browser:</span>
            <span class="info-value">{{ $browser }}</span>
        </div>
    </div>

    <p style="color: #dc2626; margin: 20px 0 0 0; font-size: 14px; font-weight: 600;">
        ⚠️ If this wasn't you, change your password immediately or contact support.
    </p>
@endsection

