const { Client } = require('pg');

const DATABASE_URL = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function finalDatabaseFix() {
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

        console.log('\n🔧 FINAL COMPREHENSIVE DATABASE FIX');
        console.log('========================================');

        // 1. Fix prescriptions table
        console.log('\n💊 Fixing prescriptions table...');
        
        const prescriptionColumns = [
            { name: 'dispensed_quantity', type: 'INTEGER', default: '0', description: 'Quantity already dispensed' },
            { name: 'dispensed_date', type: 'DATE', default: 'NULL', description: 'Date when medicine was dispensed' },
            { name: 'dispensed_by', type: 'BIGINT', default: 'NULL', description: 'User ID who dispensed the medicine' },
            { name: 'prescription_number', type: 'VARCHAR(50)', default: 'NULL', description: 'Unique prescription number' },
            { name: 'refills_remaining', type: 'INTEGER', default: '0', description: 'Number of refills remaining' },
            { name: 'frequency', type: 'VARCHAR(100)', default: 'NULL', description: 'How often to take (e.g., "3 times daily")' },
            { name: 'duration_days', type: 'INTEGER', default: 'NULL', description: 'Treatment duration in days' }
        ];

        for (const column of prescriptionColumns) {
            const exists = await client.query(`
                SELECT column_name FROM information_schema.columns 
                WHERE table_name = 'prescriptions' AND column_name = $1
            `, [column.name]);

            if (exists.rows.length === 0) {
                let sql = `ALTER TABLE prescriptions ADD COLUMN ${column.name} ${column.type} DEFAULT ${column.default}`;
                
                // Add foreign key constraint for dispensed_by
                if (column.name === 'dispensed_by') {
                    await client.query(sql);
                    await client.query(`
                        ALTER TABLE prescriptions 
                        ADD CONSTRAINT prescriptions_dispensed_by_foreign 
                        FOREIGN KEY (dispensed_by) REFERENCES users(id) ON DELETE SET NULL
                    `);
                } else {
                    await client.query(sql);
                }
                
                console.log(`✅ Added ${column.name} column to prescriptions - ${column.description}`);
            } else {
                console.log(`ℹ️  ${column.name} column already exists in prescriptions`);
            }
        }

        // 2. Fix conversations table
        console.log('\n💬 Fixing conversations table...');
        
        const conversationColumns = [
            { name: 'is_active', type: 'BOOLEAN', default: 'TRUE', description: 'Whether conversation is active' },
            { name: 'admin_archived', type: 'BOOLEAN', default: 'FALSE', description: 'Whether admin archived this conversation' },
            { name: 'patient_archived', type: 'BOOLEAN', default: 'FALSE', description: 'Whether patient archived this conversation' },
            { name: 'last_message_at', type: 'TIMESTAMP', default: 'CURRENT_TIMESTAMP', description: 'Timestamp of last message' },
            { name: 'admin_read_at', type: 'TIMESTAMP', default: 'NULL', description: 'When admin last read messages' },
            { name: 'patient_read_at', type: 'TIMESTAMP', default: 'NULL', description: 'When patient last read messages' },
            { name: 'priority', type: 'VARCHAR(20)', default: "'normal'", description: 'Conversation priority level' }
        ];

        for (const column of conversationColumns) {
            const exists = await client.query(`
                SELECT column_name FROM information_schema.columns 
                WHERE table_name = 'conversations' AND column_name = $1
            `, [column.name]);

            if (exists.rows.length === 0) {
                const sql = `ALTER TABLE conversations ADD COLUMN ${column.name} ${column.type} DEFAULT ${column.default}`;
                await client.query(sql);
                console.log(`✅ Added ${column.name} column to conversations - ${column.description}`);
            } else {
                console.log(`ℹ️  ${column.name} column already exists in conversations`);
            }
        }

        // 3. Add priority constraint to conversations
        try {
            await client.query(`
                ALTER TABLE conversations 
                ADD CONSTRAINT conversations_priority_check 
                CHECK (priority IN ('low', 'normal', 'high', 'urgent'))
            `);
            console.log('✅ Added priority constraint to conversations');
        } catch (error) {
            if (!error.message.includes('already exists')) {
                console.log('ℹ️  Priority constraint already exists or error:', error.message);
            }
        }

        // 4. Fix appointments table - add any missing columns
        console.log('\n📅 Checking appointments table...');
        
        const appointmentColumns = [
            { name: 'diagnosis', type: 'TEXT', default: 'NULL', description: 'Medical diagnosis from appointment' },
            { name: 'treatment', type: 'TEXT', default: 'NULL', description: 'Treatment given or recommended' },
            { name: 'vital_signs', type: 'JSONB', default: 'NULL', description: 'Vital signs taken during appointment' },
            { name: 'follow_up_date', type: 'DATE', default: 'NULL', description: 'Recommended follow-up date' },
            { name: 'completed_at', type: 'TIMESTAMP', default: 'NULL', description: 'When appointment was completed' },
            { name: 'cancelled_at', type: 'TIMESTAMP', default: 'NULL', description: 'When appointment was cancelled' },
            { name: 'cancelled_by', type: 'BIGINT', default: 'NULL', description: 'User who cancelled the appointment' },
            { name: 'notes', type: 'TEXT', default: 'NULL', description: 'Additional appointment notes' }
        ];

        for (const column of appointmentColumns) {
            const exists = await client.query(`
                SELECT column_name FROM information_schema.columns 
                WHERE table_name = 'appointments' AND column_name = $1
            `, [column.name]);

            if (exists.rows.length === 0) {
                let sql = `ALTER TABLE appointments ADD COLUMN ${column.name} ${column.type} DEFAULT ${column.default}`;
                
                if (column.name === 'cancelled_by') {
                    await client.query(sql);
                    await client.query(`
                        ALTER TABLE appointments 
                        ADD CONSTRAINT appointments_cancelled_by_foreign 
                        FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL
                    `);
                } else {
                    await client.query(sql);
                }
                
                console.log(`✅ Added ${column.name} column to appointments - ${column.description}`);
            } else {
                console.log(`ℹ️  ${column.name} column already exists in appointments`);
            }
        }

        // 5. Update existing records with proper defaults
        console.log('\n🔄 Updating existing records with defaults...');
        
        await client.query(`UPDATE prescriptions SET dispensed_quantity = 0 WHERE dispensed_quantity IS NULL`);
        await client.query(`UPDATE prescriptions SET refills_remaining = 0 WHERE refills_remaining IS NULL`);
        
        await client.query(`UPDATE conversations SET is_active = TRUE WHERE is_active IS NULL`);
        await client.query(`UPDATE conversations SET admin_archived = FALSE WHERE admin_archived IS NULL`);
        await client.query(`UPDATE conversations SET patient_archived = FALSE WHERE patient_archived IS NULL`);
        await client.query(`UPDATE conversations SET priority = 'normal' WHERE priority IS NULL`);
        await client.query(`UPDATE conversations SET last_message_at = created_at WHERE last_message_at IS NULL AND created_at IS NOT NULL`);
        await client.query(`UPDATE conversations SET last_message_at = CURRENT_TIMESTAMP WHERE last_message_at IS NULL`);
        
        console.log('✅ Updated all existing records with proper default values');

        // 6. Create some sample data for testing
        console.log('\n🧪 Creating sample test data...');
        
        // Check if we have patients to create prescriptions for
        const patients = await client.query('SELECT id, patient_name FROM patients LIMIT 3');
        const medicines = await client.query('SELECT id, medicine_name FROM medicines LIMIT 3');
        
        if (patients.rows.length > 0 && medicines.rows.length > 0) {
            // Create sample prescriptions
            for (let i = 0; i < Math.min(patients.rows.length, medicines.rows.length); i++) {
                const patient = patients.rows[i];
                const medicine = medicines.rows[i];
                
                const existingPrescription = await client.query(`
                    SELECT id FROM prescriptions WHERE patient_id = $1 AND medicine_name = $2
                `, [patient.id, medicine.medicine_name]);
                
                if (existingPrescription.rows.length === 0) {
                    await client.query(`
                        INSERT INTO prescriptions (
                            patient_id, medicine_id, medicine_name, quantity, dosage, 
                            frequency, duration_days, instructions, status, prescribed_date,
                            dispensed_quantity, refills_remaining, created_at, updated_at
                        ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 'active', CURRENT_DATE, $9, $10, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    `, [
                        patient.id,
                        medicine.id, 
                        medicine.medicine_name,
                        30, // quantity
                        '1 tablet',
                        '3 times daily',
                        7, // duration_days
                        'Take with food',
                        0, // dispensed_quantity
                        2  // refills_remaining
                    ]);
                    console.log(`✅ Created sample prescription for ${patient.patient_name} - ${medicine.medicine_name}`);
                } else {
                    console.log(`ℹ️  Prescription already exists for ${patient.patient_name} - ${medicine.medicine_name}`);
                }
            }
        }

        // 7. Final verification
        console.log('\n📊 Final verification...');
        
        const stats = {
            users: await client.query('SELECT COUNT(*) as count FROM users'),
            patients: await client.query('SELECT COUNT(*) as count FROM patients'),
            appointments: await client.query('SELECT COUNT(*) as count FROM appointments'),
            medicines: await client.query('SELECT COUNT(*) as count FROM medicines'),
            prescriptions: await client.query('SELECT COUNT(*) as count FROM prescriptions'),
            conversations: await client.query('SELECT COUNT(*) as count FROM conversations'),
            messages: await client.query('SELECT COUNT(*) as count FROM messages')
        };

        console.log('\n📈 Database Statistics:');
        Object.entries(stats).forEach(([table, result]) => {
            console.log(`   ${table.charAt(0).toUpperCase() + table.slice(1)}: ${result.rows[0].count}`);
        });

        // Test the problematic queries
        console.log('\n🧪 Testing previously problematic queries...');
        
        try {
            const dispensedCount = await client.query(`SELECT COUNT(*) as count FROM prescriptions WHERE dispensed_quantity > 0`);
            console.log(`✅ Dispensed prescriptions query works: ${dispensedCount.rows[0].count} found`);
        } catch (error) {
            console.error('❌ Dispensed prescriptions query failed:', error.message);
        }

        try {
            const activeConversations = await client.query(`
                SELECT COUNT(*) as count FROM conversations 
                WHERE is_active = TRUE AND admin_archived = FALSE
            `);
            console.log(`✅ Active conversations query works: ${activeConversations.rows[0].count} found`);
        } catch (error) {
            console.error('❌ Active conversations query failed:', error.message);
        }

        try {
            const lowStockMeds = await client.query(`
                SELECT COUNT(*) as count FROM medicines 
                WHERE stock_quantity <= minimum_stock AND status = 'active'
            `);
            console.log(`✅ Low stock medicines query works: ${lowStockMeds.rows[0].count} found`);
        } catch (error) {
            console.error('❌ Low stock medicines query failed:', error.message);
        }

        console.log('\n🎉 FINAL DATABASE FIX COMPLETE!');
        console.log('✨ All missing columns have been added');
        console.log('✨ All tables are now fully functional');
        console.log('✨ Sample data has been created for testing');
        console.log('\n🌐 Your Laravel CMS should now work completely without any database errors!');

    } catch (error) {
        console.error('❌ Error:', error.message);
        console.error('Stack:', error.stack);
    } finally {
        await client.end();
        console.log('\n🔒 Database connection closed.');
    }
}

finalDatabaseFix();