import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Category } from './entities/category.entity';
import { CreateCategoryDto } from './dto/create-category.dto';
import { UpdateCategoryDto } from './dto/update-category.dto';

@Injectable()
export class CategoryService {
  constructor(
    @InjectRepository(Category)
    private categoryRepository: Repository<Category>,
  ) {}

  /**
   * 创建分类
   */
  async create(createCategoryDto: CreateCategoryDto) {
    const { parentId, name, level, sort, icon, status } = createCategoryDto;

    // 验证父分类是否存在
    if (parentId > 0) {
      const parentCategory = await this.categoryRepository.findOne({
        where: { id: String(parentId) },
      });
      if (!parentCategory) {
        throw new BadRequestException('父分类不存在');
      }
    }

    // 检查同级分类名称是否重复
    const existingCategory = await this.categoryRepository.findOne({
      where: { parentId: String(parentId), name },
    });
    if (existingCategory) {
      throw new BadRequestException('同级分类名称已存在');
    }

    const category = this.categoryRepository.create({
      parentId: String(parentId || 0),
      name,
      level: level || 1,
      sort: sort || 0,
      icon: icon || '',
      status: status !== undefined ? status : 1,
    });

    const savedCategory = await this.categoryRepository.save(category);
    return {
      code: 200,
      message: '创建成功',
      data: savedCategory,
    };
  }

  /**
   * 获取分类列表
   */
  async findAll(status?: number) {
    try {
      console.log('分类服务 findAll 被调用，status:', status);
      
      // 使用最简单的查询
      const categories = await this.categoryRepository.find();
      
      console.log('查询结果数量:', categories.length);

      return {
        code: 200,
        message: '获取成功',
        data: categories,
      };
    } catch (error) {
      console.error('分类服务 findAll 错误:', error);
      throw error;
    }
  }

  /**
   * 获取分类树形结构
   */
  async findTree() {
    try {
      console.log('开始查询分类...');
      
      // 使用TypeORM查询构建器
      const categories = await this.categoryRepository
        .createQueryBuilder('category')
        .where('category.status = :status', { status: 1 })
        .orderBy('category.sort', 'ASC')
        .addOrderBy('category.id', 'ASC')
        .getMany();

      console.log(`查询到 ${categories.length} 个分类`);
      
      const tree = this.buildCategoryTree(categories);
      console.log(`构建的树形结构有 ${tree.length} 个一级分类`);
      
      return {
        code: 200,
        message: '获取成功',
        data: tree,
      };
    } catch (error) {
      console.error('分类树查询失败:', error);
      throw error;
    }
  }

  /**
   * 获取分类详情
   */
  async findOne(id: number) {
    const category = await this.categoryRepository.findOne({
      where: { id: String(id) },
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    return {
      code: 200,
      message: '获取成功',
      data: category,
    };
  }

  /**
   * 更新分类
   */
  async update(id: number, updateCategoryDto: UpdateCategoryDto) {
    const category = await this.categoryRepository.findOne({
      where: { id: String(id) },
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    const { parentId, name, level, sort, icon, status } = updateCategoryDto;

    // 验证父分类是否存在
    if (parentId && parentId > 0) {
      const parentCategory = await this.categoryRepository.findOne({
        where: { id: String(parentId) },
      });
      if (!parentCategory) {
        throw new BadRequestException('父分类不存在');
      }
    }

    // 检查同级分类名称是否重复（排除自己）
    if (name) {
      const existingCategory = await this.categoryRepository.findOne({
        where: { parentId: String(parentId || category.parentId), name },
      });
      if (existingCategory && existingCategory.id !== String(id)) {
        throw new BadRequestException('同级分类名称已存在');
      }
    }

    // 更新分类
    Object.assign(category, {
      parentId: parentId !== undefined ? parentId : category.parentId,
      name: name || category.name,
      level: level !== undefined ? level : category.level,
      sort: sort !== undefined ? sort : category.sort,
      icon: icon !== undefined ? icon : category.icon,
      status: status !== undefined ? status : category.status,
    });

    const updatedCategory = await this.categoryRepository.save(category);
    return {
      code: 200,
      message: '更新成功',
      data: updatedCategory,
    };
  }

  /**
   * 删除分类
   */
  async remove(id: number) {
    const category = await this.categoryRepository.findOne({
      where: { id: String(id) },
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    // 检查是否有子分类
    const childCategories = await this.categoryRepository.find({
      where: { parentId: String(id) },
    });

    if (childCategories.length > 0) {
      throw new BadRequestException('该分类下还有子分类，无法删除');
    }

    await this.categoryRepository.remove(category);
    return {
      code: 200,
      message: '删除成功',
    };
  }

  /**
   * 更新分类状态
   */
  async updateStatus(id: number, status: number) {
    const category = await this.categoryRepository.findOne({
      where: { id: String(id) },
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    category.status = status;
    await this.categoryRepository.save(category);

    return {
      code: 200,
      message: '状态更新成功',
    };
  }

  /**
   * 更新分类排序
   */
  async updateSort(id: number, sort: number) {
    const category = await this.categoryRepository.findOne({
      where: { id: String(id) },
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    category.sort = sort;
    await this.categoryRepository.save(category);

    return {
      code: 200,
      message: '排序更新成功',
    };
  }

  /**
   * 构建分类树形结构
   */
  private buildCategoryTree(categories: Category[]): any[] {
    const categoryMap = new Map();
    const tree: any[] = [];

    // 创建分类映射
    categories.forEach(category => {
      categoryMap.set(String(category.id), {
        ...category,
        children: [],
      });
    });

    // 构建树形结构
    categories.forEach(category => {
      if (category.parentId === '0') {
        tree.push(categoryMap.get(category.id));
      } else {
        const parent = categoryMap.get(category.parentId);
        if (parent) {
          parent.children.push(categoryMap.get(category.id));
        }
      }
    });

    return tree;
  }
}
