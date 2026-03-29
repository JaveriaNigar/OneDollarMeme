<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Meme;

$memes = Meme::whereNotNull('image_path')->where('image_path', '!=', '')->take(5)->get();
foreach ($memes as $meme) {
    echo "ID: {$meme->id}, Path: {$meme->image_path}\n";
    $fullPath = storage_path('app/public/' . $meme->image_path);
    echo "Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . " ($fullPath)\n";
}
