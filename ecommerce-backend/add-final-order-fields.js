const { Client } = require('pg');
require('dotenv').config({ path: './.env.local' });

async function addFinalOrderFields() {
  console.log('🔄 添加最终订单字段...');

  const dbConfig = {
    host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.oregon-postgres.render.com',
    port: 5432,
    user: 'tiktokshop_slkz_user',
    password: process.env.DB_PASSWORD,
    database: 'tiktokshop_slkz',
    ssl: {
      rejectUnauthorized: false,
    },
  };

  const client = new Client(dbConfig);

  try {
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 添加最终字段
    const finalColumns = [
      {
        name: 'cost_amount',
        definition: 'DECIMAL(10,2)',
        comment: '成本金额'
      },
      {
        name: 'merchant_profit',
        definition: 'DECIMAL(10,2)',
        comment: '商家利润'
      },
      {
        name: 'platform_profit',
        definition: 'DECIMAL(10,2)',
        comment: '平台利润'
      },
      {
        name: 'freight',
        definition: 'DECIMAL(10,2) DEFAULT 0',
        comment: '运费'
      },
      {
        name: 'discount_amount',
        definition: 'DECIMAL(10,2) DEFAULT 0',
        comment: '折扣金额'
      },
      {
        name: 'receiver_name',
        definition: 'VARCHAR(50)',
        comment: '收货人姓名'
      },
      {
        name: 'receiver_phone',
        definition: 'VARCHAR(20)',
        comment: '收货人电话'
      },
      {
        name: 'receiver_province',
        definition: 'VARCHAR(50)',
        comment: '收货省份'
      },
      {
        name: 'receiver_city',
        definition: 'VARCHAR(50)',
        comment: '收货城市'
      },
      {
        name: 'receiver_district',
        definition: 'VARCHAR(50)',
        comment: '收货区县'
      },
      {
        name: 'receiver_address',
        definition: 'VARCHAR(200)',
        comment: '收货地址'
      },
      {
        name: 'buyer_message',
        definition: 'TEXT',
        comment: '买家留言'
      }
    ];

    console.log('\n🔧 添加最终字段...');
    for (const col of finalColumns) {
      try {
        // 检查字段是否已存在
        const existsRes = await client.query(`
          SELECT column_name FROM information_schema.columns
          WHERE table_schema = 'public' AND table_name = 'order' AND column_name = $1
        `, [col.name]);

        if (existsRes.rows.length === 0) {
          await client.query(`ALTER TABLE "order" ADD COLUMN ${col.name} ${col.definition}`);
          console.log(`   ✅ 添加字段: ${col.name}`);
          
          // 添加注释
          if (col.comment) {
            await client.query(`COMMENT ON COLUMN "order".${col.name} IS $1`, [col.comment]);
            console.log(`   📝 添加注释: ${col.name}`);
          }
        } else {
          console.log(`   ⚠️ 字段已存在: ${col.name}`);
        }
      } catch (error) {
        console.log(`   ❌ 添加字段失败 ${col.name}: ${error.message}`);
      }
    }

    // 检查最终表结构
    console.log('\n📊 检查最终表结构...');
    const finalColumnsRes = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns
      WHERE table_schema = 'public' AND table_name = 'order'
      ORDER BY ordinal_position;
    `);
    
    console.log('最终完整字段列表:');
    finalColumnsRes.rows.forEach(col => {
      console.log(`   - ${col.column_name}: ${col.data_type} ${col.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'} ${col.column_default ? `DEFAULT ${col.column_default}` : ''}`);
    });

    console.log('\n🎉 所有订单字段添加完成！');

  } catch (error) {
    console.error('❌ 添加最终订单字段失败:', error.message);
  } finally {
    await client.end();
  }
}

addFinalOrderFields();
