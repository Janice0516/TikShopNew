import {
  Controller,
  Get,
  Post,
  Body,
  Param,
  Query,
  UseGuards,
  Request,
  Patch,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { OrderService } from './order.service';
import { CreateOrderDto } from './dto/create-order.dto';
import { QueryOrderDto } from './dto/query-order.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('订单模块')
@Controller('orders')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class OrderController {
  constructor(private readonly orderService: OrderService) {}

  @Post()
  @ApiOperation({ summary: '创建订单' })
  create(@Request() req, @Body() createOrderDto: CreateOrderDto) {
    return this.orderService.create(req.user.userId, createOrderDto);
  }

  @Get()
  @ApiOperation({ summary: '订单列表' })
  findAll(@Request() req, @Query() queryDto: QueryOrderDto) {
    const userType = req.user.type || 'user'; // user or merchant
    const userId = req.user.userId || req.user.merchantId; // 兼容商家token
    return this.orderService.findAll(+userId, queryDto, userType);
  }

  @Get('count')
  @ApiOperation({ summary: '统计订单数量（按状态）' })
  countByStatus(@Request() req) {
    const userType = req.user.type || 'user';
    const userId = req.user.userId || req.user.merchantId; // 兼容商家token
    return this.orderService.countByStatus(+userId, userType);
  }

  @Get(':id')
  @ApiOperation({ summary: '订单详情' })
  findOne(@Param('id') id: string, @Request() req) {
    const userType = req.user.type || 'user';
    const userId = req.user.userId || req.user.merchantId; // 兼容商家token
    return this.orderService.findOne(+id, +userId, userType);
  }

  @Patch(':id/cancel')
  @ApiOperation({ summary: '取消订单' })
  cancel(
    @Param('id') id: string,
    @Request() req,
    @Body('reason') reason: string,
  ) {
    const userId = req.user.userId || req.user.merchantId; // 兼容商家token
    return this.orderService.cancel(+id, +userId, reason);
  }

  @Patch(':id/confirm')
  @ApiOperation({ summary: '确认收货' })
  confirm(@Param('id') id: string, @Request() req) {
    return this.orderService.confirm(+id, req.user.userId);
  }

  @Post(':id/pay')
  @ApiOperation({ summary: '模拟支付（开发环境）' })
  mockPay(@Param('id') id: string, @Request() req) {
    return this.orderService.mockPay(+id, req.user.userId);
  }

  @Patch(':id/ship')
  @ApiOperation({ summary: '发货（商家）' })
  ship(
    @Param('id') id: string,
    @Request() req,
    @Body('logisticsCompany') logisticsCompany: string,
    @Body('trackingNumber') trackingNumber: string,
  ) {
    const userId = req.user.userId || req.user.merchantId; // 兼容商家token
    return this.orderService.ship(
      +id,
      +userId,
      logisticsCompany,
      trackingNumber,
    );
  }
}

