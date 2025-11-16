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
            // Add new emergency contact fields
            $table->string('emergency_contact_name', 255)->nullable()->comment('Full name of emergency contact');
            $table->string('emergency_contact_relationship', 100)->nullable()->comment('Relationship to patient (e.g., Parent, Spouse, Sibling)');
            $table->string('emergency_contact_phone', 20)->nullable()->comment('Primary phone number for emergency contact');
            $table->string('emergency_contact_phone_alt', 20)->nullable()->comment('Alternative phone number');
            $table->string('emergency_contact_email', 255)->nullable()->comment('Emergency contact email address');
            $table->text('emergency_contact_address')->nullable()->comment('Emergency contact address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone', 
                'emergency_contact_phone_alt',
                'emergency_contact_email',
                'emergency_contact_address'
            ]);
        });
    }
};
