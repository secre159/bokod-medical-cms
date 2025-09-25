import { Client } from 'pg';

const connectionString = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function fixConversationsTable() {
    const client = new Client({
        connectionString: connectionString,
        ssl: {
            rejectUnauthorized: false
        }
    });

    try {
        console.log('🔍 Checking conversations table structure...\n');
        await client.connect();

        // Check conversations table structure
        const conversationsColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'conversations' 
            ORDER BY ordinal_position;
        `);

        console.log('📋 Current conversations table columns:');
        const existingColumns = conversationsColumns.rows.map(row => row.column_name);
        existingColumns.forEach(col => console.log(`  - ${col}`));

        // Check what columns the code is trying to use
        const requiredColumns = [
            'id',
            'patient_id',
            'admin_id', 
            'type',
            'is_active',
            'last_message_at',
            'created_at',
            'updated_at'
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

        // Add missing columns
        if (missingColumns.length > 0) {
            console.log('\n🔧 Adding missing columns to conversations table...');
            
            const columnDefinitions = {
                'type': "VARCHAR(50) DEFAULT 'patient_admin'",
                'is_active': 'BOOLEAN DEFAULT true',
                'last_message_at': 'TIMESTAMP NULL'
            };

            for (const column of missingColumns) {
                if (columnDefinitions[column]) {
                    try {
                        console.log(`  Adding column: ${column}`);
                        await client.query(`ALTER TABLE conversations ADD COLUMN IF NOT EXISTS ${column} ${columnDefinitions[column]}`);
                        console.log(`  ✅ ${column} added successfully`);
                    } catch (error) {
                        console.error(`  ❌ Error adding ${column}:`, error.message);
                    }
                }
            }
        }

        // Verify final structure
        console.log('\n🔍 Verifying final conversations table structure...');
        const finalColumns = await client.query(`
            SELECT column_name, data_type, is_nullable, column_default 
            FROM information_schema.columns 
            WHERE table_name = 'conversations' 
            ORDER BY ordinal_position;
        `);

        console.log('Final conversations table columns:');
        finalColumns.rows.forEach(row => {
            console.log(`  - ${row.column_name} (${row.data_type}) ${row.is_nullable === 'YES' ? 'NULL' : 'NOT NULL'} ${row.column_default ? `DEFAULT: ${row.column_default}` : ''}`);
        });

        // Test conversation query that was failing
        console.log('\n🧪 Testing conversation query...');
        try {
            const testQuery = await client.query(`
                SELECT * FROM conversations 
                WHERE (patient_id = $1 AND admin_id = $2 AND type = $3) 
                LIMIT 1
            `, [1, 1, 'patient_admin']);
            
            console.log(`✅ Query test successful - ${testQuery.rows.length} results found`);
        } catch (error) {
            console.error('❌ Query test failed:', error.message);
        }

        // Show current conversation data
        console.log('\n📊 Current conversations:');
        const allConversations = await client.query('SELECT id, patient_id, admin_id, type, is_active, created_at FROM conversations ORDER BY id');
        if (allConversations.rows.length > 0) {
            allConversations.rows.forEach(row => {
                console.log(`  - ID: ${row.id}, Patient: ${row.patient_id}, Admin: ${row.admin_id}, Type: ${row.type || 'N/A'}, Active: ${row.is_active}`);
            });
        } else {
            console.log('  No conversations found');
        }

        console.log('\n🎉 Conversations table structure fix completed!');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Full error:', error);
    } finally {
        await client.end();
    }
}

console.log('🚀 Starting conversations table fix...');
fixConversationsTable();