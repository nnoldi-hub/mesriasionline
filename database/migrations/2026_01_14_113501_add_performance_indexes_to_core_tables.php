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
        // Users table - core queries
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) $table->index(['role', 'is_active'], 'users_role_active_index');
            if (Schema::hasColumn('users', 'category_id')) $table->index(['category_id', 'is_active'], 'users_category_active_index');
            if (Schema::hasColumn('users', 'location_id')) $table->index(['location_id', 'is_active'], 'users_location_active_index');
            if (Schema::hasColumn('users', 'slug')) $table->index(['slug'], 'users_slug_index');
            $table->index(['created_at'], 'users_created_at_index');
            if (Schema::hasColumn('users', 'verified_at')) $table->index(['verified_at'], 'users_verified_at_index');
            if (Schema::hasColumn('users', 'is_featured')) $table->index(['is_featured', 'is_active'], 'users_featured_active_index');
        });

        // Services table
        Schema::table('services', function (Blueprint $table) {
            $table->index(['user_id', 'is_active'], 'services_user_active_index');
            $table->index(['category_id', 'is_active'], 'services_category_active_index');
            $table->index(['is_active', 'created_at'], 'services_active_recent_index');
        });

        // Reviews table
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'craftsman_id')) $table->index(['craftsman_id', 'is_approved'], 'reviews_craftsman_approved_index');
            if (Schema::hasColumn('reviews', 'client_id')) $table->index(['client_id', 'created_at'], 'reviews_client_recent_index');
            $table->index(['rating', 'is_approved'], 'reviews_rating_approved_index');
            $table->index(['is_approved', 'created_at'], 'reviews_approved_recent_index');
        });

        // Appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['specialist_id', 'status'], 'appointments_specialist_status_index');
            $table->index(['client_id', 'status'], 'appointments_client_status_index');
            $table->index(['appointment_date', 'status'], 'appointments_date_status_index');
            $table->index(['status', 'created_at'], 'appointments_status_recent_index');
        });

        // Quote requests table
        if (Schema::hasTable('quote_requests')) {
            Schema::table('quote_requests', function (Blueprint $table) {
                if (Schema::hasColumn('quote_requests', 'craftsman_id')) $table->index(['craftsman_id', 'status'], 'quote_requests_craftsman_status_index');
                if (Schema::hasColumn('quote_requests', 'client_id')) $table->index(['client_id', 'status'], 'quote_requests_client_status_index');
                $table->index(['status', 'created_at'], 'quote_requests_status_recent_index');
                if (Schema::hasColumn('quote_requests', 'urgency')) $table->index(['urgency', 'status'], 'quote_requests_urgency_status_index');
            });
        }

        // Quotes table
        Schema::table('quotes', function (Blueprint $table) {
            $table->index(['quote_request_id', 'status'], 'quotes_request_status_index');
            $table->index(['craftsman_id', 'status'], 'quotes_craftsman_status_index');
            $table->index(['status', 'created_at'], 'quotes_status_recent_index');
        });

        // Messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['conversation_id', 'created_at'], 'messages_conversation_recent_index');
            $table->index(['sender_id', 'created_at'], 'messages_sender_recent_index');
            $table->index(['is_read', 'created_at'], 'messages_unread_recent_index');
        });

        // Conversations table
        Schema::table('conversations', function (Blueprint $table) {
            $table->index(['user1_id', 'updated_at'], 'conversations_user1_recent_index');
            $table->index(['user2_id', 'updated_at'], 'conversations_user2_recent_index');
            $table->index(['is_archived', 'updated_at'], 'conversations_archived_recent_index');
        });

        // Articles table
        Schema::table('articles', function (Blueprint $table) {
            $table->index(['is_published', 'published_at'], 'articles_published_date_index');
            $table->index(['category_id', 'is_published'], 'articles_category_published_index');
            $table->index(['slug'], 'articles_slug_index');
            $table->index(['views', 'is_published'], 'articles_views_published_index');
        });

        // Article questions table
        Schema::table('article_questions', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'questions_user_recent_index');
            $table->index(['status', 'created_at'], 'questions_status_recent_index');
            $table->index(['is_featured', 'created_at'], 'questions_featured_recent_index');
        });

        // Gallery table - skip if not exists
        if (Schema::hasTable('gallery')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'gallery_user_recent_index');
                $table->index(['is_featured', 'created_at'], 'gallery_featured_recent_index');
            });
        }

        // Profile views table
        Schema::table('profile_views', function (Blueprint $table) {
            if (Schema::hasColumn('profile_views', 'craftsman_id')) $table->index(['craftsman_id', 'created_at'], 'profile_views_craftsman_recent_index');
            if (Schema::hasColumn('profile_views', 'viewer_id')) $table->index(['viewer_id', 'created_at'], 'profile_views_viewer_recent_index');
        });

        // Notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_id', 'notifiable_type', 'read_at'], 'notifications_user_read_index');
            $table->index(['notifiable_id', 'notifiable_type', 'created_at'], 'notifications_user_recent_index');
        });

        // Referrals table
        Schema::table('referrals', function (Blueprint $table) {
            $table->index(['referrer_id', 'created_at'], 'referrals_referrer_recent_index');
            $table->index(['referred_id', 'created_at'], 'referrals_referred_recent_index');
            $table->index(['status', 'created_at'], 'referrals_status_recent_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_active_index');
            $table->dropIndex('users_category_active_index');
            $table->dropIndex('users_location_active_index');
            $table->dropIndex('users_slug_index');
            $table->dropIndex('users_created_at_index');
            $table->dropIndex('users_verified_at_index');
            $table->dropIndex('users_featured_active_index');
        });

        // Services table
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_user_active_index');
            $table->dropIndex('services_category_active_index');
            $table->dropIndex('services_active_recent_index');
        });

        // Reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_craftsman_approved_index');
            $table->dropIndex('reviews_client_recent_index');
            $table->dropIndex('reviews_rating_approved_index');
            $table->dropIndex('reviews_approved_recent_index');
        });

        // Appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('appointments_specialist_status_index');
            $table->dropIndex('appointments_client_status_index');
            $table->dropIndex('appointments_date_status_index');
            $table->dropIndex('appointments_status_recent_index');
        });

        // Quote requests table
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->dropIndex('quote_requests_craftsman_status_index');
            $table->dropIndex('quote_requests_client_status_index');
            $table->dropIndex('quote_requests_status_recent_index');
            $table->dropIndex('quote_requests_urgency_status_index');
        });

        // Quotes table
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex('quotes_request_status_index');
            $table->dropIndex('quotes_craftsman_status_index');
            $table->dropIndex('quotes_status_recent_index');
        });

        // Messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_conversation_recent_index');
            $table->dropIndex('messages_sender_recent_index');
            $table->dropIndex('messages_unread_recent_index');
        });

        // Conversations table
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('conversations_user1_recent_index');
            $table->dropIndex('conversations_user2_recent_index');
            $table->dropIndex('conversations_archived_recent_index');
        });

        // Articles table
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_published_date_index');
            $table->dropIndex('articles_category_published_index');
            $table->dropIndex('articles_slug_index');
            $table->dropIndex('articles_views_published_index');
        });

        // Article questions table
        Schema::table('article_questions', function (Blueprint $table) {
            $table->dropIndex('questions_user_recent_index');
            $table->dropIndex('questions_status_recent_index');
            $table->dropIndex('questions_featured_recent_index');
        });

        // Gallery table - skip if not exists
        if (Schema::hasTable('gallery')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->dropIndex('gallery_user_recent_index');
                $table->dropIndex('gallery_featured_recent_index');
            });
        }

        // Profile views table
        Schema::table('profile_views', function (Blueprint $table) {
            $table->dropIndex('profile_views_craftsman_recent_index');
            $table->dropIndex('profile_views_viewer_recent_index');
        });

        // Notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_index');
            $table->dropIndex('notifications_user_recent_index');
        });

        // Referrals table
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropIndex('referrals_referrer_recent_index');
            $table->dropIndex('referrals_referred_recent_index');
            $table->dropIndex('referrals_status_recent_index');
        });
    }
};
