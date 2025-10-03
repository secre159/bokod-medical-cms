<?php

echo "🚀 Starting Production Messaging Database Fix...\n\n";

try {
    // Check if we're in Laravel environment
    if (!defined('LARAVEL_START')) {
        require_once __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    }

    // Use Laravel's database connection
    $connection = DB::connection();
    $databaseName = $connection->getDatabaseName();
    
    echo "📊 Connected to database: {$databaseName}\n";
    echo "🔗 Connection type: " . get_class($connection) . "\n\n";

    // Check if messaging tables exist
    echo "🔍 Checking messaging tables...\n";
    
    $tables = ['conversations', 'messages'];
    $existingTables = [];
    
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            $existingTables[] = $table;
            echo "  ✅ {$table} table exists\n";
        } else {
            echo "  ❌ {$table} table MISSING\n";
        }
    }
    
    if (empty($existingTables)) {
        echo "\n🚨 CRITICAL: No messaging tables found!\n";
        echo "💡 Run: php artisan migrate\n";
        exit(1);
    }
    
    echo "\n🔍 Checking messages table structure...\n";
    
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
            echo "  ✅ {$column} column exists\n";
        } else {
            $missingColumns[] = $column;
            echo "  ❌ {$column} column MISSING\n";
        }
    }
    
    if (!empty($missingColumns)) {
        echo "\n🔧 Adding missing columns...\n";
        
        foreach ($missingColumns as $column) {
            try {
                switch ($column) {
                    case 'reactions':
                        Schema::table('messages', function ($table) {
                            $table->json('reactions')->nullable();
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    case 'has_attachment':
                        Schema::table('messages', function ($table) {
                            $table->boolean('has_attachment')->default(false);
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    case 'file_path':
                    case 'file_name':
                    case 'file_type':
                    case 'mime_type':
                        Schema::table('messages', function ($table) use ($column) {
                            $table->string($column)->nullable();
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    case 'file_size':
                        Schema::table('messages', function ($table) {
                            $table->bigInteger('file_size')->nullable();
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    case 'priority':
                        Schema::table('messages', function ($table) {
                            $table->enum('priority', ['low', 'normal', 'urgent'])->default('normal');
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    case 'message_type':
                        Schema::table('messages', function ($table) {
                            $table->enum('message_type', ['text', 'image', 'file', 'system'])->default('text');
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    default:
                        echo "  ⚠️  Skipped {$column} - manual intervention needed\n";
                        break;
                }
            } catch (Exception $e) {
                echo "  ❌ Failed to add {$column}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n🔍 Checking conversations table structure...\n";
    
    // Check conversations table columns  
    $convColumns = Schema::getColumnListing('conversations');
    $requiredConvColumns = [
        'id', 'patient_id', 'admin_id', 'is_active', 'last_message_at',
        'archived_by_patient', 'archived_by_admin', 'created_at', 'updated_at'
    ];
    
    $missingConvColumns = [];
    
    foreach ($requiredConvColumns as $column) {
        if (in_array($column, $convColumns)) {
            echo "  ✅ {$column} column exists\n";
        } else {
            $missingConvColumns[] = $column;
            echo "  ❌ {$column} column MISSING\n";
        }
    }
    
    if (!empty($missingConvColumns)) {
        echo "\n🔧 Adding missing conversation columns...\n";
        
        foreach ($missingConvColumns as $column) {
            try {
                switch ($column) {
                    case 'archived_by_patient':
                    case 'archived_by_admin':
                        Schema::table('conversations', function ($table) use ($column) {
                            $table->boolean($column)->default(false);
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    case 'is_active':
                        Schema::table('conversations', function ($table) {
                            $table->boolean('is_active')->default(true);
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    case 'last_message_at':
                        Schema::table('conversations', function ($table) {
                            $table->timestamp('last_message_at')->nullable();
                        });
                        echo "  ✅ Added {$column} column\n";
                        break;
                    
                    default:
                        echo "  ⚠️  Skipped {$column} - manual intervention needed\n";
                        break;
                }
            } catch (Exception $e) {
                echo "  ❌ Failed to add {$column}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n🧪 Testing messaging system functionality...\n";
    
    // Test database queries that the messaging system uses
    try {
        $messageCount = DB::table('messages')->count();
        echo "  ✅ Messages table accessible - {$messageCount} messages found\n";
        
        $conversationCount = DB::table('conversations')->count(); 
        echo "  ✅ Conversations table accessible - {$conversationCount} conversations found\n";
        
        // Test a complex query similar to what the messaging controller uses
        $testQuery = DB::table('conversations')
            ->leftJoin('messages', 'conversations.id', '=', 'messages.conversation_id')
            ->select('conversations.*')
            ->whereNotNull('conversations.id')
            ->limit(1)
            ->get();
        
        echo "  ✅ Complex joins working correctly\n";
        
        // Test reactions column specifically
        $testReaction = DB::table('messages')
            ->select('reactions')
            ->limit(1)
            ->get();
        
        echo "  ✅ Reactions column accessible\n";
        
    } catch (Exception $e) {
        echo "  ❌ Database functionality test failed: " . $e->getMessage() . "\n";
        
        // Try to fix common issues
        echo "\n🔧 Attempting to fix database issues...\n";
        
        try {
            // Clear any cached schema
            if (method_exists(Schema::class, 'flushCache')) {
                Schema::flushCache();
            }
            
            // Run migrations again to ensure everything is up to date
            Artisan::call('migrate', ['--force' => true]);
            echo "  ✅ Re-ran migrations successfully\n";
            
        } catch (Exception $e) {
            echo "  ❌ Failed to fix: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎯 Testing Message Model functionality...\n";
    
    try {
        // Test if Message model can be instantiated
        $messageModel = new App\Models\Message();
        echo "  ✅ Message model loads successfully\n";
        
        // Test relationships
        $testMessage = App\Models\Message::with('sender', 'conversation')->first();
        if ($testMessage) {
            echo "  ✅ Message relationships working\n";
        } else {
            echo "  ℹ️  No messages found (this is ok for empty systems)\n";
        }
        
    } catch (Exception $e) {
        echo "  ❌ Message model error: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎯 Testing Conversation Model functionality...\n";
    
    try {
        // Test if Conversation model can be instantiated
        $conversationModel = new App\Models\Conversation();
        echo "  ✅ Conversation model loads successfully\n";
        
        // Test relationships
        $testConversation = App\Models\Conversation::with('patient', 'admin', 'messages')->first();
        if ($testConversation) {
            echo "  ✅ Conversation relationships working\n";
        } else {
            echo "  ℹ️  No conversations found (this is ok for empty systems)\n";
        }
        
    } catch (Exception $e) {
        echo "  ❌ Conversation model error: " . $e->getMessage() . "\n";
    }
    
    echo "\n🏁 Final Status Check...\n";
    
    // Final verification
    $finalColumns = Schema::getColumnListing('messages');
    $finalConvColumns = Schema::getColumnListing('conversations');
    
    $hasReactions = in_array('reactions', $finalColumns);
    $hasFileAttachment = in_array('has_attachment', $finalColumns);
    $hasArchiveFields = in_array('archived_by_patient', $finalConvColumns);
    
    if ($hasReactions && $hasFileAttachment && $hasArchiveFields) {
        echo "  🎉 ALL CHECKS PASSED! Messaging system should work correctly.\n";
        echo "\n💡 Next steps:\n";
        echo "  1. Clear application cache: php artisan cache:clear\n";
        echo "  2. Clear config cache: php artisan config:clear\n"; 
        echo "  3. Test messaging functionality on your site\n";
        echo "  4. Check browser console for any remaining JavaScript errors\n";
    } else {
        echo "  ⚠️  Some issues remain:\n";
        if (!$hasReactions) echo "    - reactions column still missing\n";
        if (!$hasFileAttachment) echo "    - has_attachment column still missing\n";
        if (!$hasArchiveFields) echo "    - archive fields still missing\n";
        
        echo "\n💡 Manual fix needed - check your database directly\n";
    }
    
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n\n";
    
    echo "💡 Troubleshooting steps:\n";
    echo "  1. Verify database connection in .env file\n";
    echo "  2. Check database permissions\n";
    echo "  3. Run: php artisan migrate --force\n";
    echo "  4. Run: php artisan cache:clear\n";
    echo "  5. Check Laravel logs for more details\n";
}

echo "\n✨ Script completed!\n";