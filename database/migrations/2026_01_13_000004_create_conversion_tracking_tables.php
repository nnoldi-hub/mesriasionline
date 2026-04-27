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
        Schema::create('conversion_events', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('craftsman_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Event info
            $table->string('event_type', 50)->index();
            $table->json('event_data')->nullable();
            
            // Attribution data
            $table->string('source', 100)->nullable()->index(); // google, facebook, direct, referral
            $table->string('medium', 100)->nullable(); // organic, cpc, social, email
            $table->string('campaign', 255)->nullable(); // campaign name
            $table->text('referrer')->nullable();
            $table->text('landing_page')->nullable();
            
            // Device info
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 20)->nullable(); // desktop, mobile, tablet
            
            // Conversion tracking
            $table->timestamp('converted_at')->nullable();
            $table->decimal('conversion_value', 10, 2)->nullable();
            
            $table->timestamps();
            
            // Indexes for analytics queries
            $table->index(['event_type', 'created_at']);
            $table->index(['craftsman_id', 'event_type', 'created_at']);
            $table->index(['source', 'created_at']);
        });

        // Conversion funnels table for tracking user journeys
        Schema::create('conversion_funnels', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('craftsman_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Funnel stages (timestamps for when each stage was reached)
            $table->timestamp('visited_at')->nullable();
            $table->timestamp('profile_viewed_at')->nullable();
            $table->timestamp('contact_clicked_at')->nullable();
            $table->timestamp('message_sent_at')->nullable();
            $table->timestamp('quote_requested_at')->nullable();
            $table->timestamp('quote_received_at')->nullable();
            $table->timestamp('quote_accepted_at')->nullable();
            $table->timestamp('appointment_booked_at')->nullable();
            $table->timestamp('review_submitted_at')->nullable();
            
            // Attribution
            $table->string('source', 100)->nullable();
            $table->string('medium', 100)->nullable();
            $table->string('campaign', 255)->nullable();
            
            // Final conversion status
            $table->string('final_status', 50)->default('in_progress'); // in_progress, converted, abandoned
            $table->decimal('total_value', 10, 2)->nullable();
            
            $table->timestamps();
            
            $table->index(['final_status', 'created_at']);
            $table->index(['craftsman_id', 'final_status']);
        });

        // Platform-wide daily analytics
        Schema::create('platform_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            
            // Traffic
            $table->unsignedInteger('total_visits')->default(0);
            $table->unsignedInteger('unique_visitors')->default(0);
            $table->unsignedInteger('page_views')->default(0);
            
            // Users
            $table->unsignedInteger('new_registrations')->default(0);
            $table->unsignedInteger('new_craftsmen')->default(0);
            $table->unsignedInteger('new_clients')->default(0);
            $table->unsignedInteger('active_users')->default(0);
            
            // Engagement
            $table->unsignedInteger('profile_views')->default(0);
            $table->unsignedInteger('messages_sent')->default(0);
            $table->unsignedInteger('quote_requests')->default(0);
            $table->unsignedInteger('quotes_sent')->default(0);
            $table->unsignedInteger('quotes_accepted')->default(0);
            $table->unsignedInteger('appointments_booked')->default(0);
            $table->unsignedInteger('reviews_submitted')->default(0);
            
            // Revenue (if applicable)
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('affiliate_commissions', 12, 2)->default(0);
            
            // Conversion rates (stored as percentages)
            $table->decimal('visit_to_contact_rate', 5, 2)->default(0);
            $table->decimal('contact_to_quote_rate', 5, 2)->default(0);
            $table->decimal('quote_to_booking_rate', 5, 2)->default(0);
            
            // Traffic sources breakdown (JSON)
            $table->json('traffic_sources')->nullable();
            $table->json('device_breakdown')->nullable();
            $table->json('top_categories')->nullable();
            $table->json('top_locations')->nullable();
            
            $table->timestamps();
            
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_daily_stats');
        Schema::dropIfExists('conversion_funnels');
        Schema::dropIfExists('conversion_events');
    }
};
