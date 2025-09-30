<?php
/**
 * Alternative Database Backup Script
 * Uses your Laravel application to backup data via HTTP requests
 */

$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Check if Laravel app is running
$appUrl = 'http://127.0.0.1:8000'; // Update if your Laravel runs on different port
$timestamp = date('Y-m-d_H-i-s');

echo "=== Alternative Database Backup ===\n";
echo "This method uses your Laravel application to export data.\n\n";

// First, let's create a simple backup route in your Laravel app
$routeCode = "<?php
// Add this to your web.php routes file

use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

Route::get('/backup-data/{table?}', function(\$table = null) {
    try {
        if (\$table) {
            // Backup specific table
            if (!Schema::hasTable(\$table)) {
                return response()->json(['error' => 'Table not found'], 404);
            }
            
            \$data = DB::table(\$table)->get();
            return response()->json([
                'table' => \$table,
                'rows' => \$data->count(),
                'data' => \$data
            ]);
        } else {
            // List all tables
            \$tables = DB::select(\"
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_type = 'BASE TABLE'
                ORDER BY table_name
            \");
            
            return response()->json([
                'tables' => array_map(function(\$t) { return \$t->table_name; }, \$tables)
            ]);
        }
    } catch (Exception \$e) {
        return response()->json(['error' => \$e->getMessage()], 500);
    }
});
?>";

$routeFile = __DIR__ . '/backup-route-code.php';
file_put_contents($routeFile, $routeCode);

echo "📝 Created backup route code: backup-route-code.php\n";
echo "📋 To use this backup method:\n\n";
echo "1. Add the route code to your routes/web.php file\n";
echo "2. Start your Laravel development server: php artisan serve\n";
echo "3. Run this script again to download the data\n\n";

// Try to connect and backup if server is running
echo "Checking if Laravel server is running...\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'ignore_errors' => true
    ]
]);

$tablesResponse = @file_get_contents("$appUrl/backup-data", false, $context);

if ($tablesResponse === false) {
    echo "❌ Laravel server not running or route not added yet.\n";
    echo "\nTo start your Laravel server:\n";
    echo "   php artisan serve\n\n";
    echo "Then add the backup route and run this script again.\n";
    exit;
}

$tablesData = json_decode($tablesResponse, true);

if (isset($tablesData['error'])) {
    echo "❌ Error: " . $tablesData['error'] . "\n";
    echo "Make sure to add the backup route to your routes/web.php file.\n";
    exit;
}

if (!isset($tablesData['tables'])) {
    echo "❌ Unexpected response format.\n";
    exit;
}

$tables = $tablesData['tables'];
echo "✅ Found " . count($tables) . " tables:\n";
foreach ($tables as $table) {
    echo "   - $table\n";
}
echo "\n";

$backupSummary = [
    'timestamp' => $timestamp,
    'tables' => []
];

foreach ($tables as $table) {
    echo "Backing up $table... ";
    
    $tableResponse = @file_get_contents("$appUrl/backup-data/$table", false, $context);
    
    if ($tableResponse === false) {
        echo "❌ Failed\n";
        continue;
    }
    
    $tableData = json_decode($tableResponse, true);
    
    if (isset($tableData['error'])) {
        echo "❌ Error: " . $tableData['error'] . "\n";
        continue;
    }
    
    $rowCount = $tableData['rows'] ?? 0;
    echo "($rowCount rows) ";
    
    if ($rowCount > 0) {
        // Save as JSON
        $jsonFile = "$backupDir/{$table}_{$timestamp}.json";
        file_put_contents($jsonFile, json_encode($tableData['data'], JSON_PRETTY_PRINT));
        
        // Save as CSV
        $csvFile = "$backupDir/{$table}_{$timestamp}.csv";
        $fp = fopen($csvFile, 'w');
        
        $data = $tableData['data'];
        if (!empty($data)) {
            // Write headers
            fputcsv($fp, array_keys((array)$data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($fp, array_values((array)$row));
            }
        }
        fclose($fp);
        
        $backupSummary['tables'][] = [
            'table' => $table,
            'rows' => $rowCount,
            'files' => [
                basename($jsonFile),
                basename($csvFile)
            ]
        ];
        
        echo "✅\n";
    } else {
        echo "(empty)\n";
    }
}

// Save summary
$summaryFile = "$backupDir/backup_summary_{$timestamp}.json";
file_put_contents($summaryFile, json_encode($backupSummary, JSON_PRETTY_PRINT));

echo "\n🎉 Backup completed!\n";
echo "📁 Files saved in: $backupDir\n";
echo "📊 Summary: " . basename($summaryFile) . "\n";
echo "📋 Tables backed up: " . count($backupSummary['tables']) . "\n";

?>