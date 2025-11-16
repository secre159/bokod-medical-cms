# Archive Old Migrations Script
# This script moves all old migration files to a backup folder

$migrationsPath = "database\migrations"
$archivePath = "database\migrations_archive_$(Get-Date -Format 'yyyy-MM-dd_HHmmss')"

# Create archive directory
New-Item -ItemType Directory -Path $archivePath -Force | Out-Null

# Keep only the new comprehensive migration and Laravel default migrations
$filesToKeep = @(
    "2025_11_16_000000_create_comprehensive_database_schema.php",
    "0001_01_01_000000_create_users_table.php",
    "0001_01_01_000001_create_cache_table.php",
    "0001_01_01_000002_create_jobs_table.php"
)

# Get all migration files
$migrationFiles = Get-ChildItem -Path $migrationsPath -Filter "*.php"

$movedCount = 0
$keptCount = 0

foreach ($file in $migrationFiles) {
    if ($filesToKeep -contains $file.Name) {
        Write-Host "Keeping: $($file.Name)" -ForegroundColor Green
        $keptCount++
    } else {
        Move-Item -Path $file.FullName -Destination $archivePath
        Write-Host "Archived: $($file.Name)" -ForegroundColor Yellow
        $movedCount++
    }
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Archive Complete!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Kept: $keptCount files" -ForegroundColor Green
Write-Host "Archived: $movedCount files" -ForegroundColor Yellow
Write-Host "Archive location: $archivePath" -ForegroundColor Blue
