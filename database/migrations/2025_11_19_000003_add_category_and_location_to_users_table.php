<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Înlocuim sub_brand cu category_id pentru meseriași
            $table->foreignId('category_id')->nullable()->after('description')->constrained()->onDelete('set null');
            $table->foreignId('location_id')->nullable()->after('category_id')->constrained()->onDelete('set null');
            
            // Adaptare pentru meseriași
            $table->string('specialization')->nullable()->after('location_id'); // ex: "Instalații sanitare și termice"
            $table->integer('experience_years')->default(0)->after('specialization');
            $table->json('certifications')->nullable()->after('experience_years'); // certificări, atestate
            $table->boolean('has_insurance')->default(false)->after('certifications'); // asigurare profesională
            $table->string('company_name')->nullable()->after('has_insurance'); // nume firmă (opțional)
            $table->string('cui')->nullable()->after('company_name'); // CUI firmă
            
            // Zona de acoperire și disponibilitate
            $table->integer('service_radius_km')->default(30)->after('max_distance'); // rază servicii în km
            $table->json('working_hours')->nullable()->after('service_radius_km'); // program lucru
            $table->boolean('available_weekends')->default(false)->after('working_hours');
            $table->boolean('emergency_services')->default(false)->after('available_weekends'); // servicii urgență
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['location_id']);
            $table->dropColumn([
                'category_id',
                'location_id',
                'specialization',
                'experience_years',
                'certifications',
                'has_insurance',
                'company_name',
                'cui',
                'service_radius_km',
                'working_hours',
                'available_weekends',
                'emergency_services'
            ]);
        });
    }
};
