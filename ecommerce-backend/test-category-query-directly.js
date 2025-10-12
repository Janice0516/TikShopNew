const { Client } = require('pg');
require('dotenv').config({ path: './.env.local' });

async function testCategoryQueryDirectly() {
  console.log('🔍 直接测试分类查询...');

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

    // 测试1: 简单查询
    console.log('\n📊 测试1: 简单查询所有分类...');
    const res1 = await client.query('SELECT * FROM category LIMIT 5');
    console.log('查询结果:', res1.rows);

    // 测试2: 检查字段类型
    console.log('\n📊 测试2: 检查字段类型...');
    if (res1.rows.length > 0) {
      const row = res1.rows[0];
      console.log('字段类型:');
      console.log(`  id: ${typeof row.id} (${row.id})`);
      console.log(`  parent_id: ${typeof row.parent_id} (${row.parent_id})`);
      console.log(`  name: ${typeof row.name} (${row.name})`);
      console.log(`  level: ${typeof row.level} (${row.level})`);
      console.log(`  sort: ${typeof row.sort} (${row.sort})`);
      console.log(`  status: ${typeof row.status} (${row.status})`);
    }

    // 测试3: 检查是否有NULL值
    console.log('\n📊 测试3: 检查NULL值...');
    const nullCheck = await client.query(`
      SELECT 
        COUNT(*) as total,
        COUNT(id) as id_count,
        COUNT(parent_id) as parent_id_count,
        COUNT(name) as name_count,
        COUNT(level) as level_count,
        COUNT(sort) as sort_count,
        COUNT(status) as status_count
      FROM category
    `);
    
    console.log('NULL值检查:');
    console.log(`  总记录数: ${nullCheck.rows[0].total}`);
    console.log(`  id字段: ${nullCheck.rows[0].id_count}`);
    console.log(`  parent_id字段: ${nullCheck.rows[0].parent_id_count}`);
    console.log(`  name字段: ${nullCheck.rows[0].name_count}`);
    console.log(`  level字段: ${nullCheck.rows[0].level_count}`);
    console.log(`  sort字段: ${nullCheck.rows[0].sort_count}`);
    console.log(`  status字段: ${nullCheck.rows[0].status_count}`);

  } catch (error) {
    console.error('❌ 测试失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
  }
}

testCategoryQueryDirectly();
