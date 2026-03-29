<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Brand;

$brands = Brand::all();
foreach ($brands as $brand) {
    echo "ID: {$brand->id}, Name: {$brand->company_name}, Logo: {$brand->logo}\n";
}
