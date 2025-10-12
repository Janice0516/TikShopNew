#!/usr/bin/env node

// 检查数据库表结构
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

async function checkTableStructures() {
  console.log('🔍 检查数据库表结构...');
  
  const client = new Client(connectionConfig);
  
  try {
    await client.connect();
    console.log('✅ 数据库连接成功');
    
    // 检查所有表
    const tablesResult = await client.query(`
      SELECT table_name 
      FROM information_schema.tables 
      WHERE table_schema = 'public' 
      ORDER BY table_name
    `);
    
    console.log('\n📋 现有表:');
    tablesResult.rows.forEach(row => {
      console.log(`   - ${row.table_name}`);
    });
    
    // 检查每个表的结构
    for (const table of tablesResult.rows) {
      const tableName = table.table_name;
      console.log(`\n📊 表 ${tableName} 的结构:`);
      
      const columnsResult = await client.query(`
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns 
        WHERE table_name = $1 AND table_schema = 'public'
        ORDER BY ordinal_position
      `, [tableName]);
      
      columnsResult.rows.forEach(col => {
        console.log(`   - ${col.column_name}: ${col.data_type} ${col.is_nullable === 'NO' ? 'NOT NULL' : 'NULL'}`);
      });
    }
    
  } catch (error) {
    console.error('❌ 检查失败:', error.message);
  } finally {
    await client.end();
  }
}

checkTableStructures();
