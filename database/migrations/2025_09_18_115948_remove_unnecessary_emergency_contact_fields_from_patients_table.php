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
        Schema::table('patients', function (Blueprint $table) {
            // Remove unnecessary emergency contact fields
            $table->dropColumn([
                'emergency_contact_phone_alt',
                'emergency_contact_email'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Add back the removed fields
            $table->string('emergency_contact_phone_alt', 20)->nullable()->comment('Alternative phone number');
            $table->string('emergency_contact_email', 255)->nullable()->comment('Emergency contact email address');
        });
    }
};
