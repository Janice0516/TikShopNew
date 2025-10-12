#!/usr/bin/env node

// 测试数据库字段名匹配
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

async function testFieldMapping() {
  console.log('🔍 测试字段名匹配...');
  
  const client = new Client(connectionConfig);
  
  try {
    await client.connect();
    console.log('✅ 数据库连接成功');
    
    // 检查Category表结构
    console.log('\n📂 检查Category表结构...');
    const categoryColumns = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns 
      WHERE table_name = 'category' AND table_schema = 'public'
      ORDER BY ordinal_position
    `);
    
    console.log('Category表字段:');
    categoryColumns.rows.forEach(col => {
      console.log(`   - ${col.column_name}: ${col.data_type} ${col.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'}`);
    });
    
    // 检查Product表结构
    console.log('\n🛍️ 检查Product表结构...');
    const productColumns = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns 
      WHERE table_name = 'platform_product' AND table_schema = 'public'
      ORDER BY ordinal_position
    `);
    
    console.log('Product表字段:');
    productColumns.rows.forEach(col => {
      console.log(`   - ${col.column_name}: ${col.data_type} ${col.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'}`);
    });
    
    // 测试分类查询（使用正确的字段名）
    console.log('\n🌳 测试分类树构建（使用parent_id）...');
    const allCategories = await client.query('SELECT * FROM category WHERE status = 1 ORDER BY sort ASC, id ASC');
    console.log('✅ 获取所有分类:', allCategories.rows.length, '条记录');
    
    // 构建树形结构（使用parent_id）
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
    console.error('❌ 测试失败:', error.message);
  } finally {
    await client.end();
  }
}

testFieldMapping();
