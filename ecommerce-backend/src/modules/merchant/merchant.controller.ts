import { Controller, Get, Param, Query, Body, Patch, Post, UseGuards, Request } from '@nestjs/common'
import { ApiOperation, ApiTags, ApiBearerAuth } from '@nestjs/swagger'
import { MerchantService } from './merchant.service'
import { LoginMerchantDto } from './dto/login-merchant.dto'
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard'

@ApiTags('merchant')
@Controller('merchant')
export class MerchantController {
  constructor(private readonly merchantService: MerchantService) {}

  // 商家登录
  @Post('login')
  @ApiOperation({ summary: '商家登录，返回JWT' })
  async login(@Body() body: LoginMerchantDto) {
    const { username, password } = body
    return this.merchantService.login(username, password)
  }

  // 获取商家资料（需登录）
  @Get('profile')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取商家资料' })
  async getProfile(@Request() req) {
    const merchantId = req.user.merchantId
    return this.merchantService.getProfile(String(merchantId))
  }

  @Get('shop')
  @ApiOperation({ summary: '获取当前商家店铺信息' })
  async getCurrentMerchantShop() {
    return this.merchantService.getCurrentMerchantShop()
  }

  @Get('shop/:id')
  @ApiOperation({ summary: '获取商家店铺信息' })
  async getMerchantShop(@Param('id') id: string) {
    return this.merchantService.getMerchantShop(id)
  }

  @Get('products')
  @ApiOperation({ summary: '获取商家商品列表' })
  async getMerchantProducts(@Query() query: any) {
    return this.merchantService.getMerchantProducts(query)
  }

  @Get('finance/stats')
  @ApiOperation({ summary: '获取商家财务统计' })
  async getFinanceStats() {
    return this.merchantService.getFinanceStats()
  }

  @Get('dashboard/stats')
  @ApiOperation({ summary: '获取商家仪表板统计' })
  async getDashboardStats() {
    return this.merchantService.getDashboardStats()
  }

  @Get('orders/stats')
  @ApiOperation({ summary: '获取商家订单统计' })
  async getOrdersStats() {
    return this.merchantService.getOrdersStats()
  }

  @Get('orders')
  @ApiOperation({ summary: '获取商家订单列表' })
  async getMerchantOrders(@Query() query: any) {
    return this.merchantService.getMerchantOrders(query)
  }

  @Get('orders/:id')
  @ApiOperation({ summary: '获取订单详情' })
  async getOrderDetail(@Param('id') id: string) {
    return this.merchantService.getOrderDetail(id)
  }

  @Patch('orders/:id/ship')
  @ApiOperation({ summary: '发货' })
  async shipOrder(@Param('id') id: string, @Body() data: any) {
    return this.merchantService.shipOrder(id, data)
  }
}
