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
            // Add avatar field (separate from profile_picture)
            $table->string('avatar')->nullable()->after('profile_picture');
            
            // Add personal information fields
            $table->string('phone', 20)->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            
            // Add emergency contact information
            $table->string('emergency_contact')->nullable()->after('address');
            $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
            
            // Add medical information (for patients)
            $table->text('medical_history')->nullable()->after('emergency_phone');
            $table->text('allergies')->nullable()->after('medical_history');
            $table->text('notes')->nullable()->after('allergies');
            
            // Add audit fields
            $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            $table->timestamp('last_login_at')->nullable()->after('created_by');
            
            // Add foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['created_by']);
            
            // Drop the added columns
            $table->dropColumn([
                'avatar',
                'phone',
                'date_of_birth',
                'gender',
                'address',
                'emergency_contact',
                'emergency_phone',
                'medical_history',
                'allergies',
                'notes',
                'created_by',
                'last_login_at',
            ]);
        });
    }
};
