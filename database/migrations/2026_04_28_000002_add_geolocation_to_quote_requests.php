<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            // Allow open requests (no specific craftsman targeted)
            $table->unsignedBigInteger('craftsman_id')->nullable()->change();

            // Client's location coordinates when submitting the request
            $table->decimal('client_lat', 10, 8)->nullable()->after('location');
            $table->decimal('client_lng', 11, 8)->nullable()->after('client_lat');
        });
    }

    public function down(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('craftsman_id')->nullable(false)->change();
            $table->dropColumn(['client_lat', 'client_lng']);
        });
    }
};
