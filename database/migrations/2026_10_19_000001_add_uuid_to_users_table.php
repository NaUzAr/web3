<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                // Short 8-character hex UUID (e.g., 8f3b1a2c)
                $table->string('uuid', 16)->nullable()->unique()->after('id');
            });
        }

        // Generate 8-char hex UUID for any existing users
        $users = DB::table('users')->whereNull('uuid')->get();
        foreach ($users as $user) {
            do {
                $hex = bin2hex(random_bytes(4));
            } while (DB::table('users')->where('uuid', $hex)->exists());

            DB::table('users')->where('id', $user->id)->update(['uuid' => $hex]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};
