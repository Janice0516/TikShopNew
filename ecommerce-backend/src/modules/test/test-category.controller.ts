import { Controller, Get } from '@nestjs/common';
import { ApiTags, ApiOperation } from '@nestjs/swagger';

@ApiTags('测试分类')
@Controller('test-categories')
export class TestCategoryController {
  
  @Get()
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
}
