const { Client } = require('pg');

async function fixMerchantFields() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false }
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 需要添加的字段列表
    const fieldsToAdd = [
      { name: 'contact_name', type: 'VARCHAR(50)', nullable: true },
      { name: 'id_card_front', type: 'VARCHAR(255)', nullable: true },
      { name: 'id_card_back', type: 'VARCHAR(255)', nullable: true },
      { name: 'reject_reason', type: 'VARCHAR(255)', nullable: true },
      { name: 'shop_name', type: 'VARCHAR(100)', nullable: true },
      { name: 'shop_logo', type: 'VARCHAR(255)', nullable: true },
      { name: 'shop_banner', type: 'TEXT', nullable: true },
      { name: 'shop_description', type: 'VARCHAR(500)', nullable: true },
      { name: 'balance', type: 'DECIMAL(10,2)', nullable: false, default: '0.00' },
      { name: 'frozen_amount', type: 'DECIMAL(10,2)', nullable: false, default: '0.00' },
      { name: 'total_income', type: 'DECIMAL(10,2)', nullable: false, default: '0.00' },
      { name: 'total_withdraw', type: 'DECIMAL(10,2)', nullable: false, default: '0.00' }
    ];

    console.log('1. 添加缺失的字段...');
    
    for (const field of fieldsToAdd) {
      try {
        const nullable = field.nullable ? '' : 'NOT NULL';
        const defaultValue = field.default ? `DEFAULT ${field.default}` : '';
        
        await client.query(`
          ALTER TABLE merchant 
          ADD COLUMN ${field.name} ${field.type} ${nullable} ${defaultValue}
        `);
        console.log(`   ✅ ${field.name} 字段添加成功`);
      } catch (error) {
        if (error.message.includes('already exists')) {
          console.log(`   ⚠️  ${field.name} 字段已存在`);
        } else {
          console.log(`   ❌ ${field.name} 字段添加失败: ${error.message}`);
        }
      }
    }

    // 更新现有记录的字段值
    console.log('\n2. 更新现有记录的字段值...');
    await client.query(`
      UPDATE merchant SET 
        contact_name = '联系人',
        shop_name = merchant_name || '店铺',
        balance = 10000.00,
        frozen_amount = 0.00,
        total_income = 0.00,
        total_withdraw = 0.00
      WHERE contact_name IS NULL
    `);
    console.log('   ✅ 字段值更新完成');

    // 验证字段
    console.log('\n3. 验证字段...');
    const structure = await client.query(`
      SELECT column_name, data_type, is_nullable
      FROM information_schema.columns 
      WHERE table_name = 'merchant' 
      ORDER BY ordinal_position
    `);
    
    console.log('   merchant表字段数量:', structure.rows.length);
    console.log('   字段列表:', structure.rows.map(r => r.column_name).join(', '));

  } catch (error) {
    console.error('❌ 错误:', error.message);
  } finally {
    try { await client.end(); } catch {}
  }
}

fixMerchantFields();
