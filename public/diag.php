<?php
// DIAGNOSTIC FILE - STERGE DUPA FOLOSIRE
// Acces: meseriasionline.ro/diag.php?key=diag2026

if (($_GET['key'] ?? '') !== 'diag2026') {
    http_response_code(403);
    die('Forbidden');
}

echo '<style>body{font-family:monospace;padding:20px;background:#1a1a2e;color:#eee} .ok{color:#4ecca3} .err{color:#e94560} .warn{color:#f5a623} h2{color:#4ecca3;border-bottom:1px solid #333;padding-bottom:5px}</style>';
echo '<h1>🔧 Diagnostic MeseriasOnline</h1>';

// PHP Info
echo '<h2>PHP</h2>';
echo '<p class="ok">PHP Version: ' . phpversion() . '</p>';
echo '<p>Memory limit: ' . ini_get('memory_limit') . '</p>';
echo '<p>Max execution time: ' . ini_get('max_execution_time') . 's</p>';

// Extensions
echo '<h2>Extensii PHP</h2>';
$needed = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'gd', 'zip', 'curl'];
foreach ($needed as $ext) {
    $loaded = extension_loaded($ext);
    echo '<p class="' . ($loaded ? 'ok' : 'err') . '">' . ($loaded ? '✓' : '✗') . ' ' . $ext . '</p>';
}

// .env
echo '<h2>Fișier .env</h2>';
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    echo '<p class="ok">✓ .env există</p>';
    $env = parse_ini_file($envPath);
    echo '<p>APP_ENV: ' . ($env['APP_ENV'] ?? 'N/A') . '</p>';
    echo '<p>APP_DEBUG: ' . ($env['APP_DEBUG'] ?? 'N/A') . '</p>';
    echo '<p>DB_CONNECTION: ' . ($env['DB_CONNECTION'] ?? 'N/A') . '</p>';
    echo '<p>DB_DATABASE: ' . ($env['DB_DATABASE'] ?? 'N/A') . '</p>';
} else {
    echo '<p class="err">✗ .env lipsă!</p>';
}

// DB Connection
echo '<h2>Conexiune DB</h2>';
try {
    $env = parse_ini_file($envPath);
    $pdo = new PDO(
        'mysql:host=' . ($env['DB_HOST'] ?? '127.0.0.1') . ';port=' . ($env['DB_PORT'] ?? '3306') . ';dbname=' . ($env['DB_DATABASE'] ?? ''),
        $env['DB_USERNAME'] ?? '',
        $env['DB_PASSWORD'] ?? ''
    );
    echo '<p class="ok">✓ DB conectat cu succes</p>';

    // Check tables
    $tables = ['users', 'reviews', 'categories', 'platform_daily_stats', 'conversion_funnels', 'conversion_events'];
    echo '<h2>Tabele DB</h2>';
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        echo '<p class="' . ($exists ? 'ok' : 'warn') . '">' . ($exists ? '✓' : '⚠') . ' ' . $table . '</p>';
    }

    // Check users columns
    echo '<h2>Coloane tabel users</h2>';
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo '<p>' . implode(', ', $cols) . '</p>';

} catch (Exception $e) {
    echo '<p class="err">✗ DB Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

// Storage permissions
echo '<h2>Permisiuni Storage</h2>';
$dirs = [
    dirname(__DIR__) . '/storage/logs',
    dirname(__DIR__) . '/storage/framework/cache',
    dirname(__DIR__) . '/storage/framework/sessions',
    dirname(__DIR__) . '/bootstrap/cache',
];
foreach ($dirs as $dir) {
    $writable = is_writable($dir);
    echo '<p class="' . ($writable ? 'ok' : 'err') . '">' . ($writable ? '✓' : '✗') . ' ' . basename($dir) . '</p>';
}

// Laravel log - ultimele erori
echo '<h2>Ultimele erori Laravel</h2>';
$logFile = dirname(__DIR__) . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $log = file_get_contents($logFile);
    // Gaseste ultima linie cu ERROR
    preg_match_all('/\[.*?\] production\.ERROR: ([^\n]+)/', $log, $matches);
    if (!empty($matches[1])) {
        $last5 = array_slice($matches[1], -5);
        foreach ($last5 as $err) {
            echo '<p class="err">✗ ' . htmlspecialchars(substr($err, 0, 300)) . '</p>';
        }
    } else {
        echo '<p class="warn">Nu s-au găsit erori recente</p>';
    }
} else {
    echo '<p class="err">✗ Log lipsă</p>';
}

echo '<hr><p class="warn">⚠ STERGE ACEST FISIER DUPA FOLOSIRE: rm public/diag.php</p>';
