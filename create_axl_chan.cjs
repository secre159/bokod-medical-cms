const { Client } = require('pg');
const bcrypt = require('bcrypt');

const DATABASE_URL = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function createAxlChan() {
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

        console.log('\n👤 Creating Axl Chan user and patient record...');

        // User details
        const userData = {
            name: 'Axl Chan',
            email: 'axl.chan@bsu.edu.ph',
            password: 'axlchan123',
            role: 'patient'
        };

        // Patient details  
        const patientData = {
            patient_name: 'Axl Chan',
            gender: 'Male',
            address: 'Benguet State University Campus',
            course: 'Computer Science',
            civil_status: 'Single',
            phone_number: '+63-912-345-6789',
            email: 'axl.chan@bsu.edu.ph',
            date_of_birth: '2000-01-15'
        };

        console.log('\n📧 User Details:');
        console.log(`   Name: ${userData.name}`);
        console.log(`   Email: ${userData.email}`);
        console.log(`   Password: ${userData.password}`);
        console.log(`   Role: ${userData.role}`);

        // Hash the password
        console.log('\n🔐 Hashing password...');
        const saltRounds = 10;
        const hashedPassword = await bcrypt.hash(userData.password, saltRounds);
        const laravelCompatibleHash = hashedPassword.replace('$2b$', '$2y$');

        // Check if user already exists
        const existingUser = await client.query('SELECT id, email FROM users WHERE email = $1', [userData.email]);

        let userId;
        if (existingUser.rows.length > 0) {
            console.log('\n⚠️  User already exists! Updating password...');
            await client.query(`
                UPDATE users 
                SET password = $1, name = $2, role = $3, updated_at = CURRENT_TIMESTAMP 
                WHERE email = $4
            `, [laravelCompatibleHash, userData.name, userData.role, userData.email]);
            
            userId = existingUser.rows[0].id;
            console.log(`✅ Updated existing user (ID: ${userId})`);
        } else {
            // Create new user
            const result = await client.query(`
                INSERT INTO users (
                    name, email, password, role, status, registration_status, registration_source,
                    email_verified_at, created_at, updated_at
                ) VALUES ($1, $2, $3, $4, 'active', 'approved', 'admin', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                RETURNING id
            `, [userData.name, userData.email, laravelCompatibleHash, userData.role]);
            
            userId = result.rows[0].id;
            console.log(`✅ Created new user (ID: ${userId})`);
        }

        // Check if patient record exists
        const existingPatient = await client.query('SELECT id, patient_name FROM patients WHERE user_id = $1', [userId]);

        if (existingPatient.rows.length > 0) {
            console.log('\n⚠️  Patient record already exists! Updating details...');
            await client.query(`
                UPDATE patients 
                SET patient_name = $1, gender = $2, address = $3, course = $4, civil_status = $5, 
                    phone_number = $6, email = $7, date_of_birth = $8, updated_at = CURRENT_TIMESTAMP
                WHERE user_id = $9
            `, [patientData.patient_name, patientData.gender, patientData.address, patientData.course,
                patientData.civil_status, patientData.phone_number, patientData.email, 
                patientData.date_of_birth, userId]);
            
            console.log(`✅ Updated patient record for ${patientData.patient_name}`);
        } else {
            // Create patient record
            const patientResult = await client.query(`
                INSERT INTO patients (
                    patient_name, gender, address, course, civil_status, phone_number, email, 
                    date_of_birth, user_id, archived, created_at, updated_at
                ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, FALSE, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                RETURNING id
            `, [patientData.patient_name, patientData.gender, patientData.address, patientData.course,
                patientData.civil_status, patientData.phone_number, patientData.email, 
                patientData.date_of_birth, userId]);
            
            console.log(`✅ Created patient record (ID: ${patientResult.rows[0].id})`);
        }

        // Show final details
        const userDetails = await client.query(`
            SELECT u.id, u.name, u.email, u.role, u.status, u.registration_status,
                   p.id as patient_id, p.patient_name, p.gender, p.course, p.civil_status
            FROM users u
            LEFT JOIN patients p ON u.id = p.user_id
            WHERE u.email = $1
        `, [userData.email]);

        if (userDetails.rows.length > 0) {
            const user = userDetails.rows[0];
            console.log('\n📋 Complete User & Patient Details:');
            console.log('==========================================');
            console.log(`👤 User ID: ${user.id}`);
            console.log(`📧 Email: ${user.email}`);
            console.log(`🔑 Password: ${userData.password}`);
            console.log(`👨 Name: ${user.name}`);
            console.log(`🎭 Role: ${user.role}`);
            console.log(`✅ Status: ${user.status}`);
            console.log(`📝 Registration: ${user.registration_status}`);
            console.log(`🏥 Patient ID: ${user.patient_id}`);
            console.log(`👤 Patient Name: ${user.patient_name}`);
            console.log(`⚧️ Gender: ${user.gender}`);
            console.log(`🎓 Course: ${user.course}`);
            console.log(`💑 Civil Status: ${user.civil_status}`);
            console.log('==========================================');
        }

        // Update statistics
        const stats = {
            totalUsers: await client.query('SELECT COUNT(*) as count FROM users'),
            activeUsers: await client.query('SELECT COUNT(*) as count FROM users WHERE status = \'active\''),
            patients: await client.query('SELECT COUNT(*) as count FROM patients WHERE archived = false'),
            patientUsers: await client.query('SELECT COUNT(*) as count FROM users WHERE role = \'patient\'')
        };

        console.log('\n📊 Updated Database Statistics:');
        console.log(`   Total Users: ${stats.totalUsers.rows[0].count}`);
        console.log(`   Active Users: ${stats.activeUsers.rows[0].count}`);
        console.log(`   Patient Users: ${stats.patientUsers.rows[0].count}`);
        console.log(`   Patient Records: ${stats.patients.rows[0].count}`);

        console.log('\n🎉 SUCCESS! Axl Chan has been added to the system!');
        console.log(`🌐 Login credentials:`);
        console.log(`   📧 Email: ${userData.email}`);
        console.log(`   🔑 Password: ${userData.password}`);
        console.log('\n🔗 Login URL: https://bokod-medical-cms.onrender.com/login');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Stack:', error.stack);
    } finally {
        await client.end();
        console.log('\n🔒 Database connection closed.');
    }
}

createAxlChan();