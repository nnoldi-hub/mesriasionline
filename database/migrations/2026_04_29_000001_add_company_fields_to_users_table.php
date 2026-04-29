<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // is_company: diferențiază PF de persoană juridică
            if (!Schema::hasColumn('users', 'is_company')) {
                $table->boolean('is_company')->default(false)->after('cui');
            }
            // Tipul entității juridice: PFA, SRL, SA, II, RA
            if (!Schema::hasColumn('users', 'company_type')) {
                $table->string('company_type', 10)->nullable()->after('is_company');
            }
            // Număr Registrul Comerțului (ex: J40/1234/2020)
            if (!Schema::hasColumn('users', 'reg_com')) {
                $table->string('reg_com', 30)->nullable()->after('company_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_company', 'company_type', 'reg_com']);
        });
    }
};
