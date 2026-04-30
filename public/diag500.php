<?php
/**
 * Diagnostic 500 — MeseriasiOnline
 * ȘTERGE acest fișier după rezolvarea problemei!
 * Accesează: meseriasionline.ro/diag500.php
 */

// Protecție minimă — schimbă tokenul
$token = $_GET['token'] ?? '';
if ($token !== 'diag2026') {
    http_response_code(403);
    die('Acces interzis. Adaugă ?token=diag2026 la URL.');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$ok   = '✅';
$fail = '❌';
$warn = '⚠️';

function check(string $label, bool $result, string $detail = ''): void {
    global $ok, $fail;
    $icon  = $result ? $ok : $fail;
    $color = $result ? '#16a34a' : '#dc2626';
    echo "<tr><td style='padding:6px 12px;'>{$icon}</td><td style='padding:6px 12px;font-weight:600;color:{$color};'>{$label}</td><td style='padding:6px 12px;color:#64748b;font-size:13px;'>" . htmlspecialchars($detail) . "</td></tr>\n";
}

$base = dirname(__DIR__);

?><!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Diagnostic 500</title>
<style>
  body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; padding: 32px; }
  h2 { color: #f8fafc; margin-bottom: 4px; }
  table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 10px; overflow: hidden; margin-bottom: 24px; }
  th { background: #334155; padding: 8px 12px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; }
  tr:hover td { background: #243146; }
  pre { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 16px; font-size: 12px; overflow-x: auto; color: #a3e635; max-height: 320px; overflow-y: auto; }
  .section { margin-bottom: 32px; }
</style>
</head>
<body>
<h2>🔍 Diagnostic Server — MeseriasiOnline</h2>
<p style="color:#64748b;margin-bottom:24px;">Genererat: <?= date('d.m.Y H:i:s') ?></p>

<?php

// ─── 1. PHP & Extensii ──────────────────────────────────────────────
echo "<div class='section'><table><thead><tr><th colspan='3'>1. PHP & Extensii</th></tr></thead><tbody>\n";
check('PHP versiune (' . PHP_VERSION . ')', version_compare(PHP_VERSION, '8.1.0', '>='), PHP_VERSION);
check('PDO', extension_loaded('pdo'), '');
check('PDO MySQL', extension_loaded('pdo_mysql'), '');
check('mbstring', extension_loaded('mbstring'), '');
check('openssl', extension_loaded('openssl'), '');
check('tokenizer', extension_loaded('tokenizer'), '');
check('json', extension_loaded('json'), '');
check('curl', extension_loaded('curl'), '');
check('fileinfo', extension_loaded('fileinfo'), '');
echo "</tbody></table></div>\n";

// ─── 2. Fișiere critice ─────────────────────────────────────────────
echo "<div class='section'><table><thead><tr><th colspan='3'>2. Fișiere & Director</th></tr></thead><tbody>\n";
check('.env există',              file_exists($base . '/.env'), $base . '/.env');
check('vendor/ există',           is_dir($base . '/vendor'), '');
check('vendor/autoload.php',      file_exists($base . '/vendor/autoload.php'), '');
check('openai-php/client',        is_dir($base . '/vendor/openai-php/client'), is_dir($base . '/vendor/openai-php/client') ? 'instalat' : 'LIPSĂ — rulează: composer require openai-php/client');
check('bootstrap/cache/ writable',is_writable($base . '/bootstrap/cache'), '');
check('storage/ writable',        is_writable($base . '/storage'), '');
check('storage/logs/ writable',   is_writable($base . '/storage/logs'), '');
echo "</tbody></table></div>\n";

// ─── 3. .env variabile cheie ────────────────────────────────────────
echo "<div class='section'><table><thead><tr><th colspan='3'>3. Variabile .env (valori mascate)</th></tr></thead><tbody>\n";
$env = [];
if (file_exists($base . '/.env')) {
    foreach (file($base . '/.env') as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        $env[trim($k)] = trim($v);
    }
}
$get = fn(string $k) => $env[$k] ?? '';

check('APP_KEY setat',       strlen($get('APP_KEY')) > 10,     strlen($get('APP_KEY')) > 10 ? 'setat (' . strlen($get('APP_KEY')) . ' chars)' : 'LIPSĂ — rulează: php artisan key:generate');
check('APP_ENV',             in_array($get('APP_ENV'), ['production','local','staging']), $get('APP_ENV') ?: 'LIPSĂ');
check('DB_CONNECTION',       $get('DB_CONNECTION') !== '',     $get('DB_CONNECTION') ?: 'LIPSĂ');
check('DB_HOST',             $get('DB_HOST') !== '',           $get('DB_HOST') ?: 'LIPSĂ');
check('DB_DATABASE',         $get('DB_DATABASE') !== '',       $get('DB_DATABASE') ?: 'LIPSĂ');
check('DB_USERNAME',         $get('DB_USERNAME') !== '',       $get('DB_USERNAME') ?: 'LIPSĂ');
check('OPENAI_API_KEY setat',$get('OPENAI_API_KEY') !== '' && $get('OPENAI_API_KEY') !== 'sk-your-openai-api-key-here', $get('OPENAI_API_KEY') !== '' ? 'sk-***' . substr($get('OPENAI_API_KEY'), -4) : 'LIPSĂ — adaugă în .env');
check('OPENAI_MODEL',        $get('OPENAI_MODEL') !== '',      $get('OPENAI_MODEL') ?: 'nu e setat (va folosi gpt-4o-mini default)');
echo "</tbody></table></div>\n";

// ─── 4. Conexiune DB & tabele ────────────────────────────────────────
echo "<div class='section'><table><thead><tr><th colspan='3'>4. Baza de date</th></tr></thead><tbody>\n";
try {
    $dsn = ($get('DB_CONNECTION') === 'mysql' ? 'mysql' : $get('DB_CONNECTION'))
         . ':host=' . ($get('DB_HOST') ?: '127.0.0.1')
         . ';port=' . ($get('DB_PORT') ?: '3306')
         . ';dbname=' . $get('DB_DATABASE')
         . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $get('DB_USERNAME'), $get('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    check('Conexiune DB', true, $get('DB_DATABASE') . '@' . $get('DB_HOST'));

    // Verifică tabele critice
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $required = ['users', 'migrations', 'chatbot_conversations', 'chatbot_messages'];
    foreach ($required as $t) {
        check("Tabel: {$t}", in_array($t, $tables), in_array($t, $tables) ? '' : 'LIPSĂ — rulează: php artisan migrate --force');
    }

    // Verifică migrare specifică chatbot
    $ran = $pdo->query("SELECT migration FROM migrations WHERE migration LIKE '%chatbot%'")->fetchAll(PDO::FETCH_COLUMN);
    check('Migrare chatbot rulată', count($ran) > 0, count($ran) > 0 ? implode(', ', $ran) : 'LIPSĂ — php artisan migrate --force');

} catch (PDOException $e) {
    check('Conexiune DB', false, $e->getMessage());
}
echo "</tbody></table></div>\n";

// ─── 5. Laravel bootstrap test ──────────────────────────────────────
echo "<div class='section'><table><thead><tr><th colspan='3'>5. Bootstrap Laravel</th></tr></thead><tbody>\n";
try {
    require $base . '/vendor/autoload.php';
    check('Autoload OK', true, '');

    $app = require $base . '/bootstrap/app.php';
    check('bootstrap/app.php OK', true, '');

    // Testează că serviciile se boot-ează
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    check('Kernel boot OK', true, '');

} catch (Throwable $e) {
    check('Bootstrap EROARE', false, $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
}
echo "</tbody></table></div>\n";

// ─── 6. Ultimele erori din laravel.log ──────────────────────────────
$logFile = $base . '/storage/logs/laravel.log';
echo "<div class='section'><h3 style='color:#f1f5f9;margin-bottom:8px;'>6. Ultimele erori din laravel.log</h3>\n";
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last  = array_slice($lines, -80);
    // Filtrează doar liniile cu ERROR/CRITICAL/exception
    $errors = array_filter($last, fn($l) => preg_match('/ERROR|CRITICAL|exception|Exception|Stack trace/i', $l));
    echo "<pre>" . htmlspecialchars(implode('', $errors ?: array_slice($last, -30))) . "</pre>\n";
} else {
    echo "<p style='color:#f59e0b;'>Fișierul laravel.log nu există sau nu e accesibil.</p>\n";
}
echo "</div>\n";

?>
<p style="color:#475569;font-size:12px;margin-top:32px;">⚠️ ȘTERGE acest fișier după rezolvarea problemei: <code>public/diag500.php</code></p>
</body>
</html>
