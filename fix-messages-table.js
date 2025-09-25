import { Client } from 'pg';

const connectionString = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function fixMessagesTable() {
    const client = new Client({
        connectionString: connectionString,
        ssl: {
            rejectUnauthorized: false
        }
    });

    try {
        console.log('🔍 Checking messages table structure...\n');
        await client.connect();

        // Check if messages table exists
        const tablesCheck = await client.query(`
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name IN ('messages', 'conversations');
        `);

        console.log('📊 Available messaging tables:');
        tablesCheck.rows.forEach(row => {
            console.log(`  - ${row.table_name}`);
        });

        if (tablesCheck.rows.length === 0) {
            console.log('\n⚠️  No messaging tables found. Chat feature may not be set up yet.');
            return;
        }

        // Check messages table structure
        const messagesColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'messages' 
            ORDER BY ordinal_position;
        `);

        console.log('\n📋 Messages table columns:');
        const existingColumns = messagesColumns.rows.map(row => row.column_name);
        existingColumns.forEach(col => console.log(`  - ${col}`));

        // Check if reactions column exists
        const hasReactions = existingColumns.includes('reactions');
        console.log(`\n🔍 Reactions column: ${hasReactions ? '✅ EXISTS' : '❌ MISSING'}`);

        if (!hasReactions) {
            console.log('\n🔧 Adding reactions column to messages table...');
            try {
                await client.query(`
                    ALTER TABLE messages 
                    ADD COLUMN IF NOT EXISTS reactions JSONB NULL;
                `);
                console.log('✅ Reactions column added successfully');
            } catch (error) {
                console.error('❌ Error adding reactions column:', error.message);
            }
        }

        // Verify the final structure
        console.log('\n🔍 Verifying messages table structure...');
        const finalColumns = await client.query(`
            SELECT column_name, data_type, is_nullable 
            FROM information_schema.columns 
            WHERE table_name = 'messages' 
            ORDER BY ordinal_position;
        `);

        console.log('Final messages table columns:');
        finalColumns.rows.forEach(row => {
            console.log(`  - ${row.column_name} (${row.data_type}) ${row.is_nullable === 'YES' ? 'NULL' : 'NOT NULL'}`);
        });

        // Test if we can query messages table
        console.log('\n🧪 Testing messages table access...');
        const messageCount = await client.query('SELECT COUNT(*) as count FROM messages');
        console.log(`✅ Messages table accessible - ${messageCount.rows[0].count} messages found`);

        // Check conversations table too
        const conversationCount = await client.query('SELECT COUNT(*) as count FROM conversations');
        console.log(`✅ Conversations table accessible - ${conversationCount.rows[0].count} conversations found`);

        console.log('\n🎉 Messages table structure verified and ready!');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Full error:', error);
    } finally {
        await client.end();
    }
}

console.log('🚀 Starting messages table fix...');
fixMessagesTable();