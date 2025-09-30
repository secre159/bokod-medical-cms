
# PostgreSQL Backup Script for Windows
# Run this after installing PostgreSQL client tools

$timestamp = Get-Date -Format 'yyyy-MM-dd_HH-mm-ss'
$backupFile = "C:\Users\Axl Chan\Desktop\XAMPP\htdocs\cms/backups/full_backup_$timestamp.sql"
$connectionString = "postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com:5432/bokod_cms"

Write-Host "🗄️ Starting PostgreSQL Backup..."
Write-Host "📁 Backup file: $backupFile"

try {
    # Full database dump
    & pg_dump $connectionString --file=$backupFile --verbose
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Backup completed successfully!"
        Write-Host "📁 File saved: $backupFile"
        
        # Get file size
        $fileSize = (Get-Item $backupFile).Length / 1KB
        Write-Host "📊 Backup size: $([math]::Round($fileSize, 2)) KB"
        
        # Create compressed version
        $zipFile = $backupFile + '.zip'
        Compress-Archive -Path $backupFile -DestinationPath $zipFile
        Write-Host "📦 Compressed backup: $zipFile"
    } else {
        Write-Host "❌ Backup failed with exit code: $LASTEXITCODE"
    }
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)"
    Write-Host "💡 Make sure PostgreSQL client tools are installed"
}
