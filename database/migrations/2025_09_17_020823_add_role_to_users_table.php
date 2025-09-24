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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'patient'])->default('patient')->after('email');
            $table->enum('status', ['active', 'archived'])->default('active')->after('role');
            $table->string('display_name')->nullable()->after('name');
            $table->string('profile_picture')->nullable()->after('display_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'display_name', 'profile_picture']);
        });
    }
};
