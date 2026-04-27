<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
        });

        // Backfill slugs for existing users
        $users = DB::table('users')->select('id','name','slug')->get();
        foreach ($users as $user) {
            if (empty($user->slug)) {
                $slug = Str::slug($user->name.'-'.$user->id);
                // Ensure uniqueness
                $base = $slug; $i = 1;
                while (DB::table('users')->where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                DB::table('users')->where('id', $user->id)->update(['slug' => $slug]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
