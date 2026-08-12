<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('notification_settings')->insert([
            'notification_type' => 'subscription_expiring',
            'label'              => 'Abonament pe cale să expire',
            'description'        => 'Trimisă meșterilor cu câteva zile înainte ca abonamentul plătit să expire',
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
        DB::table('notification_settings')->where('notification_type', 'subscription_expiring')->delete();
    }
};
