<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suspicious_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // failed_login, rapid_submission, unusual_location, etc.
            $table->string('severity')->default('low'); // low, medium, high, critical
            $table->ipAddress('ip_address');
            $table->string('user_agent')->nullable();
            $table->text('details')->nullable(); // JSON string with additional data
            $table->integer('risk_score')->default(0); // 0-100
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('blocked_until')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['is_blocked', 'blocked_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suspicious_activities');
    }
};
