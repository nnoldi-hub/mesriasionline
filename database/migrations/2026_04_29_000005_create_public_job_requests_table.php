<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_job_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');

            // Date contact (fără cont)
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email');

            // Detalii lucrare
            $table->string('title');
            $table->text('description');
            $table->string('city')->nullable(); // locație liberă dacă nu e în lista de locații
            $table->date('preferred_date')->nullable();
            $table->enum('urgency', ['flexible', 'this_week', 'urgent'])->default('flexible');
            $table->decimal('budget_max', 10, 2)->nullable();

            // Status
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->integer('notified_craftsmen')->default(0); // câți meseriași au fost notificați

            // Token unic pentru acces fără cont
            $table->string('access_token', 64)->unique();

            $table->timestamps();

            $table->index(['category_id', 'location_id', 'status']);
            $table->index('created_at');
        });

        // Tabelă pivot: ce meseriași au văzut/răspuns la o cerere publică
        Schema::create('public_job_request_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_job_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('craftsman_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['viewed', 'interested', 'not_interested'])->default('viewed');
            $table->text('message')->nullable(); // mesaj opțional de la meseriaș
            $table->timestamps();

            $table->unique(['public_job_request_id', 'craftsman_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_job_request_responses');
        Schema::dropIfExists('public_job_requests');
    }
};
