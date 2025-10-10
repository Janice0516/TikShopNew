import { Controller, Post, Body, Get } from '@nestjs/common';
import { ApiTags, ApiOperation } from '@nestjs/swagger';

@ApiTags('测试模块')
@Controller('test')
export class TestController {
  
  @Post('admin-login')
  @ApiOperation({ summary: '测试管理员登录' })
  async testAdminLogin(@Body() body: { username: string; password: string }) {
    // 直接返回成功，用于测试
    return {
      code: 200,
      message: '登录成功',
      data: {
        token: 'test-admin-token-123456',
        adminInfo: {
          id: 1,
          username: 'admin',
          nickname: '管理员',
          role: 'admin'
        }
      }
    };
  }

  @Post('user-login')
  @ApiOperation({ summary: '测试用户登录' })
  async testUserLogin(@Body() body: { phone: string; password: string }) {
    try {
      // 简单的硬编码测试
      if (body.phone === '13800138000' && body.password === '123456') {
        return {
          code: 200,
          message: '登录成功',
          data: {
            token: 'test-user-token-123456',
            userInfo: {
              id: 1,
              phone: '13800138000',
              nickname: '测试用户',
              avatar: ''
            }
          }
        };
      } else {
        return {
          code: 401,
          message: '手机号或密码错误'
        };
      }
    } catch (error) {
      return {
        code: 500,
        message: '服务器错误',
        error: error.message
      };
    }
  }

  @Get('status')
  @ApiOperation({ summary: '测试服务状态' })
  async testStatus() {
    return {
      code: 200,
      message: '服务正常',
      data: {
        timestamp: new Date().toISOString(),
        status: 'running'
      }
    };
  }
}

