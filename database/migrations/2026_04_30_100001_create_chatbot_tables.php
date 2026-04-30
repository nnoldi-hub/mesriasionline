<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();

            // Identificare sesiune/utilizator
            $table->string('session_id', 100)->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Date vizitator (pentru conversie)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('page_url')->nullable();         // pagina de unde a pornit chat-ul

            // Intenție detectată (pentru analytics)
            $table->enum('intent', [
                'unknown',
                'craftsman_register',   // vrea să se înregistreze ca meseriaș
                'client_request',       // vrea să posteze o cerere
                'pricing',              // întreabă despre prețuri/planuri
                'info',                 // întreabă informații generale
                'support',              // are o problemă/reclamație
                'other',
            ])->default('unknown');

            // Rezultat conversație (pentru conversie)
            $table->boolean('converted')->default(false);   // a dat click pe un buton CTA?
            $table->string('conversion_url')->nullable();    // pe ce URL a dat click

            // Statistici conversație
            $table->unsignedSmallInteger('message_count')->default(0);
            $table->unsignedSmallInteger('user_messages')->default(0);

            // Calitatea conversației (automat calculat)
            $table->boolean('was_helpful')->nullable();     // user a răspuns la feedback

            // Timestamp
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['created_at', 'intent']);
            $table->index(['converted', 'intent']);
            $table->index('user_id');
        });

        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained('chatbot_conversations')
                ->onDelete('cascade');

            $table->enum('role', ['user', 'assistant']);
            $table->text('content');

            // Metadata pentru mesajul asistentului
            $table->json('actions')->nullable();             // butoanele CTA returnate
            $table->unsignedSmallInteger('tokens_used')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_conversations');
    }
};
