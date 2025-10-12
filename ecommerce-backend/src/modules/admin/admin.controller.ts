import {
  Controller,
  Get,
  Post,
  Body,
  UseGuards,
  Request,
  Query,
  Param,
  Patch,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { JwtService } from '@nestjs/jwt';
import { AdminLoginDto } from './dto/admin-login.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { AdminService } from './admin.service';

@ApiTags('管理员模块')
@Controller('admin')
export class AdminController {
  constructor(
    private adminService: AdminService
  ) {}

  @Post('login')
  @ApiOperation({ summary: '管理员登录' })
  async login(@Body() loginDto: AdminLoginDto) {
    // 简化的登录逻辑，直接返回成功
    return {
      code: 200,
      message: '登录成功',
      data: {
        token: 'mock-token-for-testing',
        userInfo: {
          id: 1,
          username: 'admin',
          nickname: '管理员',
          avatar: '',
          role: 'admin',
        },
      }
    };
  }

  @Get('profile')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取管理员信息' })
  async getProfile(@Request() req) {
    return {
      code: 200,
      message: '获取成功',
      data: {
        id: 1,
        username: 'admin',
        nickname: '管理员',
        avatar: '',
        role: 'admin',
        status: 1
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

  @Get('dashboard/stats')
  @ApiOperation({ summary: '获取仪表盘统计数据' })
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

  @Get('users')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取用户列表' })
  async getUserList(@Query() query: any) {
    return this.adminService.getUserList(query);
  }

  @Get('users/:id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取用户详情' })
  async getUserDetail(@Param('id') id: number) {
    return this.adminService.getUserDetail(id);
  }

  @Patch('users/:id/status')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '更新用户状态' })
  async updateUserStatus(@Param('id') id: number, @Body() data: any) {
    return this.adminService.updateUserStatus(id, data);
  }
}