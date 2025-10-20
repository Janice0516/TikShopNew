const { Client } = require('pg');

async function checkTables() {
  const client = new Client({
    host: 'localhost',
    port: 5432,
    database: 'tikshop_db',
    user: 'tikshop_user',
    password: 'tikshop_password'
  });

  try {
    await client.connect();
    
    // 检查merchant_credit_rating表是否存在
    const result = await client.query(`
      SELECT table_name 
      FROM information_schema.tables 
      WHERE table_schema = 'public' 
      AND table_name LIKE '%credit%'
    `);
    
    console.log('信用评级相关表:');
    result.rows.forEach(row => {
      console.log(`- ${row.table_name}`);
    });
    
    // 检查merchant_credit_rating表结构
    const tableExists = result.rows.some(row => row.table_name === 'merchant_credit_rating');
    
    if (tableExists) {
      const columns = await client.query(`
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns
        WHERE table_name = 'merchant_credit_rating'
        ORDER BY ordinal_position
      `);
      
      console.log('\nmerchant_credit_rating表结构:');
      columns.rows.forEach(col => {
        console.log(`- ${col.column_name}: ${col.data_type} (${col.is_nullable === 'YES' ? 'nullable' : 'not null'})`);
      });
      
      // 检查表中是否有数据
      const count = await client.query('SELECT COUNT(*) FROM merchant_credit_rating');
      console.log(`\n表中记录数: ${count.rows[0].count}`);
    } else {
      console.log('\nmerchant_credit_rating表不存在，需要创建');
    }
    
  } catch (error) {
    console.error('数据库连接错误:', error.message);
  } finally {
    await client.end();
  }
}

checkTables();
