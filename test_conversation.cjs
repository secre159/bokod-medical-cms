const { Client } = require('pg');

const DATABASE_URL = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function testConversationCreation() {
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

        console.log('\n🔧 Testing conversation creation...\n');

        // Get patient records (not user records) and admin users
        const patients = await client.query(`
            SELECT p.id, p.patient_name as name, p.email, p.user_id
            FROM patients p
            JOIN users u ON p.user_id = u.id
            WHERE u.role = 'patient' AND u.status = 'active' AND p.archived = false
            LIMIT 1
        `);

        const admins = await client.query(`
            SELECT id, name, email FROM users 
            WHERE role = 'admin' AND status = 'active'
            LIMIT 1
        `);

        if (patients.rows.length === 0) {
            console.log('❌ No active patient records found');
            return;
        }

        if (admins.rows.length === 0) {
            console.log('❌ No active admin users found');
            return;
        }

        const patient = patients.rows[0];
        const admin = admins.rows[0];

        console.log(`📋 Testing conversation creation:`);
        console.log(`   Patient: ${patient.name} (ID: ${patient.id})`);
        console.log(`   Admin: ${admin.name} (ID: ${admin.id})`);

        // Check if conversation already exists
        const existingConv = await client.query(`
            SELECT id, patient_id, admin_id, is_active, created_at
            FROM conversations 
            WHERE patient_id = $1 AND admin_id = $2
        `, [patient.id, admin.id]);

        if (existingConv.rows.length > 0) {
            console.log(`✅ Conversation already exists (ID: ${existingConv.rows[0].id})`);
        } else {
            console.log('🔧 Creating new conversation...');
            
            try {
                const newConv = await client.query(`
                    INSERT INTO conversations (
                        patient_id, admin_id, is_active, last_message_at, created_at, updated_at
                    ) VALUES ($1, $2, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING id, patient_id, admin_id
                `, [patient.id, admin.id]);

                console.log(`✅ Created conversation successfully (ID: ${newConv.rows[0].id})`);
                
                // Create welcome system message
                const systemMsg = await client.query(`
                    INSERT INTO messages (
                        conversation_id, sender_id, message, is_read, created_at, updated_at
                    ) VALUES ($1, $2, $3, false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING id
                `, [newConv.rows[0].id, admin.id, 'Conversation started. A medical staff member will respond to you shortly.']);

                console.log(`✅ Created system message (ID: ${systemMsg.rows[0].id})`);

            } catch (createError) {
                console.error('❌ Failed to create conversation:', createError.message);
                console.error('Error details:', createError);
            }
        }

        // Final verification
        const allConversations = await client.query('SELECT COUNT(*) as count FROM conversations');
        const allMessages = await client.query('SELECT COUNT(*) as count FROM messages');
        
        console.log(`\n📊 Final counts:`);
        console.log(`   Conversations: ${allConversations.rows[0].count}`);
        console.log(`   Messages: ${allMessages.rows[0].count}`);

        console.log('\n🎯 Conversation creation test completed!');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Stack:', error.stack);
    } finally {
        await client.end();
        console.log('\n🔒 Database connection closed.');
    }
}

testConversationCreation();