import { Client } from 'pg';

// Database connection configuration
const connectionString = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

// SQL commands to fix the users table
const sqlCommands = [
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) DEFAULT \'patient\';',
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT \'active\';',
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS registration_status VARCHAR(50) DEFAULT \'approved\';',
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP NULL;',
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS approved_by BIGINT NULL;',
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL;',
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS registration_source VARCHAR(50) DEFAULT \'admin\';',
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS display_name VARCHAR(255) NULL;',
    'ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL;'
];

async function fixDatabase() {
    const client = new Client({
        connectionString: connectionString,
        ssl: {
            rejectUnauthorized: false // Required for Render PostgreSQL
        }
    });

    try {
        console.log('🔌 Connecting to PostgreSQL database...');
        await client.connect();
        console.log('✅ Connected successfully!');

        console.log('\n📊 Checking current users table structure...');
        const currentColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'users' 
            ORDER BY ordinal_position;
        `);
        
        console.log('Current columns in users table:');
        currentColumns.rows.forEach(row => {
            console.log(`  - ${row.column_name} (${row.data_type}) ${row.is_nullable === 'YES' ? 'NULL' : 'NOT NULL'}`);
        });

        console.log('\n🔧 Adding missing columns...');
        
        for (let i = 0; i < sqlCommands.length; i++) {
            const command = sqlCommands[i];
            const columnName = command.match(/ADD COLUMN IF NOT EXISTS (\w+)/)?.[1] || `command ${i + 1}`;
            
            try {
                console.log(`  Adding column: ${columnName}`);
                await client.query(command);
                console.log(`  ✅ ${columnName} added successfully`);
            } catch (error) {
                if (error.message.includes('already exists')) {
                    console.log(`  ⚠️  ${columnName} already exists, skipping`);
                } else {
                    console.error(`  ❌ Error adding ${columnName}:`, error.message);
                }
            }
        }

        console.log('\n🔗 Adding foreign key constraints...');
        try {
            await client.query(`
                DO $$
                BEGIN
                    IF NOT EXISTS (
                        SELECT 1 FROM information_schema.table_constraints 
                        WHERE constraint_name = 'fk_users_approved_by' 
                        AND table_name = 'users'
                    ) THEN
                        ALTER TABLE users ADD CONSTRAINT fk_users_approved_by 
                        FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;
                    END IF;
                END $$;
            `);
            console.log('  ✅ Foreign key constraint added');
        } catch (error) {
            console.log('  ⚠️  Foreign key constraint might already exist:', error.message);
        }

        console.log('\n📈 Adding database indexes...');
        const indexes = [
            'CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);',
            'CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);',
            'CREATE INDEX IF NOT EXISTS idx_users_registration_status ON users(registration_status);'
        ];

        for (const indexCommand of indexes) {
            try {
                await client.query(indexCommand);
                console.log('  ✅ Index created successfully');
            } catch (error) {
                console.log('  ⚠️  Index might already exist:', error.message);
            }
        }

        console.log('\n🔍 Verifying final table structure...');
        const finalColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'users' 
            ORDER BY ordinal_position;
        `);
        
        console.log('Final users table structure:');
        finalColumns.rows.forEach(row => {
            console.log(`  - ${row.column_name} (${row.data_type}) ${row.is_nullable === 'YES' ? 'NULL' : 'NOT NULL'} ${row.column_default ? `DEFAULT: ${row.column_default}` : ''}`);
        });

        console.log('\n🎉 Database fix completed successfully!');
        console.log('🧪 You can now test patient registration on your website.');
        
    } catch (error) {
        console.error('❌ Database fix failed:', error.message);
        console.error('Stack trace:', error.stack);
        process.exit(1);
    } finally {
        await client.end();
        console.log('🔌 Database connection closed.');
    }
}

// Run the fix
console.log('🚀 Starting database fix process...');
fixDatabase();