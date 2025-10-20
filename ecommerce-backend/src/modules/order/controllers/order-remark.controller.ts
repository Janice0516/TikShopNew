import { Controller, Post, Get, Body, Query, Param, Patch, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { OrderService } from '../order.service';
import { UpdateOrderRemarkDto, OrderLogisticsDto } from '../dto/order-remark.dto';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../common/decorators/roles.decorator';

@ApiTags('订单备注管理')
@Controller('admin/order-remark')
export class OrderRemarkController {
  constructor(private readonly orderService: OrderService) {}

  @Get(':id')
  @ApiOperation({ summary: '获取订单详情（包含备注和物流信息）' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async getOrderDetail(@Param('id') id: string) {
    const order = await this.orderService.getOrderDetail(id);
    return { code: 200, message: 'success', data: order };
  }

  @Patch(':id/remark')
  @ApiOperation({ summary: '更新订单备注' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async updateOrderRemark(@Param('id') id: string, @Body() updateRemarkDto: UpdateOrderRemarkDto) {
    const order = await this.orderService.updateOrderRemark(id, updateRemarkDto);
    return { code: 200, message: 'success', data: order };
  }

  @Patch('logistics')
  @ApiOperation({ summary: '更新订单物流状态' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async updateOrderLogistics(@Body() logisticsDto: OrderLogisticsDto) {
    const order = await this.orderService.updateOrderLogistics(logisticsDto);
    return { code: 200, message: 'success', data: order };
  }

  @Post('batch-logistics')
  @ApiOperation({ summary: '批量更新订单物流状态' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async batchUpdateLogistics(@Body() orders: Array<{
    orderId: string;
    logisticsStatus: string;
    trackingNumber?: string;
    logisticsCompany?: string;
    adminRemark?: string;
  }>) {
    const results = await this.orderService.batchUpdateLogistics(orders);
    return { code: 200, message: 'success', data: results };
  }

  @Get(':id/logistics-history')
  @ApiOperation({ summary: '获取订单物流历史记录' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async getLogisticsHistory(@Param('id') id: string) {
    const history = await this.orderService.getLogisticsHistory(id);
    return { code: 200, message: 'success', data: history };
  }
}
