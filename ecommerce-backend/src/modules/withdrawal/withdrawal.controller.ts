import {
  Controller,
  Get,
  Post,
  Put,
  Body,
  Param,
  Query,
  UseGuards,
  Request,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { WithdrawalService } from './withdrawal.service';
import { BalanceService } from './balance.service';
import { CreateWithdrawalDto, UpdateWithdrawalStatusDto } from './dto/withdrawal.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('提现管理')
@Controller('withdrawal')
export class WithdrawalController {
  constructor(
    private readonly withdrawalService: WithdrawalService,
    private readonly balanceService: BalanceService,
  ) {}

  @Get('test')
  @ApiOperation({ summary: '测试提现API' })
  async test() {
    return {
      code: 200,
      message: '提现API测试成功',
      data: { timestamp: new Date().toISOString() },
    };
  }

  @Get('balance')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取商户余额信息' })
  async getMerchantBalance(@Request() req) {
    const merchantId = req.user.merchantId;
    return this.balanceService.getMerchantBalance(merchantId);
  }

  @Post()
  @ApiOperation({ summary: '创建提现申请（商户）' })
  async createWithdrawal(@Request() req, @Body() createWithdrawalDto: CreateWithdrawalDto) {
    const merchantId = req.user.merchantId;
    return this.withdrawalService.createWithdrawal(merchantId, createWithdrawalDto);
  }

  @Get('list')
  @ApiOperation({ summary: '获取提现列表（管理员）' })
  async getWithdrawalList(@Query() params: any) {
    return this.withdrawalService.getWithdrawalList(params);
  }

  @Get(':id')
  @ApiOperation({ summary: '获取提现详情' })
  async getWithdrawalDetail(@Param('id') id: string) {
    return this.withdrawalService.getWithdrawalDetail(+id);
  }

  @Put(':id/status')
  @ApiOperation({ summary: '更新提现状态（管理员）' })
  async updateWithdrawalStatus(
    @Param('id') id: string,
    @Body() updateStatusDto: UpdateWithdrawalStatusDto,
    @Request() req,
  ) {
    const adminId = req.user.adminId;
    return this.withdrawalService.updateWithdrawalStatus(+id, updateStatusDto, adminId);
  }

  @Get('merchant/list')
  @ApiOperation({ summary: '获取商户提现记录' })
  async getMerchantWithdrawals(@Request() req, @Query() params: any) {
    const merchantId = req.user.merchantId;
    return this.withdrawalService.getMerchantWithdrawals(merchantId, params);
  }
}
