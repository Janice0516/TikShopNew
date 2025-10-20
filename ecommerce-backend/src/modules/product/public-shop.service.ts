import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, LessThan, MoreThan } from 'typeorm';
import { MerchantProduct } from '../merchant/entities/merchant-product.entity';
import { Product } from './entities/product.entity';
import { Category } from '../category/entities/category.entity';
import { QueryShopProductDto } from './dto/query-shop-product.dto';

@Injectable()
export class PublicShopService {
  constructor(
    @InjectRepository(MerchantProduct)
    private merchantProductRepository: Repository<MerchantProduct>,
    @InjectRepository(Product)
    private productRepository: Repository<Product>,
    @InjectRepository(Category)
    private categoryRepository: Repository<Category>,
  ) {}

  /**
   * 获取商城商品列表（所有商家上架的商品）
   */
  async getShopProducts(queryDto: QueryShopProductDto) {
    const { page = 1, pageSize = 10, categoryId, keyword, status = 1 } = queryDto;

    const queryBuilder = this.merchantProductRepository
      .createQueryBuilder('mp')
      .leftJoinAndSelect('mp.product', 'p')
      .leftJoinAndSelect('mp.merchant', 'm')
      .leftJoinAndSelect('p.category', 'c')
      .where('mp.status = :status', { status });

    // 分类筛选
    if (categoryId) {
      queryBuilder.andWhere('p.categoryId = :categoryId', { categoryId });
    }

    // 关键词搜索
    if (keyword && keyword.trim()) {
      queryBuilder.andWhere('(p.name LIKE :keyword OR p.brand LIKE :keyword OR m.shopName LIKE :keyword)', {
        keyword: `%${keyword.trim()}%`
      });
    }

    // 分页
    const skip = (page - 1) * pageSize;
    queryBuilder.skip(skip).take(pageSize);

    // 排序：按销量和创建时间排序
    queryBuilder
      .orderBy('mp.sales', 'DESC')
      .addOrderBy('mp.createTime', 'DESC');

    const [merchantProducts, total] = await queryBuilder.getManyAndCount();

    // 格式化返回数据
    const list = merchantProducts.map(mp => ({
      id: mp.id,
      productId: mp.productId,
      name: mp.product.name,
      brand: mp.product.brand,
      mainImage: mp.product.mainImage,
      images: mp.product.images,
      video: mp.product.video,
      categoryId: mp.product.categoryId,
      categoryName: mp.product.category?.name || '',
      costPrice: mp.product.costPrice,
      suggestPrice: mp.product.suggestPrice,
      salePrice: mp.salePrice,
      profitMargin: mp.profitMargin,
      stock: mp.product.stock,
      sales: mp.sales,
      description: mp.product.description,
      status: mp.status,
      merchantId: mp.merchantId,
      merchantName: mp.merchant?.shopName || '',
      createTime: mp.createTime,
    }));

    return {
      list,
      total,
      page,
      pageSize,
      totalPages: Math.ceil(total / pageSize),
    };
  }

  /**
   * 获取商品分类列表
   */
  async getCategories() {
    const categories = await this.categoryRepository.find({
      where: { status: 1 },
      order: { sort: 'DESC', id: 'ASC' }
    });

    return {
      list: categories,
      total: categories.length
    };
  }

  /**
   * 获取推荐商品列表（Top Deals）
   */
  async getTopDeals(limit: number = 10) {
    const now = new Date();
    
    const queryBuilder = this.merchantProductRepository
      .createQueryBuilder('mp')
      .leftJoinAndSelect('mp.product', 'p')
      .leftJoinAndSelect('mp.merchant', 'm')
      .leftJoinAndSelect('p.category', 'c')
      .where('mp.status = :status', { status: 1 })
      .andWhere('mp.isTopDeal = :isTopDeal', { isTopDeal: true })
      .andWhere('(mp.recommendStartTime IS NULL OR mp.recommendStartTime <= :now)', { now })
      .andWhere('(mp.recommendEndTime IS NULL OR mp.recommendEndTime >= :now)', { now })
      .orderBy('mp.recommendPriority', 'DESC')
      .addOrderBy('mp.sales', 'DESC')
      .take(limit);

    const merchantProducts = await queryBuilder.getMany();

    return merchantProducts.map(mp => ({
      id: mp.id,
      productId: mp.productId,
      name: mp.product.name,
      brand: mp.product.brand,
      mainImage: mp.product.mainImage,
      images: mp.product.images,
      video: mp.product.video,
      categoryId: mp.product.categoryId,
      categoryName: mp.product.category?.name || '',
      costPrice: mp.product.costPrice,
      suggestPrice: mp.product.suggestPrice,
      salePrice: mp.salePrice,
      profitMargin: mp.profitMargin,
      stock: mp.product.stock,
      sales: mp.sales,
      description: mp.product.description,
      status: mp.status,
      merchantId: mp.merchantId,
      merchantName: mp.merchant?.shopName || '',
      isPopular: mp.isPopular,
      isTopDeal: mp.isTopDeal,
      recommendReason: mp.recommendReason,
      recommendPriority: mp.recommendPriority,
      createTime: mp.createTime,
    }));
  }

  /**
   * 获取热门商品列表（Popular Items）
   */
  async getPopularItems(limit: number = 10) {
    const now = new Date();
    
    const queryBuilder = this.merchantProductRepository
      .createQueryBuilder('mp')
      .leftJoinAndSelect('mp.product', 'p')
      .leftJoinAndSelect('mp.merchant', 'm')
      .leftJoinAndSelect('p.category', 'c')
      .where('mp.status = :status', { status: 1 })
      .andWhere('mp.isPopular = :isPopular', { isPopular: true })
      .andWhere('(mp.recommendStartTime IS NULL OR mp.recommendStartTime <= :now)', { now })
      .andWhere('(mp.recommendEndTime IS NULL OR mp.recommendEndTime >= :now)', { now })
      .orderBy('mp.recommendPriority', 'DESC')
      .addOrderBy('mp.sales', 'DESC')
      .take(limit);

    const merchantProducts = await queryBuilder.getMany();

    return merchantProducts.map(mp => ({
      id: mp.id,
      productId: mp.productId,
      name: mp.product.name,
      brand: mp.product.brand,
      mainImage: mp.product.mainImage,
      images: mp.product.images,
      video: mp.product.video,
      categoryId: mp.product.categoryId,
      categoryName: mp.product.category?.name || '',
      costPrice: mp.product.costPrice,
      suggestPrice: mp.product.suggestPrice,
      salePrice: mp.salePrice,
      profitMargin: mp.profitMargin,
      stock: mp.product.stock,
      sales: mp.sales,
      description: mp.product.description,
      status: mp.status,
      merchantId: mp.merchantId,
      merchantName: mp.merchant?.shopName || '',
      isPopular: mp.isPopular,
      isTopDeal: mp.isTopDeal,
      recommendReason: mp.recommendReason,
      recommendPriority: mp.recommendPriority,
      createTime: mp.createTime,
    }));
  }
}
