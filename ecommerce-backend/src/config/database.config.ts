import { registerAs } from '@nestjs/config';

export default registerAs('database', () => {
  // 根据环境变量决定数据库类型
  const dbType = process.env.DB_TYPE || (process.env.NODE_ENV === 'production' ? 'postgres' : 'mysql');
  
  if (dbType === 'postgres') {
    return {
      type: 'postgres',
      host: process.env.DB_HOST || 'localhost',
      port: parseInt(process.env.DB_PORT, 10) || 5432,
      username: process.env.DB_USERNAME || 'postgres',
      password: process.env.DB_PASSWORD || '',
      database: process.env.DB_DATABASE || 'tiktokshop',
      ssl: process.env.NODE_ENV === 'production' ? { rejectUnauthorized: false } : false,
      connectTimeoutMS: 60000,
      acquireTimeoutMS: 60000,
      timeout: 60000,
      extra: {
        max: 20,
        idleTimeoutMillis: 30000,
        connectionTimeoutMillis: 60000,
      },
    };
  } else {
    return {
      type: 'mysql',
      host: process.env.DB_HOST || 'localhost',
      port: parseInt(process.env.DB_PORT, 10) || 3306,
      username: process.env.DB_USERNAME || 'root',
      password: process.env.DB_PASSWORD || '',
      database: process.env.DB_DATABASE || 'ecommerce',
    };
  }
});

