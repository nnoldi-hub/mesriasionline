<?php
// SECURITY: Delete this file after debugging!
if (!isset($_GET['token']) || $_GET['token'] !== 'meso2026debug') {
    http_response_code(404);
    die('Not found');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre style='font-family:monospace;font-size:13px;padding:20px'>";

echo "=== PHP INFO ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "SAPI: " . php_sapi_name() . "\n\n";

echo "=== PATHS ===\n";
$base = dirname(__DIR__);
echo "Base dir: $base\n";
echo ".env exists: " . (file_exists($base . '/.env') ? 'YES' : 'NO') . "\n";
echo "vendor/autoload.php exists: " . (file_exists($base . '/vendor/autoload.php') ? 'YES' : 'NO') . "\n";
echo "bootstrap/app.php exists: " . (file_exists($base . '/bootstrap/app.php') ? 'YES' : 'NO') . "\n\n";

echo "=== EXTENSIONS ===\n";
$required = ['pdo', 'pdo_mysql', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'openssl', 'bcmath', 'fileinfo'];
foreach ($required as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? 'OK' : 'MISSING') . "\n";
}
echo "\n";

echo "=== PERMISSIONS ===\n";
$dirs = ['storage', 'storage/logs', 'storage/framework', 'bootstrap/cache'];
foreach ($dirs as $dir) {
    $path = $base . '/' . $dir;
    echo "$dir: " . (is_dir($path) ? (is_writable($path) ? 'writable' : 'NOT WRITABLE') : 'MISSING') . "\n";
}
echo "\n";

echo "=== .ENV CONTENT (masked) ===\n";
$envPath = $base . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (preg_match('/^(DB_PASSWORD|APP_KEY|MAIL_PASSWORD|.*_SECRET|.*_TOKEN)=/', $line)) {
            echo preg_replace('/=.*/', '=***HIDDEN***', $line) . "\n";
        } else {
            echo $line . "\n";
        }
    }
} else {
    echo ".env NOT FOUND\n";
}
echo "\n";

echo "=== BOOTSTRAP LARAVEL ===\n";
try {
    require $base . '/vendor/autoload.php';
    echo "autoload: OK\n";

    $app = require_once $base . '/bootstrap/app.php';
    echo "app bootstrap: OK\n";

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "kernel: OK\n";

    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    echo "kernel handle: OK (status " . $response->getStatusCode() . ")\n";

} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== LARAVEL LOG (last 20 lines) ===\n";
$logFile = $base . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last = array_slice($lines, -20);
    foreach ($last as $line) {
        echo htmlspecialchars($line);
    }
} else {
    echo "No log file found\n";
}

echo "</pre>";
