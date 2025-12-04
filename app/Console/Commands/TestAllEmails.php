<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\RegisterOtpMail;
use App\Mail\WelcomeMail;
use App\Mail\LoginOtpMail;
use App\Mail\PasswordResetOtpMail;
use App\Mail\TransactionReceiptMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class TestAllEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-all {email=abokisub@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all email templates by sending them to a specified email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing all email templates...");
        $this->info("Sending emails to: {$email}");
        $this->newLine();

        // Create a mock request for client info
        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'REMOTE_ADDR' => '105.112.55.21',
        ]);

        // Test data
        $firstName = 'John';
        $lastName = 'Doe';
        $otpCode = '123456';
        $expiresIn = 10;
        $ipAddress = '105.112.55.21';
        $location = 'Lagos, Lagos, NG';
        $browser = 'Chrome';

        try {
            // 1. Registration OTP Email
            $this->info('1. Sending Registration OTP Email...');
            Mail::to($email)->send(
                new RegisterOtpMail($firstName, $otpCode, $expiresIn, $ipAddress, $location, $browser)
            );
            $this->info('   ✓ Registration OTP Email sent!');
            $this->newLine();

            // 2. Welcome Email
            $this->info('2. Sending Welcome Email...');
            Mail::to($email)->send(new WelcomeMail($firstName));
            $this->info('   ✓ Welcome Email sent!');
            $this->newLine();

            // 3. Login OTP Email
            $this->info('3. Sending Login OTP Email...');
            Mail::to($email)->send(
                new LoginOtpMail($firstName, $otpCode, $expiresIn, $ipAddress, $location, $browser)
            );
            $this->info('   ✓ Login OTP Email sent!');
            $this->newLine();

            // 4. Password Reset OTP Email
            $this->info('4. Sending Password Reset OTP Email...');
            Mail::to($email)->send(
                new PasswordResetOtpMail($firstName, $otpCode, $expiresIn, $ipAddress, $location, $browser)
            );
            $this->info('   ✓ Password Reset OTP Email sent!');
            $this->newLine();

            // 5. Transaction Receipt Email
            $this->info('5. Sending Transaction Receipt Email...');
            Mail::to($email)->send(
                new TransactionReceiptMail(
                    $firstName,
                    'TXN' . strtoupper(uniqid()),
                    'Wallet Transfer',
                    50000.00,
                    50.00,
                    50050.00,
                    'settled',
                    now()->format('Y-m-d H:i:s'),
                    'Transfer to Jane Smith',
                    'Jane Smith',
                    150000.00
                )
            );
            $this->info('   ✓ Transaction Receipt Email sent!');
            $this->newLine();

            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info('✅ All email templates tested successfully!');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->newLine();
            $this->info("Check your inbox at: {$email}");
            $this->info("Don't forget to check your spam folder if emails don't appear.");
            $this->newLine();

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error sending emails: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
