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
        // Remove profile picture columns from users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('users', 'profile_picture')) {
                $table->dropColumn('profile_picture');
            }
        });

        // Remove profile picture column from patients table
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'profile_picture')) {
                $table->dropColumn('profile_picture');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add profile picture columns if migration is rolled back
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->string('profile_picture')->nullable();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->string('profile_picture')->nullable();
        });
    }
};
