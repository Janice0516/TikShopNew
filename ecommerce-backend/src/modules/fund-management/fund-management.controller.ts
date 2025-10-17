import { Controller, Get, Post, Body, Param, Query, UseGuards } from '@nestjs/common';
import { FundManagementService } from './fund-management.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('fund-management')
@UseGuards(JwtAuthGuard)
export class FundManagementController {
  constructor(private readonly fundManagementService: FundManagementService) {}

  /**
   * 获取商家资金概览
   */
  @Get('overview')
  async getFundOverview(@Body('merchantId') merchantId: string) {
    return this.fundManagementService.getMerchantFundOverview(merchantId);
  }

  /**
   * 获取资金流水
   */
  @Get('transactions')
  async getTransactions(
    @Body('merchantId') merchantId: string,
    @Query('page') page: string = '1',
    @Query('pageSize') pageSize: string = '10',
  ) {
    return this.fundManagementService.getMerchantFundTransactions(
      merchantId,
      parseInt(page),
      parseInt(pageSize),
    );
  }

  /**
   * 获取冻结记录
   */
  @Get('freeze-records')
  async getFreezeRecords(
    @Body('merchantId') merchantId: string,
    @Query('page') page: string = '1',
    @Query('pageSize') pageSize: string = '10',
  ) {
    return this.fundManagementService.getMerchantFreezeRecords(
      merchantId,
      parseInt(page),
      parseInt(pageSize),
    );
  }

  /**
   * 手动冻结资金（管理员功能）
   */
  @Post('freeze/:orderId')
  async freezeFunds(@Param('orderId') orderId: string) {
    await this.fundManagementService.freezeFundsOnOrder(orderId);
    return { message: '资金冻结成功' };
  }

  /**
   * 手动解冻资金（管理员功能）
   */
  @Post('unfreeze/:orderId')
  async unfreezeFunds(@Param('orderId') orderId: string) {
    await this.fundManagementService.unfreezeFundsOnCompletion(orderId);
    return { message: '资金解冻成功' };
  }
}