import {
  Controller,
  Post,
  Get,
  Patch,
  Put,
  Body,
  Request,
  Param,
  Query,
  UseGuards,
  HttpException,
  HttpStatus,
  UseInterceptors,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { MerchantService } from './merchant.service';
import { RegisterMerchantDto } from './dto/register-merchant.dto';
import { LoginMerchantDto } from './dto/login-merchant.dto';
import { QueryMerchantDto } from './dto/query-merchant.dto';
import { UpdateMerchantDto } from './dto/update-merchant.dto';
import { ResetPasswordDto } from './dto/reset-password.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { NoTransformInterceptor } from '../../common/interceptors/no-transform.interceptor';

@ApiTags('商家管理')
@Controller('merchant')
export class MerchantController {
  constructor(private readonly merchantService: MerchantService) {}

  @Post('register')
  @ApiOperation({ summary: '商家注册' })
  async register(@Body() registerDto: RegisterMerchantDto) {
    try {
      const result = await this.merchantService.register(registerDto);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('商家注册错误:', error);
      return {
        code: 400,
        message: error.message,
        data: null,
      };
    }
  }

  @Post('login')
  @ApiOperation({ summary: '商家登录' })
  async login(@Body() loginDto: LoginMerchantDto) {
    try {
      const result = await this.merchantService.login(loginDto);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('商家登录错误:', error);
      return {
        code: error.status || 400,
        message: error.message,
        data: null,
      };
    }
  }

  @Get('profile')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取商家信息' })
  async getProfile(@Request() req) {
    try {
      const merchantId = req.user.merchantId || req.user.id;
      const merchant = await this.merchantService.getMerchantById(merchantId);
      
      if (!merchant) {
        return {
          code: 404,
          message: '商家不存在',
          data: null,
        };
      }

      // 商家信息已经是安全的（findById方法已经移除了敏感信息）
      return {
        code: 200,
        message: '获取成功',
        data: merchant,
      };
    } catch (error) {
      console.error('获取商家信息失败:', error);
      return {
        code: 500,
        message: '获取商家信息失败',
        data: null,
      };
    }
  }

  @Put('profile')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '更新商家信息' })
  async updateProfile(@Request() req, @Body() updateDto: UpdateMerchantDto) {
    try {
      const merchantId = req.user.merchantId || req.user.id;
      const result = await this.merchantService.updateMerchant(merchantId, updateDto);
      
      return {
        code: 200,
        message: '更新成功',
        data: result,
      };
    } catch (error) {
      console.error('更新商家信息失败:', error);
      return {
        code: 500,
        message: '更新商家信息失败',
        data: null,
      };
    }
  }

  @Get('dashboard/stats')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取商家仪表盘统计数据' })
  async getDashboardStats(@Request() req) {
    try {
      const merchantId = req.user.merchantId || req.user.userId;
      const result = await this.merchantService.getDashboardStats(merchantId);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('获取商家仪表盘统计错误:', error);
      return {
        code: 500,
        message: 'Internal server error',
        data: null,
      };
    }
  }

  @Get('finance/stats')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取商家财务统计' })
  async getFinanceStats(@Request() req) {
    try {
      const merchantId = req.user.merchantId || req.user.userId;
      const result = await this.merchantService.getFinanceStats(merchantId);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('获取商家财务统计错误:', error);
      return {
        code: 500,
        message: 'Internal server error',
        data: null,
      };
    }
  }

  @Get('orders/stats')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取商家订单统计' })
  async getOrdersStats(@Request() req) {
    try {
      const merchantId = req.user.merchantId || req.user.userId;
      const result = await this.merchantService.getOrdersStats(merchantId);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('获取商家订单统计错误:', error);
      return {
        code: 500,
        message: 'Internal server error',
        data: null,
      };
    }
  }

  @Get('shop')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取店铺信息' })
  async getShop(@Request() req) {
    try {
      const merchantId = req.user.merchantId || req.user.userId;
      const result = await this.merchantService.getShopInfo(merchantId);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('获取店铺信息错误:', error);
      return {
        code: 500,
        message: 'Internal server error',
        data: null,
      };
    }
  }

  @Patch('shop')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '更新店铺信息' })
  updateShop(@Request() req, @Body() shopData: any) {
    return this.merchantService.updateShop(req.user.userId, shopData);
  }

  @Get('list')
  @UseGuards(JwtAuthGuard)
  @UseInterceptors(NoTransformInterceptor)
  @ApiBearerAuth()
  @ApiOperation({ summary: '商家列表（平台管理）' })
  async findAll(@Query() query: QueryMerchantDto) {
    try {
      const result = await this.merchantService.findAll(query);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('获取商家列表错误:', error);
      return {
        code: 500,
        message: 'Internal server error',
        data: null,
      };
    }
  }

  @Patch(':id/audit')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '审核商家（平台管理）' })
  audit(
    @Param('id') id: string,
    @Body() auditData: { status: number; rejectReason?: string },
  ) {
    return this.merchantService.audit(parseInt(id), auditData.status, auditData.rejectReason);
  }

  @Put(':id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '更新商家信息（平台管理）' })
  async updateMerchant(
    @Param('id') id: string,
    @Body() updateData: UpdateMerchantDto,
  ) {
    try {
      const result = await this.merchantService.updateMerchant(parseInt(id), updateData);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('更新商家信息错误:', error);
      return {
        code: 400,
        message: error.message || '更新失败',
        data: null,
      };
    }
  }

  @Patch(':id/reset-password')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '重置商家密码（平台管理）' })
  async resetPassword(
    @Param('id') id: string,
    @Body() resetPasswordData: ResetPasswordDto,
  ) {
    try {
      const result = await this.merchantService.resetPassword(parseInt(id), resetPasswordData.newPassword);
      return {
        code: 200,
        message: 'success',
        data: result,
      };
    } catch (error) {
      console.error('重置密码错误:', error);
      return {
        code: 400,
        message: error.message || '重置密码失败',
        data: null,
      };
    }
  }
}