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
        Schema::table('prescriptions', function (Blueprint $table) {
            // Add missing fields for comprehensive prescription management
            $table->string('frequency')->default('once_daily')->after('dosage');
            $table->integer('duration_days')->nullable()->after('frequency');
            $table->integer('dispensed_quantity')->default(0)->after('quantity');
            $table->integer('remaining_quantity')->nullable()->after('dispensed_quantity');
            $table->unsignedBigInteger('prescribed_by')->nullable()->after('expiry_date');
            
            // Add foreign key constraint for prescribed_by (user who prescribed)
            $table->foreign('prescribed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['prescribed_by']);
            
            // Drop added columns
            $table->dropColumn([
                'frequency', 'duration_days', 'dispensed_quantity', 
                'remaining_quantity', 'prescribed_by'
            ]);
        });
    }
};
