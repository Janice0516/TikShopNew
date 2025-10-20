import { Controller, Post, Get, Body, Query, Param, Patch, Delete, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { RolePermissionService } from '../services/role-permission.service';
import { CreateRoleDto, UpdateRoleDto, QueryRoleDto, CreatePermissionDto, UpdatePermissionDto } from '../dto/role-permission.dto';
import { JwtAuthGuard } from '../guards/jwt-auth.guard';
import { RolesGuard } from '../guards/roles.guard';
import { Roles } from '@/common/decorators/roles.decorator';

@ApiTags('角色权限管理')
@Controller('role-permission')
export class RolePermissionController {
  constructor(private readonly rolePermissionService: RolePermissionService) {}

  // ========== 角色管理 ==========

  @Post('role/create')
  @ApiOperation({ summary: '创建角色' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async createRole(@Body() createRoleDto: CreateRoleDto) {
    const role = await this.rolePermissionService.createRole(createRoleDto);
    return { code: 200, message: 'success', data: role };
  }

  @Get('role/list')
  @ApiOperation({ summary: '获取角色列表' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async getRoles(@Query() query: QueryRoleDto) {
    const result = await this.rolePermissionService.getRoles(query);
    return { code: 200, message: 'success', data: result };
  }

  @Get('role/all')
  @ApiOperation({ summary: '获取所有角色（下拉选择）' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async getAllRoles() {
    const roles = await this.rolePermissionService.getAllRoles();
    return { code: 200, message: 'success', data: roles };
  }

  @Patch('role/:id')
  @ApiOperation({ summary: '更新角色' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async updateRole(@Param('id') id: string, @Body() updateRoleDto: UpdateRoleDto) {
    const role = await this.rolePermissionService.updateRole(id, updateRoleDto);
    return { code: 200, message: 'success', data: role };
  }

  @Delete('role/:id')
  @ApiOperation({ summary: '删除角色' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async deleteRole(@Param('id') id: string) {
    const result = await this.rolePermissionService.deleteRole(id);
    return { code: 200, message: 'success', data: result };
  }

  // ========== 权限管理 ==========

  @Post('permission/create')
  @ApiOperation({ summary: '创建权限' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async createPermission(@Body() createPermissionDto: CreatePermissionDto) {
    const permission = await this.rolePermissionService.createPermission(createPermissionDto);
    return { code: 200, message: 'success', data: permission };
  }

  @Get('permission/list')
  @ApiOperation({ summary: '获取权限列表' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async getPermissions() {
    const permissions = await this.rolePermissionService.getPermissions();
    return { code: 200, message: 'success', data: permissions };
  }

  @Get('permission/grouped')
  @ApiOperation({ summary: '按分组获取权限' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async getPermissionsByGroup() {
    const permissions = await this.rolePermissionService.getPermissionsByGroup();
    return { code: 200, message: 'success', data: permissions };
  }

  @Patch('permission/:id')
  @ApiOperation({ summary: '更新权限' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async updatePermission(@Param('id') id: string, @Body() updatePermissionDto: UpdatePermissionDto) {
    const permission = await this.rolePermissionService.updatePermission(id, updatePermissionDto);
    return { code: 200, message: 'success', data: permission };
  }

  @Delete('permission/:id')
  @ApiOperation({ summary: '删除权限' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async deletePermission(@Param('id') id: string) {
    const result = await this.rolePermissionService.deletePermission(id);
    return { code: 200, message: 'success', data: result };
  }

  // ========== 系统初始化 ==========

  @Post('init-system')
  @ApiOperation({ summary: '初始化系统角色和权限' })
  @ApiBearerAuth()
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('admin')
  async initSystemData() {
    const result = await this.rolePermissionService.initSystemData();
    return { code: 200, message: 'success', data: result };
  }
}
