const { Client } = require('pg');

const DATABASE_URL = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function testMessageCreation() {
    const client = new Client({
        connectionString: DATABASE_URL,
        ssl: {
            rejectUnauthorized: false
        }
    });

    try {
        console.log('🔗 Connecting to PostgreSQL database...');
        await client.connect();
        console.log('✅ Connected successfully!');

        console.log('\n📋 Testing message creation for existing conversation...\n');

        // Find an existing conversation
        const conversations = await client.query(`
            SELECT id, patient_id, admin_id 
            FROM conversations 
            ORDER BY id DESC 
            LIMIT 1
        `);

        if (conversations.rows.length === 0) {
            console.log('❌ No conversations found');
            return;
        }

        const conversation = conversations.rows[0];
        console.log(`Found conversation ID: ${conversation.id}`);

        // Get admin info
        const admin = await client.query(`
            SELECT id, name FROM users 
            WHERE id = $1
        `, [conversation.admin_id]);

        if (admin.rows.length === 0) {
            console.log('❌ Admin user not found');
            return;
        }

        console.log(`Admin: ${admin.rows[0].name} (ID: ${admin.rows[0].id})`);

        // Check existing messages
        const existingMessages = await client.query(`
            SELECT COUNT(*) as count FROM messages 
            WHERE conversation_id = $1
        `, [conversation.id]);

        console.log(`Existing messages in conversation: ${existingMessages.rows[0].count}`);

        // Create a welcome system message
        console.log('🔧 Creating system message...');
        
        try {
            const systemMsg = await client.query(`
                INSERT INTO messages (
                    conversation_id, sender_id, message, is_read, created_at, updated_at
                ) VALUES ($1, $2, $3, false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                RETURNING id
            `, [conversation.id, admin.rows[0].id, 'Welcome! A medical staff member will respond to you shortly.']);

            console.log(`✅ Created system message successfully (ID: ${systemMsg.rows[0].id})`);

        } catch (createError) {
            console.error('❌ Failed to create message:', createError.message);
            console.error('Error details:', createError);
        }

        // Final verification
        const finalMessageCount = await client.query(`
            SELECT COUNT(*) as count FROM messages 
            WHERE conversation_id = $1
        `, [conversation.id]);

        console.log(`\n📊 Final message count for conversation ${conversation.id}: ${finalMessageCount.rows[0].count}`);

        // Show recent messages
        const recentMessages = await client.query(`
            SELECT id, sender_id, message, is_read, created_at
            FROM messages 
            WHERE conversation_id = $1
            ORDER BY created_at DESC
            LIMIT 3
        `, [conversation.id]);

        console.log('\n📋 Recent messages:');
        recentMessages.rows.forEach(msg => {
            console.log(`   ID: ${msg.id}, Sender: ${msg.sender_id}, Read: ${msg.is_read}`);
            console.log(`   Message: "${msg.message}"`);
            console.log(`   Created: ${msg.created_at}\n`);
        });

        console.log('🎯 Message creation test completed!');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Stack:', error.stack);
    } finally {
        await client.end();
        console.log('\n🔒 Database connection closed.');
    }
}

testMessageCreation();