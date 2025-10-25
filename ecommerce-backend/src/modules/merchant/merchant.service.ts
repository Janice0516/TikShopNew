import { Injectable, HttpException, HttpStatus, UnauthorizedException } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { Merchant } from './entities/merchant.entity'
import { MerchantProduct } from './entities/merchant-product.entity'
import { Product } from '../product/entities/product.entity'
import { ERROR_MESSAGES } from '../../shared-translations/constants/messages'
import { JwtService } from '@nestjs/jwt'
import * as bcrypt from 'bcrypt'

@Injectable()
export class MerchantService {
  constructor(
    @InjectRepository(Merchant)
    private readonly merchantRepository: Repository<Merchant>,
    @InjectRepository(MerchantProduct)
    private readonly merchantProductRepository: Repository<MerchantProduct>,
    @InjectRepository(Product)
    private readonly productRepository: Repository<Product>,
    private readonly jwtService: JwtService,
  ) {}

  async findOne(id: string) {
    const merchant = await this.merchantRepository.findOne({
      where: { id: id },
    })

    if (!merchant) {
      throw new HttpException(ERROR_MESSAGES.MERCHANT_NOT_FOUND, HttpStatus.NOT_FOUND)
    }

    return merchant
  }

  async findAll() {
    return this.merchantRepository.find();
  }

  async create(merchantData: any) {
    const merchant = this.merchantRepository.create(merchantData);
    return this.merchantRepository.save(merchant);
  }

  async update(id: string, merchantData: any) {
    const merchant = await this.findOne(id);
    Object.assign(merchant, merchantData);
    return this.merchantRepository.save(merchant);
  }

  async remove(id: string) {
    const merchant = await this.findOne(id);
    return this.merchantRepository.remove(merchant);
  }

  async getCurrentMerchantShop() {
    return {
      id: 1,
      name: 'TikShop Merchant',
      email: 'merchant@tiktokbusines.store',
      phone: '+60123456789',
      address: 'Kuala Lumpur, Malaysia',
      status: 'active',
      createTime: '2024-01-01 00:00:00',
      updateTime: '2024-10-24 13:00:00'
    }
  }

  async getMerchantShop(id: string) {
    const merchant = await this.merchantRepository.findOne({ where: { id: id } });
    if (!merchant) {
      throw new HttpException(ERROR_MESSAGES.MERCHANT_NOT_FOUND, HttpStatus.NOT_FOUND);
    }
    return merchant;
  }

  async getMerchantProducts(query: any) {
    try {
      // 查询商家商品，关联产品信息
      const queryBuilder = this.merchantProductRepository
        .createQueryBuilder('mp')
        .leftJoinAndSelect('mp.product', 'p')
        .where('mp.merchantId = :merchantId', { merchantId: 1 })

      // 添加状态筛选
      if (query.status) {
        queryBuilder.andWhere('mp.status = :status', { status: query.status })
      }

      // 添加关键词搜索
      if (query.keyword) {
        queryBuilder.andWhere('p.name LIKE :keyword', { keyword: `%${query.keyword}%` })
      }

      // 获取总数
      const total = await queryBuilder.getCount()

      // 分页
      const page = parseInt(query.page) || 1
      const pageSize = parseInt(query.pageSize) || 10
      const skip = (page - 1) * pageSize

      queryBuilder.skip(skip).take(pageSize)

      // 执行查询
      const merchantProducts = await queryBuilder.getMany()

      // 转换数据格式
      const products = merchantProducts.map(mp => {
        const p = mp.product
        const costPrice = parseFloat(p?.costPrice?.toString() || '0')
        const salePrice = parseFloat(mp.salePrice?.toString() || '0')
        const profit = salePrice - costPrice

        return {
          id: mp.id,
          name: p?.name || '未知商品',
          costPrice: costPrice,
          salePrice: salePrice,
          profit: profit,
          stock: p?.stock || 0,
          status: mp.status === 1 ? 'active' : 'inactive',
          image: p?.mainImage || '/uploads/images/default-product.jpg'
        }
      })

      return {
        list: products,
        total: total,
        page: page,
        pageSize: pageSize,
        totalPages: Math.ceil(total / pageSize)
      }
    } catch (error) {
      console.error('获取商家商品列表失败:', error)
      // 如果数据库查询失败，返回空列表而不是抛出错误
      return {
        list: [],
        total: 0,
        page: parseInt(query.page) || 1,
        pageSize: parseInt(query.pageSize) || 10,
        totalPages: 0
      }
    }
  }

