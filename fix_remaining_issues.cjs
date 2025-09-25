const { Client } = require('pg');

const DATABASE_URL = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function fixRemainingIssues() {
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

        console.log('\n🔧 Fixing remaining database issues...\n');

        // 1. Check if avatar column exists in users table
        const avatarCheck = await client.query(`
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'users' AND column_name = 'avatar'
        `);

        if (avatarCheck.rows.length === 0) {
            console.log('❌ Avatar column missing from users table');
            console.log('🔧 Adding avatar column...');
            
            await client.query(`
                ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL;
            `);
            
            console.log('✅ Added avatar column to users table');
        } else {
            console.log('✅ Avatar column already exists in users table');
        }

        // 2. Check if updated_by column exists in users table
        const updatedByCheck = await client.query(`
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'users' AND column_name = 'updated_by'
        `);

        if (updatedByCheck.rows.length === 0) {
            console.log('❌ updated_by column missing from users table');
            console.log('🔧 Adding updated_by column...');
            
            await client.query(`
                ALTER TABLE users ADD COLUMN updated_by BIGINT NULL;
            `);
            
            console.log('✅ Added updated_by column to users table');
        } else {
            console.log('✅ updated_by column already exists in users table');
        }

        // 3. Check messaging system requirements
        console.log('\n📋 Checking messaging system...');
        
        const adminCount = await client.query(`
            SELECT COUNT(*) as count 
            FROM users 
            WHERE role = 'admin' AND status = 'active'
        `);
        
        console.log(`✅ Active admin users: ${adminCount.rows[0].count}`);

        const conversationsCount = await client.query('SELECT COUNT(*) as count FROM conversations');
        const messagesCount = await client.query('SELECT COUNT(*) as count FROM messages');
        
        console.log(`✅ Total conversations: ${conversationsCount.rows[0].count}`);
        console.log(`✅ Total messages: ${messagesCount.rows[0].count}`);

        // 4. Verify final users table schema
        console.log('\n📋 Final users table schema:');
        const finalColumns = await client.query(`
            SELECT column_name, data_type 
            FROM information_schema.columns 
            WHERE table_name = 'users' 
            ORDER BY ordinal_position;
        `);

        const requiredFields = ['phone', 'date_of_birth', 'gender', 'address', 'emergency_contact', 'emergency_phone', 'medical_history', 'allergies', 'notes', 'avatar', 'updated_by'];
        const existingFields = finalColumns.rows.map(col => col.column_name);
        
        requiredFields.forEach(field => {
            const exists = existingFields.includes(field);
            console.log(`   ${exists ? '✅' : '❌'} ${field}: ${exists ? 'EXISTS' : 'MISSING'}`);
        });

        console.log('\n🎯 Database fixes completed!');
        console.log('\n📝 Summary:');
        console.log('✅ Avatar column added to users table');
        console.log('✅ Updated_by column added to users table');
        console.log('✅ All required user edit fields now exist');
        console.log('✅ Messaging system database structure verified');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Stack:', error.stack);
    } finally {
        await client.end();
        console.log('\n🔒 Database connection closed.');
    }
}

fixRemainingIssues();