<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('craftsman_leads', function (Blueprint $table) {
            $table->id();

            // Date de contact
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('city', 100);

            // Informații profesionale
            $table->enum('trade', ['electrician', 'instalator', 'tamplar', 'zugrav', 'mecanic']);
            $table->enum('experience_range', ['0-2', '3-5', '5+'])->default('0-2');

            // Upload-uri opționale (salvate ca path-uri)
            $table->string('profile_photo')->nullable();
            $table->string('work_photo')->nullable();

            // Stare lead
            $table->enum('status', ['nou', 'contactat', 'invitat', 'inregistrat', 'respins'])->default('nou');
            $table->text('admin_notes')->nullable();

            // Token de activare cont (unic, trimis prin email)
            $table->string('invite_token', 64)->nullable()->unique();
            $table->timestamp('invite_sent_at')->nullable();

            // La crearea contului, referință spre userul creat
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('account_created_at')->nullable();

            // Sursă trafic (pentru analytics)
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();

            $table->timestamps();

            // Indexuri pentru filtrare rapidă
            $table->index('trade');
            $table->index('status');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craftsman_leads');
    }
};
