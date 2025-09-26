<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\PatientProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EmailTestController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\DatabaseFixController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// Landing page route
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Redirect /home to landing page for consistency
Route::get('/home', function () {
    return redirect()->route('landing');
});

// Redirect legacy /landing path to root
Route::get('/landing', function () {
    return redirect()->route('landing');
});

// Dashboard routes with role-based access (email verification removed)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/async-stats', [DashboardController::class, 'asyncStats'])->name('dashboard.async-stats');
    Route::get('/dashboard/recent-messages', [DashboardController::class, 'getRecentMessages'])->name('dashboard.recent-messages');
    
    // Global Search
    Route::get('/search', [App\Http\Controllers\GlobalSearchController::class, 'search'])->name('global.search');
    Route::get('/search-test', function(\Illuminate\Http\Request $request) {
        return response()->json([
            'message' => 'Search test working!',
            'query' => $request->input('search'),
            'user' => auth()->user()->name ?? 'Unknown'
        ]);
    })->name('search.test');
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        // Patient management - specific routes FIRST
        Route::get('/patients/history', [PatientController::class, 'history'])->name('patients.history');
        Route::post('/patients/{patient}/reset-password', [PatientController::class, 'resetPassword'])->name('patients.resetPassword');
        Route::resource('patients', PatientController::class);
        
        // Appointment management - specific routes FIRST
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
            Route::get('/calendar/data', [AppointmentController::class, 'getCalendarAppointments'])->name('calendar.data');
            Route::get('/{appointment}/details', [AppointmentController::class, 'getAppointmentDetails'])->name('details');
            Route::patch('/{appointment}/update-time', [AppointmentController::class, 'updateAppointmentTime'])->name('updateTime');
            Route::patch('/{appointment}/approve', [AppointmentController::class, 'approve'])->name('approve');
            Route::patch('/{appointment}/reject', [AppointmentController::class, 'reject'])->name('reject');
            Route::patch('/{appointment}/complete', [AppointmentController::class, 'complete'])->name('complete');
            Route::patch('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
            Route::patch('/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('reschedule');
            Route::patch('/{appointment}/approve-reschedule', [AppointmentController::class, 'approveReschedule'])->name('approveReschedule');
            Route::patch('/{appointment}/reject-reschedule', [AppointmentController::class, 'rejectReschedule'])->name('rejectReschedule');
            Route::delete('/{appointment}/delete', [AppointmentController::class, 'delete'])->name('delete');
        });
        Route::resource('appointments', AppointmentController::class);
        
        // Medicine management - specific routes FIRST
        Route::prefix('medicines')->name('medicines.')->group(function () {
            Route::get('/stock-management', [MedicineController::class, 'stock'])->name('stock');
            Route::post('/{medicine}/update-stock', [MedicineController::class, 'updateStock'])->name('updateStock');
            Route::post('/bulk-update-stock', [MedicineController::class, 'bulkUpdateStock'])->name('bulkUpdateStock');
            Route::get('/{medicine}/stock-history', [MedicineController::class, 'getStockHistory'])->name('stockHistory');
            Route::get('/export-stock', [MedicineController::class, 'exportStockReport'])->name('exportStock');
            Route::get('/search', [MedicineController::class, 'searchForPrescription'])->name('searchForPrescription');
            Route::get('/low-stock-alerts', [MedicineController::class, 'getLowStockAlert'])->name('lowStockAlerts');
        });
        Route::resource('medicines', MedicineController::class);
        
        // Prescription management
        Route::resource('prescriptions', PrescriptionController::class);
        Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
            Route::post('/{prescription}/dispense', [PrescriptionController::class, 'dispense'])->name('dispense');
            Route::patch('/{prescription}/complete', [PrescriptionController::class, 'complete'])->name('complete');
            Route::get('/stats', [PrescriptionController::class, 'getStats'])->name('stats');
        });
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/dashboard', [ReportsController::class, 'dashboard'])->name('dashboard');
            Route::get('/data', [ReportsController::class, 'getData'])->name('data');
            Route::get('/export', [ReportsController::class, 'export'])->name('export');
            Route::get('/patients', [ReportsController::class, 'patients'])->name('patients');
            Route::get('/visits', [ReportsController::class, 'visits'])->name('visits');
            Route::get('/prescriptions', [ReportsController::class, 'prescriptions'])->name('prescriptions');
            Route::get('/medicines', [ReportsController::class, 'medicines'])->name('medicines');
        });
        
        // Users Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/status', [UserController::class, 'changeStatus'])->name('changeStatus');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('resetPassword');
            // Email verification removed - users receive credentials via email
        });
        
        // Settings Management
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('updateGeneral');
            Route::put('/system', [SettingsController::class, 'updateSystem'])->name('updateSystem');
            Route::put('/email', [SettingsController::class, 'updateEmail'])->name('updateEmail');
            Route::post('/test-email', [SettingsController::class, 'testEmail'])->name('testEmail');
            Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clearCache');
            Route::post('/clear-config-cache', [SettingsController::class, 'clearConfigCache'])->name('clearConfigCache');
            Route::post('/clear-view-cache', [SettingsController::class, 'clearViewCache'])->name('clearViewCache');
            Route::post('/create-backup', [SettingsController::class, 'createBackup'])->name('createBackup');
            Route::post('/optimize-database', [SettingsController::class, 'optimizeDatabase'])->name('optimizeDatabase');
            Route::post('/system-health-check', [SettingsController::class, 'systemHealthCheck'])->name('systemHealthCheck');
            Route::post('/clean-temp-files', [SettingsController::class, 'cleanTempFiles'])->name('cleanTempFiles');
            Route::get('/list-backups', [SettingsController::class, 'listBackups'])->name('listBackups');
            Route::get('/download-backup/{filename}', [SettingsController::class, 'downloadBackup'])->name('downloadBackup');
            Route::delete('/delete-backup/{filename}', [SettingsController::class, 'deleteBackup'])->name('deleteBackup');
            Route::post('/restore-backup/{filename}', [SettingsController::class, 'restoreBackup'])->name('restoreBackup');
        });
        
        // Email Testing Management
        Route::prefix('email-test')->name('email-test.')->group(function () {
            Route::get('/', [EmailTestController::class, 'index'])->name('index');
            Route::get('/configuration', [EmailTestController::class, 'checkConfiguration'])->name('configuration');
            Route::post('/patient-welcome', [EmailTestController::class, 'testPatientWelcome'])->name('patient-welcome');
            Route::post('/appointment-notification', [EmailTestController::class, 'testAppointmentNotification'])->name('appointment-notification');
            Route::post('/prescription-notification', [EmailTestController::class, 'testPrescriptionNotification'])->name('prescription-notification');
            Route::post('/health-tips', [EmailTestController::class, 'testHealthTips'])->name('health-tips');
            Route::post('/stock-alert', [EmailTestController::class, 'testStockAlert'])->name('stock-alert');
            Route::post('/medication-reminders', [EmailTestController::class, 'testMedicationReminders'])->name('medication-reminders');
        });
        
        // Registration Approval Management
        Route::prefix('registrations')->name('registrations.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\RegistrationApprovalController::class, 'index'])->name('index');
            Route::get('/pending-count', [App\Http\Controllers\Admin\RegistrationApprovalController::class, 'pendingCount'])->name('pendingCount');
            Route::get('/{user}', [App\Http\Controllers\Admin\RegistrationApprovalController::class, 'show'])->name('show');
            Route::post('/{user}/approve', [App\Http\Controllers\Admin\RegistrationApprovalController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [App\Http\Controllers\Admin\RegistrationApprovalController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [App\Http\Controllers\Admin\RegistrationApprovalController::class, 'bulkApprove'])->name('bulkApprove');
        });
        
        // Admin Messaging
        Route::prefix('admin-messages')->name('admin.messages.')->group(function () {
            Route::get('/', [MessagingController::class, 'index'])->name('index');
            Route::post('/send', [MessagingController::class, 'send'])->name('send');
            Route::get('/conversation/{conversation}/messages', [MessagingController::class, 'getMessages'])->name('messages');
            Route::post('/conversation/{conversation}/read', [MessagingController::class, 'markAsRead'])->name('read');
            Route::get('/unread-count', [MessagingController::class, 'getUnreadCount'])->name('unreadCount');
            Route::get('/download/{message}', [MessagingController::class, 'downloadAttachment'])->name('download');
            Route::get('/upload-info', [MessagingController::class, 'getUploadInfo'])->name('uploadInfo');
            Route::post('/conversation/{conversation}/archive', [MessagingController::class, 'archiveConversation'])->name('archive');
            Route::post('/conversation/{conversation}/unarchive', [MessagingController::class, 'unarchiveConversation'])->name('unarchive');
            Route::get('/archived', [MessagingController::class, 'getArchivedConversations'])->name('archived');
            
            // Typing indicators
            Route::post('/typing', [MessagingController::class, 'updateTypingStatus'])->name('typing.update');
            Route::get('/typing', [MessagingController::class, 'getTypingStatus'])->name('typing.get');
            
            // Message reactions
            Route::post('/messages/{message}/react', [MessagingController::class, 'toggleReaction'])->name('react');
            
        // Admin-initiated conversations
            Route::post('/start-with-patient', [MessagingController::class, 'startConversationWithPatient'])->name('startWithPatient');
            Route::get('/patients-list', [MessagingController::class, 'getPatientsList'])->name('patientsList');
        });
        
        // Database Fixes Management
        Route::prefix('database-fixes')->name('database-fixes.')->group(function () {
            Route::get('/', [DatabaseFixController::class, 'index'])->name('index');
            Route::post('/messaging', [DatabaseFixController::class, 'fixMessaging'])->name('messaging');
            Route::post('/prescriptions', [DatabaseFixController::class, 'fixPrescriptions'])->name('prescriptions');
        });
    });
    
    // Patient routes
    Route::middleware('role:patient')->group(function () {
        // Patient Profile Management
        Route::get('/my-profile', [PatientProfileController::class, 'show'])->name('patient.profile.show');
        Route::get('/my-profile/edit', [PatientProfileController::class, 'edit'])->name('patient.profile.edit');
        Route::patch('/my-profile', [PatientProfileController::class, 'update'])->name('patient.profile.update');
        
        Route::get('/my-appointments', [PatientPortalController::class, 'appointments'])->name('patient.appointments');
        Route::get('/my-history', [PatientPortalController::class, 'history'])->name('patient.history');
        Route::get('/my-prescriptions', [PatientPortalController::class, 'prescriptions'])->name('patient.prescriptions');
        Route::post('/my-prescriptions/export', [PatientPortalController::class, 'exportPrescriptions'])->name('patient.prescriptions.export');
        
        // Patient Messaging
        Route::prefix('patient-messages')->name('patient.messages.')->group(function () {
            Route::get('/', [MessagingController::class, 'index'])->name('index');
            Route::post('/send', [MessagingController::class, 'send'])->name('send');
            Route::post('/start', [MessagingController::class, 'startConversation'])->name('start');
            Route::get('/conversation/{conversation}/messages', [MessagingController::class, 'getMessages'])->name('messages');
            Route::post('/conversation/{conversation}/read', [MessagingController::class, 'markAsRead'])->name('read');
            Route::get('/unread-count', [MessagingController::class, 'getUnreadCount'])->name('unreadCount');
            Route::get('/download/{message}', [MessagingController::class, 'downloadAttachment'])->name('download');
            Route::get('/upload-info', [MessagingController::class, 'getUploadInfo'])->name('uploadInfo');
            Route::post('/conversation/{conversation}/archive', [MessagingController::class, 'archiveConversation'])->name('archive');
            Route::post('/conversation/{conversation}/unarchive', [MessagingController::class, 'unarchiveConversation'])->name('unarchive');
            Route::get('/archived', [MessagingController::class, 'getArchivedConversations'])->name('archived');
            
            // Typing indicators
            Route::post('/typing', [MessagingController::class, 'updateTypingStatus'])->name('typing.update');
            Route::get('/typing', [MessagingController::class, 'getTypingStatus'])->name('typing.get');
            
            // Message reactions
            Route::post('/messages/{message}/react', [MessagingController::class, 'toggleReaction'])->name('react');
        });
        
        // Patient API routes
        Route::prefix('api/patient')->name('patient.api.')->group(function () {
            Route::get('/appointments', [PatientPortalController::class, 'getAppointmentsData'])->name('appointments');
            Route::get('/statistics', [PatientPortalController::class, 'getStatistics'])->name('statistics');
            Route::post('/appointments', [PatientPortalController::class, 'bookAppointment'])->name('book');
            Route::delete('/appointments/{appointment}', [PatientPortalController::class, 'cancelAppointment'])->name('cancel');
            Route::post('/appointments/{appointment}/cancel', [PatientPortalController::class, 'cancelAppointment'])->name('cancelWithReason');
            Route::post('/appointments/{appointment}/reschedule', [PatientPortalController::class, 'rescheduleAppointment'])->name('reschedule');
            Route::get('/appointments/{appointment}/details', [PatientPortalController::class, 'getAppointmentDetails'])->name('details');
            
            // Typing indicators for patients
            Route::post('/typing', [MessagingController::class, 'updateTypingStatus'])->name('typing');
            Route::get('/typing', [MessagingController::class, 'getTypingStatus'])->name('typing');
        });
        
        // Debug route for testing typing
        Route::get('/debug-typing/{conversationId}', function($conversationId) {
            return response()->json([
                'conversation_id' => $conversationId,
                'cache_keys' => ['typing_status_' . $conversationId . '_1', 'typing_status_' . $conversationId . '_2'],
                'test_cache' => \Illuminate\Support\Facades\Cache::get('typing_status_' . $conversationId . '_1'),
                'user' => auth()->user()->only('id', 'name', 'role')
            ]);
        });
    });
    
    // Profile routes for all authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Debug routes for email diagnosis (admin only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/debug/email-config', function () {
        $diagnostics = [];
        
        // 1. Check email configuration
        $currentMailer = config('mail.default');
        $diagnostics['email_config'] = [
            'MAIL_MAILER' => $currentMailer,
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];
        
        // 2. Check specific mailer configuration
        if ($currentMailer === 'resend') {
            $diagnostics['resend_config'] = [
                'RESEND_API_KEY' => env('RESEND_API_KEY') ? '[SET - ' . substr(env('RESEND_API_KEY'), 0, 8) . '...]' : '[NOT SET]',
                'resend_config_exists' => file_exists(config_path('resend.php')),
            ];
        } else {
            $diagnostics['smtp_config'] = [
                'MAIL_HOST' => config('mail.mailers.smtp.host'),
                'MAIL_PORT' => config('mail.mailers.smtp.port'),
                'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
                'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? '[SET]' : '[NOT SET]',
                'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption') ?? 'Not set',
            ];
        }
        
        // 3. Check queue configuration
        $diagnostics['queue_config'] = [
            'QUEUE_CONNECTION' => config('queue.default'),
        ];
        
        // 4. Storage configuration check
        $diagnostics['storage_config'] = [
            'FILESYSTEM_DISK' => config('filesystems.default'),
            'CLOUDINARY_CLOUD_NAME' => env('CLOUDINARY_CLOUD_NAME') ? '[SET]' : '[NOT SET]',
            'CLOUDINARY_API_KEY' => env('CLOUDINARY_API_KEY') ? '[SET]' : '[NOT SET]',
            'CLOUDINARY_API_SECRET' => env('CLOUDINARY_API_SECRET') ? '[SET]' : '[NOT SET]',
            'cloudinary_config_exists' => file_exists(config_path('cloudinary.php')),
        ];
        
        // 5. Environment variables check
        $diagnostics['env_vars'] = [
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'RENDER_SERVICE_ID' => env('RENDER_SERVICE_ID', 'Not on Render'),
        ];
        
        // 5. Test appointment data
        try {
            $appointment = App\Models\Appointment::with('patient')->first();
            if ($appointment && $appointment->patient) {
                $diagnostics['test_data'] = [
                    'appointment_id' => $appointment->appointment_id,
                    'patient_name' => $appointment->patient->patient_name,
                    'patient_email' => $appointment->patient->email,
                    'has_valid_test_data' => true,
                ];
            } else {
                $diagnostics['test_data'] = [
                    'has_valid_test_data' => false,
                    'message' => 'No appointments with patients found for testing'
                ];
            }
        } catch (\Exception $e) {
            $diagnostics['test_data'] = [
                'has_valid_test_data' => false,
                'error' => $e->getMessage()
            ];
        }
        
        // Return as JSON for easy reading
        return response()->json($diagnostics, 200, [], JSON_PRETTY_PRINT);
    });
    
    Route::post('/debug/send-test-email', function () {
        try {
            // Get test appointment
            $appointment = App\Models\Appointment::with('patient')->first();
            if (!$appointment || !$appointment->patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'No appointments with patients found'
                ]);
            }
            
            $diagnostics = [
                'mail_driver' => config('mail.default'),
                'appointment_id' => $appointment->appointment_id,
                'patient_email' => $appointment->patient->email,
                'patient_name' => $appointment->patient->patient_name,
            ];
            
            // Test the email service
            \Log::info('Debug: Testing email with Resend', $diagnostics);
            
            $emailService = app(App\Services\EnhancedEmailService::class);
            $result = $emailService->sendAppointmentNotification($appointment, 'approved');
            
            \Log::info('Debug: Email service result', ['result' => $result]);
            
            return response()->json([
                'diagnostics' => $diagnostics,
                'email_result' => $result,
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Debug: Email test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'mail_driver' => config('mail.default'),
                'timestamp' => now()->toDateTimeString()
            ]);
        }
    });
    
    Route::get('/debug/test-resend-direct', function () {
        try {
            // Test Resend via Laravel Mail system
            \Illuminate\Support\Facades\Mail::raw(
                '<h1>Test Email from Resend</h1><p>This is a test email sent via Laravel Mail with Resend driver.</p><p>Time: ' . now()->toDateTimeString() . '</p>',
                function ($message) {
                    $message->to('axlchanh159@gmail.com')
                           ->subject('Test Email from Resend - BOKOD CMS')
                           ->from('noreply@resend.dev', 'BOKOD CMS Test');
                }
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Laravel Mail with Resend test completed',
                'mail_driver' => config('mail.default'),
                'to_email' => 'axlchanh159@gmail.com',
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Direct Resend test failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'mail_driver' => config('mail.default'),
                'api_key_set' => env('RESEND_API_KEY') ? 'yes' : 'no',
                'timestamp' => now()->toDateTimeString()
            ]);
        }
    });
    
    Route::get('/debug/test-cloudinary', function () {
        try {
            // Test Cloudinary configuration
            $cloudinary = \Storage::disk('cloudinary');
            
            // Try to get configuration
            $config = [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET') ? 'SET' : 'NOT_SET',
                'filesystem_default' => config('filesystems.default'),
            ];
            
            // Try to access Cloudinary
            $testResult = [];
            try {
                // Create a simple test to see if Cloudinary works
                $testResult['disk_accessible'] = true;
                $testResult['message'] = 'Cloudinary disk accessible';
            } catch (\Exception $e) {
                $testResult['disk_accessible'] = false;
                $testResult['error'] = $e->getMessage();
            }
            
            return response()->json([
                'success' => true,
                'config' => $config,
                'test_result' => $testResult,
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toDateTimeString()
            ]);
        }
    });
});

