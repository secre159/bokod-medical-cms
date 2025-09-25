const mysql = require('mysql2/promise');

async function testConversationCreation() {
    const connection = await mysql.createConnection({
        host: '127.0.0.1',
        port: 3306,
        database: 'bokod_pms',
        user: 'root',
        password: '',
    });

    try {
        console.log('🔗 Connecting to MySQL database...');
        console.log('✅ Connected successfully!');

        console.log('\n🔧 Testing conversation creation...\n');

        // Check table structures first
        console.log('📋 Checking conversations table structure...');
        const [conversationsStructure] = await connection.execute(`
            DESCRIBE conversations
        `);
        
        console.log('Conversations table columns:');
        conversationsStructure.forEach(row => {
            console.log(`   ${row.Field}: ${row.Type}${row.Null === 'NO' ? ' NOT NULL' : ''}${row.Default ? ` DEFAULT ${row.Default}` : ''}`);
        });

        console.log('\n📋 Checking messages table structure...');
        const [messagesStructure] = await connection.execute(`
            DESCRIBE messages
        `);
        
        console.log('Messages table columns:');
        messagesStructure.forEach(row => {
            console.log(`   ${row.Field}: ${row.Type}${row.Null === 'NO' ? ' NOT NULL' : ''}${row.Default ? ` DEFAULT ${row.Default}` : ''}`);
        });

        // Get patient and admin users
        const [patients] = await connection.execute(`
            SELECT id, name, email FROM users 
            WHERE role = 'patient' AND status = 'active'
            LIMIT 1
        `);

        const [admins] = await connection.execute(`
            SELECT id, name, email FROM users 
            WHERE role = 'admin' AND status = 'active'
            LIMIT 1
        `);

        if (patients.length === 0) {
            console.log('❌ No active patient users found');
            return;
        }

        if (admins.length === 0) {
            console.log('❌ No active admin users found');
            return;
        }

        const patient = patients[0];
        const admin = admins[0];

        console.log(`\n📋 Testing conversation creation:`);
        console.log(`   Patient: ${patient.name} (ID: ${patient.id})`);
        console.log(`   Admin: ${admin.name} (ID: ${admin.id})`);

        // Check if conversation already exists
        const [existingConv] = await connection.execute(`
            SELECT id, patient_id, admin_id, is_active, created_at
            FROM conversations 
            WHERE patient_id = ? AND admin_id = ?
        `, [patient.id, admin.id]);

        if (existingConv.length > 0) {
            console.log(`✅ Conversation already exists (ID: ${existingConv[0].id})`);
        } else {
            console.log('🔧 Creating new conversation...');
            
            try {
                // Create conversation without 'type' column since it doesn't exist
                const [newConvResult] = await connection.execute(`
                    INSERT INTO conversations (
                        patient_id, admin_id, is_active, last_message_at, created_at, updated_at
                    ) VALUES (?, ?, true, NOW(), NOW(), NOW())
                `, [patient.id, admin.id]);

                const conversationId = newConvResult.insertId;
                console.log(`✅ Created conversation successfully (ID: ${conversationId})`);
                
                // Create welcome system message
                const [systemMsgResult] = await connection.execute(`
                    INSERT INTO messages (
                        conversation_id, sender_id, message, message_type, priority, is_read, is_system_message, created_at, updated_at
                    ) VALUES (?, ?, ?, 'system', 'normal', false, true, NOW(), NOW())
                `, [conversationId, admin.id, 'Conversation started. A medical staff member will respond to you shortly.']);

                console.log(`✅ Created system message (ID: ${systemMsgResult.insertId})`);

            } catch (createError) {
                console.error('❌ Failed to create conversation:', createError.message);
                console.error('Error details:', createError);
            }
        }

        // Final verification
        const [allConversations] = await connection.execute('SELECT COUNT(*) as count FROM conversations');
        const [allMessages] = await connection.execute('SELECT COUNT(*) as count FROM messages');
        
        console.log(`\n📊 Final counts:`);
        console.log(`   Conversations: ${allConversations[0].count}`);
        console.log(`   Messages: ${allMessages[0].count}`);

        console.log('\n🎯 Conversation creation test completed!');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Stack:', error.stack);
    } finally {
        await connection.end();
        console.log('\n🔒 Database connection closed.');
    }
}

testConversationCreation();