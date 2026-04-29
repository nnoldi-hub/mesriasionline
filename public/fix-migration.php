<?php
// Protecție minimă — șterge fișierul după ce rulezi!
if (!isset($_GET['run']) || $_GET['run'] !== 'yes') {
    die('Adaugă ?run=yes la URL pentru a rula.');
}

$envFile = __DIR__ . '/../.env';
$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$key, $val] = explode('=', $line, 2);
    $env[trim($key)] = trim(trim($val), '"\'');
}

$host     = $env['DB_HOST'] ?? '127.0.0.1';
$port     = $env['DB_PORT'] ?? '3306';
$database = $env['DB_DATABASE'] ?? '';
$username = $env['DB_USERNAME'] ?? '';
$password = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("DROP TABLE IF EXISTS `public_job_request_responses`");
    echo "✅ DROP TABLE public_job_request_responses<br>";

    $pdo->exec("DROP TABLE IF EXISTS `public_job_requests`");
    echo "✅ DROP TABLE public_job_requests<br>";

    $stmt = $pdo->prepare("DELETE FROM `migrations` WHERE `migration` = ?");
    $stmt->execute(['2026_04_29_000005_create_public_job_requests_table']);
    echo "✅ Ștergere înregistrare din migrations<br>";

    echo "<br><strong>Gata! Acum rulează: php artisan migrate --force</strong><br>";
    echo "<br><em>⚠️ Șterge acest fișier de pe server: public/fix-migration.php</em>";
} catch (Exception $e) {
    echo "❌ Eroare: " . htmlspecialchars($e->getMessage());
}
