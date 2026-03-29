<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create a test user
$user = new User();
$user->name = 'Test User';
$user->email = 'test@example.com';
$user->password = bcrypt('password');
$user->email_verified_at = null; // Not verified yet
$user->save();

// Send verification email
$notification = new VerifyEmail();
$notification->createUrlUsing(function ($notifiable) {
    return URL::temporarySignedRoute(
        'verification.verify',
        Carbon::now()->addMinutes(60),
        [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ]
    );
});

$user->notify($notification);

echo "Verification email sent to test user. Check storage/logs/laravel.log for the email content.\n";

// Clean up
$user->delete();