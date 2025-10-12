const { Client } = require('pg');
require('dotenv').config({ path: './.env.local' });

async function testCategoryEntityMapping() {
  console.log('🔍 测试Category实体映射...');

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

    // 测试1: 检查表结构
    console.log('\n📊 检查category表结构...');
    const tableInfo = await client.query(`
      SELECT column_name, data_type, is_nullable, column_default
      FROM information_schema.columns
      WHERE table_name = 'category'
      ORDER BY ordinal_position
    `);
    
    console.log('表结构:');
    tableInfo.rows.forEach(row => {
      console.log(`  ${row.column_name}: ${row.data_type} ${row.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'} ${row.column_default ? 'DEFAULT ' + row.column_default : ''}`);
    });

    // 测试2: 检查数据
    console.log('\n📊 检查category数据...');
    const data = await client.query('SELECT * FROM category LIMIT 3');
    console.log('数据样本:');
    data.rows.forEach(row => {
      console.log(`  ID: ${row.id}, Parent: ${row.parent_id}, Name: ${row.name}`);
    });

    // 测试3: 检查是否有NULL值问题
    console.log('\n📊 检查NULL值...');
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

    // 测试4: 检查数据类型转换
    console.log('\n📊 检查数据类型转换...');
    const typeCheck = await client.query('SELECT id, parent_id, name, level, sort, status FROM category LIMIT 1');
    if (typeCheck.rows.length > 0) {
      const row = typeCheck.rows[0];
      console.log('数据类型:');
      console.log(`  id: ${typeof row.id} (${row.id})`);
      console.log(`  parent_id: ${typeof row.parent_id} (${row.parent_id})`);
      console.log(`  name: ${typeof row.name} (${row.name})`);
      console.log(`  level: ${typeof row.level} (${row.level})`);
      console.log(`  sort: ${typeof row.sort} (${row.sort})`);
      console.log(`  status: ${typeof row.status} (${row.status})`);
    }

  } catch (error) {
    console.error('❌ 测试失败:', error.message);
    console.error('错误详情:', error);
  } finally {
    await client.end();
  }
}

testCategoryEntityMapping();
