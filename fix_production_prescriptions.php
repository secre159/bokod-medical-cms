<?php

/**
 * PRODUCTION PRESCRIPTIONS SCHEMA FIX SCRIPT
 * 
 * This script adds missing columns to the prescriptions table in production
 */

require_once __DIR__ . '/vendor/autoload.php';
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PRODUCTION PRESCRIPTIONS SCHEMA FIX ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Environment: " . app()->environment() . "\n\n";

try {
    // Start transaction for safety
    DB::beginTransaction();
    
    echo "1. Checking prescriptions table schema...\n";
    
    if (!Schema::hasTable('prescriptions')) {
        echo "❌ Prescriptions table does not exist!\n";
        exit(1);
    }
    
    echo "✓ Prescriptions table exists\n";
    
    // Check for missing columns
    $missingColumns = [];
    $requiredColumns = [
        'prescribed_by' => 'BIGINT UNSIGNED NULL',
        'frequency' => "VARCHAR(255) DEFAULT 'once_daily'", 
        'duration_days' => 'INTEGER NULL',
        'dispensed_quantity' => 'INTEGER DEFAULT 0',
        'remaining_quantity' => 'INTEGER NULL'
    ];
    
    foreach (array_keys($requiredColumns) as $column) {
        if (!Schema::hasColumn('prescriptions', $column)) {
            $missingColumns[] = $column;
            echo "❌ Missing column: {$column}\n";
        } else {
            echo "✓ Column exists: {$column}\n";
        }
    }
    
    if (empty($missingColumns)) {
        echo "\n✅ All required columns exist! No changes needed.\n";
        DB::rollBack();
        exit(0);
    }
    
    echo "\n2. Missing columns found: " . implode(', ', $missingColumns) . "\n";
    
    // Production safety check
    if (app()->environment('production') && !env('APPLY_FIXES', false)) {
        echo "\n⚠️  PRODUCTION ENVIRONMENT DETECTED!\n";
        echo "This script will add " . count($missingColumns) . " columns to the prescriptions table.\n";
        echo "Missing columns: " . implode(', ', $missingColumns) . "\n\n";
        
        echo "To apply these fixes, run this script with APPLY_FIXES=true:\n";
        echo "APPLY_FIXES=true php fix_production_prescriptions.php\n\n";
        
        echo "🔒 NO CHANGES APPLIED (safety mode)\n";
        DB::rollBack();
        exit(0);
    }
    
    echo "\n3. Adding missing columns...\n";
    
    $sqlCommands = [];
    
    foreach ($missingColumns as $column) {
        if (isset($requiredColumns[$column])) {
            $sqlCommand = "ALTER TABLE prescriptions ADD COLUMN {$column} {$requiredColumns[$column]}";
            $sqlCommands[] = $sqlCommand;
            
            echo "   Adding column: {$column}\n";
            DB::statement($sqlCommand);
            echo "   ✅ Added successfully\n";
        }
    }
    
    // Add foreign key constraint for prescribed_by if it was added
    if (in_array('prescribed_by', $missingColumns)) {
        echo "\n   Adding foreign key constraint for prescribed_by...\n";
        
        // Check if constraint already exists
        $constraintExists = DB::select("
            SELECT constraint_name 
            FROM information_schema.table_constraints 
            WHERE table_name = 'prescriptions' 
            AND constraint_name = 'prescriptions_prescribed_by_foreign'
        ");
        
        if (empty($constraintExists)) {
            DB::statement("ALTER TABLE prescriptions ADD CONSTRAINT prescriptions_prescribed_by_foreign FOREIGN KEY (prescribed_by) REFERENCES users(id) ON DELETE SET NULL");
            echo "   ✅ Foreign key constraint added\n";
        } else {
            echo "   ✓ Foreign key constraint already exists\n";
        }
    }
    
    // Commit the changes
    DB::commit();
    
    echo "\n4. Verification...\n";
    $allGood = true;
    
    foreach (array_keys($requiredColumns) as $column) {
        if (!Schema::hasColumn('prescriptions', $column)) {
            echo "   ❌ Column still missing: {$column}\n";
            $allGood = false;
        } else {
            echo "   ✓ Column verified: {$column}\n";
        }
    }
    
    if ($allGood) {
        echo "\n✅ All columns added successfully!\n";
        
        // Update the migrations table if it exists
        if (Schema::hasTable('migrations')) {
            $migrationName = '2025_09_17_030723_add_comprehensive_fields_to_prescriptions_table';
            $migrationExists = DB::table('migrations')->where('migration', $migrationName)->exists();
            
            if (!$migrationExists) {
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => DB::table('migrations')->max('batch') + 1
                ]);
                echo "✓ Migration record added to migrations table\n";
            }
        }
    }
    
    echo "\n🎉 PRESCRIPTIONS SCHEMA FIX COMPLETED SUCCESSFULLY!\n";
    echo "The user details and prescriptions system should now work correctly.\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n🚨 NO CHANGES WERE APPLIED due to error.\n";
    exit(1);
}

echo "\n=== SCRIPT COMPLETE ===\n";