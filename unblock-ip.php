<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "� Deblocare completă pentru localhost...\n\n";

// 1. Șterge toate activitățile suspecte
$deleted = DB::table('suspicious_activities')
    ->whereIn('ip_address', ['127.0.0.1', '::1'])
    ->delete();
echo "✅ Șterse {$deleted} intrări de activitate suspectă\n";

// 2. Curăță cache-ul complet
Cache::flush();
echo "✅ Cache-ul complet golit (inclusiv failed_login, submission, etc.)\n";

// 3. Curăță toate tipurile de cache Laravel
Artisan::call('cache:clear');
Artisan::call('config:clear');
Artisan::call('view:clear');
Artisan::call('route:clear');
echo "✅ Tot cache-ul Laravel curățat\n";

echo "\n🎉 Gata! IP-ul localhost este complet deblocat.\n";
echo "🔄 Reîmprospătează pagina în browser cu Ctrl+Shift+R\n";
