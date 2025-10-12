#!/usr/bin/env node

// 直接检查Render数据库表结构
const { Client } = require('pg');

// Render数据库配置
const renderDbConfig = {
  host: 'dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com',
  port: 5432,
  user: 'tiktokshop_slkz_user',
  password: 'U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn',
  database: 'tiktokshop_slkz',
  ssl: { rejectUnauthorized: false },
  connectionTimeoutMillis: 10000,
};

async function checkDatabaseTables() {
  const client = new Client(renderDbConfig);
  
  try {
    console.log('🔍 检查Render数据库表结构...');
    
    // 连接数据库
    console.log('🔌 连接Render数据库...');
    await client.connect();
    console.log('✅ 连接Render数据库成功');
    
    // 1. 检查所有表
    console.log('📊 检查所有表...');
    const tablesResult = await client.query(`
      SELECT table_name 
      FROM information_schema.tables 
      WHERE table_schema = 'public' 
      ORDER BY table_name
    `);
    
    console.log('📋 数据库中的表:');
    tablesResult.rows.forEach(row => {
      console.log(`   - ${row.table_name}`);
    });
    
    // 2. 检查category表结构
    console.log('📂 检查category表结构...');
    try {
      const categoryStructureResult = await client.query(`
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns
        WHERE table_name = 'category'
        ORDER BY ordinal_position
      `);
      
      if (categoryStructureResult.rows.length > 0) {
        console.log('✅ category表存在，结构如下:');
        categoryStructureResult.rows.forEach(row => {
          console.log(`   - ${row.column_name}: ${row.data_type} (nullable: ${row.is_nullable})`);
        });
        
        // 检查category表数据
        const categoryDataResult = await client.query('SELECT COUNT(*) as count FROM category');
        console.log(`   📊 category表数据量: ${categoryDataResult.rows[0].count}`);
        
        if (parseInt(categoryDataResult.rows[0].count) > 0) {
          const sampleDataResult = await client.query('SELECT * FROM category LIMIT 5');
          console.log('   📋 category表样本数据:');
          sampleDataResult.rows.forEach(row => {
            console.log(`     ID: ${row.id}, Name: ${row.name}, Parent: ${row.parent_id}, Level: ${row.level}`);
          });
        }
      } else {
        console.log('❌ category表不存在');
      }
    } catch (error) {
      console.log('❌ 检查category表失败:', error.message);
    }
    
    // 3. 检查platform_product表结构
    console.log('🛍️ 检查platform_product表结构...');
    try {
      const productStructureResult = await client.query(`
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns
        WHERE table_name = 'platform_product'
        ORDER BY ordinal_position
      `);
      
      if (productStructureResult.rows.length > 0) {
        console.log('✅ platform_product表存在，结构如下:');
        productStructureResult.rows.forEach(row => {
          console.log(`   - ${row.column_name}: ${row.data_type} (nullable: ${row.is_nullable})`);
        });
        
        // 检查platform_product表数据
        const productDataResult = await client.query('SELECT COUNT(*) as count FROM platform_product');
        console.log(`   📊 platform_product表数据量: ${productDataResult.rows[0].count}`);
      } else {
        console.log('❌ platform_product表不存在');
      }
    } catch (error) {
      console.log('❌ 检查platform_product表失败:', error.message);
    }
    
    // 4. 检查其他重要表
    const importantTables = ['merchant', 'user', 'admin', 'order'];
    for (const tableName of importantTables) {
      console.log(`📊 检查${tableName}表...`);
      try {
        const countResult = await client.query(`SELECT COUNT(*) as count FROM ${tableName}`);
        console.log(`   ✅ ${tableName}表存在，数据量: ${countResult.rows[0].count}`);
      } catch (error) {
        console.log(`   ❌ ${tableName}表不存在或有问题: ${error.message}`);
      }
    }
    
    console.log('✅ 数据库表结构检查完成！');
    
  } catch (error) {
    console.error('❌ 数据库检查失败:');
    console.error('   错误信息:', error.message);
    console.error('   错误代码:', error.code);
  } finally {
    await client.end();
  }
}

checkDatabaseTables();
