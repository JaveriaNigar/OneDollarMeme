<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Check user
$user = User::where('email', 'saeedsheik577@gmail.com')->first();

if ($user) {
    echo "✅ User found!\n";
    echo "-------------------\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Email Verified: " . ($user->email_verified_at ? 'Yes ✓' : 'No ✗') . "\n";
    echo "Created: {$user->created_at}\n";
    echo "-------------------\n";
    
    // If not verified, verify now
    if (!$user->email_verified_at) {
        $user->email_verified_at = now();
        $user->save();
        echo "\n✅ Email verified successfully!\n";
    }
} else {
    echo "❌ User not found!\n";
}
