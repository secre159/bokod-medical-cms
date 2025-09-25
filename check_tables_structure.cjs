const { Client } = require('pg');

const DATABASE_URL = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function checkTablesStructure() {
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

        console.log('\n📋 Checking users table structure...');
        const usersColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_name = 'users' 
            ORDER BY ordinal_position;
        `);
        
        console.log('Users table columns:');
        usersColumns.rows.forEach(row => {
            console.log(`   ${row.column_name}: ${row.data_type}${row.is_nullable === 'NO' ? ' NOT NULL' : ''}${row.column_default ? ` DEFAULT ${row.column_default}` : ''}`);
        });

        console.log('\n📋 Checking patients table structure...');
        const patientsColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_name = 'patients' 
            ORDER BY ordinal_position;
        `);
        
        console.log('Patients table columns:');
        patientsColumns.rows.forEach(row => {
            console.log(`   ${row.column_name}: ${row.data_type}${row.is_nullable === 'NO' ? ' NOT NULL' : ''}${row.column_default ? ` DEFAULT ${row.column_default}` : ''}`);
        });

        console.log('\n📋 Checking conversations table constraints...');
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
            WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_name='conversations';
        `);
        
        console.log('Conversations table foreign keys:');
        constraints.rows.forEach(row => {
            console.log(`   ${row.constraint_name}: ${row.column_name} -> ${row.foreign_table_name}.${row.foreign_column_name}`);
        });

        console.log('\n📊 Data counts:');
        const userCounts = await client.query(`
            SELECT role, status, COUNT(*) as count 
            FROM users 
            GROUP BY role, status
        `);
        
        console.log('Users by role and status:');
        userCounts.rows.forEach(row => {
            console.log(`   ${row.role}/${row.status}: ${row.count}`);
        });

        const patientCount = await client.query('SELECT COUNT(*) as count FROM patients');
        console.log(`   Patients table: ${patientCount.rows[0].count}`);

        console.log('\n📋 Sample data comparison:');
        const sampleUsers = await client.query(`
            SELECT id, name, email, role FROM users 
            WHERE role = 'patient' AND status = 'active'
            LIMIT 3
        `);
        
        console.log('Active patient users:');
        sampleUsers.rows.forEach(row => {
            console.log(`   User ID: ${row.id}, Name: ${row.name}, Email: ${row.email}`);
        });

        const samplePatients = await client.query(`
            SELECT id, user_id, patient_name, email 
            FROM patients 
            LIMIT 3
        `);
        
        console.log('Patients records:');
        if (samplePatients.rows.length === 0) {
            console.log('   No patient records found!');
        } else {
            samplePatients.rows.forEach(row => {
                console.log(`   Patient ID: ${row.id}, User ID: ${row.user_id}, Name: ${row.patient_name}, Email: ${row.email}`);
            });
        }

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Stack:', error.stack);
    } finally {
        await client.end();
        console.log('\n🔒 Database connection closed.');
    }
}

checkTablesStructure();