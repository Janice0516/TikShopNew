import { Controller, Get } from '@nestjs/common';
import { ApiTags, ApiOperation } from '@nestjs/swagger';
import { CategoryService } from './category.service';

@ApiTags('公开分类管理')
@Controller('public-categories')
export class PublicCategoryController {
  constructor(private readonly categoryService: CategoryService) {}
  
  @Get()
  @ApiOperation({ summary: '获取公开分类列表（无需认证）' })
  async findAll() {
    try {
      console.log('公开分类API被调用');
      const result = await this.categoryService.findAll();
      return result;  // 直接返回CategoryService的结果
    } catch (error) {
      console.error('公开分类API错误:', error);
      return {
        code: 500,
        message: '获取失败',
        error: error.message
      };
    }
  }
}