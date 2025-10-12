import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Like } from 'typeorm';
import { Product } from './entities/product.entity';
import { Category } from '../category/entities/category.entity';
import { CreateProductDto } from './dto/create-product.dto';
import { UpdateProductDto } from './dto/update-product.dto';
import { QueryProductDto } from './dto/query-product.dto';

@Injectable()
export class ProductService {
  constructor(
    @InjectRepository(Product)
    private productRepository: Repository<Product>,
    @InjectRepository(Category)
    private categoryRepository: Repository<Category>,
  ) {}

  /**
   * 创建商品
   */
  async create(createProductDto: CreateProductDto) {
    // 验证分类是否存在
    const category = await this.categoryRepository.findOne({
      where: { id: String(createProductDto.categoryId) },
    });
    if (!category) {
      throw new HttpException('分类不存在', HttpStatus.BAD_REQUEST);
    }

    // 生成商品编号
    const productNo = this.generateProductNo();

    const product = this.productRepository.create({
      ...createProductDto,
      categoryId: String(createProductDto.categoryId),
      productNo,
      status: 1,
    });

    return await this.productRepository.save(product);
  }

  /**
   * 查询商品列表（分页、筛选、搜索）
   */
  async findAll(queryDto: QueryProductDto) {
    const { page = 1, pageSize = 10, categoryId, keyword, status } = queryDto;

    const queryBuilder = this.productRepository.createQueryBuilder('product');

    // 分类筛选
    if (categoryId) {
      queryBuilder.andWhere('product.categoryId = :categoryId', { categoryId });
    }

    // 关键词搜索
    if (keyword) {
      queryBuilder.andWhere('product.name LIKE :keyword', {
        keyword: `%${keyword}%`,
      });
    }

    // 状态筛选
    if (status !== undefined) {
      queryBuilder.andWhere('product.status = :status', { status });
    }

    // 分页和排序
    const [list, total] = await queryBuilder
      .orderBy('product.sort', 'DESC')
      .addOrderBy('product.id', 'DESC')
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    return {
      list,
      total,
      page,
      pageSize,
      totalPages: Math.ceil(total / pageSize),
    };
  }

  /**
   * 查询商品详情
   */
  async findOne(id: number) {
    const product = await this.productRepository.findOne({ where: { id: String(id) } });

    if (!product) {
      throw new HttpException('商品不存在', HttpStatus.NOT_FOUND);
    }

    // 查询分类信息
    const category = await this.categoryRepository.findOne({
      where: { id: String(product.categoryId) },
    });

    return {
      ...product,
      category,
    };
  }

  /**
   * 更新商品
   */
  async update(id: number, updateProductDto: UpdateProductDto) {
    await this.findOne(id); // 检查商品是否存在

    // 如果更新了分类，验证分类是否存在
    if (updateProductDto.categoryId) {
      const category = await this.categoryRepository.findOne({
        where: { id: String(updateProductDto.categoryId) },
      });
      if (!category) {
        throw new HttpException('分类不存在', HttpStatus.BAD_REQUEST);
      }
    }

    const updateData = {
      ...updateProductDto,
      categoryId: updateProductDto.categoryId ? String(updateProductDto.categoryId) : undefined,
    };
    await this.productRepository.update(String(id), updateData);
    return await this.findOne(id);
  }

  /**
   * 删除商品（软删除）
   */
  async remove(id: number) {
    await this.findOne(id);
    await this.productRepository.update(id, { status: 0 });
    return { message: '删除成功' };
  }

  /**
   * 上架/下架商品
   */
  async updateStatus(id: number, status: number) {
    await this.findOne(id);
    
    if (![0, 1].includes(status)) {
      throw new HttpException('状态值错误', HttpStatus.BAD_REQUEST);
    }

    await this.productRepository.update(id, { status });
    return { message: status === 1 ? '上架成功' : '下架成功' };
  }

  /**
   * 更新库存
   */
  async updateStock(id: number, quantity: number) {
    const product = await this.findOne(id);

    const newStock = product.stock + quantity;
    if (newStock < 0) {
      throw new HttpException('库存不足', HttpStatus.BAD_REQUEST);
    }

    await this.productRepository.update(id, { stock: newStock });
    return { message: '库存更新成功', stock: newStock };
  }

  /**
   * 扣减库存（下单时调用）
   */
  async decreaseStock(id: number, quantity: number) {
    const result = await this.productRepository
      .createQueryBuilder()
      .update(Product)
      .set({ stock: () => `stock - ${quantity}` })
      .where('id = :id', { id })
      .andWhere('stock >= :quantity', { quantity })
      .execute();

    if (result.affected === 0) {
      throw new HttpException('库存不足', HttpStatus.BAD_REQUEST);
    }

    return { message: '库存扣减成功' };
  }

  /**
   * 增加销量
   */
  async increaseSales(id: number, quantity: number) {
    await this.productRepository.increment({ id: String(id) }, 'sales', quantity);
    return { message: '销量更新成功' };
  }

  /**
   * 查询分类列表
   */
  async findAllCategories() {
    const categories = await this.categoryRepository.find({
      where: { status: 1 },
      order: { sort: 'ASC', id: 'ASC' },
    });

    // 构建树形结构
    return this.buildCategoryTree(categories);
  }

  /**
   * 查询分类详情
   */
  async findCategory(id: number) {
    const category = await this.categoryRepository.findOne({ where: { id: String(id) } });
    if (!category) {
      throw new HttpException('分类不存在', HttpStatus.NOT_FOUND);
    }
    return category;
  }

  /**
   * 生成商品编号
   */
  private generateProductNo(): string {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const random = Math.floor(Math.random() * 10000)
      .toString()
      .padStart(4, '0');

    return `P${year}${month}${day}${random}`;
  }

  /**
   * 构建分类树
   */
  private buildCategoryTree(categories: Category[], parentId: number = 0): any[] {
    const tree = [];

    categories
      .filter((cat) => Number(cat.parentId) === Number(parentId))
      .forEach((cat) => {
        const children = this.buildCategoryTree(categories, Number(cat.id));
        tree.push({
          ...cat,
          children: children.length > 0 ? children : undefined,
        });
      });

    return tree;
  }
}

