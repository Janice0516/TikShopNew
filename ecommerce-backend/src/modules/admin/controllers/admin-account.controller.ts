import { Controller, Post, Get, Body, Query, Param, Patch, Delete, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { AdminService } from '../admin.service';
import { CreateAdminDto, UpdateAdminDto, ResetPasswordDto, QueryAdminDto, CreateSalespersonAccountsDto } from '../dto/admin-account.dto';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '@/common/decorators/roles.decorator';

@ApiTags('管理员账户管理')
@Controller('admin-accounts')
export class AdminAccountController {
  constructor(private readonly adminService: AdminService) {}

  @Post('create')
  @ApiOperation({ summary: '创建管理员账户' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async createAdmin(@Body() adminData: {
    username: string;
    password: string;
    nickname?: string;
    position?: string;
    phone?: string;
    email?: string;
    roleId?: string;
    remark?: string;
  }) {
    const admin = await this.adminService.createAdmin(adminData);
    return { code: 200, message: 'success', data: admin };
  }

  @Get('list')
  @ApiOperation({ summary: '获取管理员列表' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async getAdminList(@Query() query: { page?: number; pageSize?: number }) {
    const result = await this.adminService.getAdminList(query.page || 1, query.pageSize || 10);
    return result;
  }

  @Get(':id')
  @ApiOperation({ summary: '获取管理员详情' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async getAdminDetail(@Param('id') id: string) {
    const admin = await this.adminService.getAdminDetail(parseInt(id));
    return { code: 200, message: 'success', data: admin };
  }

  @Patch(':id')
  @ApiOperation({ summary: '更新管理员信息' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async updateAdmin(@Param('id') id: string, @Body() updateData: {
    nickname?: string;
    position?: string;
    phone?: string;
    email?: string;
    roleId?: string;
    remark?: string;
    status?: number;
  }) {
    const admin = await this.adminService.updateAdmin(parseInt(id), updateData);
    return { code: 200, message: 'success', data: admin };
  }

  @Patch(':id/reset-password')
  @ApiOperation({ summary: '重置管理员密码' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async resetPassword(@Param('id') id: string, @Body('password') password: string) {
    const result = await this.adminService.resetPassword(parseInt(id), password);
    return { code: 200, message: 'success', data: result };
  }

  @Delete(':id')
  @ApiOperation({ summary: '删除管理员' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async deleteAdmin(@Param('id') id: string) {
    const result = await this.adminService.deleteAdmin(parseInt(id));
    return { code: 200, message: 'success', data: result };
  }

  @Post('batch-create-salesperson')
  @ApiOperation({ summary: '批量创建业务员账户' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async createSalespersonAccounts(@Body() salespersonData: Array<{
    username: string;
    password: string;
    nickname: string;
    phone: string;
    email?: string;
    remark?: string;
  }>) {
    const results = await this.adminService.createSalespersonAccounts(salespersonData);
    return { code: 200, message: 'success', data: results };
  }
}
