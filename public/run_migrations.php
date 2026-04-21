<?php
/**
 * Temporary migration runner for Hostinger
 * DELETE THIS FILE after running migrations!
 */

// Load Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Run migrations
$output = new \Symfony\Component\Console\Output\BufferedOutput();
$exitCode = \Artisan::call('migrate', ['--force' => true], $output);

echo "<h2>Migration Output:</h2>";
echo "<pre>" . $output->fetch() . "</pre>";

if ($exitCode === 0) {
    echo "<p style='color: green;'>✅ Migrations completed successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Migration failed!</p>";
}

echo "<hr><p style='color: red;'><strong>⚠️ IMPORTANT: DELETE THIS FILE NOW!</strong></p>";
