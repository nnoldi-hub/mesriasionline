<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lista de apeluri „De sunat azi”: sursa lead-ului, rezultatul ultimului apel
 * și următorul pas programat. Plus un istoric al fiecărui apel.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('craftsman_leads', function (Blueprint $table) {
            $table->string('source', 30)->nullable()->after('utm_campaign');   // olx, publi24, facebook, ...
            $table->string('source_url')->nullable()->after('source');         // link la anunț
            $table->string('call_outcome', 30)->nullable()->after('status');   // ultimul rezultat
            $table->unsignedSmallInteger('call_attempts')->default(0)->after('call_outcome');
            $table->timestamp('last_contacted_at')->nullable()->after('call_attempts');
            $table->timestamp('next_action_at')->nullable()->after('last_contacted_at');
            $table->string('next_action_note')->nullable()->after('next_action_at');
            $table->string('refusal_reason', 40)->nullable()->after('next_action_note');

            $table->index('next_action_at');
        });

        Schema::create('craftsman_lead_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('craftsman_lead_id')->constrained('craftsman_leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('outcome', 30);
            $table->string('refusal_reason', 40)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['outcome', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craftsman_lead_calls');

        Schema::table('craftsman_leads', function (Blueprint $table) {
            $table->dropIndex(['next_action_at']);
            $table->dropColumn([
                'source', 'source_url', 'call_outcome', 'call_attempts',
                'last_contacted_at', 'next_action_at', 'next_action_note', 'refusal_reason',
            ]);
        });
    }
};
