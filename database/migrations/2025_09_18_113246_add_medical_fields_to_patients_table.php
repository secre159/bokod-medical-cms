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
            $table->decimal('height', 5, 1)->nullable()->after('course')->comment('Height in centimeters');
            $table->decimal('weight', 5, 1)->nullable()->after('height')->comment('Weight in kilograms');
            $table->integer('systolic_bp')->nullable()->after('bmi')->comment('Systolic blood pressure');
            $table->integer('diastolic_bp')->nullable()->after('systolic_bp')->comment('Diastolic blood pressure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['height', 'weight', 'systolic_bp', 'diastolic_bp']);
        });
    }
};
