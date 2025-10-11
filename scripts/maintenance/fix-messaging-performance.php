<?php

echo "🚀 Starting Messaging Performance Optimization...\n\n";

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
    echo "🔗 Database driver: " . $connection->getDriverName() . "\n\n";

    // Check current indexes
    echo "🔍 Checking existing indexes...\n";
    
    $driver = $connection->getDriverName();
    
    if ($driver === 'mysql') {
        // MySQL index queries
        $messageIndexes = DB::select("SHOW INDEX FROM messages");
        $conversationIndexes = DB::select("SHOW INDEX FROM conversations");
    } elseif ($driver === 'pgsql') {
        // PostgreSQL index queries
        $messageIndexes = DB::select("SELECT * FROM pg_indexes WHERE tablename = 'messages'");
        $conversationIndexes = DB::select("SELECT * FROM pg_indexes WHERE tablename = 'conversations'");
    } else {
        echo "⚠️  Unsupported database driver: {$driver}\n";
        $messageIndexes = [];
        $conversationIndexes = [];
    }
    
    echo "📋 Current message table indexes: " . count($messageIndexes) . "\n";
    echo "📋 Current conversation table indexes: " . count($conversationIndexes) . "\n\n";
    
    // Add performance indexes
    echo "🔧 Adding performance indexes...\n";
    
    $indexesAdded = 0;
    
    try {
        // Messages table indexes
        if ($driver === 'mysql') {
            // Check if index exists before creating
            $existingIndexes = collect($messageIndexes)->pluck('Key_name')->toArray();
            
            if (!in_array('idx_messages_conversation_created', $existingIndexes)) {
                DB::statement("CREATE INDEX idx_messages_conversation_created ON messages (conversation_id, created_at DESC)");
                echo "✅ Added conversation_id + created_at index to messages\n";
                $indexesAdded++;
            }
            
            if (!in_array('idx_messages_sender_created', $existingIndexes)) {
                DB::statement("CREATE INDEX idx_messages_sender_created ON messages (sender_id, created_at DESC)");
                echo "✅ Added sender_id + created_at index to messages\n";
                $indexesAdded++;
            }
            
            // Conversations table indexes
            $existingConvIndexes = collect($conversationIndexes)->pluck('Key_name')->toArray();
            
            if (!in_array('idx_conversations_patient_active', $existingConvIndexes)) {
                DB::statement("CREATE INDEX idx_conversations_patient_active ON conversations (patient_id, is_active)");
                echo "✅ Added patient_id + is_active index to conversations\n";
                $indexesAdded++;
            }
            
            if (!in_array('idx_conversations_admin_active', $existingConvIndexes)) {
                DB::statement("CREATE INDEX idx_conversations_admin_active ON conversations (admin_id, is_active)");
                echo "✅ Added admin_id + is_active index to conversations\n";
                $indexesAdded++;
            }
            
            if (!in_array('idx_conversations_last_message', $existingConvIndexes)) {
                DB::statement("CREATE INDEX idx_conversations_last_message ON conversations (last_message_at DESC)");
                echo "✅ Added last_message_at index to conversations\n";
                $indexesAdded++;
            }
            
        } elseif ($driver === 'pgsql') {
            // PostgreSQL syntax
            DB::statement("CREATE INDEX IF NOT EXISTS idx_messages_conversation_created ON messages (conversation_id, created_at DESC)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_messages_sender_created ON messages (sender_id, created_at DESC)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_conversations_patient_active ON conversations (patient_id, is_active)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_conversations_admin_active ON conversations (admin_id, is_active)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_conversations_last_message ON conversations (last_message_at DESC)");
            
            echo "✅ Added all PostgreSQL performance indexes\n";
            $indexesAdded = 5;
        }
        
    } catch (Exception $e) {
        echo "⚠️  Some indexes may already exist or failed to create: " . $e->getMessage() . "\n";
    }
    
    echo "\n🧪 Testing query performance...\n";
    
    // Test common queries used by messaging system
    $start = microtime(true);
    
    // Test 1: Get conversations for user (most common query)
    $testConversations = DB::table('conversations')
        ->where('is_active', true)
        ->orderBy('last_message_at', 'desc')
        ->limit(10)
        ->get();
    
    $conversationTime = round((microtime(true) - $start) * 1000, 2);
    echo "📊 Conversation query: {$conversationTime}ms (" . count($testConversations) . " results)\n";
    
    $start = microtime(true);
    
    // Test 2: Get messages for conversation (second most common)
    if (count($testConversations) > 0) {
        $testMessages = DB::table('messages')
            ->where('conversation_id', $testConversations[0]->id)
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();
        
        $messageTime = round((microtime(true) - $start) * 1000, 2);
        echo "📊 Messages query: {$messageTime}ms (" . count($testMessages) . " results)\n";
    }
    
    // Check for slow queries configuration
    echo "\n🔍 Checking database configuration...\n";
    
    if ($driver === 'mysql') {
        try {
            $slowQuery = DB::select("SHOW VARIABLES LIKE 'slow_query_log'")[0] ?? null;
            $slowQueryTime = DB::select("SHOW VARIABLES LIKE 'long_query_time'")[0] ?? null;
            
            if ($slowQuery) {
                echo "📋 Slow query log: " . $slowQuery->Value . "\n";
            }
            if ($slowQueryTime) {
                echo "📋 Long query time: " . $slowQueryTime->Value . "s\n";
            }
        } catch (Exception $e) {
            echo "⚠️  Could not check slow query settings\n";
        }
    }
    
    // Check email configuration that might be causing delays
    echo "\n📧 Checking email notification settings...\n";
    
    $mailDriver = config('mail.default');
    $queueConnection = config('queue.default');
    
    echo "📮 Mail driver: {$mailDriver}\n";
    echo "🔄 Queue connection: {$queueConnection}\n";
    
    if ($mailDriver === 'smtp' && $queueConnection === 'sync') {
        echo "⚠️  WARNING: SMTP emails are sent synchronously!\n";
        echo "💡 This could cause delays. Consider:\n";
        echo "   1. Using 'log' driver for testing: MAIL_MAILER=log\n";
        echo "   2. Using queue for emails: QUEUE_CONNECTION=database\n";
    }
    
    // Optimize Laravel configuration
    echo "\n⚙️  Optimizing Laravel configuration...\n";
    
    try {
        // Clear and cache configs for better performance
        Artisan::call('config:cache');
        echo "✅ Configuration cached\n";
        
        Artisan::call('route:cache');
        echo "✅ Routes cached\n";
        
        Artisan::call('view:cache');
        echo "✅ Views cached\n";
        
    } catch (Exception $e) {
        echo "⚠️  Some optimizations failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Performance optimization completed!\n";
    echo "📊 Indexes added: {$indexesAdded}\n";
    
    echo "\n💡 Additional recommendations:\n";
    echo "   1. Monitor message sending times in browser dev tools\n";
    echo "   2. Consider using database queues for email notifications\n";
    echo "   3. Enable database query logging temporarily to identify slow queries\n";
    echo "   4. Use Redis cache if available for better performance\n";
    
    if ($conversationTime > 100 || (isset($messageTime) && $messageTime > 100)) {
        echo "\n⚠️  Query times are still high. Consider:\n";
        echo "   - Database server performance (CPU/Memory)\n";
        echo "   - Network latency to database\n";
        echo "   - Database maintenance (OPTIMIZE TABLE for MySQL)\n";
    }
    
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n\n";
    
    echo "💡 Troubleshooting steps:\n";
    echo "  1. Check database connection\n";
    echo "  2. Verify table permissions\n";
    echo "  3. Check Laravel logs for details\n";
}

echo "\n✨ Performance optimization script completed!\n";