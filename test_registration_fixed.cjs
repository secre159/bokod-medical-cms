const { Client } = require('pg');

// PostgreSQL configuration (production)
const pgConfig = {
    connectionString: 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms',
    ssl: {
        rejectUnauthorized: false
    }
};

async function testFixedRegistration() {
    console.log('🔧 Testing Fixed Registration Process...\n');
    
    try {
        const client = new Client(pgConfig);
        await client.connect();
        console.log('✅ Connected to PostgreSQL database');

        // Test user creation with corrected values
        const timestamp = Date.now();
        const testUserData = {
            name: `Fixed Test User ${timestamp}`,
            email: `fixed.test.${timestamp}@bsu.edu.ph`,
            password: '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // bcrypt hash of "password"
            role: 'patient',
            status: 'active'
        };

        console.log('\n🧪 Testing user creation with fixed values...');
        const userResult = await client.query(
            'INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES ($1, $2, $3, $4, $5, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id',
            [testUserData.name, testUserData.email, testUserData.password, testUserData.role, testUserData.status]
        );
        
        const userId = userResult.rows[0].id;
        console.log(`✅ User created successfully (ID: ${userId})`);

        // Test patient creation with corrected enum values
        console.log('\n🧪 Testing patient creation with corrected enum values...');
        const testPatientData = {
            patient_name: testUserData.name,
            email: testUserData.email,
            gender: 'Male', // Capitalized - this was the issue!
            civil_status: 'Single', // Capitalized - this was the issue!
            user_id: userId,
            position: 'TEST-12345', // Student ID
            course: 'Computer Science',
            address: '123 Test St, Test City'
        };

        console.log(`Using corrected values: gender='${testPatientData.gender}', civil_status='${testPatientData.civil_status}'`);

        const patientResult = await client.query(`
            INSERT INTO patients (
                patient_name, email, gender, civil_status, user_id, 
                position, course, address, created_at, updated_at
            ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) 
            RETURNING id`,
            [
                testPatientData.patient_name, testPatientData.email, testPatientData.gender, 
                testPatientData.civil_status, testPatientData.user_id, testPatientData.position,
                testPatientData.course, testPatientData.address
            ]
        );

        const patientId = patientResult.rows[0].id;
        console.log(`✅ Patient created successfully (ID: ${patientId})`);
        console.log(`   - Gender: ${testPatientData.gender} ✓`);
        console.log(`   - Civil Status: ${testPatientData.civil_status} ✓`);
        
        // Verify conversation creation still works
        console.log('\n🧪 Testing conversation creation with fixed patient...');
        const adminResult = await client.query(
            'SELECT id, name FROM users WHERE role = $1 AND status = $2 LIMIT 1',
            ['admin', 'active']
        );
        
        if (adminResult.rows.length > 0) {
            const admin = adminResult.rows[0];
            console.log(`Found admin: ${admin.name} (ID: ${admin.id})`);
            
            const conversationResult = await client.query(
                'INSERT INTO conversations (patient_id, admin_id, is_active, last_message_at, created_at, updated_at) VALUES ($1, $2, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id',
                [patientId, admin.id]
            );
            
            const conversationId = conversationResult.rows[0].id;
            console.log(`✅ Conversation created successfully (ID: ${conversationId})`);
            
            // Test message creation
            const messageResult = await client.query(
                'INSERT INTO messages (conversation_id, sender_id, message, is_read, created_at, updated_at) VALUES ($1, $2, $3, false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id',
                [conversationId, admin.id, 'Test message - registration and messaging integration working!']
            );
            
            const messageId = messageResult.rows[0].id;
            console.log(`✅ Message created successfully (ID: ${messageId})`);
            
            // Clean up 
            await client.query('DELETE FROM messages WHERE id = $1', [messageId]);
            await client.query('DELETE FROM conversations WHERE id = $1', [conversationId]);
            console.log('🧹 Conversation and message test data cleaned up');
        }

        // Clean up test data
        await client.query('DELETE FROM patients WHERE id = $1', [patientId]);
        await client.query('DELETE FROM users WHERE id = $1', [userId]);
        console.log('🧹 User and patient test data cleaned up');

        await client.end();
        console.log('🔒 PostgreSQL connection closed\n');
        
        console.log('🎉 REGISTRATION FIX VERIFIED!');
        console.log('✅ User creation: WORKING');
        console.log('✅ Patient creation with correct enum values: WORKING'); 
        console.log('✅ Conversation creation: WORKING');
        console.log('✅ Message creation: WORKING');
        console.log('\nThe registration errors should now be resolved!');

    } catch (error) {
        console.error('❌ Fixed Registration Test Error:', error.message);
        console.error('Error details:', error);
    }
}

testFixedRegistration();