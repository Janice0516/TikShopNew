import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Product } from '../product/entities/product.entity';
import { Merchant } from '../merchant/entities/merchant.entity';
import { Order } from '../order/entities/order.entity';
import { User } from '../user/entities/user.entity';

@Injectable()
export class AdminService {
  constructor(
    @InjectRepository(Product)
    private productRepository: Repository<Product>,
    @InjectRepository(Merchant)
    private merchantRepository: Repository<Merchant>,
    @InjectRepository(Order)
    private orderRepository: Repository<Order>,
    @InjectRepository(User)
    private userRepository: Repository<User>,
  ) {}

  // 获取仪表盘统计数据
  async getDashboardStats() {
    try {
      console.log('开始获取仪表盘统计数据...');
      
      // 获取商品总数
      console.log('查询商品总数...');
      const totalProducts = await this.productRepository.count();
      console.log('商品总数:', totalProducts);
      
      // 获取活跃商家数（状态为1的商家）
      console.log('查询活跃商家数...');
      const activeMerchants = await this.merchantRepository.count({
        where: { status: 1 }
      });
      console.log('活跃商家数:', activeMerchants);
      
      // 获取订单总数
      console.log('查询订单总数...');
      const totalOrders = await this.orderRepository.count();
      console.log('订单总数:', totalOrders);
      
      // 获取注册用户总数
      console.log('查询用户总数...');
      const totalUsers = await this.userRepository.count();
      console.log('用户总数:', totalUsers);

      // 获取最近订单（最近5个）
      console.log('查询最近订单...');
      const recentOrders = await this.orderRepository.find({
        take: 5,
        order: { createTime: 'DESC' }
      });
      console.log('最近订单数量:', recentOrders.length);

      // 获取热销商品（按销量排序，取前5个）
      console.log('查询热销商品...');
      const topProducts = await this.productRepository.find({
        take: 5,
        order: { sales: 'DESC' },
        where: { status: 1 }
      });
      console.log('热销商品数量:', topProducts.length);

      const result = {
        code: 200,
        message: '获取成功',
        data: {
          stats: {
            products: totalProducts,
            merchants: activeMerchants,
            orders: totalOrders,
            users: totalUsers
          },
          recentOrders: recentOrders.map(order => ({
            orderNo: order.orderNo,
            customerName: `User ${order.userId}`,
            totalAmount: order.totalAmount?.toString() || '0.00',
            status: this.getOrderStatusText(order.orderStatus),
            createTime: order.createTime
          })),
          topProducts: topProducts.map(product => ({
            name: product.name,
            sales: product.sales || 0,
            stock: product.stock || 0,
            price: product.suggestPrice?.toString() || product.costPrice?.toString() || '0.00',
            status: product.status
          }))
        }
      };
      
      console.log('仪表盘统计数据获取成功:', result);
      return result;
    } catch (error) {
      console.error('获取仪表盘统计数据失败:', error);
      console.error('错误堆栈:', error.stack);
      return {
        code: 500,
        message: '获取统计数据失败',
        data: null
      };
    }
  }

  // 获取订单状态文本
  private getOrderStatusText(status: number): string {
    const statusMap = {
      1: 'pending',
      2: 'shipped', 
      3: 'shipped',
      4: 'completed',
      5: 'cancelled'
    };
    return statusMap[status] || 'pending';
  }

  // 获取用户列表
  async getUserList(query: any) {
    try {
      const { page = 1, pageSize = 20, phone, nickname, status, startDate, endDate } = query;
      
      const queryBuilder = this.userRepository.createQueryBuilder('user');
      
      // 添加查询条件
      if (phone) {
        queryBuilder.andWhere('user.phone LIKE :phone', { phone: `%${phone}%` });
      }
      
      if (nickname) {
        queryBuilder.andWhere('user.nickname LIKE :nickname', { nickname: `%${nickname}%` });
      }
      
      if (status !== undefined && status !== '') {
        queryBuilder.andWhere('user.status = :status', { status: parseInt(status) });
      }
      
      if (startDate && endDate) {
        queryBuilder.andWhere('user.createTime BETWEEN :startDate AND :endDate', {
          startDate: `${startDate} 00:00:00`,
          endDate: `${endDate} 23:59:59`
        });
      }
      
      // 排序
      queryBuilder.orderBy('user.createTime', 'DESC');
      
      // 分页
      const skip = (page - 1) * pageSize;
      queryBuilder.skip(skip).take(pageSize);
      
      const [users, total] = await queryBuilder.getManyAndCount();
      
      return {
        code: 200,
        message: '获取成功',
        data: {
          list: users,
          total,
          page: parseInt(page),
          pageSize: parseInt(pageSize)
        }
      };
    } catch (error) {
      console.error('获取用户列表失败:', error);
      return {
        code: 500,
        message: '获取用户列表失败',
        data: null
      };
    }
  }

  // 获取用户详情
  async getUserDetail(id: number) {
    try {
      const user = await this.userRepository.findOne({
        where: { id: String(id) }
      });
      
      if (!user) {
        return {
          code: 404,
          message: '用户不存在',
          data: null
        };
      }
      
      return {
        code: 200,
        message: '获取成功',
        data: user
      };
    } catch (error) {
      console.error('获取用户详情失败:', error);
      return {
        code: 500,
        message: '获取用户详情失败',
        data: null
      };
    }
  }

  // 更新用户状态
  async updateUserStatus(id: number, data: any) {
    try {
      const user = await this.userRepository.findOne({
        where: { id: String(id) }
      });
      
      if (!user) {
        return {
          code: 404,
          message: '用户不存在',
          data: null
        };
      }
      
      await this.userRepository.update(id, { status: data.status });
      
      return {
        code: 200,
        message: '更新成功',
        data: null
      };
    } catch (error) {
      console.error('更新用户状态失败:', error);
      return {
        code: 500,
        message: '更新用户状态失败',
        data: null
      };
    }
  }
}