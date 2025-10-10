import { Controller, Get, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('简单分类管理')
@Controller('simple-categories')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class SimpleCategoryController {
  
  @Get()
  @ApiOperation({ summary: '获取简单分类列表' })
  findAll() {
    try {
      console.log('简单分类API被调用');
      return {
        code: 200,
        message: '获取成功',
        data: [
          { id: 1, name: '服装鞋包', status: 1, parentId: 0, level: 1, sort: 1 },
          { id: 2, name: '数码家电', status: 1, parentId: 0, level: 1, sort: 2 },
          { id: 3, name: '食品生鲜', status: 1, parentId: 0, level: 1, sort: 3 },
          { id: 4, name: '美妆个护', status: 1, parentId: 0, level: 1, sort: 4 },
          { id: 5, name: '家居生活', status: 1, parentId: 0, level: 1, sort: 5 }
        ]
      };
    } catch (error) {
      console.error('简单分类API错误:', error);
      return {
        code: 500,
        message: '获取失败',
        error: error.message
      };
    }
  }
}
