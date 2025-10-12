import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, DataSource } from 'typeorm';
import { Order } from './entities/order.entity';
import { OrderItem } from './entities/order-item.entity';
import { CreateOrderDto } from './dto/create-order.dto';
import { QueryOrderDto } from './dto/query-order.dto';
import { ProductService } from '../product/product.service';
import { UserService } from '../user/user.service';

@Injectable()
export class OrderService {
  constructor(
    @InjectRepository(Order)
    private orderRepository: Repository<Order>,
    @InjectRepository(OrderItem)
    private orderItemRepository: Repository<OrderItem>,
    private dataSource: DataSource,
    private productService: ProductService,
    private userService: UserService,
  ) {}

  /**
   * 创建订单
   */
  async create(userId: number, createOrderDto: CreateOrderDto) {
    const { addressId, items, buyerMessage } = createOrderDto;

    // 验证用户是否存在
    const user = await this.userService.findById(userId);

    // 验证地址（这里简化，实际需要查询user_address表）
    // TODO: 查询收货地址
    const address = {
      receiverName: '张三',
      receiverPhone: '13800138000',
      receiverProvince: '广东省',
      receiverCity: '深圳市',
      receiverDistrict: '南山区',
      receiverAddress: '科技园南区',
    };

    // 验证商品并计算价格
    let totalAmount = 0;
    let costAmount = 0;
    const orderItems = [];

    for (const item of items) {
      const product = await this.productService.findOne(item.productId);

      if (product.status !== 1) {
        throw new HttpException(
          `商品【${product.name}】已下架`,
          HttpStatus.BAD_REQUEST,
        );
      }

      if (product.stock < item.quantity) {
        throw new HttpException(
          `商品【${product.name}】库存不足`,
          HttpStatus.BAD_REQUEST,
        );
      }

      const itemTotalPrice = product.costPrice * item.quantity;
      totalAmount += itemTotalPrice;
      costAmount += product.costPrice * item.quantity;

      orderItems.push({
        productId: product.id,
        skuId: item.skuId,
        productName: product.name,
        productImage: product.mainImage,
        skuName: null,
        quantity: item.quantity,
        costPrice: product.costPrice,
        salePrice: product.costPrice, // TODO: 从merchant_product获取售价
        totalPrice: itemTotalPrice,
      });
    }

    // 计算运费（简化处理，固定6元）
    const freight = totalAmount >= 99 ? 0 : 6;
    const payAmount = totalAmount + freight;

    // 使用事务创建订单
    const queryRunner = this.dataSource.createQueryRunner();
    await queryRunner.connect();
    await queryRunner.startTransaction();

    try {
      // 生成订单号
      const orderNo = this.generateOrderNo();

      // 创建订单主表
      const order = this.orderRepository.create({
        orderNo,
        userId: String(userId),
        merchantId: String(1), // TODO: 从商品获取商家ID
        productId: String(orderItems[0].productId), // 假设只有一个商品
        quantity: orderItems[0].quantity,
        totalAmount,
        costAmount,
        merchantProfit: totalAmount - costAmount,
        platformProfit: 0,
        freight,
        discountAmount: 0,
        payAmount,
        ...address,
        orderStatus: 1, // 待付款
        payStatus: 0, // 未支付
        buyerMessage,
      });

      const savedOrder = await queryRunner.manager.save(order);

      // 创建订单明细
      for (const item of orderItems) {
        const orderItem = this.orderItemRepository.create({
          orderId: String(savedOrder.id),
          productId: String(item.productId),
          ...item,
        });
        await queryRunner.manager.save(orderItem);
      }

      // 扣减库存
      for (const item of items) {
        await this.productService.decreaseStock(item.productId, item.quantity);
      }

      await queryRunner.commitTransaction();

      return {
        orderId: savedOrder.id,
        orderNo: savedOrder.orderNo,
        payAmount: savedOrder.payAmount,
      };
    } catch (error) {
      await queryRunner.rollbackTransaction();
      throw error;
    } finally {
      await queryRunner.release();
    }
  }

  /**
   * 查询订单列表
   */
  async findAll(userId: number, queryDto: QueryOrderDto, userType = 'user') {
    const { page, pageSize, orderStatus, orderNo } = queryDto;

    const queryBuilder = this.orderRepository.createQueryBuilder('order');

    // 根据用户类型筛选
    if (userType === 'user') {
      queryBuilder.where('order.user_id = :userId', { userId });
    } else if (userType === 'merchant') {
      queryBuilder.where('order.merchant_id = :userId', { userId });
    }

    // 订单状态筛选
    if (orderStatus !== undefined) {
      queryBuilder.andWhere('order.order_status = :orderStatus', {
        orderStatus,
      });
    }

    // 订单号搜索
    if (orderNo) {
      queryBuilder.andWhere('order.order_no LIKE :orderNo', {
        orderNo: `%${orderNo}%`,
      });
    }

    // 分页
    const [list, total] = await queryBuilder
      .orderBy('order.id', 'DESC')
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    // 查询订单明细
    for (const order of list) {
      const items = await this.orderItemRepository.find({
        where: { orderId: String(order.id) },
      });
      (order as any).items = items;
    }

    return {
      list,
      total,
      page,
      pageSize,
      totalPages: Math.ceil(total / pageSize),
    };
  }

