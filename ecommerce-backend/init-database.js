#!/usr/bin/env node

// 数据库初始化脚本
const { Client } = require('pg');

async function initializeDatabase() {
  // 首先连接到默认数据库
  const client = new Client({
    host: process.env.DB_HOST || 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
    port: parseInt(process.env.DB_PORT, 10) || 5432,
    user: process.env.DB_USERNAME || 'tiktokshop_slkz_user',
    password: process.env.DB_PASSWORD || 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
    database: 'postgres', // 连接到默认数据库
    ssl: process.env.NODE_ENV === 'production' ? { rejectUnauthorized: false } : false,
    connectionTimeoutMillis: 60000,
  });

  try {
    console.log('🔌 连接到PostgreSQL服务器...');
    await client.connect();
    console.log('✅ 连接成功！');
    
    const targetDatabase = process.env.DB_DATABASE || 'tiktokshop_slkz';
    
    // 检查数据库是否存在
    console.log(`🔍 检查数据库 '${targetDatabase}' 是否存在...`);
    const dbCheck = await client.query(
      'SELECT 1 FROM pg_database WHERE datname = $1',
      [targetDatabase]
    );
    
    if (dbCheck.rows.length > 0) {
      console.log(`✅ 数据库 '${targetDatabase}' 已存在`);
    } else {
      console.log(`📝 创建数据库 '${targetDatabase}'...`);
      await client.query(`CREATE DATABASE "${targetDatabase}"`);
      console.log(`✅ 数据库 '${targetDatabase}' 创建成功！`);
    }
    
    await client.end();
    console.log('🔚 初始化完成');
    
  } catch (error) {
    console.error('❌ 数据库初始化失败:');
    console.error('   错误类型:', error.code);
    console.error('   错误信息:', error.message);
    
    if (error.code === 'ECONNREFUSED') {
      console.log('💡 建议: 检查数据库服务是否正在运行');
    } else if (error.code === '28P01') {
      console.log('💡 建议: 检查用户名和密码是否正确');
    }
    
    process.exit(1);
  }
}

initializeDatabase();
