<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class DatabaseFixController extends Controller
{
    /**
     * Show database fix dashboard
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Access denied. Admin access required.');
        }
        
        $checks = $this->runChecks();
        return view('admin.database-fixes', compact('checks'));
    }

    /**
     * Run database checks
     */
    private function runChecks()
    {
        $checks = [
            'messaging' => $this->checkMessagingSystem(),
            'prescriptions' => $this->checkPrescriptionsSystem(),
        ];

        return $checks;
    }

    /**
     * Check messaging system
     */
    private function checkMessagingSystem()
    {
        $issues = [];
        $fixes = [];

        try {
            // Check conversations-patient relationships
            if (Schema::hasTable('conversations') && Schema::hasTable('patients')) {
                $conversations = DB::table('conversations')->get();
                $brokenRelationships = 0;

                foreach ($conversations as $conv) {
                    $patient = DB::table('patients')->where('id', $conv->patient_id)->first();
                    if (!$patient) {
                        $brokenRelationships++;
                        // Try to find correct patient
                        $messageSenders = DB::table('messages')
                            ->join('users', 'messages.sender_id', '=', 'users.id')
                            ->where('messages.conversation_id', $conv->id)
                            ->where('users.role', 'patient')
                            ->distinct()
                            ->select('users.id as user_id', 'users.name')
                            ->first();

                        if ($messageSenders) {
                            $correctPatient = DB::table('patients')
                                ->where('user_id', $messageSenders->user_id)
                                ->first();

                            if ($correctPatient) {
                                $fixes[] = [
                                    'conversation_id' => $conv->id,
                                    'old_patient_id' => $conv->patient_id,
                                    'new_patient_id' => $correctPatient->id,
                                    'patient_name' => $correctPatient->patient_name
                                ];
                            }
                        }
                    }
                }

                if ($brokenRelationships > 0) {
                    $issues[] = "Found {$brokenRelationships} conversations with broken patient relationships";
                }
            }
        } catch (\Exception $e) {
            $issues[] = "Error checking messaging system: " . $e->getMessage();
        }

        return [
            'status' => empty($issues) ? 'ok' : 'needs_fix',
            'issues' => $issues,
            'fixes' => $fixes
        ];
    }

    /**
     * Check prescriptions system
     */
    private function checkPrescriptionsSystem()
    {
        $issues = [];
        $missingColumns = [];

        try {
            if (!Schema::hasTable('prescriptions')) {
                $issues[] = "Prescriptions table does not exist";
                return ['status' => 'error', 'issues' => $issues, 'fixes' => []];
            }

            // Get database driver to use appropriate syntax
            $driver = DB::getDriverName();
            $requiredColumns = $this->getColumnDefinitions($driver);

            foreach (array_keys($requiredColumns) as $column) {
                if (!Schema::hasColumn('prescriptions', $column)) {
                    $missingColumns[] = $column;
                }
            }

            if (!empty($missingColumns)) {
                $issues[] = "Missing columns in prescriptions table: " . implode(', ', $missingColumns);
            }
        } catch (\Exception $e) {
            $issues[] = "Error checking prescriptions system: " . $e->getMessage();
        }

        return [
            'status' => empty($issues) ? 'ok' : 'needs_fix',
            'issues' => $issues,
            'fixes' => $missingColumns
        ];
    }

    /**
     * Fix messaging database schema for error alerts
     */
    public function fixMessagingDatabase(Request $request)
    {
        // Simple security check
        $secret = $request->get('secret');
        if ($secret !== 'bokod_cms_messaging_fix_2024') {
            abort(404, 'Not found');
        }

        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $output = [];
        $output[] = "🚀 Starting Messaging Database Fix...";
        
        try {
            // Check messaging tables
            $tables = ['conversations', 'messages'];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $output[] = "✅ {$table} table exists";
                } else {
                    $output[] = "❌ {$table} table MISSING";
                }
            }
            
            // Check messages table structure
            $columns = Schema::getColumnListing('messages');
            $requiredColumns = [
                'reactions', 'has_attachment', 'file_path', 'file_name', 
                'file_type', 'mime_type', 'file_size', 'priority', 'message_type'
            ];
            
            $missingColumns = array_diff($requiredColumns, $columns);
            
            // Add missing columns
            if (!empty($missingColumns)) {
                $output[] = "🔧 Adding missing columns...";
                
                foreach ($missingColumns as $column) {
                    try {
                        switch ($column) {
                            case 'reactions':
                                Schema::table('messages', function ($table) {
                                    $table->json('reactions')->nullable();
                                });
                                $output[] = "✅ Added reactions column";
                                break;
                                
                            case 'has_attachment':
                                Schema::table('messages', function ($table) {
                                    $table->boolean('has_attachment')->default(false);
                                });
                                $output[] = "✅ Added has_attachment column";
                                break;
                                
                            case 'file_path':
                            case 'file_name':
                            case 'file_type':
                            case 'mime_type':
                                Schema::table('messages', function ($table) use ($column) {
                                    $table->string($column)->nullable();
                                });
                                $output[] = "✅ Added {$column} column";
                                break;
                                
                            case 'file_size':
                                Schema::table('messages', function ($table) {
                                    $table->bigInteger('file_size')->nullable();
                                });
                                $output[] = "✅ Added file_size column";
                                break;
                                
                            case 'priority':
                                Schema::table('messages', function ($table) {
                                    $table->enum('priority', ['low', 'normal', 'urgent'])->default('normal');
                                });
                                $output[] = "✅ Added priority column";
                                break;
                                
                            case 'message_type':
                                Schema::table('messages', function ($table) {
                                    $table->enum('message_type', ['text', 'image', 'file', 'system'])->default('text');
                                });
                                $output[] = "✅ Added message_type column";
                                break;
                        }
                    } catch (Exception $e) {
                        $output[] = "❌ Failed to add {$column}: " . $e->getMessage();
                    }
                }
            } else {
                $output[] = "✅ All required columns already exist";
            }
            
            // Check conversations table
            $convColumns = Schema::getColumnListing('conversations');
            $requiredConvColumns = ['archived_by_patient', 'archived_by_admin', 'is_active', 'last_message_at'];
            $missingConvColumns = array_diff($requiredConvColumns, $convColumns);
            
            if (!empty($missingConvColumns)) {
                $output[] = "🔧 Adding missing conversation columns...";
                
                foreach ($missingConvColumns as $column) {
                    try {
                        switch ($column) {
                            case 'archived_by_patient':
                            case 'archived_by_admin':
                            case 'is_active':
                                Schema::table('conversations', function ($table) use ($column) {
                                    $table->boolean($column)->default($column === 'is_active' ? true : false);
                                });
                                $output[] = "✅ Added {$column} column";
                                break;
                                
                            case 'last_message_at':
                                Schema::table('conversations', function ($table) {
                                    $table->timestamp('last_message_at')->nullable();
                                });
                                $output[] = "✅ Added last_message_at column";
                                break;
                        }
                    } catch (Exception $e) {
                        $output[] = "❌ Failed to add {$column}: " . $e->getMessage();
                    }
                }
            }
            
            // Clear caches
            $output[] = "🧹 Clearing caches...";
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            $output[] = "✅ Caches cleared";
            
            $output[] = "🎉 MESSAGING FIX COMPLETED!";
            
            return response()->json([
                'success' => true,
                'message' => 'Messaging database fix completed successfully!',
                'output' => $output
            ]);
            
        } catch (Exception $e) {
            $output[] = "❌ CRITICAL ERROR: " . $e->getMessage();
            return response()->json([
                'success' => false,
                'error' => 'Fix failed: ' . $e->getMessage(),
                'output' => $output
            ], 500);
        }
    }

    /**
     * Optimize messaging system performance
     */
    public function optimizeMessagingPerformance(Request $request)
    {
        // Simple security check
        $secret = $request->get('secret');
        if ($secret !== 'bokod_cms_perf_fix_2024') {
            abort(404, 'Not found');
        }

        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $output = [];
        $output[] = "🚀 Starting Messaging Performance Optimization...";
        
        try {
            $driver = DB::getDriverName();
            $output[] = "🔗 Database driver: {$driver}";
            
            // Add performance indexes
            $indexesAdded = 0;
            
            if ($driver === 'mysql') {
                // Check existing indexes first
                $messageIndexes = DB::select("SHOW INDEX FROM messages");
                $conversationIndexes = DB::select("SHOW INDEX FROM conversations");
                
                $existingIndexes = collect($messageIndexes)->pluck('Key_name')->toArray();
                $existingConvIndexes = collect($conversationIndexes)->pluck('Key_name')->toArray();
                
                // Add indexes if they don't exist
                if (!in_array('idx_messages_conversation_created', $existingIndexes)) {
                    DB::statement("CREATE INDEX idx_messages_conversation_created ON messages (conversation_id, created_at DESC)");
                    $output[] = "✅ Added conversation_id + created_at index";
                    $indexesAdded++;
                }
                
                if (!in_array('idx_conversations_last_message', $existingConvIndexes)) {
                    DB::statement("CREATE INDEX idx_conversations_last_message ON conversations (last_message_at DESC)");
                    $output[] = "✅ Added last_message_at index";
                    $indexesAdded++;
                }
                
            } elseif ($driver === 'pgsql') {
                // PostgreSQL indexes
                DB::statement("CREATE INDEX IF NOT EXISTS idx_messages_conversation_created ON messages (conversation_id, created_at DESC)");
                DB::statement("CREATE INDEX IF NOT EXISTS idx_conversations_last_message ON conversations (last_message_at DESC)");
                $output[] = "✅ Added PostgreSQL performance indexes";
                $indexesAdded = 2;
            }
            
            // Test query performance
            $start = microtime(true);
            $testConversations = DB::table('conversations')
                ->where('is_active', true)
                ->orderBy('last_message_at', 'desc')
                ->limit(10)
                ->get();
            
            $queryTime = round((microtime(true) - $start) * 1000, 2);
            $output[] = "📊 Conversation query: {$queryTime}ms";
            
            // Check email settings
            $mailDriver = config('mail.default');
            $queueConnection = config('queue.default');
            
            $output[] = "📮 Mail driver: {$mailDriver}";
            $output[] = "🔄 Queue connection: {$queueConnection}";
            
            if ($mailDriver === 'smtp' && $queueConnection === 'sync') {
                $output[] = "⚠️  WARNING: SMTP emails sent synchronously (causes delays)";
                $output[] = "💡 Consider: MAIL_MAILER=log or QUEUE_CONNECTION=database";
            }
            
            // Cache optimization
            \Artisan::call('config:cache');
            \Artisan::call('route:cache');
            $output[] = "✅ Laravel caches optimized";
            
            $output[] = "🎉 Performance optimization completed!";
            $output[] = "📊 Indexes added: {$indexesAdded}";
            
            if ($queryTime > 100) {
                $output[] = "⚠️  Query time still high - check database server performance";
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Messaging performance optimization completed!',
                'output' => $output,
                'query_time_ms' => $queryTime,
                'indexes_added' => $indexesAdded
            ]);
            
        } catch (Exception $e) {
            $output[] = "❌ ERROR: " . $e->getMessage();
            return response()->json([
                'success' => false,
                'error' => 'Performance optimization failed: ' . $e->getMessage(),
                'output' => $output
            ], 500);
        }
    }

    /**
     * Fix messaging system issues
     */
    public function fixMessaging(Request $request)
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $fixCount = 0;
            $conversations = DB::table('conversations')->get();
            $patients = DB::table('patients')->get();

            foreach ($conversations as $conv) {
                $patient = $patients->where('id', $conv->patient_id)->first();

                if (!$patient) {
                    // Try to find the correct patient by looking at message senders
                    $messageSenders = DB::table('messages')
                        ->join('users', 'messages.sender_id', '=', 'users.id')
                        ->where('messages.conversation_id', $conv->id)
                        ->where('users.role', 'patient')
                        ->distinct()
                        ->select('users.id as user_id', 'users.name')
                        ->first();

                    if ($messageSenders) {
                        $correctPatient = $patients->where('user_id', $messageSenders->user_id)->first();

                        if ($correctPatient) {
                            DB::table('conversations')
                                ->where('id', $conv->id)
                                ->update(['patient_id' => $correctPatient->id]);
                            $fixCount++;
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Fixed {$fixCount} conversation relationships",
                'fixes_applied' => $fixCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error fixing messaging system: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fix prescriptions system issues
     */
    public function fixPrescriptions(Request $request)
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // Get database driver to use appropriate syntax
            $driver = DB::getDriverName();
            $requiredColumns = $this->getColumnDefinitions($driver);

            $addedColumns = [];

            foreach ($requiredColumns as $column => $definition) {
                if (!Schema::hasColumn('prescriptions', $column)) {
                    DB::statement("ALTER TABLE prescriptions ADD COLUMN {$column} {$definition}");
                    $addedColumns[] = $column;
                }
            }

            // Add foreign key constraint for prescribed_by if it was added
            if (in_array('prescribed_by', $addedColumns)) {
                // Check if constraint already exists first
                $constraintExists = $this->checkConstraintExists($driver, 'prescriptions', 'prescriptions_prescribed_by_foreign');

                if (!$constraintExists) {
                    DB::statement("ALTER TABLE prescriptions ADD CONSTRAINT prescriptions_prescribed_by_foreign FOREIGN KEY (prescribed_by) REFERENCES users(id) ON DELETE SET NULL");
                }
            }

            // Update migrations table if exists
            if (Schema::hasTable('migrations')) {
                $migrationName = '2025_09_17_030723_add_comprehensive_fields_to_prescriptions_table';
                $migrationExists = DB::table('migrations')->where('migration', $migrationName)->exists();

                if (!$migrationExists) {
                    DB::table('migrations')->insert([
                        'migration' => $migrationName,
                        'batch' => DB::table('migrations')->max('batch') + 1
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Added " . count($addedColumns) . " missing columns to prescriptions table",
                'columns_added' => $addedColumns
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error fixing prescriptions system: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get column definitions based on database driver
     */
    private function getColumnDefinitions($driver)
    {
        if ($driver === 'pgsql') {
            // PostgreSQL syntax
            return [
                'prescribed_by' => 'BIGINT NULL',
                'frequency' => "VARCHAR(255) DEFAULT 'once_daily'",
                'duration_days' => 'INTEGER NULL',
                'dispensed_quantity' => 'INTEGER DEFAULT 0',
                'remaining_quantity' => 'INTEGER NULL'
            ];
        } else {
            // MySQL syntax (default)
            return [
                'prescribed_by' => 'BIGINT UNSIGNED NULL',
                'frequency' => "VARCHAR(255) DEFAULT 'once_daily'",
                'duration_days' => 'INTEGER NULL',
                'dispensed_quantity' => 'INTEGER DEFAULT 0',
                'remaining_quantity' => 'INTEGER NULL'
            ];
        }
    }

    /**
     * Check if a foreign key constraint exists
     */
    private function checkConstraintExists($driver, $tableName, $constraintName)
    {
        if ($driver === 'pgsql') {
            // PostgreSQL query
            $result = DB::select("
                SELECT constraint_name 
                FROM information_schema.table_constraints 
                WHERE table_name = ? 
                AND constraint_name = ?
                AND table_schema = current_schema()
            ", [$tableName, $constraintName]);
        } else {
            // MySQL query
            $result = DB::select("
                SELECT constraint_name 
                FROM information_schema.table_constraints 
                WHERE table_name = ? 
                AND constraint_name = ?
                AND table_schema = DATABASE()
            ", [$tableName, $constraintName]);
        }
        
        return !empty($result);
    }
}
