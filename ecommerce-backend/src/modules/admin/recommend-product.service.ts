import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Between, MoreThan, LessThan } from 'typeorm';
import { MerchantProduct } from '../merchant/entities/merchant-product.entity';
import { UpdateRecommendProductDto, QueryRecommendProductsDto } from './dto/recommend-product.dto';

@Injectable()
export class RecommendProductService {
  constructor(
    @InjectRepository(MerchantProduct)
    private readonly merchantProductRepository: Repository<MerchantProduct>,
  ) {}

  async getRecommendProducts(queryDto: QueryRecommendProductsDto) {
    const { page = 1, pageSize = 10, recommendType, merchantId, keyword, status } = queryDto;
    const skip = (page - 1) * pageSize;

    const queryBuilder = this.merchantProductRepository
      .createQueryBuilder('mp')
      .leftJoinAndSelect('mp.product', 'p')
      .leftJoinAndSelect('mp.merchant', 'm')
      .leftJoinAndSelect('p.category', 'c')
      .where('mp.status = :status', { status: 1 }); // 只显示上架商品

    // 推荐类型筛选
    if (recommendType === 'popular') {
      queryBuilder.andWhere('mp.isPopular = :isPopular', { isPopular: true });
    } else if (recommendType === 'top_deal') {
      queryBuilder.andWhere('mp.isTopDeal = :isTopDeal', { isTopDeal: true });
    } else if (recommendType === 'all') {
      queryBuilder.andWhere('(mp.isPopular = :isPopular OR mp.isTopDeal = :isTopDeal)', {
        isPopular: true,
        isTopDeal: true,
      });
    }

    // 商家筛选
    if (merchantId) {
      queryBuilder.andWhere('mp.merchantId = :merchantId', { merchantId });
    }

    // 关键词搜索
    if (keyword) {
      queryBuilder.andWhere('(p.name LIKE :keyword OR m.shopName LIKE :keyword)', {
        keyword: `%${keyword}%`,
      });
    }

    // 推荐状态筛选
    const now = new Date();
    if (status === 'active') {
      queryBuilder.andWhere(
        '(mp.recommendStartTime IS NULL OR mp.recommendStartTime <= :now) AND (mp.recommendEndTime IS NULL OR mp.recommendEndTime >= :now)',
        { now }
      );
    } else if (status === 'expired') {
      queryBuilder.andWhere('mp.recommendEndTime IS NOT NULL AND mp.recommendEndTime < :now', { now });
    }

    // 排序
    queryBuilder
      .orderBy('mp.recommendPriority', 'DESC')
      .addOrderBy('mp.sales', 'DESC')
      .addOrderBy('mp.createTime', 'DESC');

    const [list, total] = await queryBuilder.skip(skip).take(pageSize).getManyAndCount();

    const formattedList = list.map((mp) => ({
      id: mp.id,
      merchantId: mp.merchantId,
      merchantName: mp.merchant.shopName,
      productId: mp.productId,
      productName: mp.product.name,
      productImage: mp.product.mainImage,
      categoryName: mp.product.category?.name || '',
      salePrice: mp.salePrice,
      sales: mp.sales,
      isPopular: mp.isPopular,
      isTopDeal: mp.isTopDeal,
      recommendReason: mp.recommendReason,
      recommendPriority: mp.recommendPriority,
      recommendStartTime: mp.recommendStartTime,
      recommendEndTime: mp.recommendEndTime,
      createTime: mp.createTime,
      updateTime: mp.updateTime,
    }));

    return {
      list: formattedList,
      total,
      page,
      pageSize,
      totalPages: Math.ceil(total / pageSize),
    };
  }

  async updateRecommendProduct(id: number, updateDto: UpdateRecommendProductDto) {
    const merchantProduct = await this.merchantProductRepository.findOne({
      where: { id },
      relations: ['product', 'merchant'],
    });

    if (!merchantProduct) {
      throw new Error('商品不存在');
    }

    // 更新推荐信息
    Object.assign(merchantProduct, updateDto);

    // 处理时间字段
    if (updateDto.recommendStartTime) {
      merchantProduct.recommendStartTime = new Date(updateDto.recommendStartTime);
    }
    if (updateDto.recommendEndTime) {
      merchantProduct.recommendEndTime = new Date(updateDto.recommendEndTime);
    }

    await this.merchantProductRepository.save(merchantProduct);

    return {
      id: merchantProduct.id,
      merchantName: merchantProduct.merchant.shopName,
      productName: merchantProduct.product.name,
      isPopular: merchantProduct.isPopular,
      isTopDeal: merchantProduct.isTopDeal,
      recommendReason: merchantProduct.recommendReason,
      recommendPriority: merchantProduct.recommendPriority,
      recommendStartTime: merchantProduct.recommendStartTime,
      recommendEndTime: merchantProduct.recommendEndTime,
    };
  }

  async batchUpdateRecommendProducts(updates: Array<{ id: number; data: UpdateRecommendProductDto }>) {
    const results = [];
    
    for (const update of updates) {
      try {
        const result = await this.updateRecommendProduct(update.id, update.data);
        results.push({ success: true, ...result });
      } catch (error) {
        results.push({ 
          success: false, 
          id: update.id, 
          error: error.message 
        });
      }
    }

    return results;
  }

  async getRecommendStats() {
    const now = new Date();
    
    const [
      totalPopular,
      activePopular,
      totalTopDeal,
      activeTopDeal,
      expiredRecommendations
    ] = await Promise.all([
      this.merchantProductRepository.count({ where: { isPopular: true } }),
      this.merchantProductRepository.count({
        where: {
          isPopular: true,
          recommendStartTime: LessThan(now),
          recommendEndTime: MoreThan(now),
        },
      }),
      this.merchantProductRepository.count({ where: { isTopDeal: true } }),
      this.merchantProductRepository.count({
        where: {
          isTopDeal: true,
          recommendStartTime: LessThan(now),
          recommendEndTime: MoreThan(now),
        },
      }),
      this.merchantProductRepository.count({
        where: {
          recommendEndTime: LessThan(now),
        },
      }),
    ]);

    return {
      totalPopular,
      activePopular,
      totalTopDeal,
      activeTopDeal,
      expiredRecommendations,
    };
  }
}
