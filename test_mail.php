<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('This is a test email from OneDollarMeme', function($message) {
        $message->to('saeedsheik577@gmail.com')
                ->subject('Test Email - OneDollarMeme');
    });
    echo "Test email sent successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
