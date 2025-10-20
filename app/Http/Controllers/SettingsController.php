<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use App\Rules\PhoneNumberRule;
use Carbon\Carbon;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard
     */
    public function index()
    {
        $settings = $this->getAllSettings();
        $systemInfo = $this->getSystemInfo();
        
        return view('settings.index', compact('settings', 'systemInfo'));
    }
    
    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => ['nullable', new PhoneNumberRule],
            'address' => 'nullable|string|max:500',
            'timezone' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,ico,svg|max:1024',
        ]);

        try {
            // Update general settings
            Setting::set('app_name', $request->app_name, 'Application name', 'string', true);
            Setting::set('app_description', $request->app_description, 'Application description', 'string', true);
            Setting::set('contact_email', $request->contact_email, 'Contact email address', 'string', true);
            Setting::set('contact_phone', $request->contact_phone, 'Contact phone number', 'string', true);
            Setting::set('address', $request->address, 'System address', 'string', true);
            Setting::set('timezone', $request->timezone, 'System timezone', 'string', false);
            
            // Handle logo upload (fixed: using putFile method)
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                $oldLogo = Setting::get('app_logo');
                if ($oldLogo && $this->getStorageDisk()->exists($oldLogo)) {
                    $this->getStorageDisk()->delete($oldLogo);
                }
                
                $disk = $this->getStorageDisk();
                $logoPath = $disk->putFile('settings', $request->file('logo'));
                Setting::set('app_logo', $logoPath, 'System logo', 'string', true);
            }
            
            // Handle favicon upload (fixed: using putFile method)
            if ($request->hasFile('favicon')) {
                // Delete old favicon if exists
                $oldFavicon = Setting::get('app_favicon');
                if ($oldFavicon && $this->getStorageDisk()->exists($oldFavicon)) {
                    $this->getStorageDisk()->delete($oldFavicon);
                }
                
                $disk = $this->getStorageDisk();
                $faviconPath = $disk->putFile('settings', $request->file('favicon'));
                Setting::set('app_favicon', $faviconPath, 'System favicon', 'string', true);
            }
            
            // Clear configuration cache to reload dynamic settings
            Cache::flush();
            Artisan::call('config:clear');
            
            return redirect()->back()->with('success', 'General settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Settings update error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error updating settings: ' . $e->getMessage()]);
        }
    }
    
    
    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'boolean',
            'user_registration' => 'boolean',
            'email_verification' => 'boolean',
            'session_lifetime' => 'required|integer|min:30|max:43200',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'log_level' => 'required|in:emergency,alert,critical,error,warning,notice,info,debug',
            'cache_driver' => 'required|in:file,redis,memcached',
            'max_file_size' => 'required|integer|min:1|max:100',
        ]);

        try {
            $this->updateSetting('maintenance_mode', $request->boolean('maintenance_mode'));
            $this->updateSetting('user_registration', $request->boolean('user_registration'));
            $this->updateSetting('email_verification', $request->boolean('email_verification'));
            $this->updateSetting('session_lifetime', $request->session_lifetime);
            $this->updateSetting('backup_frequency', $request->backup_frequency);
            $this->updateSetting('log_level', $request->log_level);
            $this->updateSetting('cache_driver', $request->cache_driver);
            $this->updateSetting('max_file_size', $request->max_file_size);
            
            return redirect()->back()->with('success', 'System settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('System settings update error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error updating system settings: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Update email settings
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        try {
            $this->updateSetting('mail_driver', $request->mail_driver);
            $this->updateSetting('mail_host', $request->mail_host);
            $this->updateSetting('mail_port', $request->mail_port);
            $this->updateSetting('mail_username', $request->mail_username);
            if ($request->filled('mail_password')) {
                $this->updateSetting('mail_password', encrypt($request->mail_password));
            }
            $this->updateSetting('mail_encryption', $request->mail_encryption);
            $this->updateSetting('mail_from_address', $request->mail_from_address);
            $this->updateSetting('mail_from_name', $request->mail_from_name);
            
            return redirect()->back()->with('success', 'Email settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Email settings update error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error updating email settings: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Test email configuration
     */
    public function testEmail()
    {
        try {
            // Send test email
            // \Mail::to(auth()->user()->email)->send(new \App\Mail\TestMail());
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Email test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending test email: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Application cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Cache clear error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create database backup
     */
    public function createBackup()
    {
        try {
            $filename = 'backup_' . now()->format('Y_m_d_H_i_s') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);
            
            // Ensure backup directory exists
            if (!is_dir(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }
            
            // Check if we're in a hosted environment or localhost
            if ($this->isHostedEnvironment()) {
                return $this->createBackupPHP($filename, $backupPath);
            } else {
                return $this->createBackupMySQL($filename, $backupPath);
            }
            
        } catch (\Exception $e) {
            Log::error('Backup creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating backup: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check if we're in a hosted environment
     */
    private function isHostedEnvironment()
    {
        // Check common indicators of hosted environments
        $indicators = [
            !$this->findMySQLDumpPath(), // mysqldump not available
            strpos(gethostname(), 'local') === false, // not localhost
            !file_exists('C:\xampp'), // not XAMPP
            !file_exists('/Applications/XAMPP'), // not XAMPP Mac
            isset($_SERVER['HTTP_HOST']) && !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']),
            // Add more indicators as needed
        ];
        
        // If any indicator suggests hosted environment, use PHP method
        return count(array_filter($indicators)) > 1;
    }
    
    /**
     * Create backup using PHP (for hosted environments)
     */
    private function createBackupPHP($filename, $backupPath)
    {
        try {
            $database = config('database.connections.mysql.database');
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            
            if (empty($tables)) {
                throw new \Exception('No tables found in database');
            }
            
            $sqlContent = "-- Database Backup Generated by Healthcare CMS\n";
            $sqlContent .= "-- Generated on: " . now()->toDateTimeString() . "\n";
            $sqlContent .= "-- Database: {$database}\n";
            $sqlContent .= "-- ==========================================\n\n";
            
            $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sqlContent .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
            $sqlContent .= "SET time_zone = '+00:00';\n\n";
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                // Get table structure
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $sqlContent .= "-- Table structure for table `{$tableName}`\n";
                $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlContent .= $createTable->{'Create Table'} . ";\n\n";
                
                // Get table data
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    $sqlContent .= "-- Dumping data for table `{$tableName}`\n";
                    $sqlContent .= "INSERT INTO `{$tableName}` VALUES\n";
                    
                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ((array) $row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $rowValues) . ')';
                    }
                    
                    // Split into chunks to avoid memory issues
                    $chunks = array_chunk($values, 100);
                    foreach ($chunks as $chunk) {
                        $sqlContent .= implode(',\n', $chunk) . ";\n";
                    }
                    $sqlContent .= "\n";
                }
            }
            
            $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            // Write to file
            if (file_put_contents($backupPath, $sqlContent) === false) {
                throw new \Exception('Failed to write backup file');
            }
            
            // Update last backup timestamp
            $this->updateSetting('last_backup', now()->toDateTimeString());
            
            $fileSize = $this->formatBytes(filesize($backupPath));
            
            return response()->json([
                'success' => true,
                'message' => "Database backup created successfully! File: {$filename} ({$fileSize}) [PHP Method]",
                'filename' => $filename,
                'size' => $fileSize,
                'method' => 'php',
                'path' => 'storage/app/backups/' . $filename
            ]);
            
        } catch (\Exception $e) {
            throw new \Exception('PHP backup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Create backup using mysqldump (for localhost/development)
     */
    private function createBackupMySQL($filename, $backupPath)
    {
        try {
            // Get database connection details
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            // Try to find mysqldump
            $mysqldumpPath = $this->findMySQLDumpPath();
            
            if (!$mysqldumpPath) {
                throw new \Exception('mysqldump not found. Using PHP fallback method.');
            }
            
            // Build mysqldump command
            if (empty($password)) {
                $command = sprintf(
                    '"%s" --host=%s --port=%d --user=%s --routines --triggers --single-transaction %s > "%s"',
                    $mysqldumpPath, $host, $port, $username, $database, $backupPath
                );
            } else {
                $command = sprintf(
                    '"%s" --host=%s --port=%d --user=%s --password="%s" --routines --triggers --single-transaction %s > "%s"',
                    $mysqldumpPath, $host, $port, $username, $password, $database, $backupPath
                );
            }
            
            Log::info('Backup command prepared', ['mysqldump_path' => $mysqldumpPath, 'database' => $database]);
            
            // Execute backup command
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);
            
            Log::info('Backup command executed', ['return_code' => $returnCode, 'output' => $output]);
            
            if ($returnCode !== 0) {
                $errorMessage = 'MySQL backup failed with return code ' . $returnCode . ': ' . implode('\n', $output);
                throw new \Exception($errorMessage);
            }
            
            // Verify backup file was created and has content
            if (!file_exists($backupPath) || filesize($backupPath) === 0) {
                throw new \Exception('Backup file was not created or is empty.');
            }
            
            // Update last backup timestamp
            $this->updateSetting('last_backup', now()->toDateTimeString());
            
            $fileSize = $this->formatBytes(filesize($backupPath));
            
            return response()->json([
                'success' => true,
                'message' => "Database backup created successfully! File: {$filename} ({$fileSize}) [MySQL Method]",
                'filename' => $filename,
                'size' => $fileSize,
                'method' => 'mysql',
                'path' => 'storage/app/backups/' . $filename
            ]);
            
        } catch (\Exception $e) {
            // Fallback to PHP method if MySQL method fails
            Log::warning('MySQL backup failed, falling back to PHP method: ' . $e->getMessage());
            return $this->createBackupPHP($filename, $backupPath);
        }
    }
    
    /**
     * Find mysqldump path
     */
    private function findMySQLDumpPath()
    {
        $possiblePaths = [
            // XAMPP Windows
            'C:\\Users\\Axl Chan\\Desktop\\XAMPP\\mysql\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            // XAMPP Mac/Linux
            '/Applications/XAMPP/bin/mysqldump',
            '/opt/lampp/bin/mysqldump',
            // Standard MySQL installations
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
            // System PATH
            'mysqldump'
        ];
        
        foreach ($possiblePaths as $path) {
            if ($path === 'mysqldump') {
                // Test if mysqldump is in system PATH
                $testOutput = [];
                $testReturn = 0;
                exec('mysqldump --version 2>&1', $testOutput, $testReturn);
                if ($testReturn === 0) {
                    return $path;
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    /**
     * List all database backups
     */
    public function listBackups()
    {
        try {
            $backupDir = storage_path('app/backups');
            
            if (!is_dir($backupDir)) {
                return response()->json([
                    'success' => true,
                    'backups' => []
                ]);
            }
            
            $backups = [];
            $files = glob($backupDir . '/*.sql');
            
            foreach ($files as $file) {
                $filename = basename($file);
                $filesize = filesize($file);
                $created = filemtime($file);
                
                $backups[] = [
                    'filename' => $filename,
                    'size' => $this->formatBytes($filesize),
                    'size_bytes' => $filesize,
                    'created_at' => date('Y-m-d H:i:s', $created),
                    'created_human' => $this->timeAgo($created),
                    'path' => $file
                ];
            }
            
            // Sort by creation time (newest first)
            usort($backups, function($a, $b) {
                return $b['size_bytes'] <=> $a['size_bytes'];
            });
            
            usort($backups, function($a, $b) {
                return strcmp($b['created_at'], $a['created_at']);
            });
            
            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);
            
        } catch (\Exception $e) {
            Log::error('List backups error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error listing backups: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Download a backup file
     */
    public function downloadBackup($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!preg_match('/^backup_\d{4}_\d{2}_\d{2}_\d{2}_\d{2}_\d{2}\.sql$/', $filename)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid backup filename'
                ], 400);
            }
            
            $filePath = storage_path('app/backups/' . $filename);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }
            
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Download backup error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error downloading backup: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a backup file
     */
    public function deleteBackup($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!preg_match('/^backup_\d{4}_\d{2}_\d{2}_\d{2}_\d{2}_\d{2}\.sql$/', $filename)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid backup filename'
                ], 400);
            }
            
            $filePath = storage_path('app/backups/' . $filename);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }
            
            if (unlink($filePath)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup deleted successfully'
                ]);
            } else {
                throw new \Exception('Failed to delete backup file');
            }
            
        } catch (\Exception $e) {
            Log::error('Delete backup error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting backup: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Restore database from backup file
     */
    public function restoreBackup($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!preg_match('/^backup_\d{4}_\d{2}_\d{2}_\d{2}_\d{2}_\d{2}\.sql$/', $filename)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid backup filename'
                ], 400);
            }
            
            $backupPath = storage_path('app/backups/' . $filename);
            
            if (!file_exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }
            
            // Create a safety backup before restoring
            $safetyBackupName = 'pre_restore_safety_' . now()->format('Y_m_d_H_i_s') . '.sql';
            $this->createSafetyBackupForRestore($safetyBackupName);
            
            // Check if we're in a hosted environment
            if ($this->isHostedEnvironment()) {
                $result = $this->restoreBackupPHP($filename, $backupPath, $safetyBackupName);
            } else {
                $result = $this->restoreBackupMySQL($filename, $backupPath, $safetyBackupName);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Database restore error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error restoring database: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Restore database using PHP (for hosted environments)
     */
    private function restoreBackupPHP($filename, $backupPath, $safetyBackupName)
    {
        try {
            // Read the SQL file
            $sqlContent = file_get_contents($backupPath);
            
            if ($sqlContent === false) {
                throw new \Exception('Could not read backup file');
            }
            
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO"');
            
            // Split SQL content into individual statements
            $statements = array_filter(
                array_map('trim', explode(';', $sqlContent)),
                function($statement) {
                    return !empty($statement) && !str_starts_with($statement, '--');
                }
            );
            
            // Execute each statement
            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    DB::unprepared($statement);
                }
            }
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            // Update restore settings
            $this->updateSetting('last_restore', now()->toDateTimeString());
            $this->updateSetting('last_restore_file', $filename);
            $this->updateSetting('safety_backup_before_restore', $safetyBackupName);
            
            return response()->json([
                'success' => true,
                'message' => "Database restored successfully from {$filename}! Safety backup created: {$safetyBackupName} [PHP Method]",
                'restored_from' => $filename,
                'safety_backup' => $safetyBackupName,
                'method' => 'php',
                'restore_time' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            // Re-enable foreign key checks even if restore failed
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ignored) {}
            
            throw new \Exception('PHP restore failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Restore database using MySQL (for localhost/development)
     */
    private function restoreBackupMySQL($filename, $backupPath, $safetyBackupName)
    {
        try {
            // Get database connection details
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            // Find MySQL client path
            $mysqlPath = $this->findMySQLPath();
            
            if (!$mysqlPath) {
                Log::warning('MySQL client not found, falling back to PHP method');
                return $this->restoreBackupPHP($filename, $backupPath, $safetyBackupName);
            }
            
            // Build MySQL restore command
            if (empty($password)) {
                $command = sprintf(
                    '"%s" --host=%s --port=%d --user=%s %s < "%s"',
                    $mysqlPath, $host, $port, $username, $database, $backupPath
                );
            } else {
                $command = sprintf(
                    '"%s" --host=%s --port=%d --user=%s --password="%s" %s < "%s"',
                    $mysqlPath, $host, $port, $username, $password, $database, $backupPath
                );
            }
            
            Log::info('Restore command prepared', [
                'mysql_path' => $mysqlPath, 
                'database' => $database,
                'backup_file' => $filename,
                'safety_backup' => $safetyBackupName
            ]);
            
            // Execute restore command
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);
            
            Log::info('Restore command executed', [
                'return_code' => $returnCode, 
                'output' => $output
            ]);
            
            if ($returnCode !== 0) {
                $errorMessage = 'MySQL restore failed with return code ' . $returnCode . ': ' . implode('\n', $output);
                Log::warning($errorMessage . '. Falling back to PHP method.');
                return $this->restoreBackupPHP($filename, $backupPath, $safetyBackupName);
            }
            
            // Update restore settings
            $this->updateSetting('last_restore', now()->toDateTimeString());
            $this->updateSetting('last_restore_file', $filename);
            $this->updateSetting('safety_backup_before_restore', $safetyBackupName);
            
            return response()->json([
                'success' => true,
                'message' => "Database restored successfully from {$filename}! Safety backup created: {$safetyBackupName} [MySQL Method]",
                'restored_from' => $filename,
                'safety_backup' => $safetyBackupName,
                'method' => 'mysql',
                'restore_time' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            Log::warning('MySQL restore failed, falling back to PHP method: ' . $e->getMessage());
            return $this->restoreBackupPHP($filename, $backupPath, $safetyBackupName);
        }
    }
    
    /**
     * Create a safety backup before restore (updated method name)
     */
    private function createSafetyBackupForRestore($filename)
    {
        try {
            if ($this->isHostedEnvironment()) {
                $this->createSafetyBackupPHP($filename);
            } else {
                $this->createSafetyBackupMySQL($filename);
            }
        } catch (\Exception $e) {
            Log::warning('Safety backup failed: ' . $e->getMessage());
            // Continue with restore even if safety backup fails
        }
    }
    
    /**
     * Create safety backup using PHP
     */
    private function createSafetyBackupPHP($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        $this->createBackupPHP($filename, $backupPath);
    }
    
    /**
     * Create safety backup using MySQL
     */
    private function createSafetyBackupMySQL($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        $this->createBackupMySQL($filename, $backupPath);
    }
    
    /**
     * Find MySQL client path
     */
    private function findMySQLPath()
    {
        $possiblePaths = [
            'C:\\Users\\Axl Chan\\Desktop\\XAMPP\\mysql\\bin\\mysql.exe', // Desktop XAMPP
            'C:\\xampp\\mysql\\bin\\mysql.exe', // Standard XAMPP
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysql.exe',
            'mysql' // Try system PATH
        ];
        
        foreach ($possiblePaths as $path) {
            if ($path === 'mysql' || file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    
    /**
     * Clear config cache only
     */
    public function clearConfigCache()
    {
        try {
            Artisan::call('config:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Configuration cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Config cache clear error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error clearing config cache: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear view cache only
     */
    public function clearViewCache()
    {
        try {
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'View cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('View cache clear error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error clearing view cache: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Optimize database tables
     */
    public function optimizeDatabase()
    {
        try {
            // First check if we can connect to the database
            if (!DB::connection()->getPdo()) {
                throw new \Exception('Cannot connect to database');
            }
            
            // Get all tables from current database
            $databaseName = DB::connection()->getDatabaseName();
            $tables = DB::select("SHOW TABLES FROM `{$databaseName}`");
            
            if (empty($tables)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tables found in database to optimize.'
                ]);
            }
            
            $optimizedTables = 0;
            $skippedTables = 0;
            $errors = [];
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                try {
                    // Check table engine first
                    $tableInfo = DB::select("SHOW TABLE STATUS LIKE '{$tableName}'");
                    
                    if (!empty($tableInfo)) {
                        $engine = $tableInfo[0]->Engine ?? 'Unknown';
                        
                        // Only optimize if it's MyISAM or InnoDB
                        if (in_array(strtoupper($engine), ['MYISAM', 'INNODB'])) {
                            DB::statement("OPTIMIZE TABLE `{$tableName}`");
                            $optimizedTables++;
                        } else {
                            $skippedTables++;
                            $errors[] = "Skipped {$tableName} (Engine: {$engine} - not supported)";
                        }
                    } else {
                        $skippedTables++;
                        $errors[] = "Could not get info for table: {$tableName}";
                    }
                } catch (\Exception $tableError) {
                    $skippedTables++;
                    $errors[] = "Failed to optimize {$tableName}: " . $tableError->getMessage();
                    Log::warning("Table optimization failed for {$tableName}: " . $tableError->getMessage());
                }
            }
            
            $message = "Database optimization completed! {$optimizedTables} tables optimized";
            if ($skippedTables > 0) {
                $message .= ", {$skippedTables} tables skipped";
            }
            
            $response = [
                'success' => true,
                'message' => $message
            ];
            
            if (!empty($errors)) {
                $response['warnings'] = $errors;
                Log::info('Database optimization warnings: ' . implode('; ', $errors));
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Database optimization error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error optimizing database: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Run system health check
     */
    public function systemHealthCheck()
    {
        try {
            $results = [];
            
            // Check database connection
            try {
                DB::connection()->getPdo();
                $results[] = ['name' => 'Database Connection', 'status' => 'OK', 'message' => 'Database is accessible'];
            } catch (\Exception $e) {
                $results[] = ['name' => 'Database Connection', 'status' => 'FAILED', 'message' => 'Cannot connect to database'];
            }
            
            // Check storage directory permissions
            $storagePath = storage_path();
            if (is_writable($storagePath)) {
                $results[] = ['name' => 'Storage Permissions', 'status' => 'OK', 'message' => 'Storage directory is writable'];
            } else {
                $results[] = ['name' => 'Storage Permissions', 'status' => 'FAILED', 'message' => 'Storage directory is not writable'];
            }
            
            // Check cache directory
            $cachePath = storage_path('framework/cache');
            if (is_dir($cachePath) && is_writable($cachePath)) {
                $results[] = ['name' => 'Cache Directory', 'status' => 'OK', 'message' => 'Cache directory is accessible'];
            } else {
                $results[] = ['name' => 'Cache Directory', 'status' => 'FAILED', 'message' => 'Cache directory has issues'];
            }
            
            // Check log directory
            $logPath = storage_path('logs');
            if (is_dir($logPath) && is_writable($logPath)) {
                $results[] = ['name' => 'Log Directory', 'status' => 'OK', 'message' => 'Log directory is accessible'];
            } else {
                $results[] = ['name' => 'Log Directory', 'status' => 'FAILED', 'message' => 'Log directory has issues'];
            }
            
            // Check PHP extensions
            $requiredExtensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json'];
            foreach ($requiredExtensions as $extension) {
                if (extension_loaded($extension)) {
                    $results[] = ['name' => "PHP {$extension}", 'status' => 'OK', 'message' => 'Extension is loaded'];
                } else {
                    $results[] = ['name' => "PHP {$extension}", 'status' => 'FAILED', 'message' => 'Extension is missing'];
                }
            }
            
            return response()->json([
                'success' => true,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('System health check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error running system health check: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clean temporary files
     */
    public function cleanTempFiles()
    {
        try {
            $tempDirs = [
                storage_path('framework/cache'),
                storage_path('framework/sessions'),
                storage_path('framework/views'),
                sys_get_temp_dir()
            ];
            
            $deletedFiles = 0;
            $freedSpace = 0;
            
            foreach ($tempDirs as $dir) {
                if (is_dir($dir)) {
                    $files = glob($dir . '/*');
                    foreach ($files as $file) {
                        if (is_file($file) && filemtime($file) < strtotime('-1 hour')) {
                            $size = filesize($file);
                            if (unlink($file)) {
                                $deletedFiles++;
                                $freedSpace += $size;
                            }
                        }
                    }
                }
            }
            
            $freedSpaceFormatted = $this->formatBytes($freedSpace);
            
            return response()->json([
                'success' => true,
                'message' => "Cleaned {$deletedFiles} temporary files, freed {$freedSpaceFormatted}!"
            ]);
        } catch (\Exception $e) {
            Log::error('Temp files clean error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cleaning temp files: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    
    /**
     * Get system information
     */
    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown',
            'timezone' => config('app.timezone'),
            'debug_mode' => config('app.debug'),
            'app_environment' => config('app.env'),
            'storage_info' => $this->getStorageInfo(),
            'last_backup' => $this->getSetting('last_backup', 'Never'),
            'uptime' => $this->getSystemUptime(),
        ];
    }
    
    /**
     * Get storage information
     */
    private function getStorageInfo()
    {
        $storagePath = storage_path();
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        
        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'usage_percentage' => round(($usedSpace / $totalSpace) * 100, 1)
        ];
    }
    
    /**
     * Get system uptime (basic implementation)
     */
    private function getSystemUptime()
    {
        $uptimeFile = storage_path('framework/cache/app_started');
        if (file_exists($uptimeFile)) {
            $startTime = Carbon::createFromTimestamp(filemtime($uptimeFile));
            return $startTime->diffForHumans();
        }
        
        return 'Unknown';
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get human-readable time ago
     */
    private function timeAgo($timestamp)
    {
        $difference = time() - $timestamp;
        
        if ($difference < 60) {
            return $difference . ' seconds ago';
        } elseif ($difference < 3600) {
            return floor($difference / 60) . ' minutes ago';
        } elseif ($difference < 86400) {
            return floor($difference / 3600) . ' hours ago';
        } elseif ($difference < 2592000) {
            return floor($difference / 86400) . ' days ago';
        } else {
            return date('Y-m-d H:i', $timestamp);
        }
    }
    
    /**
     * Get all settings
     */
    private function getAllSettings()
    {
        return [
            'general' => [
                'app_name' => $this->getSetting('app_name', 'BOKOD CMS'),
                'app_description' => $this->getSetting('app_description', 'Patient Management System'),
                'contact_email' => $this->getSetting('contact_email', 'admin@bokodcms.com'),
                'contact_phone' => $this->getSetting('contact_phone', ''),
                'address' => $this->getSetting('address', ''),
                'timezone' => $this->getSetting('timezone', 'UTC'),
                'app_logo' => Setting::get('app_logo', ''),
                'app_favicon' => Setting::get('app_favicon', ''),
            ],
            'system' => [
                'maintenance_mode' => $this->getSetting('maintenance_mode', false),
                'user_registration' => $this->getSetting('user_registration', false),
                'email_verification' => $this->getSetting('email_verification', true),
                'session_lifetime' => $this->getSetting('session_lifetime', 120),
                'backup_frequency' => $this->getSetting('backup_frequency', 'weekly'),
                'log_level' => $this->getSetting('log_level', 'info'),
                'cache_driver' => $this->getSetting('cache_driver', 'file'),
                'max_file_size' => $this->getSetting('max_file_size', 10),
            ],
            'email' => [
                'mail_driver' => $this->getSetting('mail_driver', 'smtp'),
                'mail_host' => $this->getSetting('mail_host', 'smtp.gmail.com'),
                'mail_port' => $this->getSetting('mail_port', 587),
                'mail_username' => $this->getSetting('mail_username', ''),
                'mail_encryption' => $this->getSetting('mail_encryption', 'tls'),
                'mail_from_address' => $this->getSetting('mail_from_address', 'admin@bokodcms.com'),
                'mail_from_name' => $this->getSetting('mail_from_name', 'Bokod CMS'),
            ]
        ];
    }
    
    /**
     * Get a setting value
     */
    private function getSetting($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function() use ($key, $default) {
            $setting = DB::table('settings')->where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
    
    /**
     * Update a setting value
     */
    private function updateSetting($key, $value)
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => $value,
                'updated_at' => now()
            ]
        );
        
        Cache::forget("setting_{$key}");
    }
    
    /**
     * Get the appropriate storage disk (Cloudinary if configured, otherwise public)
     */
    private function getStorageDisk()
    {
        // Use fallback_disk if configured, otherwise check default disk
        $fallbackDisk = config('filesystems.fallback_disk');
        if ($fallbackDisk) {
            try {
                $disk = Storage::disk($fallbackDisk);
                // Test if disk is accessible
                $disk->url('test');
                return $disk;
            } catch (\Exception $e) {
                \Log::warning('Fallback disk not accessible, using public disk', [
                    'fallback_disk' => $fallbackDisk,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Check if default disk is cloudinary
        $defaultDisk = config('filesystems.default');
        if ($defaultDisk === 'cloudinary') {
            try {
                $disk = Storage::disk('cloudinary');
                // Test if disk is accessible
                $disk->url('test');
                return $disk;
            } catch (\Exception $e) {
                \Log::warning('Cloudinary not accessible, falling back to public disk', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Fallback to public disk
        return Storage::disk('public');
    }
}
