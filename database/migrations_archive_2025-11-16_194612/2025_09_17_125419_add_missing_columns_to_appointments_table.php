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
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'diagnosis')) {
                $table->text('diagnosis')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'treatment_notes')) {
                $table->text('treatment_notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['notes', 'diagnosis', 'treatment_notes']);
        });
    }
};
