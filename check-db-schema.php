<?php
/**
 * Database Schema Checker
 * Checks local MySQL database structure
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->boot();

echo "🔍 Local Database Schema Check\n";
echo "===============================\n\n";

try {
    // Check database connection
    $connection = Illuminate\Support\Facades\DB::connection();
    $dbName = $connection->getDatabaseName();
    
    echo "✅ Connected to database: {$dbName}\n";
    echo "📊 Database driver: " . $connection->getDriverName() . "\n\n";
    
    // Get all tables
    $tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
    $tableColumn = 'Tables_in_' . $dbName;
    
    echo "📋 Found " . count($tables) . " tables:\n";
    echo str_repeat("-", 50) . "\n";
    
    $tableDetails = [];
    
    foreach ($tables as $table) {
        $tableName = $table->$tableColumn;
        echo "📁 {$tableName}\n";
        
        // Get columns for each table
        $columns = Illuminate\Support\Facades\DB::select("DESCRIBE {$tableName}");
        
        $columnInfo = [];
        foreach ($columns as $column) {
            $columnInfo[] = [
                'field' => $column->Field,
                'type' => $column->Type,
                'null' => $column->Null,
                'key' => $column->Key,
                'default' => $column->Default,
                'extra' => $column->Extra
            ];
        }
        
        $tableDetails[$tableName] = $columnInfo;
        
        echo "   Columns (" . count($columns) . "): ";
        echo implode(', ', array_column($columnInfo, 'field')) . "\n";
        echo "\n";
    }
    
    // Save detailed schema to file
    $schemaFile = __DIR__ . '/database-schema-local.json';
    file_put_contents($schemaFile, json_encode($tableDetails, JSON_PRETTY_PRINT));
    
    echo "💾 Detailed schema saved to: " . basename($schemaFile) . "\n";
    
    // Check for common tables that should exist
    $requiredTables = [
        'users',
        'patients', 
        'appointments',
        'medicines',
        'prescriptions',
        'conversations',
        'messages',
        'notifications'
    ];
    
    $existingTables = array_map(function($table) use ($tableColumn) {
        return $table->$tableColumn;
    }, $tables);
    
    echo "\n🔍 Required Tables Check:\n";
    echo str_repeat("-", 30) . "\n";
    
    foreach ($requiredTables as $requiredTable) {
        $exists = in_array($requiredTable, $existingTables);
        $status = $exists ? "✅" : "❌";
        echo "{$status} {$requiredTable}\n";
        
        if (!$exists) {
            echo "   ⚠️  Missing table: {$requiredTable}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\n💡 Make sure:\n";
    echo "1. XAMPP MySQL is running\n";
    echo "2. Database 'bokod_pms' exists\n";
    echo "3. Database credentials in .env are correct\n";
}

echo "\n🎯 Next steps:\n";
echo "1. Compare with production PostgreSQL schema\n";
echo "2. Create migration differences if needed\n";
echo "3. Ensure all required columns exist\n";
?>