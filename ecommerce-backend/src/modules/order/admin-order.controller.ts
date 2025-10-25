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
  Inject,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { OrderService } from './order.service';
import { QueryOrderDto } from './dto/query-order.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Order } from './entities/order.entity';
import { OrderItem } from './entities/order-item.entity';
import { Merchant } from '../merchant/entities/merchant.entity';

@ApiTags('管理员订单管理')
@Controller('admin/orders')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class AdminOrderController {
  constructor(
    private readonly orderService: OrderService,
    @InjectRepository(Order)
    private orderRepository: Repository<Order>,
    @InjectRepository(OrderItem)
    private orderItemRepository: Repository<OrderItem>,
    @InjectRepository(Merchant)
    private merchantRepository: Repository<Merchant>,
  ) {}

  @Get()
  @ApiOperation({ summary: '获取所有订单列表（管理员）' })
  async findAll(@Query() queryDto: QueryOrderDto) {
    try {
      const { page = 1, pageSize = 10 } = queryDto;
      
      // 简化查询，先返回空数据
      const list = [];
      const total = 0;

      return {
        code: 200,
        message: '获取成功',
        data: {
          list,
          total,
          page,
          pageSize,
          totalPages: Math.ceil(total / pageSize),
        },
      };
    } catch (error) {
      console.error('获取订单列表失败:', error);
      return {
        code: 500,
        message: '获取订单列表失败',
        data: null,
      };
    }
  }

  @Get('count')
  @ApiOperation({ summary: '统计订单数量（管理员）' })
  async countByStatus() {
    try {
      const counts = await this.orderRepository
        .createQueryBuilder('order')
        .select('order.order_status', 'status')
        .addSelect('COUNT(*)', 'count')
        .groupBy('order.order_status')
        .getRawMany();

      const result = {
        pending: 0,    // 待支付
        paid: 0,       // 已支付
        shipped: 0,    // 已发货
        completed: 0,  // 已完成
        cancelled: 0,  // 已取消
      };

      counts.forEach(item => {
        switch (item.status) {
          case 0:
            result.pending = parseInt(item.count);
            break;
          case 1:
            result.paid = parseInt(item.count);
            break;
          case 2:
            result.shipped = parseInt(item.count);
            break;
          case 3:
            result.completed = parseInt(item.count);
            break;
          case 4:
            result.cancelled = parseInt(item.count);
            break;
        }
      });

      return {
        code: 200,
        message: '获取成功',
        data: result,
      };
    } catch (error) {
      console.error('统计订单数量失败:', error);
      return {
        code: 500,
        message: '统计失败',
        data: null,
      };
    }
  }

  @Get(':id')
  @ApiOperation({ summary: '获取订单详情（管理员）' })
  async findOne(@Param('id') id: string) {
    try {
      const order = await this.orderRepository.findOne({
        where: { id: String(id) },
      });

      if (!order) {
        return {
          code: 404,
          message: '订单不存在',
          data: null,
        };
      }

      // 查询订单明细
      const items = await this.orderItemRepository.find({
        where: { orderId: String(order.id) },
      });

      (order as any).items = items;

      return {
        code: 200,
        message: '获取成功',
        data: order,
      };
    } catch (error) {
      console.error('获取订单详情失败:', error);
      return {
        code: 500,
        message: '获取订单详情失败',
        data: null,
      };
    }
  }

  @Patch(':id/cancel')
  @ApiOperation({ summary: '取消订单（管理员）' })
  async cancel(@Param('id') id: string, @Body('reason') reason: string) {
    try {
      const order = await this.orderRepository.findOne({
        where: { id: String(id) },
      });

      if (!order) {
        return {
          code: 404,
          message: '订单不存在',
          data: null,
        };
      }

      if (order.orderStatus === 4) {
        return {
          code: 400,
          message: '订单已取消',
          data: null,
        };
      }

      // 更新订单状态
      await this.orderRepository.update(String(id), {
        orderStatus: 4,
        cancelReason: reason || '管理员取消',
        cancelTime: new Date(),
      });

      return {
        code: 200,
        message: '订单取消成功',
        data: null,
      };
    } catch (error) {
      console.error('取消订单失败:', error);
      return {
        code: 500,
        message: '取消订单失败',
        data: null,
      };
    }
  }

  @Patch(':id/ship')
  @ApiOperation({ summary: '发货（管理员）' })
  async ship(
    @Param('id') id: string,
    @Body('logisticsCompany') logisticsCompany: string,
    @Body('trackingNumber') trackingNumber: string,
  ) {
    try {
      const order = await this.orderRepository.findOne({
        where: { id: String(id) },
      });

      if (!order) {
        return { code: 404, message: '订单不存在', data: null };
      }

      if (order.orderStatus !== 1) {
        return { code: 400, message: '订单状态不允许发货', data: null };
      }

      // 新增：校验商户余额为负数禁止发货
      const merchant = await this.merchantRepository.findOne({
        where: { id: order.merchantId },
      });
      if (!merchant) {
        return { code: 404, message: '商家不存在', data: null };
      }
      const balance =
        typeof (merchant.balance as any) === 'string'
          ? parseFloat(merchant.balance as any)
          : merchant.balance || 0;
      if (balance < 0) {
        return {
          code: 400,
          message: '商户账户资金为负数，请先充值后再发货',
          data: null,
        };
      }

      // 更新订单状态
      await this.orderRepository.update(String(id), {
        orderStatus: 2,
        shipTime: new Date(),
      });

      return { code: 200, message: '发货成功', data: null };
    } catch (error) {
      console.error('发货失败:', error);
      return { code: 500, message: '发货失败', data: null };
    }
  }
}
