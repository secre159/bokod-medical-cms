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
        // Add each column only if it does not already exist
        if (!Schema::hasColumn('medicines', 'generic_name')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('generic_name')->nullable()->after('medicine_name');
            });
        }
        if (!Schema::hasColumn('medicines', 'brand_name')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('brand_name')->nullable()->after('generic_name');
            });
        }
        if (!Schema::hasColumn('medicines', 'therapeutic_class')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('therapeutic_class')->nullable()->after('category');
            });
        }
        if (!Schema::hasColumn('medicines', 'indication')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->text('indication')->nullable()->after('description');
            });
        }
        if (!Schema::hasColumn('medicines', 'dosage_instructions')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->text('dosage_instructions')->nullable()->after('strength');
            });
        }
        if (!Schema::hasColumn('medicines', 'age_restrictions')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('age_restrictions')->nullable()->after('dosage_instructions');
            });
        }
        if (!Schema::hasColumn('medicines', 'unit_measure')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('unit_measure')->nullable()->after('unit');
            });
        }
        if (!Schema::hasColumn('medicines', 'balance_per_card')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->integer('balance_per_card')->nullable()->after('stock_quantity');
            });
        }
        if (!Schema::hasColumn('medicines', 'on_hand_per_count')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->integer('on_hand_per_count')->nullable()->after('balance_per_card');
            });
        }
        if (!Schema::hasColumn('medicines', 'shortage_overage')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->integer('shortage_overage')->default(0)->after('on_hand_per_count');
            });
        }
        if (!Schema::hasColumn('medicines', 'inventory_remarks')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->text('inventory_remarks')->nullable()->after('notes');
            });
        }
        if (!Schema::hasColumn('medicines', 'manufacturing_date')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->date('manufacturing_date')->nullable()->after('batch_number');
            });
        }
        // expiry_date likely exists already, but guard anyway
        if (!Schema::hasColumn('medicines', 'storage_conditions')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('storage_conditions')->nullable()->after('expiry_date');
            });
        }
        if (!Schema::hasColumn('medicines', 'drug_interactions')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->text('drug_interactions')->nullable()->after('contraindications');
            });
        }
        if (!Schema::hasColumn('medicines', 'pregnancy_category')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('pregnancy_category', 10)->nullable()->after('drug_interactions');
            });
        }
        if (!Schema::hasColumn('medicines', 'warnings')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->text('warnings')->nullable()->after('pregnancy_category');
            });
        }
        if (!Schema::hasColumn('medicines', 'medicine_image')) {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('medicine_image')->nullable()->after('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop only columns we may have added
        Schema::table('medicines', function (Blueprint $table) {
            $drops = [
                'generic_name',
                'brand_name',
                'therapeutic_class',
                'indication',
                'dosage_instructions',
                'age_restrictions',
                'unit_measure',
                'balance_per_card',
                'on_hand_per_count',
                'shortage_overage',
                'inventory_remarks',
                'manufacturing_date',
                'storage_conditions',
                'drug_interactions',
                'pregnancy_category',
                'warnings',
                'medicine_image',
            ];
            foreach ($drops as $col) {
                if (Schema::hasColumn('medicines', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};