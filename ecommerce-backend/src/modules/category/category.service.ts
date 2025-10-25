import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Category } from './entities/category.entity';
import { CreateCategoryDto } from './dto/create-category.dto';
import { UpdateCategoryDto } from './dto/update-category.dto';
import { UpdateCategoryImageDto } from './dto/update-category-image.dto';

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
        where: { id: parentId },
      });
      if (!parentCategory) {
        throw new BadRequestException('父分类不存在');
      }
    }

    // 检查同级分类名称是否重复
    const existingCategory = await this.categoryRepository.findOne({
      where: { parentId: parentId || 0, name },
    });
    if (existingCategory) {
      throw new BadRequestException('同级分类名称已存在');
    }

    const category = this.categoryRepository.create({
      parentId: parentId || 0,
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
      
      // 查询分类，包含图片信息
      const queryBuilder = this.categoryRepository
        .createQueryBuilder('category')
        .select([
          'category.id',
          'category.parentId',
          'category.name',
          'category.level',
          'category.sort',
          'category.icon',
          'category.imageUrl',
          'category.iconClass',
          'category.status',
          'category.createTime',
          'category.updateTime'
        ])
        .addSelect('category.imageUrl', 'imageUrl')
        .addSelect('category.iconClass', 'iconClass');

      // 只有当status是1时才添加WHERE条件（0表示显示所有分类）
      if (status === 1) {
        queryBuilder.where('category.status = :status', { status: 1 });
      }

      const categories = await queryBuilder
        .orderBy('category.sort', 'ASC')
        .addOrderBy('category.id', 'ASC')
        .getMany();
      
      console.log('查询结果数量:', categories.length);
      console.log('第一个分类:', categories[0]);

      return {
        code: 200,
        message: '获取成功',
        data: categories,
      };
    } catch (error) {
      console.error('分类服务 findAll 错误:', error);
      console.error('错误堆栈:', error.stack);
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
      where: { id },
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
    console.log('更新分类请求:', { id, updateCategoryDto });
    
    const category = await this.categoryRepository.findOne({
      where: { id },
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    const { parentId, name, level, sort, icon, status } = updateCategoryDto;

    // 验证父分类是否存在
    if (parentId && parentId > 0) {
      const parentCategory = await this.categoryRepository.findOne({
        where: { id: parentId },
      });
      if (!parentCategory) {
        throw new BadRequestException('父分类不存在');
      }
    }

    // 检查同级分类名称是否重复（排除自己）
    if (name) {
      const existingCategory = await this.categoryRepository.findOne({
        where: { parentId: parentId || category.parentId, name },
      });
      if (existingCategory && existingCategory.id !== id) {
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
      where: { id },
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    // 检查是否有子分类
    const childCategories = await this.categoryRepository.find({
      where: { parentId: id },
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
      where: { id },
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
      where: { id },
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
      categoryMap.set(category.id, {
        ...category,
        children: [],
      });
    });

    // 构建树形结构
    categories.forEach(category => {
      if (category.parentId === 0) {
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

  /**
   * 更新分类图片
   */
  async updateImage(id: number, updateImageDto: UpdateCategoryImageDto) {
    const category = await this.categoryRepository.findOne({
      where: { id },
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    // 更新图片相关字段
    if (updateImageDto.image_url !== undefined) {
      category.imageUrl = updateImageDto.image_url;
    }
    if (updateImageDto.icon_class !== undefined) {
      category.iconClass = updateImageDto.icon_class;
    }
    if (updateImageDto.name !== undefined) {
      category.name = updateImageDto.name;
    }

    await this.categoryRepository.save(category);

    return {
      code: 200,
      message: '分类图片更新成功',
      data: {
        id: category.id,
        name: category.name,
        imageUrl: category.imageUrl,
        iconClass: category.iconClass,
      },
    };
  }

  /**
   * 获取分类图片信息
   */
  async getImageInfo(id: number) {
    const category = await this.categoryRepository.findOne({
      where: { id },
      select: ['id', 'name', 'imageUrl', 'iconClass', 'icon'],
    });

    if (!category) {
      throw new NotFoundException('分类不存在');
    }

    return {
      code: 200,
      message: '获取成功',
      data: {
        id: category.id,
        name: category.name,
        imageUrl: category.imageUrl,
        iconClass: category.iconClass,
        icon: category.icon,
      },
    };
  }
}
