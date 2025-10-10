import { Controller, Get } from '@nestjs/common';
import { ApiTags, ApiOperation } from '@nestjs/swagger';

@ApiTags('公开分类管理')
@Controller('public-categories')
export class PublicCategoryController {
  
  @Get()
  @ApiOperation({ summary: '获取公开分类列表（无需认证）' })
  findAll() {
    try {
      console.log('公开分类API被调用');
      return {
        code: 200,
        message: '获取成功',
        data: [
          { 
            id: 1, 
            parentId: 0, 
            name: '服装鞋包', 
            level: 1, 
            sort: 1, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 2, 
            parentId: 0, 
            name: '数码家电', 
            level: 1, 
            sort: 2, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 3, 
            parentId: 0, 
            name: '食品生鲜', 
            level: 1, 
            sort: 3, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 4, 
            parentId: 0, 
            name: '美妆个护', 
            level: 1, 
            sort: 4, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 5, 
            parentId: 0, 
            name: '家居生活', 
            level: 1, 
            sort: 5, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 6, 
            parentId: 0, 
            name: '运动户外', 
            level: 1, 
            sort: 6, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 7, 
            parentId: 0, 
            name: '母婴用品', 
            level: 1, 
            sort: 7, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 8, 
            parentId: 0, 
            name: '汽车用品', 
            level: 1, 
            sort: 8, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 9, 
            parentId: 0, 
            name: '图书音像', 
            level: 1, 
            sort: 9, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          },
          { 
            id: 10, 
            parentId: 0, 
            name: '宠物用品', 
            level: 1, 
            sort: 10, 
            icon: null, 
            status: 1, 
            createTime: '2025-10-05T16:56:53.000Z',
            updateTime: '2025-10-05T16:56:53.000Z'
          }
        ]
      };
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
