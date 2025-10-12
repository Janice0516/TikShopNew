const { Client } = require('pg');
require('dotenv').config({ path: './.env.local' });

async function updateOrderTable() {
  console.log('🔄 更新订单表结构...');

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

    // 检查现有表结构
    console.log('\n📊 检查现有订单表结构...');
    const columnsRes = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns
      WHERE table_schema = 'public' AND table_name = 'order'
      ORDER BY ordinal_position;
    `);
    
    console.log('现有字段:');
    columnsRes.rows.forEach(col => {
      console.log(`   - ${col.column_name}: ${col.data_type} ${col.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'} ${col.column_default ? `DEFAULT ${col.column_default}` : ''}`);
    });

    // 添加新字段
    const newColumns = [
      {
        name: 'order_status',
        definition: 'SMALLINT DEFAULT 1',
        comment: '订单状态 1待支付 2待发货 3待收货 4已完成 5已取消'
      },
      {
        name: 'pay_status', 
        definition: 'SMALLINT DEFAULT 0',
        comment: '支付状态 0未支付 1已支付'
      },
      {
        name: 'pay_type',
        definition: 'SMALLINT',
        comment: '支付方式 1微信 2支付宝'
      },
      {
        name: 'pay_time',
        definition: 'TIMESTAMP',
        comment: '支付时间'
      },
      {
        name: 'ship_time',
        definition: 'TIMESTAMP',
        comment: '发货时间'
      }
    ];

    console.log('\n🔧 添加新字段...');
    for (const col of newColumns) {
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
    console.log('\n📊 检查更新后的表结构...');
    const finalColumnsRes = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns
      WHERE table_schema = 'public' AND table_name = 'order'
      ORDER BY ordinal_position;
    `);
    
    console.log('更新后的字段:');
    finalColumnsRes.rows.forEach(col => {
      console.log(`   - ${col.column_name}: ${col.data_type} ${col.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'} ${col.column_default ? `DEFAULT ${col.column_default}` : ''}`);
    });

    console.log('\n🎉 订单表结构更新完成！');

  } catch (error) {
    console.error('❌ 更新订单表失败:', error.message);
  } finally {
    await client.end();
  }
}

updateOrderTable();
