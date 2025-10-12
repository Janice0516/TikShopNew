import { Controller, Get } from '@nestjs/common';
import { ApiTags, ApiOperation } from '@nestjs/swagger';

@ApiTags('测试模块')
@Controller('test-admin')
export class TestAdminController {
  @Get('dashboard/stats')
  @ApiOperation({ summary: '测试仪表盘统计数据' })
  async getDashboardStats() {
    return {
      code: 200,
      message: '获取成功',
      data: {
        stats: {
          products: 8,
          merchants: 3,
          orders: 0,
          users: 0
        },
        recentOrders: [],
        topProducts: []
      }
    };
  }

  @Get('test')
  @ApiOperation({ summary: '测试端点' })
  async test() {
    return {
      code: 200,
      message: '测试成功',
      data: { test: 'hello world' }
    };
  }
}
