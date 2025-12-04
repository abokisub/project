@extends('emails.layout')

@section('content')
    <h1 style="color: #111827; margin: 0 0 20px 0; font-size: 24px;">Hello {{ $firstName }},</h1>
    
    <div style="text-align: center; margin: 30px 0;">
        <div style="font-size: 48px; margin-bottom: 20px;">🎉</div>
        <h2 style="color: #111827; margin: 0 0 10px 0; font-size: 22px;">Welcome to KoboPoint!</h2>
        <p style="color: #374151; margin: 0;">Your account has been successfully created.</p>
    </div>

    <p style="color: #374151; margin: 20px 0;">You can now enjoy:</p>

    <div style="margin: 20px 0;">
        <div style="padding: 10px 0; color: #374151;">
            <span style="color: #10b981; font-size: 18px; margin-right: 10px;">✓</span>
            <strong>Fast money transfers</strong>
        </div>
        <div style="padding: 10px 0; color: #374151;">
            <span style="color: #10b981; font-size: 18px; margin-right: 10px;">✓</span>
            <strong>Merchant tools</strong>
        </div>
        <div style="padding: 10px 0; color: #374151;">
            <span style="color: #10b981; font-size: 18px; margin-right: 10px;">✓</span>
            <strong>Bills, airtime & data</strong>
        </div>
        <div style="padding: 10px 0; color: #374151;">
            <span style="color: #10b981; font-size: 18px; margin-right: 10px;">✓</span>
            <strong>Savings & goal pots</strong>
        </div>
        <div style="padding: 10px 0; color: #374151;">
            <span style="color: #10b981; font-size: 18px; margin-right: 10px;">✓</span>
            <strong>Secure wallet with PIN & biometrics</strong>
        </div>
    </div>

    <p style="color: #374151; margin: 30px 0 20px 0;">
        We're excited to have you on board.
    </p>

    <p style="color: #374151; margin: 20px 0 0 0;">
        If you have any questions, our support team is always here to help.
    </p>

    <p style="color: #111827; margin: 30px 0 0 0; font-weight: 600;">
        Welcome once again,<br>
        The KoboPoint Team
    </p>
@endsection

