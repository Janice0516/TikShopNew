#!/usr/bin/env node

// 测试数据库连接和查询
const { Client } = require('pg');

const connectionConfig = {
  host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 15000,
};

async function testDatabaseQueries() {
  console.log('🔍 测试数据库查询...');
  
  const client = new Client(connectionConfig);
  
  try {
    await client.connect();
    console.log('✅ 数据库连接成功');
    
    // 测试分类查询
    console.log('\n📂 测试分类查询...');
    const categoryResult = await client.query('SELECT * FROM category WHERE status = 1 ORDER BY sort ASC, id ASC LIMIT 5');
    console.log('✅ 分类查询成功:', categoryResult.rows.length, '条记录');
    categoryResult.rows.forEach(cat => {
      console.log(`   - ${cat.name} (ID: ${cat.id}, Parent: ${cat.parent_id})`);
    });
    
    // 测试商品查询
    console.log('\n🛍️ 测试商品查询...');
    const productResult = await client.query('SELECT * FROM platform_product LIMIT 5');
    console.log('✅ 商品查询成功:', productResult.rows.length, '条记录');
    productResult.rows.forEach(prod => {
      console.log(`   - ${prod.name} (ID: ${prod.id}, Category: ${prod.category_id})`);
    });
    
    // 测试分类树构建
    console.log('\n🌳 测试分类树构建...');
    const allCategories = await client.query('SELECT * FROM category WHERE status = 1 ORDER BY sort ASC, id ASC');
    console.log('✅ 获取所有分类:', allCategories.rows.length, '条记录');
    
    // 构建树形结构
    function buildCategoryTree(categories, parentId = 0) {
      const tree = [];
      categories
        .filter(cat => cat.parent_id === parentId)
        .forEach(cat => {
          const children = buildCategoryTree(categories, cat.id);
          tree.push({
            ...cat,
            children: children.length > 0 ? children : undefined,
          });
        });
      return tree;
    }
    
    const tree = buildCategoryTree(allCategories.rows);
    console.log('✅ 分类树构建成功:', tree.length, '个根分类');
    tree.forEach(root => {
      console.log(`   - ${root.name} (${root.children ? root.children.length : 0} 个子分类)`);
    });
    
  } catch (error) {
    console.error('❌ 数据库查询失败:', error.message);
  } finally {
    await client.end();
  }
}

testDatabaseQueries();
