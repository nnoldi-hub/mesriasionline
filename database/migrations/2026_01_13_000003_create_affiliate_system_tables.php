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
        // Affiliate Programs - different commission structures
        Schema::create('affiliate_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('commission_value', 10, 2)->default(10.00); // 10% or fixed amount
            $table->decimal('min_payout', 10, 2)->default(100.00); // Minimum payout threshold
            $table->integer('cookie_days')->default(30); // Cookie duration
            $table->boolean('is_active')->default(true);
            $table->json('rules')->nullable(); // Additional rules/conditions
            $table->timestamps();
        });

        // Affiliates - users who can refer others
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->nullable()->constrained('affiliate_programs')->onDelete('set null');
            $table->string('referral_code', 20)->unique();
            $table->string('payment_method')->nullable(); // iban, paypal, etc.
            $table->string('payment_details')->nullable(); // IBAN number, PayPal email, etc.
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('pending');
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('pending_earnings', 12, 2)->default(0);
            $table->decimal('paid_earnings', 12, 2)->default(0);
            $table->integer('total_referrals')->default(0);
            $table->integer('successful_referrals')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('referral_code');
            $table->index('status');
        });

        // Referrals - tracking who referred whom
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('referral_code', 20);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('landing_page')->nullable();
            $table->string('referrer_url')->nullable();
            $table->enum('status', ['clicked', 'registered', 'converted', 'expired'])->default('clicked');
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['referral_code', 'status']);
            $table->index('referred_user_id');
        });

        // Commissions - earnings for affiliates
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->foreignId('referral_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('transaction_type'); // registration, subscription, booking, etc.
            $table->string('transaction_id')->nullable(); // Reference to the original transaction
            $table->decimal('transaction_amount', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0); // Percentage
            $table->decimal('commission_amount', 12, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
            $table->index('transaction_type');
        });

        // Payouts - payments to affiliates
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
        });

        // Add referral tracking to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('referred_by_code', 20)->nullable()->after('remember_token');
            $table->foreignId('referred_by_affiliate_id')->nullable()->after('referred_by_code');
            $table->timestamp('referral_converted_at')->nullable()->after('referred_by_affiliate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['referred_by_code', 'referred_by_affiliate_id', 'referral_converted_at']);
        });

        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('affiliate_programs');
    }
};
