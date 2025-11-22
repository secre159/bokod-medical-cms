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
            // Add consultation_type column if it doesn't exist
            if (!Schema::hasColumn('prescriptions', 'consultation_type')) {
                $table->string('consultation_type', 100)->nullable()->after('notes');
            }
            
            // Also add remaining_quantity if it doesn't exist (from the model)
            if (!Schema::hasColumn('prescriptions', 'remaining_quantity')) {
                $table->integer('remaining_quantity')->nullable()->after('dispensed_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (Schema::hasColumn('prescriptions', 'consultation_type')) {
                $table->dropColumn('consultation_type');
            }
            
            if (Schema::hasColumn('prescriptions', 'remaining_quantity')) {
                $table->dropColumn('remaining_quantity');
            }
        });
    }
};
