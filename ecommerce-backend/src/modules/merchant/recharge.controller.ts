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
import { RechargeService } from './recharge.service';
import { CreateRechargeDto, AuditRechargeDto, QueryRechargeDto } from './dto/recharge.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('商户充值管理')
@Controller('recharge')
export class RechargeController {
  constructor(private readonly rechargeService: RechargeService) {}

  // 商家申请充值
  @Post('merchant')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '商家申请充值' })
  async createRecharge(@Request() req, @Body() createDto: CreateRechargeDto) {
    const merchantId = req.user.merchantId;
    return this.rechargeService.createRecharge(merchantId, createDto);
  }

  // 获取商家充值记录
  @Get('merchant')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取商家充值记录' })
  async getMerchantRecharges(@Request() req, @Query() query: QueryRechargeDto) {
    const merchantId = req.user.merchantId;
    return this.rechargeService.getMerchantRecharges(merchantId, query);
  }

  // 获取充值记录列表（管理员）
  @Get('admin')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取充值记录列表' })
  async getRechargeList(@Query() query: QueryRechargeDto) {
    return this.rechargeService.getRechargeList(query);
  }

  // 获取充值详情
  @Get(':id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取充值详情' })
  async getRechargeDetail(@Param('id') id: string) {
    return this.rechargeService.getRechargeDetail(+id);
  }

  // 审核充值
  @Post('audit')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '审核充值' })
  async auditRecharge(@Request() req, @Body() auditDto: AuditRechargeDto) {
    const adminId = req.user.id;
    const adminName = req.user.username || '管理员';
    return this.rechargeService.auditRecharge(adminId, adminName, auditDto);
  }

  // 获取充值统计
  @Get('stats/overview')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取充值统计' })
  async getRechargeStats() {
    return this.rechargeService.getRechargeStats();
  }
}
