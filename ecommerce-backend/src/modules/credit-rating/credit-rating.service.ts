import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { MerchantCreditRating } from './entities/merchant-credit-rating.entity';
import { CreateCreditRatingDto, UpdateCreditRatingDto, QueryCreditRatingDto } from './dto/credit-rating.dto';
import { Merchant } from '../merchant/entities/merchant.entity';
import { Order } from '../order/entities/order.entity';

@Injectable()
export class CreditRatingService {
  constructor(
    @InjectRepository(MerchantCreditRating)
    private creditRatingRepository: Repository<MerchantCreditRating>,
    @InjectRepository(Merchant)
    private merchantRepository: Repository<Merchant>,
    @InjectRepository(Order)
    private orderRepository: Repository<Order>,
  ) {}

  /**
   * 创建信用评级
   */
  async createCreditRating(createCreditRatingDto: CreateCreditRatingDto, evaluatorId: number) {
    const { merchantId, rating, score, level, evaluationDate, validUntil, evaluationReason } = createCreditRatingDto;

    // 检查商户是否已有有效的信用评级
    const existingRating = await this.creditRatingRepository.findOne({
      where: { merchantId: String(merchantId), status: 1 },
    });

    if (existingRating) {
      // 将现有评级设为无效
      existingRating.status = 0;
      await this.creditRatingRepository.save(existingRating);
    }

    const creditRating = this.creditRatingRepository.create({
      merchantId: String(merchantId),
      rating,
      score,
      level,
      evaluationDate: new Date(evaluationDate),
      validUntil: new Date(validUntil),
      evaluatorId: String(evaluatorId),
      evaluationReason: evaluationReason || '',
      status: 1,
    });

    const savedRating = await this.creditRatingRepository.save(creditRating);
    return {
      code: 200,
      message: '信用评级创建成功',
      data: savedRating,
    };
  }

  /**
   * 自动计算商户信用评级
   */
  async calculateMerchantRating(merchantId: number, evaluatorId: number) {
    // 获取商户信息
    const merchant = await this.merchantRepository.findOne({
      where: { id: String(merchantId) }
    });

    if (!merchant) {
      throw new NotFoundException('商户不存在');
    }

    // 获取商户订单数据
    const orderStats = await this.orderRepository
      .createQueryBuilder('order')
      .select([
        'COUNT(*) as totalOrders',
        'SUM(CASE WHEN order.status = "completed" THEN 1 ELSE 0 END) as completedOrders',
        'SUM(CASE WHEN order.status = "cancelled" THEN 1 ELSE 0 END) as cancelledOrders',
        'AVG(CASE WHEN order.status = "completed" THEN order.totalAmount ELSE NULL END) as avgOrderAmount',
        'COUNT(CASE WHEN order.createTime >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recentOrders'
      ])
      .where('order.merchantId = :merchantId', { merchantId })
      .getRawOne();

    // 计算信用分数
    const totalOrders = parseInt(orderStats.totalOrders) || 0;
    const completedOrders = parseInt(orderStats.completedOrders) || 0;
    const cancelledOrders = parseInt(orderStats.cancelledOrders) || 0;
    const avgOrderAmount = parseFloat(orderStats.avgOrderAmount) || 0;
    const recentOrders = parseInt(orderStats.recentOrders) || 0;

    // 订单完成率 (40%)
    const completionRate = totalOrders > 0 ? (completedOrders / totalOrders) * 100 : 0;
    
    // 订单取消率 (30%)
    const cancellationRate = totalOrders > 0 ? (cancelledOrders / totalOrders) * 100 : 0;
    
    // 活跃度 (20%)
    const activityScore = Math.min(recentOrders * 2, 20);
    
    // 订单金额 (10%)
    const amountScore = Math.min(avgOrderAmount / 100, 10);

    // 计算总分
    let score = 0;
    score += (completionRate * 0.4); // 完成率权重40%
    score += ((100 - cancellationRate) * 0.3); // 取消率权重30%
    score += activityScore; // 活跃度权重20%
    score += amountScore; // 金额权重10%

    // 确保分数在0-100之间
    score = Math.max(0, Math.min(100, score));

    // 根据分数确定等级和星级
    const { level, rating } = this.getLevelAndRatingByScore(score);

    // 生成评级原因
    const evaluationReason = this.generateEvaluationReason({
      completionRate,
      cancellationRate,
      recentOrders,
      avgOrderAmount,
      totalOrders
    });

    // 创建信用评级
    const createDto: CreateCreditRatingDto = {
      merchantId,
      rating,
      score: Math.round(score * 100) / 100,
      level,
      evaluationDate: new Date().toISOString().split('T')[0],
      validUntil: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 一年后
      evaluationReason
    };

    return this.createCreditRating(createDto, evaluatorId);
  }

