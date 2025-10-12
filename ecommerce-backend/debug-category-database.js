const { Client } = require('pg');
require('dotenv').config({ path: './.env.local' });

async function debugCategoryDatabase() {
  console.log('🔍 调试分类数据库查询...');

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
    const res1 = await client.query('SELECT * FROM category LIMIT 3');
    console.log('查询结果:', res1.rows);

    // 测试2: 带条件的查询
    console.log('\n📊 测试2: 带状态条件的查询...');
    const res2 = await client.query('SELECT * FROM category WHERE status = $1 LIMIT 3', [1]);
    console.log('查询结果:', res2.rows);

    // 测试3: 排序查询
    console.log('\n📊 测试3: 排序查询...');
    const res3 = await client.query('SELECT * FROM category ORDER BY sort ASC, id ASC LIMIT 3');
    console.log('查询结果:', res3.rows);

    // 测试4: 检查字段类型
    console.log('\n📊 测试4: 检查字段类型...');
    const res4 = await client.query(`
      SELECT column_name, data_type, is_nullable 
      FROM information_schema.columns 
      WHERE table_name = 'category' 
      ORDER BY ordinal_position
    `);
    console.log('字段信息:');
    res4.rows.forEach(row => {
      console.log(`  ${row.column_name}: ${row.data_type} ${row.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'}`);
    });

    // 测试5: 检查数据完整性
    console.log('\n📊 测试5: 检查数据完整性...');
    const res5 = await client.query('SELECT COUNT(*) as total FROM category');
    console.log('总分类数:', res5.rows[0].total);

    const res6 = await client.query('SELECT COUNT(*) as active FROM category WHERE status = 1');
    console.log('启用分类数:', res6.rows[0].active);

  } catch (error) {
    console.error('❌ 数据库查询失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
  }
}

debugCategoryDatabase();
