import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, Length, IsOptional, IsInt, Min, IsArray } from 'class-validator';

export class CreateRoleDto {
  @ApiProperty({ description: '角色代码', example: 'SALES_MANAGER' })
  @IsString()
  @IsNotEmpty({ message: '角色代码不能为空' })
  @Length(2, 50, { message: '角色代码长度为2-50个字符' })
  code: string;

  @ApiProperty({ description: '角色名称', example: '销售经理' })
  @IsString()
  @IsNotEmpty({ message: '角色名称不能为空' })
  @Length(2, 100, { message: '角色名称长度为2-100个字符' })
  name: string;

  @ApiProperty({ description: '角色描述', example: '负责销售团队管理', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 255, { message: '角色描述长度不能超过255个字符' })
  description?: string;

  @ApiProperty({ description: '权限ID列表', example: ['1', '2', '3'], required: false })
  @IsOptional()
  @IsArray()
  permissionIds?: string[];
}

export class UpdateRoleDto {
  @ApiProperty({ description: '角色名称', example: '销售经理', required: false })
  @IsOptional()
  @IsString()
  @Length(2, 100, { message: '角色名称长度为2-100个字符' })
  name?: string;

  @ApiProperty({ description: '角色描述', example: '负责销售团队管理', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 255, { message: '角色描述长度不能超过255个字符' })
  description?: string;

  @ApiProperty({ description: '状态 (0禁用 1启用)', example: 1, required: false })
  @IsOptional()
  @IsInt()
  status?: number;

  @ApiProperty({ description: '权限ID列表', example: ['1', '2', '3'], required: false })
  @IsOptional()
  @IsArray()
  permissionIds?: string[];
}

export class QueryRoleDto {
  @ApiProperty({ description: '角色代码', required: false })
  @IsOptional()
  @IsString()
  code?: string;

  @ApiProperty({ description: '角色名称', required: false })
  @IsOptional()
  @IsString()
  name?: string;

  @ApiProperty({ description: '状态 (0禁用 1启用)', example: 1, required: false })
  @IsOptional()
  @IsInt()
  status?: number;

  @ApiProperty({ description: '当前页码', example: 1, default: 1, required: false })
  @IsOptional()
  @IsInt()
  @Min(1)
  page?: number;

  @ApiProperty({ description: '每页数量', example: 10, default: 10, required: false })
  @IsOptional()
  @IsInt()
  @Min(1)
  limit?: number;
}

export class CreatePermissionDto {
  @ApiProperty({ description: '权限代码', example: 'USER_VIEW' })
  @IsString()
  @IsNotEmpty({ message: '权限代码不能为空' })
  @Length(2, 50, { message: '权限代码长度为2-50个字符' })
  code: string;

  @ApiProperty({ description: '权限名称', example: '查看用户' })
  @IsString()
  @IsNotEmpty({ message: '权限名称不能为空' })
  @Length(2, 100, { message: '权限名称长度为2-100个字符' })
  name: string;

  @ApiProperty({ description: '权限描述', example: '查看用户信息权限', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 255, { message: '权限描述长度不能超过255个字符' })
  description?: string;

  @ApiProperty({ description: '权限分组', example: '用户管理' })
  @IsString()
  @IsNotEmpty({ message: '权限分组不能为空' })
  @Length(2, 100, { message: '权限分组长度为2-100个字符' })
  group: string;
}

export class UpdatePermissionDto {
  @ApiProperty({ description: '权限名称', example: '查看用户', required: false })
  @IsOptional()
  @IsString()
  @Length(2, 100, { message: '权限名称长度为2-100个字符' })
  name?: string;

  @ApiProperty({ description: '权限描述', example: '查看用户信息权限', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 255, { message: '权限描述长度不能超过255个字符' })
  description?: string;

  @ApiProperty({ description: '权限分组', example: '用户管理', required: false })
  @IsOptional()
  @IsString()
  @Length(2, 100, { message: '权限分组长度为2-100个字符' })
  group?: string;

  @ApiProperty({ description: '状态 (0禁用 1启用)', example: 1, required: false })
  @IsOptional()
  @IsInt()
  status?: number;
}
