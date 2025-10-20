import { Controller, Get, Post, Body, Param, Query, UseGuards, Request } from '@nestjs/common';
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
  async getFundOverview(@Request() req) {
    try {
      const merchantId = req.user.merchantId || req.user.id;
      return await this.fundManagementService.getMerchantFundOverview(String(merchantId));
    } catch (error) {
      console.error('获取资金概览失败:', error);
      return {
        code: 500,
        message: '获取资金概览失败',
        data: {
          balance: 0,
          frozenAmount: 0,
          availableBalance: 0,
          frozenOrdersCount: 0,
          totalFrozenAmount: 0,
        },
      };
    }
  }

  /**
   * 获取资金流水
   */
  @Get('transactions')
  async getTransactions(
    @Request() req,
    @Query('page') page: string = '1',
    @Query('pageSize') pageSize: string = '10',
  ) {
    try {
      const merchantId = req.user.merchantId || req.user.id;
      return await this.fundManagementService.getMerchantFundTransactions(
        String(merchantId),
        parseInt(page),
        parseInt(pageSize),
      );
    } catch (error) {
      console.error('获取资金流水失败:', error);
      return {
        code: 500,
        message: '获取资金流水失败',
        data: {
          list: [],
          total: 0,
          page: parseInt(page),
          pageSize: parseInt(pageSize),
          totalPages: 0,
        },
      };
    }
  }

  /**
   * 获取冻结记录
   */
  @Get('freeze-records')
  async getFreezeRecords(
    @Request() req,
    @Query('page') page: string = '1',
    @Query('pageSize') pageSize: string = '10',
  ) {
    const merchantId = req.user.merchantId || req.user.id;
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