  /**
   * 根据分数获取等级和星级
   */
  private getLevelAndRatingByScore(score: number): { level: string; rating: number } {
    if (score >= 95) return { level: 'AAA', rating: 5 };
    if (score >= 90) return { level: 'AA', rating: 5 };
    if (score >= 85) return { level: 'A', rating: 4 };
    if (score >= 80) return { level: 'BBB', rating: 4 };
    if (score >= 70) return { level: 'BB', rating: 3 };
    if (score >= 60) return { level: 'B', rating: 3 };
    if (score >= 50) return { level: 'C', rating: 2 };
    return { level: 'C', rating: 1 };
  }

  /**
   * 生成评级原因
   */
  private generateEvaluationReason(stats: any): string {
    const reasons = [];
    
    if (stats.completionRate >= 95) {
      reasons.push('订单完成率优秀');
    } else if (stats.completionRate >= 85) {
      reasons.push('订单完成率良好');
    } else if (stats.completionRate < 70) {
      reasons.push('订单完成率偏低');
    }

    if (stats.cancellationRate <= 5) {
      reasons.push('订单取消率低');
    } else if (stats.cancellationRate >= 15) {
      reasons.push('订单取消率较高');
    }

    if (stats.recentOrders >= 10) {
      reasons.push('近期订单活跃');
    } else if (stats.recentOrders < 3) {
      reasons.push('近期订单较少');
    }

    if (stats.avgOrderAmount >= 100) {
      reasons.push('平均订单金额较高');
    }

    if (stats.totalOrders >= 100) {
      reasons.push('历史订单数量充足');
    }

    return reasons.length > 0 ? reasons.join('，') : '基于综合经营数据评估';
  }

  /**
   * 获取信用评级列表
   */
  async getCreditRatingList(params: QueryCreditRatingDto) {
    const { page = 1, pageSize = 10, merchantId, level, status } = params;
    
    const queryBuilder = this.creditRatingRepository
      .createQueryBuilder('rating')
      .leftJoinAndSelect('rating.merchant', 'merchant')
      .orderBy('rating.createTime', 'DESC');

    // 商户筛选
    if (merchantId) {
      queryBuilder.andWhere('rating.merchantId = :merchantId', { merchantId });
    }

    // 等级筛选
    if (level) {
      queryBuilder.andWhere('rating.level = :level', { level });
    }

    // 状态筛选
    if (status !== undefined) {
      queryBuilder.andWhere('rating.status = :status', { status });
    }

    const [ratings, total] = await queryBuilder
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    return {
      code: 200,
      message: '获取成功',
      data: {
        list: ratings,
        total,
        page,
        pageSize,
      },
    };
  }

  /**
   * 获取商户当前有效信用评级
   */
  async getMerchantCurrentRating(merchantId: number) {
    const rating = await this.creditRatingRepository.findOne({
      where: { merchantId: String(merchantId), status: 1 },
      relations: ['merchant'],
      order: { createTime: 'DESC' }
    });

    if (!rating) {
      return {
        code: 404,
        message: '未找到有效的信用评级',
        data: null
      };
    }

    return {
      code: 200,
      message: '获取当前信用评级成功',
      data: rating
    };
  }

  /**
   * 获取商户信用评级历史
   */
  async getMerchantRatingHistory(merchantId: number, params: any) {
    const { page = 1, pageSize = 10 } = params;
    
    const queryBuilder = this.creditRatingRepository
      .createQueryBuilder('rating')
      .leftJoinAndSelect('rating.merchant', 'merchant')
      .where('rating.merchantId = :merchantId', { merchantId })
      .orderBy('rating.createTime', 'DESC');

    const [ratings, total] = await queryBuilder
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    return {
      code: 200,
      message: '获取信用评级历史成功',
      data: {
        list: ratings,
        total,
        page,
        pageSize,
      },
    };
  }

