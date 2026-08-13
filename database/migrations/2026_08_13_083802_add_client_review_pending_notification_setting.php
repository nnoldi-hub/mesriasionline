<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('notification_settings')->insert([
            'notification_type' => 'client_review_pending',
            'label'              => 'Client fără recenzie',
            'description'        => 'Trimisă meseriașului când clientul nu a lăsat recenzie la câteva zile după finalizare, ca să-l poată contacta personal',
            'is_enabled'         => true,
            'email_enabled'      => true,
            'database_enabled'   => true,
            'push_enabled'       => true,
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('notification_settings')->where('notification_type', 'client_review_pending')->delete();
    }
};
