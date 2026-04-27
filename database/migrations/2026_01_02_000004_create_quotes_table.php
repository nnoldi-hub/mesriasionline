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
        // Quote Requests - Cereri de ofertă de la clienți
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('craftsman_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time')->nullable(); // morning, afternoon, evening
            $table->json('images')->nullable(); // array of image paths
            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->enum('urgency', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'quoted', 'accepted', 'rejected', 'expired', 'completed'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['client_id', 'status']);
            $table->index(['craftsman_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // Quotes - Oferte de la meseriași
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('craftsman_id')->constrained('users')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->decimal('price_max', 10, 2)->nullable(); // For range pricing
            $table->text('description');
            $table->text('materials_included')->nullable();
            $table->integer('estimated_duration_hours')->nullable();
            $table->integer('estimated_duration_days')->nullable();
            $table->date('available_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->json('breakdown')->nullable(); // detailed price breakdown
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired', 'withdrawn'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->index(['quote_request_id', 'status']);
            $table->index(['craftsman_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('quote_requests');
    }
};
