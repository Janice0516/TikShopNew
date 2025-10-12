const { Client } = require('pg');

async function checkMerchantFields() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false }
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 检查merchant表的实际字段
    console.log('1. 检查merchant表字段...');
    const structure = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns 
      WHERE table_name = 'merchant' 
      ORDER BY ordinal_position
    `);
    
    console.log('   merchant表实际字段:');
    structure.rows.forEach(row => {
      console.log(`   - ${row.column_name}: ${row.data_type} (nullable: ${row.is_nullable})`);
    });

    // 检查是否有merchant_uid字段
    const hasMerchantUid = structure.rows.some(row => row.column_name === 'merchant_uid');
    console.log(`\n   merchant_uid字段存在: ${hasMerchantUid}`);

    if (!hasMerchantUid) {
      console.log('\n2. 添加merchant_uid字段...');
      await client.query(`
        ALTER TABLE merchant 
        ADD COLUMN merchant_uid VARCHAR(20) UNIQUE
      `);
      console.log('   ✅ merchant_uid字段添加成功');

      // 为现有记录生成merchant_uid
      console.log('\n3. 为现有记录生成merchant_uid...');
      const merchants = await client.query('SELECT id, username FROM merchant');
      
      for (const merchant of merchants.rows) {
        const merchantUid = `M${merchant.id.padStart(6, '0')}`;
        await client.query(
          'UPDATE merchant SET merchant_uid = $1 WHERE id = $2',
          [merchantUid, merchant.id]
        );
        console.log(`   - ${merchant.username}: ${merchantUid}`);
      }
      
      console.log('   ✅ merchant_uid生成完成');
    }

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

checkMerchantFields();
