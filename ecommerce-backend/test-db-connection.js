#!/usr/bin/env node

// 数据库连接测试脚本
const { Client } = require('pg');

async function testDatabaseConnection() {
  const client = new Client({
    host: process.env.DB_HOST || 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
    port: parseInt(process.env.DB_PORT, 10) || 5432,
    user: process.env.DB_USERNAME || 'tiktokshop_slkz_user',
    password: process.env.DB_PASSWORD || 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
    database: process.env.DB_DATABASE || 'tiktokshop_slkz',
    ssl: process.env.NODE_ENV === 'production' ? { rejectUnauthorized: false } : false,
    connectionTimeoutMillis: 60000,
  });

  try {
    console.log('🔌 尝试连接数据库...');
    console.log('📋 连接信息:');
    console.log(`   Host: ${client.host}`);
    console.log(`   Port: ${client.port}`);
    console.log(`   User: ${client.user}`);
    console.log(`   Database: ${client.database}`);
    
    await client.connect();
    console.log('✅ 数据库连接成功！');
    
    // 测试查询
    const result = await client.query('SELECT version()');
    console.log('📊 PostgreSQL版本:', result.rows[0].version);
    
    // 检查数据库是否存在
    const dbResult = await client.query('SELECT current_database()');
    console.log('🗄️ 当前数据库:', dbResult.rows[0].current_database);
    
    await client.end();
    console.log('🔚 连接已关闭');
    
  } catch (error) {
    console.error('❌ 数据库连接失败:');
    console.error('   错误类型:', error.code);
    console.error('   错误信息:', error.message);
    
    if (error.code === 'ECONNREFUSED') {
      console.log('💡 建议: 检查数据库服务是否正在运行');
    } else if (error.code === '28P01') {
      console.log('💡 建议: 检查用户名和密码是否正确');
    } else if (error.code === '3D000') {
      console.log('💡 建议: 数据库不存在，需要先创建数据库');
    }
    
    process.exit(1);
  }
}

testDatabaseConnection();
