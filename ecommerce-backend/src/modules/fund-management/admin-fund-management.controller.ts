import { Controller, Get, Post, Body, Param, Query, UseGuards, Request } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { FundManagementService } from './fund-management.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '@/common/decorators/roles.decorator';

@ApiTags('管理员资金管理')
@Controller('admin/fund-management')
@UseGuards(JwtAuthGuard, RolesGuard)
@ApiBearerAuth()
export class AdminFundManagementController {
  constructor(private readonly fundManagementService: FundManagementService) {}

  /**
   * 获取所有商户资金概览（管理员）
   */
  @Get('overview')
  @ApiOperation({ summary: '获取所有商户资金概览' })
  async getAllMerchantFundOverview() {
    // 这里可以返回所有商户的资金概览统计
    return {
      code: 200,
      message: '获取成功',
      data: {
        totalMerchants: 0,
        totalBalance: 0,
        totalFrozenAmount: 0,
        totalTransactions: 0
      }
    };
  }

  /**
   * 获取指定商户资金流水（管理员）
   */
  @Get('merchant/:merchantId/transactions')
  @ApiOperation({ summary: '获取指定商户资金流水' })
  async getMerchantTransactions(
    @Param('merchantId') merchantId: string,
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
   * 获取指定商户冻结记录（管理员）
   */
  @Get('merchant/:merchantId/freeze-records')
  @ApiOperation({ summary: '获取指定商户冻结记录' })
  async getMerchantFreezeRecords(
    @Param('merchantId') merchantId: string,
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
   * 获取指定商户资金概览（管理员）
   */
  @Get('merchant/:merchantId/overview')
  @ApiOperation({ summary: '获取指定商户资金概览' })
  async getMerchantFundOverview(@Param('merchantId') merchantId: string) {
    return this.fundManagementService.getMerchantFundOverview(merchantId);
  }

  /**
   * 手动冻结资金（管理员功能）
   */
  @Post('freeze/:orderId')
  @ApiOperation({ summary: '手动冻结资金' })
  @Roles('admin')
  async freezeFunds(@Param('orderId') orderId: string) {
    await this.fundManagementService.freezeFundsOnOrder(orderId);
    return { message: '资金冻结成功' };
  }

  /**
   * 手动解冻资金（管理员功能）
   */
  @Post('unfreeze/:orderId')
  @ApiOperation({ summary: '手动解冻资金' })
  @Roles('admin')
  async unfreezeFunds(@Param('orderId') orderId: string) {
    await this.fundManagementService.unfreezeFundsOnCompletion(orderId);
    return { message: '资金解冻成功' };
  }

  /**
   * 获取操作记录（管理员）
   */
  @Get('operations')
  @ApiOperation({ summary: '获取资金操作记录' })
  async getOperations(
    @Query('page') page: string = '1',
    @Query('pageSize') pageSize: string = '10',
    @Query('merchantId') merchantId?: string,
    @Query('operationType') operationType?: string,
  ) {
    // 这里可以返回所有商户的资金操作记录
    return {
      code: 200,
      message: '获取成功',
      data: {
        list: [],
        total: 0,
        page: parseInt(page),
        pageSize: parseInt(pageSize)
      }
    };
  }
}
