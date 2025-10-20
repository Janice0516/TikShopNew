import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { FundManagementService } from './fund-management.service';
import { FundManagementController } from './fund-management.controller';
import { AdminFundManagementController } from './admin-fund-management.controller';
import { FundFreezeRecord } from './entities/fund-freeze-record.entity';
import { FundTransaction } from './entities/fund-transaction.entity';
import { Merchant } from '../merchant/entities/merchant.entity';
import { Order } from '../order/entities/order.entity';
import { Product } from '../product/entities/product.entity';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      FundFreezeRecord,
      FundTransaction,
      Merchant,
      Order,
      Product,
    ]),
  ],
  controllers: [FundManagementController, AdminFundManagementController],
  providers: [FundManagementService],
  exports: [FundManagementService],
})
export class FundManagementModule {}