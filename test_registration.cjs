const mysql = require('mysql2/promise');
const { Client } = require('pg');

// MySQL configuration (local)
const mysqlConfig = {
    host: '127.0.0.1',
    port: 3306,
    database: 'bokod_pms',
    user: 'root',
    password: '',
};

// PostgreSQL configuration (production)
const pgConfig = {
    connectionString: 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms',
    ssl: {
        rejectUnauthorized: false
    }
};

async function testMySQLRegistration() {
    console.log('🔧 Testing MySQL Registration (Local Development)...\n');
    
    try {
        const connection = await mysql.createConnection(mysqlConfig);
        console.log('✅ Connected to MySQL database');

        // Check users table structure
        console.log('\n📋 Users table structure:');
        const [usersStructure] = await connection.execute('DESCRIBE users');
        usersStructure.forEach(row => {
            console.log(`   ${row.Field}: ${row.Type}${row.Null === 'NO' ? ' NOT NULL' : ''}${row.Default ? ` DEFAULT ${row.Default}` : ''}`);
        });

        // Check patients table structure
        console.log('\n📋 Patients table structure:');
        const [patientsStructure] = await connection.execute('DESCRIBE patients');
        patientsStructure.forEach(row => {
            console.log(`   ${row.Field}: ${row.Type}${row.Null === 'NO' ? ' NOT NULL' : ''}${row.Default ? ` DEFAULT ${row.Default}` : ''}`);
        });

        // Test user creation with minimal data
        const testUserData = {
            name: 'Test User MySQL',
            email: `test.mysql.${Date.now()}@bsu.edu.ph`,
            password: 'hashed_password_here',
            role: 'patient',
            status: 'active'
        };

        console.log('\n🧪 Testing user creation...');
        const [userResult] = await connection.execute(
            'INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            [testUserData.name, testUserData.email, testUserData.password, testUserData.role, testUserData.status]
        );
        
        const userId = userResult.insertId;
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

        const [patientResult] = await connection.execute(
            'INSERT INTO patients (patient_name, email, gender, civil_status, user_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            [testPatientData.patient_name, testPatientData.email, testPatientData.gender, testPatientData.civil_status, testPatientData.user_id]
        );

        const patientId = patientResult.insertId;
        console.log(`✅ Patient created successfully (ID: ${patientId})`);

        // Clean up test data
        await connection.execute('DELETE FROM patients WHERE id = ?', [patientId]);
        await connection.execute('DELETE FROM users WHERE id = ?', [userId]);
        console.log('🧹 Test data cleaned up');

        await connection.end();
        console.log('🔒 MySQL connection closed\n');
        return { success: true, message: 'MySQL registration test passed' };

    } catch (error) {
        console.error('❌ MySQL Registration Test Error:', error.message);
        return { success: false, message: error.message, stack: error.stack };
    }
}

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
        
        patientsStructure.rows.forEach(row => {
            console.log(`   ${row.column_name}: ${row.data_type}${row.is_nullable === 'NO' ? ' NOT NULL' : ''}${row.column_default ? ` DEFAULT ${row.column_default}` : ''}`);
        });

        // Test user creation with minimal data
        const testUserData = {
            name: 'Test User PostgreSQL',
            email: `test.postgresql.${Date.now()}@bsu.edu.ph`,
            password: 'hashed_password_here',
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

async function runRegistrationTests() {
    console.log('🔍 Running Registration System Tests\n');
    console.log('=' .repeat(60));
    
    const mysqlResult = await testMySQLRegistration();
    console.log('=' .repeat(60));
    const pgResult = await testPostgreSQLRegistration();
    console.log('=' .repeat(60));
    
    console.log('📊 Test Results Summary:');
    console.log(`MySQL (Local): ${mysqlResult.success ? '✅ PASS' : '❌ FAIL'} - ${mysqlResult.message}`);
    console.log(`PostgreSQL (Production): ${pgResult.success ? '✅ PASS' : '❌ FAIL'} - ${pgResult.message}`);
    
    if (!mysqlResult.success || !pgResult.success) {
        console.log('\n⚠️  Registration issues detected! Check the error messages above for details.');
        
        if (!mysqlResult.success) {
            console.log('MySQL Error:', mysqlResult.message);
        }
        
        if (!pgResult.success) {
            console.log('PostgreSQL Error:', pgResult.message);
        }
    } else {
        console.log('\n🎉 All registration tests passed! Registration should work correctly.');
    }
}

runRegistrationTests();