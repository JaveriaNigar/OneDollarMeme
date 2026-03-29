<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$email = 'kinzasaed688@gmail.com';

// Delete existing user if any
DB::table('users')->where('email', $email)->delete();

echo "Removed existing user (if any): $email\n";

// Now verify the email directly
DB::table('users')->where('email', $email)->update([
    'email_verified_at' => Carbon::now()
]);

echo "Email verified successfully: $email\n";
echo "You can now login with this email.\n";
