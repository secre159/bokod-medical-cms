<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Update the role column to ensure it can accept 'it' role value
     */
    public function up(): void
    {
        // Use Laravel's Schema builder which handles both MySQL and PostgreSQL
        Schema::table('users', function (Blueprint $table) {
            // Change the role column to varchar to accept 'it' role
            $table->string('role', 50)->default('user')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - role column remains as VARCHAR
    }
};
