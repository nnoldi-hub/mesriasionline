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
        Schema::create('client_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // ex: "Acasă", "Birou", "Părinți"
            $table->string('street');
            $table->string('number')->nullable();
            $table->string('building')->nullable(); // bloc
            $table->string('entrance')->nullable(); // scara
            $table->string('floor')->nullable(); // etaj
            $table->string('apartment')->nullable(); // apartament
            $table->string('city');
            $table->string('county'); // județ
            $table->string('postal_code')->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('notes')->nullable(); // indicații suplimentare
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'is_default']);
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_addresses');
    }
};
