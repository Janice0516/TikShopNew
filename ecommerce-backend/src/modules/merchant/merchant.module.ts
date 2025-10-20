import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { JwtModule } from '@nestjs/jwt';
import { ConfigService } from '@nestjs/config';
import { MerchantService } from './merchant.service';
import { MerchantController } from './merchant.controller';
import { WithdrawalInfoService } from './withdrawal-info.service';
import { WithdrawalInfoController } from './withdrawal-info.controller';
import { RechargeService } from './recharge.service';
import { RechargeController } from './recharge.controller';
import { MerchantProductService } from './merchant-product.service';
import { MerchantProductController } from './merchant-product.controller';
import { Merchant } from './entities/merchant.entity';
import { MerchantWithdrawalInfo } from './entities/merchant-withdrawal-info.entity';
import { MerchantRecharge } from './entities/merchant-recharge.entity';
import { MerchantProduct } from './entities/merchant-product.entity';
import { Product } from '../product/entities/product.entity';
import { InviteCodeModule } from '../invite-code/invite-code.module';

@Module({
  imports: [
    TypeOrmModule.forFeature([Merchant, MerchantWithdrawalInfo, MerchantRecharge, MerchantProduct, Product]),
    InviteCodeModule,
    JwtModule.registerAsync({
      inject: [ConfigService],
      useFactory: (configService: ConfigService) => ({
        secret: configService.get('jwt.secret'),
        signOptions: {
          expiresIn: configService.get('jwt.expiresIn'),
        },
      }),
    }),
  ],
  controllers: [MerchantController, WithdrawalInfoController, RechargeController, MerchantProductController],
  providers: [MerchantService, WithdrawalInfoService, RechargeService, MerchantProductService],
  exports: [MerchantService, WithdrawalInfoService, RechargeService, MerchantProductService],
})
export class MerchantModule {}

