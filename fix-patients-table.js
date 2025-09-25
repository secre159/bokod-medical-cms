import { Client } from 'pg';

const connectionString = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function fixPatientsTable() {
    const client = new Client({
        connectionString: connectionString,
        ssl: {
            rejectUnauthorized: false
        }
    });

    try {
        console.log('🔍 Checking patients table structure...\n');
        await client.connect();

        // Check current patients table structure
        const patientsColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'patients' 
            ORDER BY ordinal_position;
        `);

        console.log('📊 Current patients table columns:');
        const existingColumns = patientsColumns.rows.map(row => row.column_name);
        existingColumns.forEach(col => console.log(`  - ${col}`));

        // Check what columns the code is trying to insert
        const requiredColumns = [
            'patient_name',
            'position', 
            'course',
            'date_of_birth',
            'gender',
            'phone_number',
            'email',
            'address',
            'emergency_contact_name',
            'emergency_contact_relationship', 
            'emergency_contact_phone',
            'emergency_contact_address',
            'height',
            'weight',
            'bmi',
            'civil_status',
            'user_id',
            'archived'
        ];

        console.log('\n🔍 Checking required columns:');
        const missingColumns = [];
        requiredColumns.forEach(col => {
            const exists = existingColumns.includes(col);
            console.log(`  ${col}: ${exists ? '✅ EXISTS' : '❌ MISSING'}`);
            if (!exists) {
                missingColumns.push(col);
            }
        });

        if (missingColumns.length > 0) {
            console.log('\n🔧 Adding missing columns to patients table...');
            
            const columnDefinitions = {
                'emergency_contact_name': 'VARCHAR(255) NULL',
                'emergency_contact_relationship': 'VARCHAR(100) NULL', 
                'emergency_contact_phone': 'VARCHAR(20) NULL',
                'emergency_contact_address': 'TEXT NULL',
                'height': 'NUMERIC NULL',
                'weight': 'NUMERIC NULL',
                'bmi': 'NUMERIC NULL'
            };

            for (const column of missingColumns) {
                if (columnDefinitions[column]) {
                    try {
                        console.log(`  Adding column: ${column}`);
                        await client.query(`ALTER TABLE patients ADD COLUMN IF NOT EXISTS ${column} ${columnDefinitions[column]}`);
                        console.log(`  ✅ ${column} added successfully`);
                    } catch (error) {
                        console.error(`  ❌ Error adding ${column}:`, error.message);
                    }
                }
            }
        }

        // Verify the final structure
        console.log('\n🔍 Verifying final patients table structure...');
        const finalColumns = await client.query(`
            SELECT column_name, data_type, is_nullable 
            FROM information_schema.columns 
            WHERE table_name = 'patients' 
            ORDER BY ordinal_position;
        `);

        console.log('Final patients table columns:');
        finalColumns.rows.forEach(row => {
            console.log(`  - ${row.column_name} (${row.data_type}) ${row.is_nullable === 'YES' ? 'NULL' : 'NOT NULL'}`);
        });

        // Test patient creation
        console.log('\n🧪 Testing patient creation...');
        const testPatientData = {
            patient_name: 'Test Patient',
            email: 'test-patient@example.com', 
            phone_number: '1234567890',
            position: 'TEST-002',
            course: 'Test Course',
            date_of_birth: '2000-01-01',
            gender: 'Male',
            address: 'Test Address',
            emergency_contact_name: 'Emergency Contact',
            emergency_contact_relationship: 'Parent',
            emergency_contact_phone: '9876543210',
            emergency_contact_address: 'Emergency Address',
            height: 170.5,
            weight: 65.0,
            bmi: 22.5,
            civil_status: 'Single',
            user_id: 1, // Assuming user ID 1 exists
            archived: false
        };

        // Clean up any existing test data
        await client.query('DELETE FROM patients WHERE email = $1', [testPatientData.email]);

        // Build insert query dynamically
        const finalColumnsList = finalColumns.rows.map(row => row.column_name);
        const availableFields = Object.keys(testPatientData).filter(field => finalColumnsList.includes(field));
        const values = availableFields.map(field => testPatientData[field]);
        const placeholders = availableFields.map((_, index) => `$${index + 1}`);

        const insertQuery = `
            INSERT INTO patients (${availableFields.join(', ')}, created_at, updated_at) 
            VALUES (${placeholders.join(', ')}, NOW(), NOW()) 
            RETURNING id;
        `;

        console.log('Testing insert query:', insertQuery);
        const result = await client.query(insertQuery, values);
        console.log('✅ Patient creation test successful! Patient ID:', result.rows[0].id);

        // Clean up test data
        await client.query('DELETE FROM patients WHERE id = $1', [result.rows[0].id]);
        console.log('✅ Test data cleaned up');

        console.log('\n🎉 Patients table fix completed successfully!');
        
    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Full error:', error);
    } finally {
        await client.end();
    }
}

console.log('🚀 Starting patients table fix...');
fixPatientsTable();