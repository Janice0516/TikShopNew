import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { CreditRatingController } from './credit-rating.controller';
import { CreditRatingService } from './credit-rating.service';
import { MerchantCreditRating } from './entities/merchant-credit-rating.entity';
import { Merchant } from '../merchant/entities/merchant.entity';
import { Order } from '../order/entities/order.entity';

@Module({
  imports: [TypeOrmModule.forFeature([MerchantCreditRating, Merchant, Order])],
  controllers: [CreditRatingController],
  providers: [CreditRatingService],
  exports: [CreditRatingService],
})
export class CreditRatingModule {}
