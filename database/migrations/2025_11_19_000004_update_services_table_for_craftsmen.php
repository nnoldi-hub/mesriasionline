<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Înlocuim sub_brand cu category_id
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            
            // Adaptare pentru servicii meseriași
            $table->text('detailed_description')->nullable()->after('description');
            $table->enum('pricing_type', ['fixed', 'hourly', 'per_project'])->default('fixed')->after('price');
            $table->decimal('min_price', 8, 2)->nullable()->after('pricing_type'); // pentru interval preț
            $table->decimal('max_price', 8, 2)->nullable()->after('min_price');
            
            // Durata și complexitate
            $table->integer('min_duration')->nullable()->after('duration'); // minute minime
            $table->integer('max_duration')->nullable()->after('min_duration'); // minute maxime
            $table->enum('complexity', ['easy', 'medium', 'hard'])->default('medium')->after('max_duration');
            
            // Materiale și echipamente
            $table->boolean('materials_included')->default(false)->after('equipment_needed');
            $table->text('required_materials')->nullable()->after('materials_included');
            $table->boolean('client_provides_materials')->default(false)->after('required_materials');
            
            // Redenumim is_mobile în is_on_location (servicii la locația clientului)
            // Pentru meseriași, majoritatea serviciilor sunt la client
            $table->renameColumn('is_mobile', 'is_on_location');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'detailed_description',
                'pricing_type',
                'min_price',
                'max_price',
                'min_duration',
                'max_duration',
                'complexity',
                'materials_included',
                'required_materials',
                'client_provides_materials'
            ]);
            $table->renameColumn('is_on_location', 'is_mobile');
        });
    }
};
