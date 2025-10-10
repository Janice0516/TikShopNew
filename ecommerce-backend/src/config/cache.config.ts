import { Module } from '@nestjs/common';
import { CacheModule } from '@nestjs/cache-manager';
import { ConfigModule, ConfigService } from '@nestjs/config';
import * as redisStore from 'cache-manager-redis-store';

@Module({
  imports: [
    CacheModule.registerAsync({
      imports: [ConfigModule],
      useFactory: async (configService: ConfigService) => ({
        store: redisStore,
        host: configService.get('redis.host', 'localhost'),
        port: configService.get('redis.port', 6379),
        password: configService.get('redis.password'),
        db: configService.get('redis.db', 0),
        ttl: 300, // 默认5分钟缓存
        max: 1000, // 最大缓存条目数
      }),
      inject: [ConfigService],
      isGlobal: true,
    }),
  ],
})
export class CacheConfigModule {}
