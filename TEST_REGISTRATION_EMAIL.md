# Test Registration Email

## 🔍 Debug Steps

Since your SMTP is working (test emails sent successfully), but registration emails aren't arriving, let's debug:

### Step 1: Register a New User

1. Register a new user through the Flutter app
2. Use an email address you can check immediately

### Step 2: Check Logs Immediately After Registration

```bash
# Windows PowerShell:
Get-Content storage/logs/laravel.log -Tail 50
```

Look for:
- `=== REGISTRATION OTP PROCESS START ===`
- `Generated OTP for user X: XXXXXX`
- `Calling EmailHelper::sendRegisterOtp...`
- `✓ OTP email sent successfully`
- Any error messages

### Step 3: Get OTP from Database (If Email Not Received)

```bash
php artisan tinker
```

Then:
```php
$user = \App\Models\User::latest()->first();
echo "User Email: " . $user->email . PHP_EOL;

$otp = \App\Models\Otp::where('user_id', $user->id)
    ->where('type', 'register')
    ->where('used', false)
    ->latest()
    ->first();

if ($otp) {
    echo "OTP Code: " . $otp->code . PHP_EOL;
    echo "Expires at: " . $otp->expires_at . PHP_EOL;
    echo "Created at: " . $otp->created_at . PHP_EOL;
} else {
    echo "No OTP found. Try registering again." . PHP_EOL;
}
```

### Step 4: Test Email Sending Directly

Test if email works for the registered user:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::latest()->first();
$otp = \App\Services\OtpService::generate($user, 'register', 1);
\App\Helpers\EmailHelper::sendRegisterOtp($user, $otp, request(), 1);
echo "Test email sent to: " . $user->email . PHP_EOL;
```

Check your email inbox.

## 🔧 Possible Issues

1. **Email address mismatch**: Make sure you're checking the exact email used during registration
2. **Email delay**: Wait 1-2 minutes, sometimes there's a delay
3. **Silent failure**: Check logs for any errors that might be caught silently
4. **Queue issue**: If using queues, make sure queue worker is running

## ✅ What We Know

- ✅ SMTP is configured correctly (SSL, port 465)
- ✅ Test emails work (`php artisan email:test-all`)
- ✅ Email code is in registration flow
- ❓ Need to verify: Is email actually being called during registration?

## 📝 Next Steps

1. Register a new user
2. Check logs immediately
3. Share the log output so we can see what's happening

