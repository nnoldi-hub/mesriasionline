<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `services` MODIFY `pricing_type` ENUM('fixed', 'hourly', 'per_project', 'range', 'on_request') DEFAULT 'fixed'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `services` MODIFY `pricing_type` ENUM('fixed', 'hourly', 'per_project') DEFAULT 'fixed'");
    }
};
