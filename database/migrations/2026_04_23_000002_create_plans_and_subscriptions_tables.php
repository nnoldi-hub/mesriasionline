<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Free, Starter, Pro
            $table->string('slug')->unique();              // free, starter, pro
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 8, 2)->default(0); // 0 = gratuit
            $table->integer('max_quotes_per_month')->default(0); // 0 = nelimitat
            $table->boolean('featured_listing')->default(false);
            $table->boolean('priority_visibility')->default(false);
            $table->boolean('badge_visible')->default(false);
            $table->json('features')->nullable();          // lista bullet points pt UI
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('restrict');
            $table->enum('status', ['active', 'cancelled', 'expired', 'trial'])->default('trial');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('payment_provider')->nullable();  // stripe, paypal, manual
            $table->string('payment_reference')->nullable(); // ID tranzacție extern
            $table->integer('quotes_used_this_month')->default(0);
            $table->timestamp('quotes_reset_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['ends_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
