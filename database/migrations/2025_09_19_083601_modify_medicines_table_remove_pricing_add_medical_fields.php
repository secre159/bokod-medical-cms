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
        Schema::table('medicines', function (Blueprint $table) {
            // Remove pricing fields
            $table->dropColumn([
                'unit_price',
                'selling_price', 
                'unit_value',
                'total_value'
            ]);
            
            // Add new medical information fields
            $table->string('therapeutic_class')->nullable()->after('category');
            $table->text('indication')->nullable()->after('description');
            $table->text('dosage_instructions')->nullable()->after('strength');
            $table->string('age_restrictions')->nullable()->after('dosage_instructions');
            $table->text('drug_interactions')->nullable()->after('contraindications');
            $table->string('pregnancy_category', 10)->nullable()->after('drug_interactions');
            $table->text('warnings')->nullable()->after('pregnancy_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Remove new medical fields
            $table->dropColumn([
                'therapeutic_class',
                'indication',
                'dosage_instructions',
                'age_restrictions',
                'drug_interactions',
                'pregnancy_category',
                'warnings'
            ]);
            
            // Re-add pricing fields
            $table->decimal('unit_price', 10, 2)->nullable()->after('minimum_stock');
            $table->decimal('selling_price', 10, 2)->nullable()->after('unit_price');
            $table->decimal('unit_value', 10, 2)->nullable()->after('unit_measure');
            $table->decimal('total_value', 10, 2)->nullable()->after('selling_price');
        });
    }
};
