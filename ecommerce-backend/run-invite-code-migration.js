const mysql = require('mysql2/promise');
const fs = require('fs');

async function runMigration() {
  try {
    // 创建数据库连接
    const connection = await mysql.createConnection({
      host: 'localhost',
      user: 'root',
      password: '', // 尝试无密码
      database: 'tikshop'
    });

    console.log('Connected to MySQL database');

    // 读取SQL文件
    const sqlContent = fs.readFileSync('/root/TikShop/ecommerce-backend/migrations/add_invite_code_table.sql', 'utf8');
    
    // 分割SQL语句（以分号分割）
    const statements = sqlContent.split(';').filter(stmt => stmt.trim().length > 0);
    
    // 执行每个SQL语句
    for (const statement of statements) {
      if (statement.trim()) {
        console.log('Executing:', statement.trim().substring(0, 50) + '...');
        await connection.execute(statement);
      }
    }

    console.log('Migration completed successfully');
    await connection.end();
  } catch (error) {
    console.error('Migration failed:', error.message);
    
    // 尝试使用密码连接
    try {
      console.log('Trying with password...');
      const connection = await mysql.createConnection({
        host: 'localhost',
        user: 'root',
        password: '123456',
        database: 'tikshop'
      });

      console.log('Connected to MySQL database with password');

      const sqlContent = fs.readFileSync('/root/TikShop/ecommerce-backend/migrations/add_invite_code_table.sql', 'utf8');
      const statements = sqlContent.split(';').filter(stmt => stmt.trim().length > 0);
      
      for (const statement of statements) {
        if (statement.trim()) {
          console.log('Executing:', statement.trim().substring(0, 50) + '...');
          await connection.execute(statement);
        }
      }

      console.log('Migration completed successfully');
      await connection.end();
    } catch (error2) {
      console.error('Migration failed with password too:', error2.message);
    }
  }
}

runMigration();
