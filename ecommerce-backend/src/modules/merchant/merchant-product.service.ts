import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { MerchantProduct } from './entities/merchant-product.entity';
import { Product } from '../product/entities/product.entity';
import { SelectProductDto, UpdateProductPriceDto, UpdateProductStatusDto, QueryMerchantProductDto } from './dto/merchant-product.dto';

@Injectable()
export class MerchantProductService {
  constructor(
    @InjectRepository(MerchantProduct)
    private merchantProductRepository: Repository<MerchantProduct>,
    @InjectRepository(Product)
    private productRepository: Repository<Product>,
  ) {}

  // 商家选品
  async selectProduct(merchantId: number, selectProductDto: SelectProductDto) {
    const { productId, salePrice, profitMargin } = selectProductDto;

    // 检查商品是否存在
    const product = await this.productRepository.findOne({
      where: { id: String(productId) }
    });

    if (!product) {
      throw new HttpException('商品不存在', HttpStatus.NOT_FOUND);
    }

    // 检查是否已经选过该商品
    const existingProduct = await this.merchantProductRepository.findOne({
      where: { merchantId, productId }
    });

    if (existingProduct) {
      return {
        success: false,
        message: '该商品已在您的店铺中，请勿重复添加',
        code: 'DUPLICATE_PRODUCT',
        data: {
          productId: existingProduct.productId,
          productName: product.name,
          currentPrice: existingProduct.salePrice,
          status: existingProduct.status === 1 ? '已上架' : '已下架'
        }
      };
    }

    // 计算利润率
    const calculatedProfitMargin = profitMargin || ((salePrice - product.costPrice) / salePrice * 100);

    // 创建商家商品记录
    const merchantProduct = this.merchantProductRepository.create({
      merchantId,
      productId,
      salePrice,
      profitMargin: calculatedProfitMargin,
      status: 1, // 默认上架
    });

    const savedProduct = await this.merchantProductRepository.save(merchantProduct);
    
    return {
      success: true,
      message: '商品添加成功',
      code: 'SUCCESS',
      data: {
        id: savedProduct.id,
        productId: savedProduct.productId,
        productName: product.name,
        salePrice: savedProduct.salePrice,
        profitMargin: savedProduct.profitMargin,
        status: savedProduct.status === 1 ? '已上架' : '已下架'
      }
    };
  }

  // 获取商家商品列表
  async getMerchantProducts(merchantId: number, queryDto: QueryMerchantProductDto) {
    const { page = 1, pageSize = 10, status, keyword } = queryDto;

    const queryBuilder = this.merchantProductRepository
      .createQueryBuilder('mp')
      .leftJoinAndSelect('mp.product', 'p')
      .where('mp.merchantId = :merchantId', { merchantId });

    // 状态筛选
    if (status !== undefined && status !== null) {
      queryBuilder.andWhere('mp.status = :status', { status });
    }

    // 关键词搜索
    if (keyword && keyword.trim()) {
      queryBuilder.andWhere('(p.name LIKE :keyword OR p.brand LIKE :keyword)', {
        keyword: `%${keyword.trim()}%`
      });
    }

    // 分页
    const skip = (page - 1) * pageSize;
    queryBuilder.skip(skip).take(pageSize);

    // 排序
    queryBuilder.orderBy('mp.createTime', 'DESC');

    const [merchantProducts, total] = await queryBuilder.getManyAndCount();

    // 格式化返回数据
    const list = merchantProducts.map(mp => ({
      id: mp.id,
      productId: mp.productId,
      name: mp.product.name,
      brand: mp.product.brand,
      mainImage: mp.product.mainImage,
      categoryId: mp.product.categoryId,
      categoryName: '', // 暂时设为空，后续可以通过categoryId查询
      costPrice: mp.product.costPrice,
      suggestPrice: mp.product.suggestPrice,
      salePrice: mp.salePrice,
      discountPrice: mp.discountPrice,
      discountStartTime: mp.discountStartTime,
      discountEndTime: mp.discountEndTime,
      isDiscountActive: mp.isDiscountActive,
      profitMargin: mp.profitMargin,
      stock: mp.product.stock,
      sales: mp.sales,
      status: mp.status,
      createTime: mp.createTime,
    }));

    return { list, total, page, pageSize };
  }

  // 更新商品价格
  async updateProductPrice(merchantId: number, merchantProductId: number, updateDto: UpdateProductPriceDto) {
    const merchantProduct = await this.merchantProductRepository.findOne({
      where: { id: merchantProductId, merchantId },
      relations: ['product']
    });

    if (!merchantProduct) {
      throw new HttpException('商品不存在', HttpStatus.NOT_FOUND);
    }

    // 更新价格和利润率
    merchantProduct.salePrice = updateDto.salePrice;
    merchantProduct.profitMargin = (updateDto.salePrice - merchantProduct.product.costPrice) / updateDto.salePrice * 100;

    // 更新折扣价相关字段
    if (updateDto.discountPrice !== undefined) {
      merchantProduct.discountPrice = updateDto.discountPrice;
    }
    if (updateDto.discountStartTime !== undefined) {
      merchantProduct.discountStartTime = updateDto.discountStartTime ? new Date(updateDto.discountStartTime) : null;
    }
    if (updateDto.discountEndTime !== undefined) {
      merchantProduct.discountEndTime = updateDto.discountEndTime ? new Date(updateDto.discountEndTime) : null;
    }
    if (updateDto.isDiscountActive !== undefined) {
      merchantProduct.isDiscountActive = updateDto.isDiscountActive;
    }

    return await this.merchantProductRepository.save(merchantProduct);
  }

  // 更新商品状态（上下架）
  async updateProductStatus(merchantId: number, merchantProductId: number, updateDto: UpdateProductStatusDto) {
    const merchantProduct = await this.merchantProductRepository.findOne({
      where: { id: merchantProductId, merchantId }
    });

    if (!merchantProduct) {
      throw new HttpException('商品不存在', HttpStatus.NOT_FOUND);
    }

    merchantProduct.status = updateDto.status;
    return await this.merchantProductRepository.save(merchantProduct);
  }

  // 删除商家商品
  async deleteMerchantProduct(merchantId: number, merchantProductId: number) {
    const result = await this.merchantProductRepository.delete({
      id: merchantProductId,
      merchantId
    });

    if (result.affected === 0) {
      throw new HttpException('商品不存在', HttpStatus.NOT_FOUND);
    }

    return { message: '删除成功' };
  }
}
