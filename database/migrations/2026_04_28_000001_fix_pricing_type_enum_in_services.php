<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Sintaxa ALTER ... MODIFY ... ENUM e specifica MySQL; pe alte drivere
        // (ex. SQLite, folosit in teste) coloana ramane cum a fost creata.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE `services` MODIFY `pricing_type` ENUM('fixed', 'hourly', 'per_project', 'range', 'on_request') DEFAULT 'fixed'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE `services` MODIFY `pricing_type` ENUM('fixed', 'hourly', 'per_project') DEFAULT 'fixed'");
    }
};
