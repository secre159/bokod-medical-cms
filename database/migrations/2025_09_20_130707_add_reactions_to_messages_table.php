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
        Schema::table('messages', function (Blueprint $table) {
            // Add reactions column as JSON to store reactions data
            // Format: {'👍': {'count': 2, 'users': [1, 5]}, '❤️': {'count': 1, 'users': [3]}}
            $table->json('reactions')->nullable()->after('is_system_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('reactions');
        });
    }
};
