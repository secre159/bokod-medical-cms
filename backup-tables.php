<?php
/**
 * Simple Database Table Backup Script
 * Exports PostgreSQL tables to CSV and JSON format
 */

// Database configuration - UPDATE THESE VALUES
$dbConfig = [
    'host' => 'dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com',
    'port' => '5432',
    'database' => 'bokod_cms',
    'username' => 'bokod_user',
    'password' => 'QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7'
];

$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

try {
    // Connect to PostgreSQL
    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully!\n";
    
    // Get all tables
    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_type = 'BASE TABLE'
        ORDER BY table_name
    ");
    
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $timestamp = date('Y-m-d_H-i-s');
    
    echo "Found " . count($tables) . " tables to backup:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    echo "\n";
    
    $backupSummary = [
        'timestamp' => $timestamp,
        'tables' => []
    ];
    
    foreach ($tables as $table) {
        echo "Backing up table: $table... ";
        
        // Get table data
        $stmt = $pdo->query("SELECT * FROM \"$table\"");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $rowCount = count($data);
        echo "($rowCount rows)";
        
        if ($rowCount > 0) {
            // Export as JSON
            $jsonFile = "$backupDir/{$table}_{$timestamp}.json";
            file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
            
            // Export as CSV
            $csvFile = "$backupDir/{$table}_{$timestamp}.csv";
            $fp = fopen($csvFile, 'w');
            
            // Write header
            fputcsv($fp, array_keys($data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($fp, $row);
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
            
            echo " ✓\n";
        } else {
            echo " (empty table)\n";
        }
    }
    
    // Create backup summary
    $summaryFile = "$backupDir/backup_summary_{$timestamp}.json";
    file_put_contents($summaryFile, json_encode($backupSummary, JSON_PRETTY_PRINT));
    
    echo "\n=== Backup completed successfully! ===\n";
    echo "Backup directory: $backupDir\n";
    echo "Summary file: " . basename($summaryFile) . "\n";
    echo "Total tables backed up: " . count($backupSummary['tables']) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nTo fix this:\n";
    echo "1. Get your database connection details from Render dashboard\n";
    echo "2. Update the \$dbConfig array at the top of this script\n";
    echo "3. Make sure your IP is allowed in the database settings\n";
}
?>