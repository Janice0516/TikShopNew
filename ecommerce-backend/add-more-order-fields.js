const { Client } = require('pg');
require('dotenv').config({ path: './.env.local' });

async function addMoreOrderFields() {
  console.log('🔄 添加更多订单字段...');

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

    // 添加更多字段
    const additionalColumns = [
      {
        name: 'receive_time',
        definition: 'TIMESTAMP',
        comment: '收货时间'
      },
      {
        name: 'finish_time',
        definition: 'TIMESTAMP',
        comment: '完成时间'
      },
      {
        name: 'cancel_time',
        definition: 'TIMESTAMP',
        comment: '取消时间'
      },
      {
        name: 'cancel_reason',
        definition: 'TEXT',
        comment: '取消原因'
      },
      {
        name: 'transaction_id',
        definition: 'VARCHAR(100)',
        comment: '交易ID'
      },
      {
        name: 'pay_amount',
        definition: 'DECIMAL(10,2)',
        comment: '支付金额'
      }
    ];

    console.log('\n🔧 添加额外字段...');
    for (const col of additionalColumns) {
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
    
    console.log('最终字段列表:');
    finalColumnsRes.rows.forEach(col => {
      console.log(`   - ${col.column_name}: ${col.data_type} ${col.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'} ${col.column_default ? `DEFAULT ${col.column_default}` : ''}`);
    });

    console.log('\n🎉 所有订单字段添加完成！');

  } catch (error) {
    console.error('❌ 添加订单字段失败:', error.message);
  } finally {
    await client.end();
  }
}

addMoreOrderFields();
