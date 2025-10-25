import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { MerchantController } from './merchant.controller';
import { MerchantService } from './merchant.service';
import { MerchantProductController } from './merchant-product.controller';
import { MerchantProductService } from './merchant-product.service';
import { Merchant } from './entities/merchant.entity';
import { MerchantProduct } from './entities/merchant-product.entity';
import { Product } from '../product/entities/product.entity';
import { MerchantRecharge } from './entities/merchant-recharge.entity'
import { RechargeController } from './recharge.controller'
import { RechargeService } from './recharge.service'

@Module({
  imports: [
    TypeOrmModule.forFeature([Merchant, MerchantProduct, Product, MerchantRecharge])
  ],
  controllers: [MerchantController, MerchantProductController, RechargeController],
  providers: [MerchantService, MerchantProductService, RechargeService],
  exports: [MerchantService, MerchantProductService],
})
export class MerchantModule {}
