import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  UseGuards,
  Request,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { WithdrawalInfoService } from './withdrawal-info.service';
import { CreateWithdrawalInfoDto, UpdateWithdrawalInfoDto, SetDefaultWithdrawalInfoDto } from './dto/withdrawal-info.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('商户提款信息管理')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
@Controller('merchant/withdrawal-info')
export class WithdrawalInfoController {
  constructor(private readonly withdrawalInfoService: WithdrawalInfoService) {}

  @Post()
  @ApiOperation({ summary: '创建提款信息' })
  async create(@Request() req, @Body() createWithdrawalInfoDto: CreateWithdrawalInfoDto) {
    const merchantId = req.user.id;
    return this.withdrawalInfoService.createWithdrawalInfo(merchantId, createWithdrawalInfoDto);
  }

  @Get()
  @ApiOperation({ summary: '获取提款信息列表' })
  async findAll(@Request() req) {
    const merchantId = req.user.id;
    return this.withdrawalInfoService.getWithdrawalInfoList(merchantId);
  }

  @Get(':id')
  @ApiOperation({ summary: '获取提款信息详情' })
  async findOne(@Request() req, @Param('id') id: string) {
    const merchantId = req.user.id;
    return this.withdrawalInfoService.getWithdrawalInfoDetail(+id, merchantId);
  }

  @Put(':id')
  @ApiOperation({ summary: '更新提款信息' })
  async update(
    @Request() req,
    @Param('id') id: string,
    @Body() updateWithdrawalInfoDto: UpdateWithdrawalInfoDto,
  ) {
    const merchantId = req.user.id;
    return this.withdrawalInfoService.updateWithdrawalInfo(+id, merchantId, updateWithdrawalInfoDto);
  }

  @Delete(':id')
  @ApiOperation({ summary: '删除提款信息' })
  async remove(@Request() req, @Param('id') id: string) {
    const merchantId = req.user.id;
    return this.withdrawalInfoService.deleteWithdrawalInfo(+id, merchantId);
  }

  @Post('set-default')
  @ApiOperation({ summary: '设置默认提款信息' })
  async setDefault(@Request() req, @Body() setDefaultDto: SetDefaultWithdrawalInfoDto) {
    const merchantId = req.user.id;
    return this.withdrawalInfoService.setDefaultWithdrawalInfo(merchantId, setDefaultDto);
  }

  @Get('utils/banks')
  @ApiOperation({ summary: '获取马来西亚银行列表' })
  async getBanks() {
    return this.withdrawalInfoService.getMalaysianBanks();
  }

  @Get('utils/wallets')
  @ApiOperation({ summary: '获取马来西亚电子钱包列表' })
  async getWallets() {
    return this.withdrawalInfoService.getMalaysianWallets();
  }
}

