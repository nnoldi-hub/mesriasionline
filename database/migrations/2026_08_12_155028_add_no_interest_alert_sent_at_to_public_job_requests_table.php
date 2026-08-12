<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_job_requests', function (Blueprint $table) {
            $table->timestamp('no_interest_alert_sent_at')->nullable()->after('notified_craftsmen');
        });
    }

    public function down(): void
    {
        Schema::table('public_job_requests', function (Blueprint $table) {
            $table->dropColumn('no_interest_alert_sent_at');
        });
    }
};
