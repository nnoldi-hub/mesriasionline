<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('craftsman_leads', function (Blueprint $table) {
            $table->foreignId('referred_by_user_id')->nullable()->after('user_id')
                ->constrained('users')->onDelete('set null');
            $table->boolean('referral_reward_given')->default(false)->after('referred_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('craftsman_leads', function (Blueprint $table) {
            $table->dropForeign(['referred_by_user_id']);
            $table->dropColumn(['referred_by_user_id', 'referral_reward_given']);
        });
    }
};
