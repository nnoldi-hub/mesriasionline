<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type')->unique();
            $table->string('label');
            $table->string('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('database_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('notification_settings')->insert([
            [
                'notification_type' => 'new_quote_request',
                'label'             => 'Cerere Ofertă Nouă',
                'description'       => 'Trimisă meșterilor când un client postează o cerere de ofertă în categoria lor',
                'is_enabled'        => true,
                'email_enabled'     => true,
                'database_enabled'  => true,
                'push_enabled'      => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'notification_type' => 'quote_received',
                'label'             => 'Ofertă Primită',
                'description'       => 'Trimisă clienților când un meșter le-a trimis o ofertă la cererea lor',
                'is_enabled'        => true,
                'email_enabled'     => true,
                'database_enabled'  => true,
                'push_enabled'      => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'notification_type' => 'quote_accepted',
                'label'             => 'Ofertă Acceptată',
                'description'       => 'Trimisă meșterilor când un client le-a acceptat oferta',
                'is_enabled'        => true,
                'email_enabled'     => true,
                'database_enabled'  => true,
                'push_enabled'      => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'notification_type' => 'new_appointment',
                'label'             => 'Programare Nouă',
                'description'       => 'Trimisă meșterilor când un client face o programare',
                'is_enabled'        => true,
                'email_enabled'     => true,
                'database_enabled'  => true,
                'push_enabled'      => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'notification_type' => 'new_message',
                'label'             => 'Mesaj Nou',
                'description'       => 'Trimisă utilizatorilor când primesc un mesaj nou în conversație',
                'is_enabled'        => true,
                'email_enabled'     => true,
                'database_enabled'  => true,
                'push_enabled'      => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'notification_type' => 'new_review',
                'label'             => 'Recenzie Nouă',
                'description'       => 'Trimisă meșterilor când primesc o recenzie de la un client',
                'is_enabled'        => true,
                'email_enabled'     => true,
                'database_enabled'  => true,
                'push_enabled'      => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
