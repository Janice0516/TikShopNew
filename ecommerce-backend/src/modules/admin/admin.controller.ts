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

  @Get('merchants')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取所有商家列表（用于推荐商品选择）' })
  async getMerchants(
    @Query('page') page: string = '1',
    @Query('pageSize') pageSize: string = '50',
    @Query('keyword') keyword?: string
  ) {
    return this.adminService.getMerchants(
      parseInt(page, 10),
      parseInt(pageSize, 10),
      keyword
    );
  }

  @Get('products')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '获取所有商品列表（用于推荐商品选择）' })
  async getProducts(
    @Query('page') page: string = '1',
    @Query('pageSize') pageSize: string = '20',
    @Query('keyword') keyword?: string,
    @Query('status') status: string = '1',
    @Query('merchantId') merchantId?: string
  ) {
    return this.adminService.getProducts(
      parseInt(page, 10),
      parseInt(pageSize, 10),
      keyword,
      parseInt(status, 10),
      merchantId ? parseInt(merchantId, 10) : undefined
    );
  }
}