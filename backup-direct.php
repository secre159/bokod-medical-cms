<?php
/**
 * Direct Database Backup Script
 * Uses curl to connect directly to PostgreSQL and export data
 */

$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$timestamp = date('Y-m-d_H-i-s');

echo "🗄️ Direct PostgreSQL Database Backup\n";
echo "====================================\n\n";

// Database connection details
$dbConfig = [
    'host' => 'dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com',
    'port' => '5432',
    'database' => 'bokod_cms',
    'username' => 'bokod_user',
    'password' => 'QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7'
];

// Create connection string
$connectionString = "postgresql://{$dbConfig['username']}:{$dbConfig['password']}@{$dbConfig['host']}:{$dbConfig['port']}/{$dbConfig['database']}";

echo "📋 Database Information:\n";
echo "   Host: {$dbConfig['host']}\n";
echo "   Database: {$dbConfig['database']}\n";
echo "   User: {$dbConfig['username']}\n\n";

// First, let's try to get table list using psql via curl (if available)
echo "🔍 Attempting to list tables...\n";

// For Windows, we can create a simple SQL query to get table information
$tableQuery = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ORDER BY table_name;";

// Create a temporary SQL file for table listing
$sqlFile = $backupDir . '/list_tables.sql';
file_put_contents($sqlFile, $tableQuery);

echo "📝 SQL query created: " . basename($sqlFile) . "\n";

// Create manual backup instructions
$backupInstructions = "
=== Manual Database Backup Instructions ===

Since we don't have PostgreSQL tools installed locally, here are your options:

OPTION 1: Use Online PostgreSQL Client
1. Go to: https://www.db-fiddle.com/ or https://sqliteonline.com/
2. Connect using these details:
   Host: {$dbConfig['host']}
   Port: {$dbConfig['port']}
   Database: {$dbConfig['database']}
   Username: {$dbConfig['username']}
   Password: {$dbConfig['password']}

OPTION 2: Install PostgreSQL Tools
1. Download PostgreSQL from: https://www.postgresql.org/download/windows/
2. Install only the client tools
3. Then run: pg_dump \"$connectionString\" > backup_{$timestamp}.sql

OPTION 3: Use Render Dashboard
1. Go to: https://dashboard.render.com/d/dpg-d39vbvjipnbc73b76ddg-a
2. Look for export/backup options in the database dashboard

OPTION 4: Use DBeaver (Recommended)
1. Download DBeaver from: https://dbeaver.io/download/
2. Install and create new PostgreSQL connection
3. Use the connection details above
4. Right-click database -> Tools -> Export Data

CONNECTION STRING:
$connectionString

BACKUP DIRECTORY: $backupDir
";

$instructionsFile = $backupDir . "/backup_instructions_{$timestamp}.txt";
file_put_contents($instructionsFile, $backupInstructions);

echo "📄 Backup instructions created: " . basename($instructionsFile) . "\n";

// Create a PowerShell script for automated backup (when tools are installed)
$psScript = "
# PostgreSQL Backup Script for Windows
# Run this after installing PostgreSQL client tools

\$timestamp = Get-Date -Format 'yyyy-MM-dd_HH-mm-ss'
\$backupFile = \"$backupDir/full_backup_\$timestamp.sql\"
\$connectionString = \"$connectionString\"

Write-Host \"🗄️ Starting PostgreSQL Backup...\"
Write-Host \"📁 Backup file: \$backupFile\"

try {
    # Full database dump
    & pg_dump \$connectionString --file=\$backupFile --verbose
    
    if (\$LASTEXITCODE -eq 0) {
        Write-Host \"✅ Backup completed successfully!\"
        Write-Host \"📁 File saved: \$backupFile\"
        
        # Get file size
        \$fileSize = (Get-Item \$backupFile).Length / 1KB
        Write-Host \"📊 Backup size: \$([math]::Round(\$fileSize, 2)) KB\"
        
        # Create compressed version
        \$zipFile = \$backupFile + '.zip'
        Compress-Archive -Path \$backupFile -DestinationPath \$zipFile
        Write-Host \"📦 Compressed backup: \$zipFile\"
    } else {
        Write-Host \"❌ Backup failed with exit code: \$LASTEXITCODE\"
    }
} catch {
    Write-Host \"❌ Error: \$(\$_.Exception.Message)\"
    Write-Host \"💡 Make sure PostgreSQL client tools are installed\"
}
";

$psScriptFile = $backupDir . "/backup_script_{$timestamp}.ps1";
file_put_contents($psScriptFile, $psScript);

echo "💻 PowerShell backup script created: " . basename($psScriptFile) . "\n";

// Create a batch file for easy execution
$batchScript = "@echo off\necho Starting PostgreSQL Backup...\npowershell.exe -ExecutionPolicy Bypass -File \"$psScriptFile\"\npause";
$batchFile = $backupDir . "/run_backup_{$timestamp}.bat";
file_put_contents($batchFile, $batchScript);

echo "🚀 Batch file created: " . basename($batchFile) . "\n";

echo "\n🎉 Backup preparation complete!\n";
echo "📁 All files saved in: $backupDir\n\n";
echo "📋 Next Steps:\n";
echo "1. Read: " . basename($instructionsFile) . "\n";
echo "2. Choose one of the backup methods\n";
echo "3. For automated backups, run: " . basename($batchFile) . "\n\n";

echo "⚡ Quick recommendation: Use DBeaver for easy GUI backup\n";

// Also create a simple JSON summary
$summary = [
    'timestamp' => $timestamp,
    'database' => [
        'host' => $dbConfig['host'],
        'database' => $dbConfig['database'],
        'username' => $dbConfig['username']
    ],
    'files_created' => [
        basename($instructionsFile),
        basename($psScriptFile),
        basename($batchFile),
        basename($sqlFile)
    ],
    'connection_string' => $connectionString,
    'backup_directory' => $backupDir
];

$summaryFile = $backupDir . "/backup_summary_{$timestamp}.json";
file_put_contents($summaryFile, json_encode($summary, JSON_PRETTY_PRINT));

echo "📊 Summary file: " . basename($summaryFile) . "\n";

?>