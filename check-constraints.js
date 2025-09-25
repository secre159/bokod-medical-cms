import { Client } from 'pg';

const connectionString = 'postgresql://bokod_user:QDpObjPcGz2zyBChhBbH2p5L1NJjCNZ7@dpg-d39vbvjipnbc73b76ddg-a.singapore-postgres.render.com/bokod_cms';

async function checkConstraints() {
    const client = new Client({
        connectionString: connectionString,
        ssl: {
            rejectUnauthorized: false
        }
    });

    try {
        await client.connect();
        console.log('🔍 Checking database constraints...\n');

        // Check all constraints on users table
        const constraints = await client.query(`
            SELECT 
                conname as constraint_name,
                contype as constraint_type,
                pg_get_constraintdef(oid) as constraint_definition
            FROM pg_constraint 
            WHERE conrelid = 'users'::regclass
            ORDER BY conname;
        `);

        console.log('🔒 Constraints on users table:');
        constraints.rows.forEach(row => {
            console.log(`\n${row.constraint_name} (${row.constraint_type}):`);
            console.log(`  ${row.constraint_definition}`);
        });

        // Check specifically the status constraint
        const statusConstraint = constraints.rows.find(row => 
            row.constraint_name.includes('status') || 
            row.constraint_definition.toLowerCase().includes('status')
        );

        if (statusConstraint) {
            console.log('\n📋 Status constraint details:');
            console.log(statusConstraint.constraint_definition);
            
            // Extract allowed values from the constraint
            const match = statusConstraint.constraint_definition.match(/\(status.*?IN \(([^)]+)\)\)/);
            if (match) {
                console.log('\n✅ Allowed status values:');
                const allowedValues = match[1].split(',').map(v => v.trim().replace(/'/g, ''));
                allowedValues.forEach(value => console.log(`  - ${value}`));
            }
        }

        // Check registration_status constraint too
        const regStatusConstraint = constraints.rows.find(row => 
            row.constraint_name.includes('registration_status') || 
            row.constraint_definition.toLowerCase().includes('registration_status')
        );

        if (regStatusConstraint) {
            console.log('\n📋 Registration Status constraint details:');
            console.log(regStatusConstraint.constraint_definition);
        }

        // Try to get enum values if they exist
        console.log('\n🏷️  Checking for enum types:');
        const enums = await client.query(`
            SELECT 
                t.typname as enum_name,
                array_agg(e.enumlabel ORDER BY e.enumsortorder) as enum_values
            FROM pg_type t 
            JOIN pg_enum e ON t.oid = e.enumtypid  
            WHERE t.typname LIKE '%status%'
            GROUP BY t.typname;
        `);

        if (enums.rows.length > 0) {
            enums.rows.forEach(row => {
                console.log(`  ${row.enum_name}: [${row.enum_values.join(', ')}]`);
            });
        } else {
            console.log('  No enum types found for status fields');
        }

    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await client.end();
    }
}

console.log('🚀 Starting constraint check...');
checkConstraints();