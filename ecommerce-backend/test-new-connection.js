#!/usr/bin/env node

// 使用新的Render数据库连接信息
const { Client } = require('pg');

// 新的连接配置（基于您截图中的信息）
const newConnectionConfig = {
  host: 'dpg-d3kgpsd6ubrc73dvbjm0-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn', // 请确认这是正确的密码
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 15000,
};

async function testNewConnection() {
  console.log('🔍 测试新的Render数据库连接...');
  console.log('📡 连接信息:');
  console.log(`   Host: ${newConnectionConfig.host}`);
  console.log(`   Port: ${newConnectionConfig.port}`);
  console.log(`   Database: ${newConnectionConfig.database}`);
  console.log(`   Username: ${newConnectionConfig.user}`);
  console.log(`   SSL: ${JSON.stringify(newConnectionConfig.ssl)}`);
  
  const client = new Client(newConnectionConfig);
  
  try {
    console.log('\n⏳ 正在连接...');
    await client.connect();
    console.log('✅ 连接成功！');
    
    // 测试查询
    console.log('\n📊 测试数据库查询...');
    const versionResult = await client.query('SELECT version()');
    console.log(`✅ 数据库版本: ${versionResult.rows[0].version.split(' ')[0]}`);
    
    // 检查现有表
    console.log('\n📋 检查现有表...');
    const tablesResult = await client.query(`
      SELECT table_name 
      FROM information_schema.tables 
      WHERE table_schema = 'public' 
      ORDER BY table_name
    `);
    
    if (tablesResult.rows.length > 0) {
      console.log('✅ 现有表:');
      tablesResult.rows.forEach(row => {
        console.log(`   - ${row.table_name}`);
      });
    } else {
      console.log('⚠️ 没有找到表，需要创建表结构');
    }
    
    // 检查分类表
    console.log('\n📂 检查分类表...');
    try {
      const categoryResult = await client.query('SELECT COUNT(*) as count FROM category');
      console.log(`✅ 分类数量: ${categoryResult.rows[0].count}`);
    } catch (error) {
      console.log('⚠️ 分类表不存在或有问题:', error.message);
    }
    
    // 检查商品表
    console.log('\n🛍️ 检查商品表...');
    try {
      const productResult = await client.query('SELECT COUNT(*) as count FROM platform_product');
      console.log(`✅ 商品数量: ${productResult.rows[0].count}`);
    } catch (error) {
      console.log('⚠️ 商品表不存在或有问题:', error.message);
    }
    
    console.log('\n🎉 数据库连接测试完成！');
    
    // 如果表不存在，询问是否创建
    if (tablesResult.rows.length === 0) {
      console.log('\n💡 建议：运行以下命令创建表和数据：');
      console.log('   node create-tables-with-new-connection.js');
    }
    
  } catch (error) {
    console.log('❌ 连接失败:', error.message);
    console.log('\n🔧 可能的解决方案：');
    console.log('   1. 检查密码是否正确');
    console.log('   2. 确认数据库服务状态');
    console.log('   3. 检查网络连接');
  } finally {
    try {
      await client.end();
      console.log('🔌 连接已关闭');
    } catch (error) {
      // 忽略关闭连接的错误
    }
  }
}

testNewConnection();
