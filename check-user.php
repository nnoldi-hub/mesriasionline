<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📋 Ultimii utilizatori:\n\n";

$users = DB::table('users')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($users as $user) {
    echo "ID: {$user->id} | Nume: {$user->name} | Email: {$user->email} | Rol: {$user->role}\n";
}
