<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('quote_request_id')->nullable()->after('appointment_id')
                ->constrained()->onDelete('cascade');
        });

        // appointment_id trebuie să devină opțional (recenziile pot veni și din quote_requests).
        // Folosim SQL brut pentru că doctrine/dbal (necesar pentru ->change()) nu e instalat.
        DB::statement('ALTER TABLE reviews MODIFY appointment_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['quote_request_id']);
            $table->dropColumn('quote_request_id');
        });

        DB::statement('ALTER TABLE reviews MODIFY appointment_id BIGINT UNSIGNED NOT NULL');
    }
};
