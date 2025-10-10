import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { FundManagementController } from './fund-management.controller';
import { FundManagementService } from './fund-management.service';
import { FundOperation } from './entities/fund-operation.entity';
import { Merchant } from '../merchant/entities/merchant.entity';

@Module({
  imports: [TypeOrmModule.forFeature([FundOperation, Merchant])],
  controllers: [FundManagementController],
  providers: [FundManagementService],
  exports: [FundManagementService],
})
export class FundManagementModule {}
