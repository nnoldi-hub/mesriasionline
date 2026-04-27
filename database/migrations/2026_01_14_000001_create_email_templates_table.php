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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Numele intern al template-ului');
            $table->string('slug')->unique()->comment('Identificator unic pentru template');
            $table->string('subject')->comment('Subiectul email-ului');
            $table->text('body')->comment('Conținutul email-ului în format Markdown/HTML');
            $table->string('notification_type')->nullable()->comment('Tipul notificării asociate');
            $table->json('variables')->nullable()->comment('Variabilele disponibile în template');
            $table->string('category')->default('general')->comment('Categoria template-ului');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false)->comment('Template implicit pentru tipul de notificare');
            $table->timestamps();
            
            $table->index('notification_type');
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
