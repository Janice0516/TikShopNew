import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { WithdrawalController } from './withdrawal.controller';
import { WithdrawalService } from './withdrawal.service';
import { BalanceService } from './balance.service';
import { MerchantWithdrawal } from './entities/merchant-withdrawal.entity';
import { Merchant } from '../merchant/entities/merchant.entity';

@Module({
  imports: [TypeOrmModule.forFeature([MerchantWithdrawal, Merchant])],
  controllers: [WithdrawalController],
  providers: [WithdrawalService, BalanceService],
  exports: [WithdrawalService, BalanceService],
})
export class WithdrawalModule {}
