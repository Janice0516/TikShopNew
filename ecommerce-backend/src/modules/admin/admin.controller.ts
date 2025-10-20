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
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { AdminService } from './admin.service';
import { AdminLoginDto } from './dto/admin-login.dto';

@ApiTags('管理员模块')
@Controller('admin')
export class AdminController {
  constructor(
    private adminService: AdminService
  ) {}

  @Post('login')
  @ApiOperation({ summary: '管理员登录' })
  async login(@Body() loginDto: AdminLoginDto) {
    return this.adminService.login(loginDto.username, loginDto.password);
  }

  @Get('dashboard/stats')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取仪表盘统计数据' })
  async getDashboardStats() {
    return this.adminService.getDashboardStats();
  }

  @Post('generate-batch')
  @ApiOperation({ summary: '批量生成管理员账户' })
  async generateBatchAdmins(@Body() body: { count: number; password: string }) {
    return this.adminService.generateBatchAdmins(body.count, body.password);
  }

  @Get('list')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取管理员列表' })
  async getAdminList(@Query('page') page: number = 1, @Query('pageSize') pageSize: number = 10) {
    return this.adminService.getAdminList(page, pageSize);
  }
}