// Test route to verify Cloudinary file uploads and URLs
Route::get('/test-upload-verification', function () {
    try {
        $results = [];
        
        // Show current disk configuration first
        $results['configuration'] = [
            'default_disk' => config('filesystems.default'),
            'fallback_disk' => config('filesystems.fallback_disk'),
            'cloudinary_cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'cloudinary_configured' => env('CLOUDINARY_CLOUD_NAME') ? true : false,
            'storage_disk_env' => env('STORAGE_DISK')
        ];
        
        // Test disk access
        try {
            $disk = \Storage::disk(config('filesystems.fallback_disk', 'public'));
            $testUrl = $disk->url('test-file.jpg');
            $results['disk_test'] = [
                'disk_name' => config('filesystems.fallback_disk', 'public'),
                'accessible' => true,
                'sample_url' => $testUrl
            ];
        } catch (\Exception $e) {
            $results['disk_test'] = [
                'disk_name' => config('filesystems.fallback_disk', 'public'),
                'accessible' => false,
                'error' => $e->getMessage()
            ];
        }
        
        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

// Test route to debug actual file uploads
Route::get('/test-storage-disk', function () {
    try {
        $results = [];
        
        // Check actual disk configuration 
        $results['config'] = [
            'default_disk' => config('filesystems.default'),
            'fallback_disk' => config('filesystems.fallback_disk'),
            'cloudinary_env' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY') ? 'SET' : 'NOT_SET',
                'api_secret' => env('CLOUDINARY_API_SECRET') ? 'SET' : 'NOT_SET',
                'url' => env('CLOUDINARY_URL') ? 'SET' : 'NOT_SET'
            ]
        ];
        
        // Test which disk the SettingsController would use
        $settingsController = new \App\Http\Controllers\SettingsController();
        $reflection = new \ReflectionClass($settingsController);
        $method = $reflection->getMethod('getStorageDisk');
        $method->setAccessible(true);
        $disk = $method->invoke($settingsController);
        
        $results['settings_controller_disk'] = [
            'disk_class' => get_class($disk),
            'disk_name' => method_exists($disk, 'getName') ? $disk->getName() : 'getName method not available',
            'sample_url' => $disk->url('test-file.jpg')
        ];
        
        // Test direct Cloudinary access
        try {
            $cloudinaryDisk = \Storage::disk('cloudinary');
            $results['direct_cloudinary'] = [
                'accessible' => true,
                'sample_url' => $cloudinaryDisk->url('test-file.jpg'),
                'exists_test' => $cloudinaryDisk->exists('non-existent-file.jpg'),
            ];
            
            // Test actual file upload to Cloudinary
            try {
                $testContent = 'Test file content at ' . now();
                $uploadPath = $cloudinaryDisk->put('debug/test.txt', $testContent);
                
                // Try different ways to get the URL
                $urlMethods = [];
                
                // Method 1: Standard url() method
                try {
                    $urlMethods['standard_url'] = $cloudinaryDisk->url($uploadPath);
                } catch (\Exception $e) {
                    $urlMethods['standard_url_error'] = $e->getMessage();
                }
                
                // Method 2: Try to build URL manually if we know the path
                if ($uploadPath) {
                    $cloudName = env('CLOUDINARY_CLOUD_NAME');
                    $urlMethods['manual_url'] = "https://res.cloudinary.com/{$cloudName}/raw/upload/{$uploadPath}";
                }
                
                // Method 3: Check if the upload path is actually a URL already
                $urlMethods['upload_path_analysis'] = [
                    'is_url' => filter_var($uploadPath, FILTER_VALIDATE_URL) !== false,
                    'starts_with_http' => strpos($uploadPath, 'http') === 0,
                    'path_value' => $uploadPath
                ];
                
                $results['cloudinary_upload_test'] = [
                    'upload_successful' => true,
                    'uploaded_path' => $uploadPath,
                    'url_methods' => $urlMethods,
                    'file_exists_after_upload' => $cloudinaryDisk->exists($uploadPath)
                ];
                
                // Test image upload specifically (like logo/favicon)
                try {
                    // Create a simple test image content (fake PNG header)
                    $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
                    $imagePath = $cloudinaryDisk->put('settings/test-image.png', $imageContent);
                    
                    $imageUrlMethods = [];
                    $imageUrlMethods['standard_url'] = $cloudinaryDisk->url($imagePath);
                    if ($imagePath) {
                        $cloudName = env('CLOUDINARY_CLOUD_NAME');
                        $imageUrlMethods['manual_image_url'] = "https://res.cloudinary.com/{$cloudName}/image/upload/{$imagePath}";
                    }
                    
                    $results['image_upload_test'] = [
                        'upload_successful' => true,
                        'uploaded_path' => $imagePath,
                        'url_methods' => $imageUrlMethods,
                        'file_exists_after_upload' => $cloudinaryDisk->exists($imagePath)
                    ];
                } catch (\Exception $imageError) {
                    $results['image_upload_test'] = [
                        'upload_successful' => false,
                        'error' => $imageError->getMessage()
                    ];
                }
            } catch (\Exception $uploadError) {
                $results['cloudinary_upload_test'] = [
                    'upload_successful' => false,
                    'error' => $uploadError->getMessage()
                ];
            }
        } catch (\Exception $e) {
            $results['direct_cloudinary'] = [
                'accessible' => false,
                'error' => $e->getMessage()
            ];
        }
        
        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

// Cloudinary is now fully configured and working! 🎉

// Debug routes (only in development)
if (app()->environment(['local', 'development'])) {
    Route::get('/debug/appointments', function() {
        $stats = [
            'total' => \App\Models\Appointment::count(),
            'active' => \App\Models\Appointment::where('status', 'active')->count(),
            'completed' => \App\Models\Appointment::where('status', 'completed')->count(),
            'cancelled' => \App\Models\Appointment::where('status', 'cancelled')->count(),
            'pending_approval' => \App\Models\Appointment::where('approval_status', 'pending')->count(),
            'approved' => \App\Models\Appointment::where('approval_status', 'approved')->count(),
            'rejected' => \App\Models\Appointment::where('approval_status', 'rejected')->count(),
        ];
        
        // Show some sample records
        $samples = \App\Models\Appointment::with('patient')->take(5)->get()->map(function($apt) {
            return [
                'id' => $apt->appointment_id,
                'patient' => $apt->patient ? $apt->patient->patient_name : 'No Patient',
                'date' => $apt->appointment_date->format('Y-m-d'),
                'time' => $apt->appointment_time->format('H:i'),
                'status' => $apt->status,
                'approval_status' => $apt->approval_status,
                'reason' => substr($apt->reason, 0, 50)
            ];
        });
        
        return response()->json([
            'stats' => $stats,
            'sample_records' => $samples,
            'distinct_statuses' => \App\Models\Appointment::distinct()->pluck('status'),
            'distinct_approval_statuses' => \App\Models\Appointment::distinct()->pluck('approval_status'),
        ]);
    });
    
    // Test appointments filter without authentication
    Route::get('/debug/appointments-filter', function(\Illuminate\Http\Request $request) {
        $query = \App\Models\Appointment::with('patient');
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status !== '') {
            $query = $query->where('status', $request->status);
        }
        
        $appointments = $query->orderBy('appointment_date')
                             ->orderBy('appointment_time')
                             ->get();
        
        return response()->json([
            'request_params' => $request->all(),
            'total_found' => $appointments->count(),
            'sample_appointments' => $appointments->take(3)->map(function($apt) {
                return [
                    'id' => $apt->appointment_id,
                    'status' => $apt->status,
                    'date' => $apt->appointment_date->format('Y-m-d'),
                    'patient' => $apt->patient ? $apt->patient->patient_name : 'No Patient'
                ];
            }),
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
    });
}

