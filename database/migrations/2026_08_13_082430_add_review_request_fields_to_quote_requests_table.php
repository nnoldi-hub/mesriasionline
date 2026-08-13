<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->string('review_token', 64)->nullable()->unique()->after('status');
            $table->timestamp('review_requested_at')->nullable()->after('review_token');
            $table->timestamp('review_reminder_sent_at')->nullable()->after('review_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->dropColumn(['review_token', 'review_requested_at', 'review_reminder_sent_at']);
        });
    }
};
