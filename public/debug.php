<?php
// STERGE ACEST FISIER DUPA DEPANARE
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>\n";
echo "PHP version: " . PHP_VERSION . "\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test autoload
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "ERROR: vendor/autoload.php not found!\n";
    exit;
}
require $autoload;
echo "Autoload: OK\n";

// Test AffiliateService
if (class_exists('App\\Services\\AffiliateService')) {
    echo "AffiliateService: FOUND\n";
} else {
    echo "AffiliateService: NOT FOUND\n";
}

// Test bootstrap
try {
    $app = require dirname(__DIR__) . '/bootstrap/app.php';
    echo "Bootstrap: OK\n";
} catch (\Throwable $e) {
    echo "Bootstrap ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit;
}

// Test toate serviciile
$services = [
    'App\\Services\\AffiliateService',
    'App\\Services\\PaymentService',
    'App\\Services\\SeoService',
    'App\\Services\\TwoFactorAuthService',
];
echo "\nServices check:\n";
foreach ($services as $s) {
    echo "  " . $s . ": " . (class_exists($s) ? "OK" : "MISSING") . "\n";
}

// Test syntax fisiere cheie
$files = [
    'app/Services/AffiliateService.php',
    'app/Services/SeoService.php',
    'routes/api.php',
    'routes/web.php',
];
echo "\nFile syntax check:\n";
foreach ($files as $f) {
    $path = dirname(__DIR__) . '/' . $f;
    if (!file_exists($path)) {
        echo "  $f: FILE MISSING\n";
        continue;
    }
    $firstBytes = file_get_contents($path, false, null, 0, 10);
    $hex = implode(' ', array_map(fn($b) => sprintf('%02X', ord($b)), str_split($firstBytes)));
    echo "  $f: first bytes = $hex\n";
}

echo "\nDone.\n</pre>";
