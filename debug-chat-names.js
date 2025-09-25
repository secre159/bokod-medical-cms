import { Client } from 'pg';

const connectionString = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function debugChatNames() {
    const client = new Client({
        connectionString: connectionString,
        ssl: {
            rejectUnauthorized: false
        }
    });

    try {
        console.log('🔍 Debugging chat names and user data...\n');
        await client.connect();

        // Check users table
        console.log('👥 Users in the system:');
        const users = await client.query(`
            SELECT id, name, display_name, email, role, status, profile_picture 
            FROM users 
            ORDER BY id;
        `);
        
        users.rows.forEach(user => {
            console.log(`  - ID: ${user.id}, Name: "${user.name}", Display: "${user.display_name || 'N/A'}", Email: ${user.email}, Role: ${user.role}, Status: ${user.status}`);
        });

        // Check patients table  
        console.log('\n🏥 Patients in the system:');
        const patients = await client.query(`
            SELECT id, patient_name, email, user_id, position
            FROM patients 
            ORDER BY id;
        `);
        
        patients.rows.forEach(patient => {
            console.log(`  - ID: ${patient.id}, Name: "${patient.patient_name}", Email: ${patient.email}, User ID: ${patient.user_id}, Position: ${patient.position}`);
        });

        // Check conversations
        console.log('\n💬 Conversations:');
        const conversations = await client.query(`
            SELECT c.id, c.patient_id, c.admin_id, c.type, c.is_active,
                   p.patient_name as patient_name, p.position as patient_position,
                   u.name as admin_name
            FROM conversations c
            LEFT JOIN patients p ON c.patient_id = p.id
            LEFT JOIN users u ON c.admin_id = u.id
            ORDER BY c.id;
        `);
        
        conversations.rows.forEach(conv => {
            console.log(`  - Conv ID: ${conv.id}, Patient: "${conv.patient_name}" (ID: ${conv.patient_id}), Admin: "${conv.admin_name}" (ID: ${conv.admin_id}), Type: ${conv.type}`);
        });

        // Check messages and their senders
        console.log('\n📨 Messages and senders:');
        const messages = await client.query(`
            SELECT m.id, m.conversation_id, m.sender_id, m.message,
                   u.name as sender_name, u.role as sender_role,
                   SUBSTRING(m.message, 1, 50) as message_preview
            FROM messages m
            LEFT JOIN users u ON m.sender_id = u.id
            ORDER BY m.id;
        `);
        
        messages.rows.forEach(msg => {
            console.log(`  - Msg ID: ${msg.id}, Sender: "${msg.sender_name}" (ID: ${msg.sender_id}, Role: ${msg.sender_role}), Conv: ${msg.conversation_id}`);
            console.log(`    Preview: "${msg.message_preview}..."`);
        });

        // Check for users with missing names
        console.log('\n⚠️  Users with missing/empty names:');
        const usersWithoutNames = await client.query(`
            SELECT id, name, display_name, email, role 
            FROM users 
            WHERE name IS NULL OR name = '' OR TRIM(name) = '';
        `);
        
        if (usersWithoutNames.rows.length > 0) {
            console.log('Found users with missing names:');
            usersWithoutNames.rows.forEach(user => {
                console.log(`  - ID: ${user.id}, Name: "${user.name}", Email: ${user.email}, Role: ${user.role}`);
            });
        } else {
            console.log('✅ All users have names');
        }

        // Check for profile pictures
        console.log('\n🖼️  Profile picture status:');
        const profilePictureStats = await client.query(`
            SELECT 
                COUNT(*) as total_users,
                COUNT(profile_picture) as users_with_pics,
                COUNT(*) - COUNT(profile_picture) as users_without_pics
            FROM users;
        `);
        
        const stats = profilePictureStats.rows[0];
        console.log(`  - Total users: ${stats.total_users}`);
        console.log(`  - Users with profile pictures: ${stats.users_with_pics}`);
        console.log(`  - Users without profile pictures: ${stats.users_without_pics}`);

        // Test a specific message query like the controller does
        console.log('\n🧪 Testing message query with sender relationship:');
        const testQuery = await client.query(`
            SELECT m.id, m.message, m.sender_id,
                   u.name as sender_name, u.display_name, u.profile_picture
            FROM messages m
            LEFT JOIN users u ON m.sender_id = u.id
            WHERE m.conversation_id = (SELECT id FROM conversations LIMIT 1)
            ORDER BY m.created_at ASC;
        `);
        
        if (testQuery.rows.length > 0) {
            console.log('Messages with sender data:');
            testQuery.rows.forEach(msg => {
                console.log(`  - Message: "${msg.message.substring(0, 30)}...", Sender: "${msg.sender_name}", Profile: ${msg.profile_picture ? 'Has pic' : 'No pic'}`);
            });
        } else {
            console.log('No messages found in conversations');
        }

        console.log('\n🎉 Chat names debug completed!');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Full error:', error);
    } finally {
        await client.end();
    }
}

console.log('🚀 Starting chat names debug...');
debugChatNames();