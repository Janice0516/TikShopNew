import {
  Controller,
  Get,
  Post,
  Body,
  Param,
  Query,
  UseGuards,
  Request,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { FundManagementService } from './fund-management.service';
import { 
  IncreaseFundDto,
  FreezeFundDto, 
  UnfreezeFundDto, 
  DeductFundDto, 
  RefundFundDto, 
  QueryFundOperationDto 
} from './dto/fund-management.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('资金管理')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
@Controller('fund-management')
export class FundManagementController {
  constructor(private readonly fundManagementService: FundManagementService) {}

  @Post('increase')
  @ApiOperation({ summary: '增加商户资金' })
  async increaseFund(@Request() req, @Body() increaseFundDto: IncreaseFundDto) {
    const adminId = req.user.id;
    const adminName = req.user.username || '管理员';
    return this.fundManagementService.increaseFund(adminId, adminName, increaseFundDto);
  }

  @Post('freeze')
  @ApiOperation({ summary: '冻结商户资金' })
  async freezeFund(@Request() req, @Body() freezeFundDto: FreezeFundDto) {
    const adminId = req.user.id;
    const adminName = req.user.username || '管理员';
    return this.fundManagementService.freezeFund(adminId, adminName, freezeFundDto);
  }

  @Post('unfreeze')
  @ApiOperation({ summary: '解冻商户资金' })
  async unfreezeFund(@Request() req, @Body() unfreezeFundDto: UnfreezeFundDto) {
    const adminId = req.user.id;
    const adminName = req.user.username || '管理员';
    return this.fundManagementService.unfreezeFund(adminId, adminName, unfreezeFundDto);
  }

  @Post('deduct')
  @ApiOperation({ summary: '扣除商户资金' })
  async deductFund(@Request() req, @Body() deductFundDto: DeductFundDto) {
    const adminId = req.user.id;
    const adminName = req.user.username || '管理员';
    return this.fundManagementService.deductFund(adminId, adminName, deductFundDto);
  }

  @Post('refund')
  @ApiOperation({ summary: '退还商户资金' })
  async refundFund(@Request() req, @Body() refundFundDto: RefundFundDto) {
    const adminId = req.user.id;
    const adminName = req.user.username || '管理员';
    return this.fundManagementService.refundFund(adminId, adminName, refundFundDto);
  }

  @Get('merchant/:merchantId/info')
  @ApiOperation({ summary: '获取商户资金信息' })
  async getMerchantFundInfo(@Param('merchantId') merchantId: string) {
    return this.fundManagementService.getMerchantFundInfo(+merchantId);
  }

  @Get('operations')
  @ApiOperation({ summary: '获取资金操作记录' })
  async getFundOperationList(@Query() query: QueryFundOperationDto) {
    return this.fundManagementService.getFundOperationList(query);
  }

  @Get('operation-types')
  @ApiOperation({ summary: '获取操作类型列表' })
  async getOperationTypes() {
    const types = [
      { value: 1, label: '充值' },
      { value: 2, label: '提现' },
      { value: 3, label: '冻结' },
      { value: 4, label: '解冻' },
      { value: 5, label: '扣款' },
      { value: 6, label: '退款' },
    ];
    return {
      code: 200,
      message: '获取成功',
      data: types,
    };
  }
}
