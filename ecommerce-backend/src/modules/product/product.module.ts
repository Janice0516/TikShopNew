import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ProductService } from './product.service';
import { ProductController } from './product.controller';
import { PublicProductController } from './public-product.controller';
import { PublicShopController } from './public-shop.controller';
import { PublicMerchantController } from './public-merchant.controller';
import { PublicShopService } from './public-shop.service';
import { Product } from './entities/product.entity';
import { Category } from '../category/entities/category.entity';
import { MerchantProduct } from '../merchant/entities/merchant-product.entity';
import { Merchant } from '../merchant/entities/merchant.entity';

@Module({
  imports: [TypeOrmModule.forFeature([Product, Category, MerchantProduct, Merchant])],
  controllers: [ProductController, PublicProductController, PublicShopController, PublicMerchantController],
  providers: [ProductService, PublicShopService],
  exports: [ProductService, PublicShopService],
})
export class ProductModule {}
