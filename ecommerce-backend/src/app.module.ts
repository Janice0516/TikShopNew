import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ScheduleModule } from '@nestjs/schedule';
import { JwtModule } from '@nestjs/jwt';

// 配置
import databaseConfig from './config/database.config';
import redisConfig from './config/redis.config';
import jwtConfig from './config/jwt.config';

// 实体
import { User } from './modules/user/entities/user.entity';
import { Admin } from './modules/admin/entities/admin.entity';
import { Category } from './modules/category/entities/category.entity';
import { MerchantWithdrawal } from './modules/withdrawal/entities/merchant-withdrawal.entity';
import { MerchantCreditRating } from './modules/credit-rating/entities/merchant-credit-rating.entity';
import { FundOperation } from './modules/fund-management/entities/fund-operation.entity';
import { MerchantWithdrawalInfo } from './modules/merchant/entities/merchant-withdrawal-info.entity';
import { MerchantRecharge } from './modules/merchant/entities/merchant-recharge.entity';

// 模块
import { UserModule } from './modules/user/user.module';
import { AuthModule } from './modules/auth/auth.module';
import { AdminModule } from './modules/admin/admin.module';
import { MerchantModule } from './modules/merchant/merchant.module';
import { ProductModule } from './modules/product/product.module';
import { CartModule } from './modules/cart/cart.module';
import { OrderModule } from './modules/order/order.module';
import { UploadModule } from './modules/upload/upload.module';
import { TestModule } from './modules/test/test.module';
import { CacheConfigModule } from './config/cache.config';
import { CategoryModule } from './modules/category/category.module';
import { WithdrawalModule } from './modules/withdrawal/withdrawal.module';
import { CreditRatingModule } from './modules/credit-rating/credit-rating.module';
import { FundManagementModule } from './modules/fund-management/fund-management.module';

@Module({
  imports: [
    // 配置模块
    ConfigModule.forRoot({
      isGlobal: true,
      load: [databaseConfig, redisConfig, jwtConfig],
      envFilePath: ['.env.local', '.env'],
    }),

    // 数据库模块
    TypeOrmModule.forRootAsync({
      inject: [ConfigService],
      useFactory: (configService: ConfigService) => ({
        type: 'mysql',
        host: configService.get('database.host'),
        port: configService.get('database.port'),
        username: configService.get('database.username'),
        password: configService.get('database.password'),
        database: configService.get('database.database'),
        entities: [User, Admin, Category, MerchantWithdrawal, MerchantCreditRating, FundOperation, MerchantWithdrawalInfo, MerchantRecharge],
        autoLoadEntities: true,
        synchronize: false, // 生产环境必须为false
        logging: process.env.NODE_ENV === 'development',
        timezone: '+08:00',
        charset: 'utf8mb4',
      }),
    }),

    // 定时任务模块
    ScheduleModule.forRoot(),

    // JWT模块
    JwtModule.registerAsync({
      inject: [ConfigService],
      useFactory: (configService: ConfigService) => ({
        secret: configService.get('jwt.secret'),
        signOptions: { expiresIn: '7d' },
      }),
      global: true,
    }),

    // 业务模块
    AuthModule,
    UserModule,
    AdminModule,
    MerchantModule,
    FundManagementModule,
    ProductModule,
    CartModule,
    OrderModule,
    UploadModule,
    TestModule,
    CacheConfigModule,
    CategoryModule,
    WithdrawalModule,
    CreditRatingModule,
  ],
})
export class AppModule {}

