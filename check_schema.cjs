const { Pool } = require('pg');

async function checkSchema() {
  const pool = new Pool({
    host: 'localhost',
    port: 5432,
    database: 'cms',
    user: 'postgres',
    password: 'root',
  });

  try {
    console.log('🔗 Connecting to PostgreSQL database...');
    const client = await pool.connect();
    console.log('✅ Connected successfully!\n');

    // Check conversations table structure
    console.log('📋 Conversations table structure:');
    const conversationsSchema = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns 
      WHERE table_name = 'conversations' 
      ORDER BY ordinal_position;
    `);
    
    conversationsSchema.rows.forEach(row => {
      console.log(`   ${row.column_name}: ${row.data_type}${row.is_nullable === 'NO' ? ' NOT NULL' : ''}${row.column_default ? ` DEFAULT ${row.column_default}` : ''}`);
    });

    console.log('\n📋 Messages table structure:');
    const messagesSchema = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns 
      WHERE table_name = 'messages' 
      ORDER BY ordinal_position;
    `);
    
    messagesSchema.rows.forEach(row => {
      console.log(`   ${row.column_name}: ${row.data_type}${row.is_nullable === 'NO' ? ' NOT NULL' : ''}${row.column_default ? ` DEFAULT ${row.column_default}` : ''}`);
    });

    client.release();
    await pool.end();
    console.log('\n🔒 Database connection closed.');

  } catch (error) {
    console.error('❌ Error:', error);
    await pool.end();
  }
}

checkSchema();