  async getFinanceStats() {
    const currentRevenue = 12500.00;
    const previousRevenue = 11000.00;
    const revenueChange = ((currentRevenue - previousRevenue) / previousRevenue * 100).toFixed(1);
    
    const currentOrders = 25;
    const previousOrders = 20;
    const ordersChange = ((currentOrders - previousOrders) / previousOrders * 100).toFixed(1);
    
    const currentProducts = 10;
    const previousProducts = 8;
    const productsChange = ((currentProducts - previousProducts) / previousProducts * 100).toFixed(1);
    
    const currentCustomers = 150;
    const previousCustomers = 120;
    const customersChange = ((currentCustomers - previousCustomers) / previousCustomers * 100).toFixed(1);

    return {
      totalRevenue: currentRevenue,
      totalOrders: currentOrders,
      totalProducts: currentProducts,
      totalCustomers: currentCustomers,
      monthlyRevenue: 3500.00,
      monthlyOrders: 8,
      monthlyProducts: 3,
      monthlyCustomers: 45,
      revenueChange: parseFloat(revenueChange),
      ordersChange: parseFloat(ordersChange),
      productsChange: parseFloat(productsChange),
      customersChange: parseFloat(customersChange)
    };
  }

  async getDashboardStats() {
    const currentProducts = 10;
    const previousProducts = 8;
    const productsChange = ((currentProducts - previousProducts) / previousProducts * 100).toFixed(1);
    
    const currentOrders = 25;
    const previousOrders = 20;
    const ordersChange = ((currentOrders - previousOrders) / previousOrders * 100).toFixed(1);
    
    const currentRevenue = 12500.00;
    const previousRevenue = 11000.00;
    const revenueChange = ((currentRevenue - previousRevenue) / previousRevenue * 100).toFixed(1);
    
    const currentCustomers = 150;
    const previousCustomers = 120;
    const customersChange = ((currentCustomers - previousCustomers) / previousCustomers * 100).toFixed(1);
    
    const currentPendingOrders = 5;
    const previousPendingOrders = 6;
    const pendingOrdersChange = ((currentPendingOrders - previousPendingOrders) / previousPendingOrders * 100).toFixed(1);

    return {
      totalProducts: currentProducts,
      totalOrders: currentOrders,
      totalRevenue: currentRevenue,
      totalCustomers: currentCustomers,
      onShelfProducts: 10,
      offShelfProducts: 0,
      todaysSales: 0,
      pendingOrders: currentPendingOrders,
      completedOrders: 20,
      cancelledOrders: 0,
      productsChange: parseFloat(productsChange),
      ordersChange: parseFloat(ordersChange),
      revenueChange: parseFloat(revenueChange),
      customersChange: parseFloat(customersChange),
      pendingOrdersChange: parseFloat(pendingOrdersChange)
    };
  }

  async getOrdersStats() {
    const currentTotalOrders = 25;
    const previousTotalOrders = 20;
    const totalOrdersChange = ((currentTotalOrders - previousTotalOrders) / previousTotalOrders * 100).toFixed(1);
    
    const currentPendingOrders = 5;
    const previousPendingOrders = 6;
    const pendingOrdersChange = ((currentPendingOrders - previousPendingOrders) / previousPendingOrders * 100).toFixed(1);
    
    const currentCompletedOrders = 20;
    const previousCompletedOrders = 15;
    const completedOrdersChange = ((currentCompletedOrders - previousCompletedOrders) / previousCompletedOrders * 100).toFixed(1);
    
    const currentTodayOrders = 3;
    const previousTodayOrders = 2;
    const todayOrdersChange = ((currentTodayOrders - previousTodayOrders) / previousTodayOrders * 100).toFixed(1);

    return {
      totalOrders: currentTotalOrders,
      pendingOrders: currentPendingOrders,
      completedOrders: currentCompletedOrders,
      cancelledOrders: 0,
      todayOrders: currentTodayOrders,
      weeklyOrders: 12,
      monthlyOrders: 25,
      totalRevenue: 12500.00,
      todayRevenue: 450.00,
      weeklyRevenue: 2100.00,
      monthlyRevenue: 12500.00,
      totalOrdersChange: parseFloat(totalOrdersChange),
      pendingOrdersChange: parseFloat(pendingOrdersChange),
      completedOrdersChange: parseFloat(completedOrdersChange),
      todayOrdersChange: parseFloat(todayOrdersChange)
    };
  }

