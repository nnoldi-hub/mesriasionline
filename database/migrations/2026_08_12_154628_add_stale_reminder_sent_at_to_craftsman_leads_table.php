<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('craftsman_leads', function (Blueprint $table) {
            $table->timestamp('stale_reminder_sent_at')->nullable()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('craftsman_leads', function (Blueprint $table) {
            $table->dropColumn('stale_reminder_sent_at');
        });
    }
};
