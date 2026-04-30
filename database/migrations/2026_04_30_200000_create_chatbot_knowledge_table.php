<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('question_example');           // Ex: "Cum mă înscriu ca meseriaș?"
            $table->text('keywords');                      // Ex: "inscrie,meserias,cont nou,devino meserias"
            $table->text('answer');                        // Răspunsul direct afișat
            $table->string('cta_label')->nullable();       // Ex: "Înscrie-te acum"
            $table->string('cta_url')->nullable();         // Ex: "/register?type=craftsman"
            $table->integer('priority')->default(0);       // Mai mare = verificat primul
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge');
    }
};
