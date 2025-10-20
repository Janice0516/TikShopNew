import {
  Controller,
  Get,
  Post,
  Body,
  Param,
  Query,
  UseGuards,
  Request,
  Patch,
  Inject,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { UserService } from './user.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { User } from './entities/user.entity';

@ApiTags('管理员用户管理')
@Controller('admin/users')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class AdminUserController {
  constructor(
    private readonly userService: UserService,
    @InjectRepository(User)
    private userRepository: Repository<User>,
  ) {}

  @Get()
  @ApiOperation({ summary: '获取用户列表（管理员）' })
  async findAll(@Query() query: any) {
    try {
      const { page = 1, pageSize = 10 } = query;
      
      // 简化查询，先返回空数据
      const list = [];
      const total = 0;

      return {
        code: 200,
        message: '获取成功',
        data: {
          list,
          total,
          page: parseInt(page),
          pageSize: parseInt(pageSize),
          totalPages: Math.ceil(total / pageSize),
        },
      };
    } catch (error) {
      console.error('获取用户列表失败:', error);
      return {
        code: 500,
        message: '获取用户列表失败',
        data: null,
      };
    }
  }

  @Get('count')
  @ApiOperation({ summary: '统计用户数量（管理员）' })
  async countUsers() {
    try {
      const totalUsers = await this.userRepository.count();
      const activeUsers = await this.userRepository.count({
        where: { status: 1 },
      });
      const disabledUsers = await this.userRepository.count({
        where: { status: 0 },
      });

      // 今日新增用户
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const todayUsers = await this.userRepository.count({
        where: {
          createTime: {
            $gte: today,
          } as any,
        },
      });

      return {
        code: 200,
        message: '获取成功',
        data: {
          totalUsers,
          activeUsers,
          disabledUsers,
          todayUsers,
        },
      };
    } catch (error) {
      console.error('统计用户数量失败:', error);
      return {
        code: 500,
        message: '统计失败',
        data: null,
      };
    }
  }

  @Get(':id')
  @ApiOperation({ summary: '获取用户详情（管理员）' })
  async findOne(@Param('id') id: string) {
    try {
      const user = await this.userRepository.findBy({ id: id });

      if (!user || user.length === 0) {
        return {
          code: 404,
          message: '用户不存在',
          data: null,
        };
      }

      // 移除密码字段
      const { password, ...safeUser } = user[0];

      return {
        code: 200,
        message: '获取成功',
        data: safeUser,
      };
    } catch (error) {
      console.error('获取用户详情失败:', error);
      return {
        code: 500,
        message: '获取用户详情失败',
        data: null,
      };
    }
  }

  @Patch(':id/status')
  @ApiOperation({ summary: '更新用户状态（管理员）' })
  async updateStatus(@Param('id') id: string, @Body('status') status: number) {
    try {
      const user = await this.userRepository.findBy({ id: id });

      if (!user || user.length === 0) {
        return {
          code: 404,
          message: '用户不存在',
          data: null,
        };
      }

      await this.userRepository.update(id, { status });

      return {
        code: 200,
        message: '状态更新成功',
        data: null,
      };
    } catch (error) {
      console.error('更新用户状态失败:', error);
      return {
        code: 500,
        message: '更新用户状态失败',
        data: null,
      };
    }
  }

  @Patch(':id/reset-password')
  @ApiOperation({ summary: '重置用户密码（管理员）' })
  async resetPassword(@Param('id') id: string, @Body('newPassword') newPassword: string) {
    try {
      const user = await this.userRepository.findBy({ id: id });

      if (!user || user.length === 0) {
        return {
          code: 404,
          message: '用户不存在',
          data: null,
        };
      }

      // 加密新密码
      const bcrypt = require('bcrypt');
      const hashedPassword = await bcrypt.hash(newPassword, 10);

      await this.userRepository.update(id, { 
        password: hashedPassword 
      });

      return {
        code: 200,
        message: '密码重置成功',
        data: null,
      };
    } catch (error) {
      console.error('重置用户密码失败:', error);
      return {
        code: 500,
        message: '重置用户密码失败',
        data: null,
      };
    }
  }
}
