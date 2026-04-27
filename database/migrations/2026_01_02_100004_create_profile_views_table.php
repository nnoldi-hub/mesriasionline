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
        // Profile views tracking for analytics
        Schema::create('profile_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('craftsman_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('viewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('source')->nullable(); // google, direct, facebook, etc.
            $table->timestamp('viewed_at');
            
            $table->index(['craftsman_id', 'viewed_at']);
            $table->index('viewed_at');
        });

        // Service clicks tracking
        Schema::create('service_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->enum('action', ['view', 'contact', 'quote_request', 'book'])->default('view');
            $table->timestamp('clicked_at');
            
            $table->index(['service_id', 'clicked_at']);
            $table->index(['action', 'clicked_at']);
        });

        // Daily stats aggregation for performance
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('profile_views')->default(0);
            $table->integer('service_views')->default(0);
            $table->integer('contact_clicks')->default(0);
            $table->integer('quote_requests')->default(0);
            $table->integer('bookings')->default(0);
            $table->integer('messages_received')->default(0);
            $table->integer('reviews_received')->default(0);
            $table->decimal('avg_rating', 3, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
        Schema::dropIfExists('service_clicks');
        Schema::dropIfExists('profile_views');
    }
};
