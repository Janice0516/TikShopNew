#!/usr/bin/env node

// 测试Render数据库连接
const { Client } = require('pg');

const renderDbConfig = {
  host: 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 30000,
  idleTimeoutMillis: 30000,
  query_timeout: 30000,
  statement_timeout: 30000,
  keepAlive: true,
  keepAliveInitialDelayMillis: 0
};

async function testConnection() {
  const client = new Client(renderDbConfig);
  
  try {
    console.log('🔌 测试Render PostgreSQL连接...');
    await client.connect();
    console.log('✅ 连接成功！');
    
    // 测试查询
    const result = await client.query('SELECT version()');
    console.log('📊 PostgreSQL版本:', result.rows[0].version);
    
    // 检查表是否存在
    const tablesResult = await client.query(`
      SELECT table_name 
      FROM information_schema.tables 
      WHERE table_schema = 'public' 
      ORDER BY table_name
    `);
    
    console.log('📋 现有表:');
    tablesResult.rows.forEach(row => {
      console.log(`   - ${row.table_name}`);
    });
    
    // 检查数据
    const countResult = await client.query('SELECT COUNT(*) as count FROM merchant');
    console.log(`👥 商家数量: ${countResult.rows[0].count}`);
    
    const productCountResult = await client.query('SELECT COUNT(*) as count FROM product');
    console.log(`🛍️ 商品数量: ${productCountResult.rows[0].count}`);
    
  } catch (error) {
    console.error('❌ 连接失败:');
    console.error('   错误类型:', error.code);
    console.error('   错误信息:', error.message);
    console.error('   详细信息:', error);
  } finally {
    await client.end();
  }
}

testConnection();
