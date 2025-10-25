#!/usr/bin/env node

const bcrypt = require('bcrypt');
const mysql = require('mysql2/promise');

async function testAdminPassword() {
  try {
    // 连接数据库
    const connection = await mysql.createConnection({
      host: '127.0.0.1',
      port: 3306,
      user: 'tikshop',
      password: 'TikShop_MySQL_#2025!9pQwXz',
      database: 'ecommerce'
    });

    // 查询管理员账户
    const [rows] = await connection.execute(
      'SELECT id, username, password FROM admin WHERE username = ?',
      ['admin']
    );

    if (rows.length === 0) {
      console.log('❌ 未找到管理员账户');
      return;
    }

    const admin = rows[0];
    console.log('👤 管理员账户信息:');
    console.log(`   ID: ${admin.id}`);
    console.log(`   用户名: ${admin.username}`);
    console.log(`   密码哈希: ${admin.password}`);

    // 测试密码
    const testPasswords = ['123456', 'admin', 'password', 'admin123'];
    
    for (const password of testPasswords) {
      const isValid = await bcrypt.compare(password, admin.password);
      console.log(`🔐 测试密码 "${password}": ${isValid ? '✅ 正确' : '❌ 错误'}`);
      if (isValid) {
        console.log(`🎉 找到正确密码: ${password}`);
        break;
      }
    }

    await connection.end();
  } catch (error) {
    console.error('❌ 错误:', error.message);
  }
}

testAdminPassword();
