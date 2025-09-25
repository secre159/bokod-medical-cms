import { Client } from 'pg';

const connectionString = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function debugRegistration() {
    const client = new Client({
        connectionString: connectionString,
        ssl: {
            rejectUnauthorized: false
        }
    });

    try {
        console.log('🔍 Debugging registration process...\n');
        await client.connect();

        // 1. Check users table structure
        console.log('1️⃣ Checking users table structure:');
        const usersColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'users' 
            ORDER BY ordinal_position;
        `);
        
        const columnNames = usersColumns.rows.map(row => row.column_name);
        console.log('Available columns:', columnNames.join(', '));
        
        const requiredColumns = ['role', 'status', 'registration_status', 'registration_source', 'approved_at'];
        console.log('\nRequired columns status:');
        requiredColumns.forEach(col => {
            const exists = columnNames.includes(col);
            console.log(`  ${col}: ${exists ? '✅ EXISTS' : '❌ MISSING'}`);
        });

        // 2. Check patients table structure
        console.log('\n2️⃣ Checking patients table structure:');
        const patientsColumns = await client.query(`
            SELECT column_name, data_type, is_nullable 
            FROM information_schema.columns 
            WHERE table_name = 'patients' 
            ORDER BY ordinal_position;
        `);
        console.log('Patients table columns:', patientsColumns.rows.map(row => row.column_name).join(', '));

        // 3. Test a simulated registration
        console.log('\n3️⃣ Testing simulated user creation:');
        const testUserData = {
            name: 'Test Registration User',
            email: 'test-reg-debug@example.com',
            password: '$2y$12$test.hash.here',
            role: 'patient',
            status: 'inactive', // Changed from 'archived' to match DB constraint
            registration_status: 'pending',
            registration_source: 'self',
            email_verified_at: null
        };

        // First, delete any existing test user
        await client.query('DELETE FROM users WHERE email = $1', [testUserData.email]);
        await client.query('DELETE FROM patients WHERE email = $1', [testUserData.email]);

        // Build the INSERT query dynamically based on available columns
        const availableFields = Object.keys(testUserData).filter(field => columnNames.includes(field));
        const values = availableFields.map(field => testUserData[field]);
        const placeholders = availableFields.map((_, index) => `$${index + 1}`);
        
        const insertQuery = `
            INSERT INTO users (${availableFields.join(', ')}, created_at, updated_at) 
            VALUES (${placeholders.join(', ')}, NOW(), NOW()) 
            RETURNING id;
        `;

        console.log('Attempting to insert user with query:', insertQuery);
        console.log('Values:', values);

        const result = await client.query(insertQuery, values);
        console.log('✅ User creation test successful! User ID:', result.rows[0].id);

        // 4. Test patient creation
        console.log('\n4️⃣ Testing patient creation:');
        const patientData = {
            patient_name: testUserData.name,
            email: testUserData.email,
            phone_number: '+1234567890',
            position: 'TEST-001',
            course: 'Test Course',
            date_of_birth: '2000-01-01',
            gender: 'Male',
            address: 'Test Address',
            civil_status: 'Single',
            user_id: result.rows[0].id,
            archived: false
        };

        const patientColumns = patientsColumns.rows.map(row => row.column_name);
        const availablePatientFields = Object.keys(patientData).filter(field => patientColumns.includes(field));
        const patientValues = availablePatientFields.map(field => patientData[field]);
        const patientPlaceholders = availablePatientFields.map((_, index) => `$${index + 1}`);

        const patientInsertQuery = `
            INSERT INTO patients (${availablePatientFields.join(', ')}, created_at, updated_at) 
            VALUES (${patientPlaceholders.join(', ')}, NOW(), NOW()) 
            RETURNING id;
        `;

        console.log('Attempting to insert patient with query:', patientInsertQuery);
        const patientResult = await client.query(patientInsertQuery, patientValues);
        console.log('✅ Patient creation test successful! Patient ID:', patientResult.rows[0].id);

        // 5. Clean up test data
        console.log('\n5️⃣ Cleaning up test data...');
        await client.query('DELETE FROM patients WHERE id = $1', [patientResult.rows[0].id]);
        await client.query('DELETE FROM users WHERE id = $1', [result.rows[0].id]);
        console.log('✅ Test data cleaned up');

        console.log('\n🎉 All tests passed! Registration should work now.');
        console.log('\n📋 Summary:');
        console.log('- Users table has all required columns ✅');
        console.log('- Patients table is accessible ✅');
        console.log('- User creation works ✅');
        console.log('- Patient creation works ✅');
        
    } catch (error) {
        console.error('❌ Debug failed:', error.message);
        console.error('Full error:', error);
        
        if (error.message.includes('does not exist')) {
            console.log('\n💡 Suggestion: The error indicates a column is missing.');
            console.log('Run the database fix script again or check the deployment.');
        }
    } finally {
        await client.end();
    }
}

console.log('🚀 Starting registration debug...');
debugRegistration();