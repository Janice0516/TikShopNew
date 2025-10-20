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
import { Merchant } from './modules/merchant/entities/merchant.entity';
import { MerchantProduct } from './modules/merchant/entities/merchant-product.entity';
import { MerchantRecharge } from './modules/merchant/entities/merchant-recharge.entity';
import { MerchantWithdrawalInfo } from './modules/merchant/entities/merchant-withdrawal-info.entity';
import { Product } from './modules/product/entities/product.entity';
import { Order } from './modules/order/entities/order.entity';
import { OrderItem } from './modules/order/entities/order-item.entity';
import { Cart } from './modules/cart/entities/cart.entity';
import { MerchantWithdrawal } from './modules/withdrawal/entities/merchant-withdrawal.entity';
import { MerchantCreditRating } from './modules/credit-rating/entities/merchant-credit-rating.entity';
import { FundOperation } from './modules/fund-management/entities/fund-operation.entity';
import { FundFreezeRecord } from './modules/fund-management/entities/fund-freeze-record.entity';
import { FundTransaction } from './modules/fund-management/entities/fund-transaction.entity';
import { SystemSettings } from './modules/settings/entities/system-settings.entity';
import { InviteCode } from './modules/invite-code/entities/invite-code.entity';
import { Role } from './modules/auth/entities/role.entity';
import { Permission } from './modules/auth/entities/permission.entity';

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
import { HealthModule } from './modules/health/health.module';
import { SettingsModule } from './modules/settings/settings.module';
import { InviteCodeModule } from './modules/invite-code/invite-code.module';
import { RolePermissionModule } from './modules/auth/modules/role-permission.module';

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
      useFactory: (configService: ConfigService) => {
        const dbType = configService.get('database.type') || 'postgres';
        const baseConfig = {
          host: configService.get('database.host'),
          port: configService.get('database.port'),
          username: configService.get('database.username'),
          password: configService.get('database.password'),
          database: configService.get('database.database'),
          entities: [
            User, 
            Admin, 
            Category, 
            Merchant, 
            MerchantProduct, 
            MerchantRecharge, 
            MerchantWithdrawalInfo, 
            Product, 
            Order, 
            OrderItem, 
            Cart, 
            MerchantWithdrawal,
            MerchantCreditRating,
            FundOperation,
            FundFreezeRecord,
            FundTransaction,
            SystemSettings,
            InviteCode,
            Role,
            Permission
          ],
          autoLoadEntities: true,
          synchronize: false, // 生产环境必须为false
          logging: process.env.NODE_ENV === 'development',
        };

        if (dbType === 'postgres') {
          return {
            ...baseConfig,
            type: 'postgres' as const,
            ssl: configService.get('database.ssl'),
            extra: {
              max: 20,
              idleTimeoutMillis: 30000,
              connectionTimeoutMillis: 2000,
            },
          };
        } else {
          return {
            ...baseConfig,
            type: 'mysql' as const,
            timezone: '+08:00',
            charset: 'utf8mb4',
          };
        }
      },
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
    HealthModule,
    SettingsModule,
    InviteCodeModule,
    RolePermissionModule,
  ],
})
export class AppModule {}

