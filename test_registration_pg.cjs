const { Client } = require('pg');

// PostgreSQL configuration (production)
const pgConfig = {
    connectionString: 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms',
    ssl: {
        rejectUnauthorized: false
    }
};

async function testPostgreSQLRegistration() {
    console.log('🔧 Testing PostgreSQL Registration (Production)...\n');
    
    try {
        const client = new Client(pgConfig);
        await client.connect();
        console.log('✅ Connected to PostgreSQL database');

        // Check users table structure
        console.log('\n📋 Users table structure:');
        const usersStructure = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_name = 'users' 
            ORDER BY ordinal_position;
        `);
        
        console.log('Users table columns:');
        usersStructure.rows.forEach(row => {
            console.log(`   ${row.column_name}: ${row.data_type}${row.is_nullable === 'NO' ? ' NOT NULL' : ''}${row.column_default ? ` DEFAULT ${row.column_default}` : ''}`);
        });

        // Check patients table structure
        console.log('\n📋 Patients table structure:');
        const patientsStructure = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_name = 'patients' 
            ORDER BY ordinal_position;
        `);
        
        console.log('Patients table columns:');
        patientsStructure.rows.forEach(row => {
            console.log(`   ${row.column_name}: ${row.data_type}${row.is_nullable === 'NO' ? ' NOT NULL' : ''}${row.column_default ? ` DEFAULT ${row.column_default}` : ''}`);
        });

        // Check foreign key constraints
        console.log('\n📋 Checking foreign key constraints:');
        const constraints = await client.query(`
            SELECT 
                tc.constraint_name,
                tc.table_name,
                kcu.column_name,
                ccu.table_name AS foreign_table_name,
                ccu.column_name AS foreign_column_name
            FROM information_schema.table_constraints AS tc
            JOIN information_schema.key_column_usage AS kcu
                ON tc.constraint_name = kcu.constraint_name
            JOIN information_schema.constraint_column_usage AS ccu
                ON ccu.constraint_name = tc.constraint_name
            WHERE tc.constraint_type = 'FOREIGN KEY' 
            AND (tc.table_name='patients' OR tc.table_name='users')
            ORDER BY tc.table_name, tc.constraint_name;
        `);
        
        constraints.rows.forEach(row => {
            console.log(`   ${row.table_name}.${row.column_name} -> ${row.foreign_table_name}.${row.foreign_column_name} (${row.constraint_name})`);
        });

        // Test user creation with minimal data
        const timestamp = Date.now();
        const testUserData = {
            name: `Test User ${timestamp}`,
            email: `test.${timestamp}@bsu.edu.ph`,
            password: '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // bcrypt hash of "password"
            role: 'patient',
            status: 'active'
        };

        console.log('\n🧪 Testing user creation...');
        const userResult = await client.query(
            'INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES ($1, $2, $3, $4, $5, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id',
            [testUserData.name, testUserData.email, testUserData.password, testUserData.role, testUserData.status]
        );
        
        const userId = userResult.rows[0].id;
        console.log(`✅ User created successfully (ID: ${userId})`);

        // Test patient creation
        console.log('\n🧪 Testing patient creation...');
        const testPatientData = {
            patient_name: testUserData.name,
            email: testUserData.email,
            gender: 'Male',
            civil_status: 'Single',
            user_id: userId
        };

        const patientResult = await client.query(
            'INSERT INTO patients (patient_name, email, gender, civil_status, user_id, created_at, updated_at) VALUES ($1, $2, $3, $4, $5, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id',
            [testPatientData.patient_name, testPatientData.email, testPatientData.gender, testPatientData.civil_status, testPatientData.user_id]
        );

        const patientId = patientResult.rows[0].id;
        console.log(`✅ Patient created successfully (ID: ${patientId})`);

        // Test the conversation creation process with the new patient
        console.log('\n🧪 Testing conversation creation with new patient...');
        
        // Find an admin user
        const adminResult = await client.query(
            'SELECT id, name FROM users WHERE role = $1 AND status = $2 LIMIT 1',
            ['admin', 'active']
        );
        
        if (adminResult.rows.length > 0) {
            const admin = adminResult.rows[0];
            console.log(`Found admin: ${admin.name} (ID: ${admin.id})`);
            
            // Try creating a conversation using the correct patient ID
            const conversationResult = await client.query(
                'INSERT INTO conversations (patient_id, admin_id, is_active, last_message_at, created_at, updated_at) VALUES ($1, $2, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) RETURNING id',
                [patientId, admin.id]
            );
            
            const conversationId = conversationResult.rows[0].id;
            console.log(`✅ Conversation created successfully (ID: ${conversationId})`);
            
            // Clean up conversation
            await client.query('DELETE FROM conversations WHERE id = $1', [conversationId]);
            console.log('🧹 Conversation test data cleaned up');
        } else {
            console.log('⚠️ No admin users found - skipping conversation test');
        }

        // Clean up test data
        await client.query('DELETE FROM patients WHERE id = $1', [patientId]);
        await client.query('DELETE FROM users WHERE id = $1', [userId]);
        console.log('🧹 Test data cleaned up');

        await client.end();
        console.log('🔒 PostgreSQL connection closed\n');
        return { success: true, message: 'PostgreSQL registration test passed' };

    } catch (error) {
        console.error('❌ PostgreSQL Registration Test Error:', error.message);
        console.error('Error details:', error);
        return { success: false, message: error.message, details: error };
    }
}

console.log('🔍 Testing Registration System for PostgreSQL (Production)\n');
console.log('=' .repeat(60));

testPostgreSQLRegistration().then(result => {
    console.log('=' .repeat(60));
    console.log('📊 Test Result:');
    console.log(`PostgreSQL (Production): ${result.success ? '✅ PASS' : '❌ FAIL'} - ${result.message}`);
    
    if (!result.success) {
        console.log('\n⚠️  Registration issues detected!');
        console.log('Error:', result.message);
        if (result.details) {
            console.log('Details:', result.details);
        }
    } else {
        console.log('\n🎉 Registration test passed! Registration should work correctly on production.');
    }
});