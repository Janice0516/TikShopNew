import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  Query,
  UseGuards,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { CategoryService } from './category.service';
import { CreateCategoryDto } from './dto/create-category.dto';
import { UpdateCategoryDto } from './dto/update-category.dto';
import { UpdateCategoryImageDto } from './dto/update-category-image.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('分类管理')
@Controller('category')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class CategoryController {
  constructor(private readonly categoryService: CategoryService) {}

  @Post()
  @ApiOperation({ summary: '创建分类' })
  create(@Body() createCategoryDto: CreateCategoryDto) {
    return this.categoryService.create(createCategoryDto);
  }

  @Get()
  @ApiOperation({ summary: '获取分类列表' })
  findAll(@Query('status') status?: number) {
    return this.categoryService.findAll(status);
  }

  @Get('test')
  @ApiOperation({ summary: '测试分类API' })
  testFindAll() {
    try {
      console.log('测试分类API被调用');
      return {
        code: 200,
        message: '测试成功',
        data: [
          { id: 1, name: '测试分类1', status: 1 },
          { id: 2, name: '测试分类2', status: 1 }
        ]
      };
    } catch (error) {
      console.error('测试分类API错误:', error);
      return {
        code: 500,
        message: '测试失败',
        error: error.message
      };
    }
  }

  @Get('tree')
  @ApiOperation({ summary: '获取分类树形结构' })
  findTree() {
    return this.categoryService.findTree();
  }

  // 图片管理API - 必须在 :id 路由之前
  @Put(':id/image')
  @ApiOperation({ summary: '更新分类图片' })
  updateImage(@Param('id') id: string, @Body() updateImageDto: UpdateCategoryImageDto) {
    return this.categoryService.updateImage(+id, updateImageDto);
  }

  @Get(':id/image')
  @ApiOperation({ summary: '获取分类图片信息' })
  getImageInfo(@Param('id') id: string) {
    return this.categoryService.getImageInfo(+id);
  }

  @Get(':id')
  @ApiOperation({ summary: '获取分类详情' })
  findOne(@Param('id') id: string) {
    return this.categoryService.findOne(+id);
  }

  @Put(':id')
  @ApiOperation({ summary: '更新分类' })
  update(@Param('id') id: string, @Body() updateCategoryDto: UpdateCategoryDto) {
    return this.categoryService.update(+id, updateCategoryDto);
  }

  @Delete(':id')
  @ApiOperation({ summary: '删除分类' })
  remove(@Param('id') id: string) {
    return this.categoryService.remove(+id);
  }

  @Put(':id/status')
  @ApiOperation({ summary: '更新分类状态' })
  updateStatus(@Param('id') id: string, @Body('status') status: number) {
    return this.categoryService.updateStatus(+id, status);
  }

  @Put(':id/sort')
  @ApiOperation({ summary: '更新分类排序' })
  updateSort(@Param('id') id: string, @Body('sort') sort: number) {
    return this.categoryService.updateSort(+id, sort);
  }
}