  /**
   * 获取信用评级统计信息
   */
  async getCreditRatingStats() {
    try {
      const totalRatings = await this.creditRatingRepository.count();
      const activeRatings = await this.creditRatingRepository.count({ where: { status: 1 } });
      
      return {
        code: 200,
        message: '获取信用评级统计成功',
        data: {
          totalRatings,
          activeRatings,
          avgScore: 0,
          levelDistribution: {
            AAA: 0,
            AA: 0,
            A: 0,
            BBB: 0,
            BB: 0,
            B: 0,
            C: 0
          }
        }
      };
    } catch (error) {
      console.error('获取信用评级统计失败:', error);
      return {
        code: 500,
        message: '获取信用评级统计失败',
        data: null
      };
    }
  }

  /**
   * 批量重新计算所有商户信用评级
   */
  async recalculateAllMerchantRatings(evaluatorId: number) {
    const merchants = await this.merchantRepository.find({
      where: { status: 1 } // 只计算活跃商户
    });

    const results = [];
    for (const merchant of merchants) {
      try {
        const result = await this.calculateMerchantRating(Number(merchant.id), evaluatorId);
        results.push({
          merchantId: merchant.id,
          merchantName: merchant.merchantName,
          success: true,
          data: result.data
        });
      } catch (error) {
        results.push({
          merchantId: merchant.id,
          merchantName: merchant.merchantName,
          success: false,
          error: error.message
        });
      }
    }

    return {
      code: 200,
      message: '批量重新计算完成',
      data: {
        total: merchants.length,
        success: results.filter(r => r.success).length,
        failed: results.filter(r => !r.success).length,
        results
      }
    };
  }

  /**
   * 获取信用评级详情
   */
  async getCreditRatingDetail(id: number) {
    const rating = await this.creditRatingRepository.findOne({
      where: { id: String(id) },
      relations: ['merchant'],
    });

    if (!rating) {
      throw new NotFoundException('信用评级记录不存在');
    }

    return {
      code: 200,
      message: '获取成功',
      data: rating,
    };
  }

  /**
   * 更新信用评级
   */
  async updateCreditRating(id: number, updateCreditRatingDto: UpdateCreditRatingDto) {
    const rating = await this.creditRatingRepository.findOne({
      where: { id: String(id) },
    });

    if (!rating) {
      throw new NotFoundException('信用评级记录不存在');
    }

    // 更新字段
    Object.assign(rating, updateCreditRatingDto);
    
    // 处理日期字段
    if (updateCreditRatingDto.evaluationDate) {
      rating.evaluationDate = new Date(updateCreditRatingDto.evaluationDate);
    }
    if (updateCreditRatingDto.validUntil) {
      rating.validUntil = new Date(updateCreditRatingDto.validUntil);
    }

    await this.creditRatingRepository.save(rating);

    return {
      code: 200,
      message: '信用评级更新成功',
      data: rating,
    };
  }

  /**
   * 删除信用评级
   */
  async deleteCreditRating(id: number) {
    const rating = await this.creditRatingRepository.findOne({
      where: { id: String(id) },
    });

    if (!rating) {
      throw new NotFoundException('信用评级记录不存在');
    }

    await this.creditRatingRepository.remove(rating);

    return {
      code: 200,
      message: '信用评级删除成功',
    };
  }

  /**
   * 根据分数自动计算等级
   */
  calculateLevelByScore(score: number): string {
    if (score >= 95) return 'AAA';
    if (score >= 90) return 'AA';
    if (score >= 80) return 'A';
    if (score >= 70) return 'BBB';
    if (score >= 60) return 'BB';
    if (score >= 50) return 'B';
    return 'C';
  }

  /**
   * 根据等级计算分数范围
   */
  getScoreRangeByLevel(level: string): { min: number; max: number } {
    const ranges = {
      'AAA': { min: 95, max: 100 },
      'AA': { min: 90, max: 94 },
      'A': { min: 80, max: 89 },
      'BBB': { min: 70, max: 79 },
      'BB': { min: 60, max: 69 },
      'B': { min: 50, max: 59 },
      'C': { min: 0, max: 49 },
    };
    return ranges[level] || { min: 0, max: 100 };
  }
}
