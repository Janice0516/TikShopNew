import { Controller, Get, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('修复分类管理')
@Controller('fixed-categories')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class FixedCategoryController {
  
  @Get()
  @ApiOperation({ summary: '获取修复分类列表' })
  findAll() {
    try {
      console.log('修复分类API被调用');
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
          }
        ]
      };
    } catch (error) {
      console.error('修复分类API错误:', error);
      return {
        code: 500,
        message: '获取失败',
        error: error.message
      };
    }
  }

  @Get('tree')
  @ApiOperation({ summary: '获取修复分类树形结构' })
  findTree() {
    try {
      console.log('修复分类树API被调用');
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
            updateTime: '2025-10-05T16:56:53.000Z',
            children: [
              {
                id: 6,
                parentId: 1,
                name: '男装',
                level: 2,
                sort: 1,
                icon: null,
                status: 1,
                createTime: '2025-10-05T16:56:53.000Z',
                updateTime: '2025-10-05T16:56:53.000Z',
                children: []
              },
              {
                id: 7,
                parentId: 1,
                name: '女装',
                level: 2,
                sort: 2,
                icon: null,
                status: 1,
                createTime: '2025-10-05T16:56:53.000Z',
                updateTime: '2025-10-05T16:56:53.000Z',
                children: []
              }
            ]
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
            updateTime: '2025-10-05T16:56:53.000Z',
            children: [
              {
                id: 10,
                parentId: 2,
                name: '手机',
                level: 2,
                sort: 1,
                icon: null,
                status: 1,
                createTime: '2025-10-05T16:56:53.000Z',
                updateTime: '2025-10-05T16:56:53.000Z',
                children: []
              },
              {
                id: 11,
                parentId: 2,
                name: '电脑',
                level: 2,
                sort: 2,
                icon: null,
                status: 1,
                createTime: '2025-10-05T16:56:53.000Z',
                updateTime: '2025-10-05T16:56:53.000Z',
                children: []
              }
            ]
          }
        ]
      };
    } catch (error) {
      console.error('修复分类树API错误:', error);
      return {
        code: 500,
        message: '获取失败',
        error: error.message
      };
    }
  }
}
