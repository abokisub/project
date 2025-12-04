@extends('emails.layout')

@section('content')
    <h1 style="color: #111827; margin: 0 0 20px 0; font-size: 24px;">Hello {{ $firstName }},</h1>
    
    <p style="color: #374151; margin: 0 0 20px 0;">
        Here is your transaction receipt from KoboPoint.
    </p>

    <div style="margin: 30px 0;">
        <h2 style="color: #111827; margin: 0 0 20px 0; font-size: 18px;">🧾 Transaction Details</h2>
        
        <table class="table">
            <tr>
                <th>Transaction ID</th>
                <td style="font-family: monospace; color: #3b82f6;">{{ $transactionId }}</td>
            </tr>
            <tr>
                <th>Type</th>
                <td>{{ $transactionType }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td style="font-weight: 600; color: #111827;">₦{{ number_format($amount, 2) }}</td>
            </tr>
            <tr>
                <th>Fee</th>
                <td>₦{{ number_format($fee, 2) }}</td>
            </tr>
            <tr>
                <th>Total Debited</th>
                <td style="font-weight: 600; color: #dc2626;">₦{{ number_format($total, 2) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;
                        {{ $status === 'settled' ? 'background-color: #d1fae5; color: #065f46;' : 
                           ($status === 'pending' ? 'background-color: #fef3c7; color: #92400e;' : 'background-color: #fee2e2; color: #991b1b;') }}">
                        {{ ucfirst($status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Date</th>
                <td>{{ $date }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $description ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>{{ $transactionType === 'transfer' ? 'Receiver' : 'Sender' }}</th>
                <td>{{ $party }}</td>
            </tr>
            <tr style="background-color: #f9fafb;">
                <th style="font-size: 16px;">Balance After</th>
                <td style="font-size: 16px; font-weight: 600; color: #10b981;">₦{{ number_format($newBalance, 2) }}</td>
            </tr>
        </table>
    </div>

    <p style="color: #374151; margin: 30px 0 20px 0;">
        Thank you for using KoboPoint.
    </p>

    <p style="color: #374151; margin: 20px 0 0 0;">
        If you have questions, contact support anytime.
    </p>
@endsection

