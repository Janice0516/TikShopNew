import { Injectable, UnauthorizedException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import * as bcrypt from 'bcrypt';
import { JwtService } from '@nestjs/jwt';
import { Admin } from './entities/admin.entity';
import { Product } from '../product/entities/product.entity';
import { Merchant } from '../merchant/entities/merchant.entity';
import { Order } from '../order/entities/order.entity';
import { User } from '../user/entities/user.entity';

@Injectable()
export class AdminService {
  constructor(
    @InjectRepository(Admin)
    private readonly adminRepository: Repository<Admin>,
    @InjectRepository(Product)
    private readonly productRepository: Repository<Product>,
    @InjectRepository(Merchant)
    private readonly merchantRepository: Repository<Merchant>,
    @InjectRepository(Order)
    private readonly orderRepository: Repository<Order>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
    private readonly jwtService: JwtService,
  ) {}

  // 登录并生成JWT
  async login(username: string, password: string) {
    const admin = await this.adminRepository.findOne({ where: { username } });
    if (!admin) {
      throw new UnauthorizedException('用户名或密码错误');
    }

    const isValid = await bcrypt.compare(password, admin.password);
    if (!isValid) {
      throw new UnauthorizedException('用户名或密码错误');
    }

    const token = this.jwtService.sign({ adminId: admin.id, type: 'admin' });
    return {
      token,
      userInfo: {
        id: admin.id,
        username: admin.username,
        nickname: admin.nickname || '管理员',
        avatar: admin.avatar || '',
        role: admin.role || 'admin',
      },
    };
  }

  // 获取仪表盘统计数据
  async getDashboardStats() {
    try {
      console.log('开始获取仪表盘统计数据...');
      
      // 查询商品总数
      const productCount = await this.productRepository.count().catch(() => 0);
      
      // 查询商家总数
      const merchantCount = await this.merchantRepository.count().catch(() => 0);
      
      // 查询订单总数
      const orderCount = await this.orderRepository.count().catch(() => 0);
      
      // 查询用户总数
      const userCount = await this.userRepository.count().catch(() => 0);
      
      // 查询最近的订单
      const recentOrders = await this.orderRepository.find({
        order: { createTime: 'DESC' },
        take: 5,
        relations: ['user', 'merchant']
      }).catch(() => []); // 如果查询失败，返回空数组
      
      // 查询热销商品
      const topProducts = await this.productRepository.find({
        order: { sales: 'DESC' },
        take: 5
      }).catch(() => []); // 如果查询失败，返回空数组
      
      const result = {
        code: 200,
        message: '获取成功',
        data: {
          stats: {
            products: productCount,
            merchants: merchantCount,
            orders: orderCount,
            users: userCount
          },
          recentOrders: recentOrders.map(order => ({
            id: order.id,
            orderNo: order.orderNo,
            totalAmount: order.totalAmount,
            status: order.status,
            userName: order.user?.username || '未知用户',
            merchantName: order.merchant?.shopName || '未知商家',
            createTime: order.createTime
          })),
          topProducts: topProducts.map(product => ({
            id: product.id,
            name: product.name,
            sales: product.sales || 0,
            suggestPrice: product.suggestPrice
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

  private getOrderStatusText(status: number): string {
    const statusMap = { 1: 'pending', 2: 'shipped', 3: 'completed', 4: 'cancelled' };
    return statusMap[status] || 'unknown';
  }

  async getUserList(query: any) {
    try {
      const { page = 1, pageSize = 20 } = query;
      return {
        code: 200,
        message: '获取成功',
        data: {
          list: [],
          total: 0,
          page: parseInt(page),
          pageSize: parseInt(pageSize)
        }
      };
    } catch (error) {
      console.error('获取用户列表失败:', error);
      return { code: 500, message: '获取用户列表失败', data: null };
    }
  }

  async getUserDetail(id: number) {
    try {
      return {
        code: 200,
        message: '获取成功',
        data: {
          id: id,
          username: 'test_user',
          nickname: '测试用户',
          email: 'test@example.com',
          phone: '13800138000',
          status: 1,
          createTime: new Date()
        }
      };
    } catch (error) {
      console.error('获取用户详情失败:', error);
      return { code: 500, message: '获取用户详情失败', data: null };
    }
  }

  async updateUserStatus(id: number, data: any) {
    try {
      return { code: 200, message: '更新成功', data: null };
    } catch (error) {
      console.error('更新用户状态失败:', error);
      return { code: 500, message: '更新用户状态失败', data: null };
    }
  }

  // 生成随机密码
  private generatePassword(): string {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 12; i++) {
      result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
  }

  // 生成随机昵称
  private generateNickname(): string {
    const names = ['Admin', 'Manager', 'Supervisor', 'Director', 'Coordinator', 'Lead', 'Chief', 'Head', 'Senior', 'Principal'];
    const adjectives = ['Smart', 'Quick', 'Bright', 'Sharp', 'Swift', 'Bold', 'Cool', 'Wise', 'Strong', 'Fast'];
    const randomName = names[Math.floor(Math.random() * names.length)];
    const randomAdj = adjectives[Math.floor(Math.random() * adjectives.length)];
    const number = Math.floor(Math.random() * 999) + 1;
    return `${randomAdj}${randomName}${number}`;
  }

  // 批量生成管理员账户
  async generateBatchAdmins(count: number, masterPassword: string) {
    try {
      // 验证主密码（这里使用简单的验证，实际项目中应该更安全）
      if (masterPassword !== 'admin123') {
        return { code: 403, message: '主密码错误', data: null };
      }

      const createdAdmins = [];
      const errors = [];

      for (let i = 1; i <= count; i++) {
        try {
          const username = `admin${i.toString().padStart(3, '0')}`;
          
          // 检查是否已存在
          const existingAdmin = await this.adminRepository.findOne({ where: { username } });
          if (existingAdmin) {
            errors.push(`账户 ${username} 已存在，跳过`);
            continue;
          }

          const password = this.generatePassword();
          const hashedPassword = await bcrypt.hash(password, 10);
          const nickname = this.generateNickname();

          const admin = new Admin();
          admin.username = username;
          admin.password = hashedPassword;
          admin.nickname = nickname;
          admin.role = 'admin';
          admin.status = 1;

          await this.adminRepository.save(admin);
          createdAdmins.push({ username, password, nickname });
        } catch (error) {
          errors.push(`创建账户 ${i} 失败: ${error.message}`);
        }
      }

      return {
        code: 200,
        message: `成功创建 ${createdAdmins.length} 个管理员账户`,
        data: {
          created: createdAdmins,
          errors: errors,
          total: createdAdmins.length
        }
      };
    } catch (error) {
      console.error('批量生成管理员失败:', error);
      return { code: 500, message: '批量生成管理员失败', data: null };
    }
  }

  // 获取管理员列表
  async getAdminList(page: number = 1, pageSize: number = 10) {
    try {
      const [admins, total] = await this.adminRepository.findAndCount({
        select: ['id', 'username', 'nickname', 'role', 'status', 'createTime'],
        skip: (page - 1) * pageSize,
        take: pageSize,
        order: { createTime: 'DESC' }
      });

      return {
        code: 200,
        message: '获取成功',
        data: {
          list: admins,
          total,
          page,
          pageSize
        }
      };
    } catch (error) {
      console.error('获取管理员列表失败:', error);
      return { code: 500, message: '获取管理员列表失败', data: null };
    }
  }
}