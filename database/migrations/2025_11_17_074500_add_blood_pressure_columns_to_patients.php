<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'systolic_bp')) {
                $table->integer('systolic_bp')->nullable();
            }
            if (!Schema::hasColumn('patients', 'diastolic_bp')) {
                $table->integer('diastolic_bp')->nullable();
            }
            if (!Schema::hasColumn('patients', 'blood_pressure')) {
                $table->string('blood_pressure', 20)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['systolic_bp', 'diastolic_bp', 'blood_pressure']);
        });
    }
};
