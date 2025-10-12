const { Client } = require('pg');

async function checkCategoryData() {
  console.log('🔍 检查分类数据\n');

  const client = new Client({
    host: 'dpg-d1bqj8j8i3mhq7qj8qg0-a.oregon-postgres.render.com',
    port: 5432,
    user: 'tikshop_user',
    password: 'tikshop_password',
    database: 'tiktokshop_slkz',
    ssl: { rejectUnauthorized: false }
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功');

    // 检查分类表是否存在
    console.log('\n1. 检查分类表结构...');
    const tableQuery = `
      SELECT column_name, data_type, is_nullable 
      FROM information_schema.columns 
      WHERE table_name = 'category' 
      ORDER BY ordinal_position;
    `;
    const tableResult = await client.query(tableQuery);
    
    if (tableResult.rows.length === 0) {
      console.log('❌ 分类表不存在');
      return;
    }
    
    console.log('✅ 分类表存在，字段结构：');
    tableResult.rows.forEach(row => {
      console.log(`   - ${row.column_name}: ${row.data_type} (${row.is_nullable === 'YES' ? 'nullable' : 'not null'})`);
    });

    // 检查分类数据
    console.log('\n2. 检查分类数据...');
    const dataQuery = 'SELECT * FROM category ORDER BY id LIMIT 10;';
    const dataResult = await client.query(dataQuery);
    
    console.log(`📊 分类数据总数: ${dataResult.rows.length}`);
    
    if (dataResult.rows.length === 0) {
      console.log('❌ 数据库中没有分类数据');
      
      // 插入一些测试分类数据
      console.log('\n3. 插入测试分类数据...');
      const insertQuery = `
        INSERT INTO category (name, parent_id, sort, status, create_time, update_time) VALUES
        ('电子产品', '0', 1, 1, NOW(), NOW()),
        ('服装鞋帽', '0', 2, 1, NOW(), NOW()),
        ('家居生活', '0', 3, 1, NOW(), NOW()),
        ('美妆护肤', '0', 4, 1, NOW(), NOW()),
        ('食品饮料', '0', 5, 1, NOW(), NOW()),
        ('手机数码', '1', 1, 1, NOW(), NOW()),
        ('电脑办公', '1', 2, 1, NOW(), NOW()),
        ('男装', '2', 1, 1, NOW(), NOW()),
        ('女装', '2', 2, 1, NOW(), NOW()),
        ('童装', '2', 3, 1, NOW(), NOW());
      `;
      
      await client.query(insertQuery);
      console.log('✅ 已插入10条测试分类数据');
      
      // 再次查询验证
      const verifyResult = await client.query('SELECT * FROM category ORDER BY id;');
      console.log(`📊 插入后分类数据总数: ${verifyResult.rows.length}`);
      console.log('📋 分类列表:');
      verifyResult.rows.forEach(row => {
        console.log(`   - ID: ${row.id}, 名称: ${row.name}, 父级: ${row.parent_id}, 状态: ${row.status}`);
      });
      
    } else {
      console.log('✅ 分类数据存在:');
      dataResult.rows.forEach(row => {
        console.log(`   - ID: ${row.id}, 名称: ${row.name}, 父级: ${row.parent_id}, 状态: ${row.status}`);
      });
    }

  } catch (error) {
    console.error('❌ 数据库操作失败:', error.message);
  } finally {
    await client.end();
  }
}

checkCategoryData();