  /**
   * 查询订单详情
   */
  async findOne(id: number, userId: number, userType = 'user') {
    const order = await this.orderRepository.findOne({ where: { id: String(id) } });

    if (!order) {
      throw new HttpException('订单不存在', HttpStatus.NOT_FOUND);
    }

    // 验证权限
    if (userType === 'user' && order.userId !== String(userId)) {
      throw new HttpException('无权查看该订单', HttpStatus.FORBIDDEN);
    }
    if (userType === 'merchant' && order.merchantId !== String(userId)) {
      throw new HttpException('无权查看该订单', HttpStatus.FORBIDDEN);
    }

    // 查询订单明细
    const items = await this.orderItemRepository.find({
      where: { orderId: String(order.id) },
    });

    return {
      ...order,
      items,
    };
  }

  /**
   * 取消订单
   */
  async cancel(id: number, userId: number, reason: string) {
    const order = await this.findOne(id, userId);

    // 只有待付款状态可以取消
    if (order.orderStatus !== 1) {
      throw new HttpException('该订单不能取消', HttpStatus.BAD_REQUEST);
    }

    // 更新订单状态
    await this.orderRepository.update(id, {
      orderStatus: 5, // 已取消
      cancelTime: new Date(),
      cancelReason: reason,
    });

    // 恢复库存
    for (const item of order.items) {
      await this.productService.updateStock(String(item.productId), item.quantity);
    }

    return { message: '订单已取消' };
  }

  /**
   * 确认收货
   */
  async confirm(id: number, userId: number) {
    const order = await this.findOne(id, userId);

    // 只有待收货状态可以确认
    if (order.orderStatus !== 3) {
      throw new HttpException('该订单不能确认收货', HttpStatus.BAD_REQUEST);
    }

    // 更新订单状态
    await this.orderRepository.update(id, {
      orderStatus: 4, // 已完成
      receiveTime: new Date(),
      finishTime: new Date(),
    });

    // TODO: 结算商家收益

    return { message: '确认收货成功' };
  }

  /**
   * 模拟支付（开发环境）
   */
  async mockPay(id: number, userId: number) {
    const order = await this.findOne(id, userId);

    if (order.orderStatus !== 1) {
      throw new HttpException('订单状态错误', HttpStatus.BAD_REQUEST);
    }

    if (order.payStatus === 1) {
      throw new HttpException('订单已支付', HttpStatus.BAD_REQUEST);
    }

    // 更新支付状态
    await this.orderRepository.update(id, {
      orderStatus: 2, // 待发货
      payStatus: 1, // 已支付
      payType: 1, // 微信支付
      payTime: new Date(),
      transactionId: `MOCK${Date.now()}`,
    });

    return { message: '支付成功' };
  }

  /**
   * 发货（商家操作）
   */
  async ship(
    id: number,
    merchantId: number,
    logisticsCompany: string,
    trackingNumber: string,
  ) {
    const order = await this.findOne(id, merchantId, 'merchant');

    if (order.orderStatus !== 2) {
      throw new HttpException('订单状态错误', HttpStatus.BAD_REQUEST);
    }

    // 更新订单状态
    await this.orderRepository.update(id, {
      orderStatus: 3, // 待收货
      shipTime: new Date(),
    });

    // TODO: 插入物流信息到order_logistics表

    return { message: '发货成功' };
  }

  /**
   * 生成订单号
   */
  private generateOrderNo(): string {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hour = String(date.getHours()).padStart(2, '0');
    const minute = String(date.getMinutes()).padStart(2, '0');
    const second = String(date.getSeconds()).padStart(2, '0');
    const random = Math.floor(Math.random() * 10000)
      .toString()
      .padStart(4, '0');

    return `${year}${month}${day}${hour}${minute}${second}${random}`;
  }

  /**
   * 统计订单数量（按状态）
   */
  async countByStatus(userId: number, userType = 'user') {
    const where: any = {};
    if (userType === 'user') {
      where.userId = userId;
    } else if (userType === 'merchant') {
      where.merchantId = userId;
    }

    const [
      waitPayCount,
      waitShipCount,
      waitReceiveCount,
      finishedCount,
    ] = await Promise.all([
      this.orderRepository.count({ where: { ...where, orderStatus: 1 } }),
      this.orderRepository.count({ where: { ...where, orderStatus: 2 } }),
      this.orderRepository.count({ where: { ...where, orderStatus: 3 } }),
      this.orderRepository.count({ where: { ...where, orderStatus: 4 } }),
    ]);

    return {
      waitPay: waitPayCount,
      waitShip: waitShipCount,
      waitReceive: waitReceiveCount,
      finished: finishedCount,
    };
  }
}

