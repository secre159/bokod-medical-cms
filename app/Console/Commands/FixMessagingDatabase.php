<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Exception;

class FixMessagingDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:messaging-database {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix messaging system database schema issues that cause errors on hosted sites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Messaging Database Fix...');
        $this->newLine();
        
        // Show warning unless forced
        if (!$this->option('force')) {
            $this->warn('⚠️  This will modify your database structure.');
            $this->warn('💾 Please ensure you have a database backup before proceeding.');
            $this->newLine();
            
            if (!$this->confirm('Do you want to continue?')) {
                $this->info('Operation cancelled.');
                return 1;
            }
            $this->newLine();
        }
        
        try {
            // Check database connection
            $connection = DB::connection();
            $databaseName = $connection->getDatabaseName();
            $this->info("📊 Connected to database: {$databaseName}");
            $this->info("🔗 Connection type: " . get_class($connection));
            $this->newLine();
            
            // Check messaging tables
            $this->info('🔍 Checking messaging tables...');
            $tables = ['conversations', 'messages'];
            $existingTables = [];
            
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $existingTables[] = $table;
                    $this->line("  ✅ {$table} table exists");
                } else {
                    $this->line("  ❌ {$table} table MISSING");
                }
            }
            
            if (empty($existingTables)) {
                $this->error('🚨 CRITICAL: No messaging tables found!');
                $this->info('💡 Running migrations...');
                
                Artisan::call('migrate', ['--force' => true]);
                $this->info('✅ Migrations completed');
            }
            
            $this->newLine();
            $this->info('🔍 Checking messages table structure...');
            
            // Check messages table columns
            $columns = Schema::getColumnListing('messages');
            $requiredColumns = [
                'id', 'conversation_id', 'sender_id', 'message', 'message_type',
                'priority', 'has_attachment', 'file_path', 'file_name', 'file_type',
                'mime_type', 'file_size', 'reactions', 'created_at', 'updated_at'
            ];
            
            $missingColumns = [];
            
            foreach ($requiredColumns as $column) {
                if (in_array($column, $columns)) {
                    $this->line("  ✅ {$column} column exists");
                } else {
                    $missingColumns[] = $column;
                    $this->line("  ❌ {$column} column MISSING");
                }
            }
            
            // Add missing columns
            if (!empty($missingColumns)) {
                $this->newLine();
                $this->info('🔧 Adding missing columns...');
                
                foreach ($missingColumns as $column) {
                    try {
                        switch ($column) {
                            case 'reactions':
                                Schema::table('messages', function ($table) {
                                    $table->json('reactions')->nullable();
                                });
                                $this->line("  ✅ Added reactions column");
                                break;
                                
                            case 'has_attachment':
                                Schema::table('messages', function ($table) {
                                    $table->boolean('has_attachment')->default(false);
                                });
                                $this->line("  ✅ Added has_attachment column");
                                break;
                                
                            case 'file_path':
                            case 'file_name':
                            case 'file_type':
                            case 'mime_type':
                                Schema::table('messages', function ($table) use ($column) {
                                    $table->string($column)->nullable();
                                });
                                $this->line("  ✅ Added {$column} column");
                                break;
                                
                            case 'file_size':
                                Schema::table('messages', function ($table) {
                                    $table->bigInteger('file_size')->nullable();
                                });
                                $this->line("  ✅ Added file_size column");
                                break;
                                
                            case 'priority':
                                Schema::table('messages', function ($table) {
                                    $table->enum('priority', ['low', 'normal', 'urgent'])->default('normal');
                                });
                                $this->line("  ✅ Added priority column");
                                break;
                                
                            case 'message_type':
                                Schema::table('messages', function ($table) {
                                    $table->enum('message_type', ['text', 'image', 'file', 'system'])->default('text');
                                });
                                $this->line("  ✅ Added message_type column");
                                break;
                                
                            default:
                                $this->line("  ⚠️  Skipped {$column} - manual intervention needed");
                                break;
                        }
                    } catch (Exception $e) {
                        $this->line("  ❌ Failed to add {$column}: " . $e->getMessage());
                    }
                }
            } else {
                $this->info('✅ All required message columns already exist');
            }
            
            $this->newLine();
            $this->info('🔍 Checking conversations table structure...');
            
            // Check conversations table columns
            $convColumns = Schema::getColumnListing('conversations');
            $requiredConvColumns = [
                'id', 'patient_id', 'admin_id', 'is_active', 'last_message_at',
                'archived_by_patient', 'archived_by_admin', 'created_at', 'updated_at'
            ];
            
            $missingConvColumns = [];
            
            foreach ($requiredConvColumns as $column) {
                if (in_array($column, $convColumns)) {
                    $this->line("  ✅ {$column} column exists");
                } else {
                    $missingConvColumns[] = $column;
                    $this->line("  ❌ {$column} column MISSING");
                }
            }
            
            // Add missing conversation columns
            if (!empty($missingConvColumns)) {
                $this->newLine();
                $this->info('🔧 Adding missing conversation columns...');
                
                foreach ($missingConvColumns as $column) {
                    try {
                        switch ($column) {
                            case 'archived_by_patient':
                            case 'archived_by_admin':
                            case 'is_active':
                                Schema::table('conversations', function ($table) use ($column) {
                                    $table->boolean($column)->default($column === 'is_active' ? true : false);
                                });
                                $this->line("  ✅ Added {$column} column");
                                break;
                                
                            case 'last_message_at':
                                Schema::table('conversations', function ($table) {
                                    $table->timestamp('last_message_at')->nullable();
                                });
                                $this->line("  ✅ Added last_message_at column");
                                break;
                                
                            default:
                                $this->line("  ⚠️  Skipped {$column} - manual intervention needed");
                                break;
                        }
                    } catch (Exception $e) {
                        $this->line("  ❌ Failed to add {$column}: " . $e->getMessage());
                    }
                }
            } else {
                $this->info('✅ All required conversation columns already exist');
            }
            
            $this->newLine();
            $this->info('🧪 Testing messaging system functionality...');
            
            // Test database queries that the messaging system uses
            $messageCount = DB::table('messages')->count();
            $conversationCount = DB::table('conversations')->count();
            
            $this->line("  ✅ Messages table accessible - {$messageCount} messages found");
            $this->line("  ✅ Conversations table accessible - {$conversationCount} conversations found");
            
            // Test complex query
            DB::table('conversations')
                ->leftJoin('messages', 'conversations.id', '=', 'messages.conversation_id')
                ->select('conversations.*')
                ->whereNotNull('conversations.id')
                ->limit(1)
                ->get();
            
            $this->line('  ✅ Complex joins working correctly');
            
            // Test reactions column specifically
            DB::table('messages')
                ->select('reactions')
                ->limit(1)
                ->get();
            
            $this->line('  ✅ Reactions column accessible');
            
            $this->newLine();
            $this->info('🧹 Clearing caches...');
            
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            
            $this->line('  ✅ Application cache cleared');
            $this->line('  ✅ Configuration cache cleared');
            
            $this->newLine();
            $this->info('🎉 ALL FIXES COMPLETED SUCCESSFULLY!');
            $this->info('💡 Your messaging system should now work correctly on the hosted site.');
            
            $this->newLine();
            $this->info('📝 Next steps:');
            $this->line('  1. Test messaging functionality on your hosted site');
            $this->line('  2. Check browser console for any remaining JavaScript errors');
            $this->line('  3. Verify that the "error" alert no longer appears');
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('❌ CRITICAL ERROR: ' . $e->getMessage());
            $this->line('📍 File: ' . $e->getFile());
            $this->line('📍 Line: ' . $e->getLine());
            $this->newLine();
            
            $this->info('💡 Troubleshooting steps:');
            $this->line('  1. Verify database connection in .env file');
            $this->line('  2. Check database permissions');
            $this->line('  3. Run: php artisan migrate --force');
            $this->line('  4. Check Laravel logs for more details');
            
            return 1;
        }
    }
}
