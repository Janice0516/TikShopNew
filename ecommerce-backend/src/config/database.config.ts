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
      connectTimeoutMS: 10000, // 减少到10秒
      acquireTimeoutMS: 10000, // 减少到10秒
      timeout: 10000, // 减少到10秒
      extra: {
        max: 10, // 减少连接池大小
        idleTimeoutMillis: 10000, // 减少到10秒
        connectionTimeoutMillis: 10000, // 减少到10秒
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

