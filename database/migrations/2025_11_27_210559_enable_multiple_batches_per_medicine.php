<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Enable multiple batches per medicine by removing unique constraint on medicine_name
     * and adding composite unique constraint on medicine_name + batch_number
     */
    public function up(): void
    {
        // Step 1: Update existing records without batch_number to have a default batch number
        DB::table('medicines')
            ->whereNull('batch_number')
            ->orWhere('batch_number', '')
            ->get()
            ->each(function ($medicine) {
                $year = Carbon::parse($medicine->created_at)->format('Y');
                $batchNumber = "BATCH-{$year}-{$medicine->id}";
                
                // Set default manufacturing date if null
                $manufacturingDate = $medicine->manufacturing_date 
                    ?? Carbon::parse($medicine->created_at)->format('Y-m-d');
                
                // Set default expiry date if null (2 years from manufacturing)
                $expiryDate = $medicine->expiry_date 
                    ?? Carbon::parse($manufacturingDate)->addYears(2)->format('Y-m-d');
                
                DB::table('medicines')
                    ->where('id', $medicine->id)
                    ->update([
                        'batch_number' => $batchNumber,
                        'manufacturing_date' => $manufacturingDate,
                        'expiry_date' => $expiryDate,
                    ]);
            });
        
        // Step 2: Modify the medicines table
        Schema::table('medicines', function (Blueprint $table) {
            // Drop the unique constraint on medicine_name if it exists
            $table->dropUnique(['medicine_name']);
            
            // Make batch_number NOT NULL
            $table->string('batch_number', 50)->nullable(false)->change();
            
            // Add composite unique constraint on medicine_name + batch_number
            $table->unique(['medicine_name', 'batch_number'], 'medicines_name_batch_unique');
            
            // Add index for efficient batch queries
            $table->index(['medicine_name', 'batch_number', 'expiry_date'], 'medicines_name_batch_expiry_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('medicines_name_batch_unique');
            
            // Drop the index
            $table->dropIndex('medicines_name_batch_expiry_index');
            
            // Make batch_number nullable again
            $table->string('batch_number', 50)->nullable()->change();
            
            // Re-add unique constraint on medicine_name
            // Note: This will fail if there are duplicate medicine names
            // $table->unique('medicine_name');
        });
    }
};
