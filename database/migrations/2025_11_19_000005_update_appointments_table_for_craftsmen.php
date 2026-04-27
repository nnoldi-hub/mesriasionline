<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Tip cerere: programare directă sau cerere ofertă
            $table->enum('request_type', ['appointment', 'quote'])->default('appointment')->after('id');
            
            // Pentru cereri de ofertă
            $table->text('work_description')->nullable()->after('notes'); // descriere detaliată lucrare
            $table->json('work_photos')->nullable()->after('work_description'); // poze cu lucrarea
            $table->date('preferred_start_date')->nullable()->after('work_photos');
            $table->enum('urgency', ['low', 'medium', 'high', 'emergency'])->default('medium')->after('preferred_start_date');
            
            // Ofertă meseriaș
            $table->decimal('quoted_price', 8, 2)->nullable()->after('urgency');
            $table->text('quote_details')->nullable()->after('quoted_price');
            $table->integer('estimated_duration_hours')->nullable()->after('quote_details');
            $table->date('quote_valid_until')->nullable()->after('estimated_duration_hours');
            
            // Status extins pentru workflow meseriași
            $table->dropColumn('status');
        });
        
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('status', [
                'pending',           // așteptare confirmare
                'quote_sent',        // ofertă trimisă
                'quote_accepted',    // ofertă acceptată
                'quote_rejected',    // ofertă respinsă
                'confirmed',         // confirmat
                'in_progress',       // în desfășurare
                'completed',         // finalizat
                'cancelled',         // anulat
                'needs_revisit'      // necesită revenire
            ])->default('pending')->after('request_type');
        });
        
        Schema::table('appointments', function (Blueprint $table) {
            // Date finalizare
            $table->date('actual_start_date')->nullable()->after('status');
            $table->date('actual_end_date')->nullable()->after('actual_start_date');
            $table->integer('actual_duration_hours')->nullable()->after('actual_end_date');
            $table->text('completion_notes')->nullable()->after('actual_duration_hours');
            $table->json('completion_photos')->nullable()->after('completion_notes'); // poze lucrare finalizată
            
            // Garanție și follow-up
            $table->integer('warranty_months')->default(0)->after('completion_photos');
            $table->date('warranty_expires_at')->nullable()->after('warranty_months');
            $table->boolean('requires_followup')->default(false)->after('warranty_expires_at');
            $table->date('followup_date')->nullable()->after('requires_followup');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'request_type',
                'work_description',
                'work_photos',
                'preferred_start_date',
                'urgency',
                'quoted_price',
                'quote_details',
                'estimated_duration_hours',
                'quote_valid_until',
                'actual_start_date',
                'actual_end_date',
                'actual_duration_hours',
                'completion_notes',
                'completion_photos',
                'warranty_months',
                'warranty_expires_at',
                'requires_followup',
                'followup_date'
            ]);
            
            $table->dropColumn('status');
        });
        
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
        });
    }
};