  async getMerchantOrders(query: any) {
    const orders = [
      {
        id: 1,
        orderNumber: 'ORD001',
        customerName: '张三',
        customerPhone: '13800138000',
        totalAmount: 299.00,
        status: 'pending',
        createTime: '2024-10-24 10:00:00',
        items: [
          {
            productName: '商品A',
            quantity: 2,
            price: 149.50
          }
        ]
      },
      {
        id: 2,
        orderNumber: 'ORD002',
        customerName: '李四',
        customerPhone: '13800138001',
        totalAmount: 199.00,
        status: 'pending',
        createTime: '2024-10-24 11:00:00',
        items: [
          {
            productName: '商品B',
            quantity: 1,
            price: 199.00
          }
        ]
      },
      {
        id: 3,
        orderNumber: 'ORD003',
        customerName: '王五',
        customerPhone: '13800138002',
        totalAmount: 399.00,
        status: 'completed',
        createTime: '2024-10-24 09:00:00',
        items: [
          {
            productName: '商品C',
            quantity: 1,
            price: 399.00
          }
        ]
      }
    ];

    let filteredOrders = orders;
    if (query.status) {
      filteredOrders = orders.filter(order => order.status === query.status);
    }

    const page = parseInt(query.page) || 1;
    const pageSize = parseInt(query.pageSize) || 10;
    const start = (page - 1) * pageSize;
    const end = start + pageSize;
    const paginatedOrders = filteredOrders.slice(start, end);

    return {
      items: paginatedOrders,
      total: filteredOrders.length,
      page: page,
      pageSize: pageSize,
      totalPages: Math.ceil(filteredOrders.length / pageSize)
    };
  }

  async getOrderDetail(id: string) {
    const order = {
      id: parseInt(id),
      orderNumber: 'ORD' + id.padStart(3, '0'),
      customerName: '张三',
      customerPhone: '13800138000',
      customerAddress: '北京市朝阳区xxx街道xxx号',
      totalAmount: 299.00,
      status: 'pending',
      createTime: '2024-10-24 10:00:00',
      items: [
        {
          productName: '商品A',
          quantity: 2,
          price: 149.50,
          image: '/uploads/images/product-a.jpg'
        }
      ],
      shippingInfo: {
        shippingMethod: '顺丰快递',
        trackingNumber: '',
        estimatedDelivery: '2024-10-26'
      }
    };

    return order;
  }

  async shipOrder(id: string, data: any) {
    return {
      success: true,
      message: '发货成功',
      trackingNumber: 'SF' + Date.now(),
      orderId: id
    };
  }

  /**
   * 商家登录，生成JWT
   */
  async login(username: string, password: string) {
    const merchant = await this.merchantRepository.findOne({ where: { username } });
    if (!merchant) {
      throw new UnauthorizedException('用户名或密码错误');
    }

    const isValid = await bcrypt.compare(password, merchant.password);
    if (!isValid) {
      throw new UnauthorizedException('用户名或密码错误');
    }

    const token = this.jwtService.sign({ merchantId: merchant.id, type: 'merchant' });

    const merchantInfo = {
      id: merchant.id,
      merchantUid: merchant.merchantUid,
      username: merchant.username,
      merchantName: merchant.merchantName,
      shopName: merchant.shopName,
      contactName: merchant.contactName,
      contactPhone: merchant.contactPhone,
      status: merchant.status,
      balance: Number(merchant.balance || 0),
      frozenAmount: Number(merchant.frozenAmount || 0),
      totalIncome: Number(merchant.totalIncome || 0),
      totalWithdraw: Number(merchant.totalWithdraw || 0),
      createTime: merchant.createTime,
      updateTime: merchant.updateTime,
    };

    return {
      token,
      merchantInfo,
    };
  }

  /**
   * 获取商家资料
   */
  async getProfile(merchantId: string) {
    const merchant = await this.merchantRepository.findOne({ where: { id: merchantId } });
    if (!merchant) {
      throw new HttpException(ERROR_MESSAGES.MERCHANT_NOT_FOUND, HttpStatus.NOT_FOUND);
    }
    return {
      id: merchant.id,
      merchantUid: merchant.merchantUid,
      username: merchant.username,
      merchantName: merchant.merchantName,
      shopName: merchant.shopName,
      contactName: merchant.contactName,
      contactPhone: merchant.contactPhone,
      status: merchant.status,
      balance: Number(merchant.balance || 0),
      frozenAmount: Number(merchant.frozenAmount || 0),
      totalIncome: Number(merchant.totalIncome || 0),
      totalWithdraw: Number(merchant.totalWithdraw || 0),
      createTime: merchant.createTime,
      updateTime: merchant.updateTime,
    };
  }
}
