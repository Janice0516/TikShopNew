import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, Length, IsOptional, IsInt, Min, IsEmail, IsPhoneNumber } from 'class-validator';

export class CreateAdminDto {
  @ApiProperty({ description: '用户名', example: 'admin001' })
  @IsString()
  @IsNotEmpty({ message: '用户名不能为空' })
  @Length(3, 50, { message: '用户名长度为3-50个字符' })
  username: string;

  @ApiProperty({ description: '密码', example: 'password123' })
  @IsString()
  @IsNotEmpty({ message: '密码不能为空' })
  @Length(6, 20, { message: '密码长度为6-20个字符' })
  password: string;

  @ApiProperty({ description: '昵称', example: '管理员' })
  @IsString()
  @IsNotEmpty({ message: '昵称不能为空' })
  @Length(2, 50, { message: '昵称长度为2-50个字符' })
  nickname: string;

  @ApiProperty({ description: '职务', example: '系统管理员', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 50, { message: '职务长度不能超过50个字符' })
  position?: string;

  @ApiProperty({ description: '手机号', example: '13800138000', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 20, { message: '手机号长度不能超过20个字符' })
  phone?: string;

  @ApiProperty({ description: '邮箱', example: 'admin@example.com', required: false })
  @IsOptional()
  @IsEmail({}, { message: '请输入正确的邮箱格式' })
  email?: string;

  @ApiProperty({ description: '角色ID', example: 'uuid-string', required: false })
  @IsOptional()
  @IsString()
  roleId?: string;

  @ApiProperty({ description: '备注', example: '系统管理员账户', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 255, { message: '备注长度不能超过255个字符' })
  remark?: string;

  @ApiProperty({ description: '状态', example: 1, default: 1, required: false })
  @IsOptional()
  @IsInt({ message: '状态必须是整数' })
  @Min(0, { message: '状态不能小于0' })
  status?: number;
}

export class UpdateAdminDto {
  @ApiProperty({ description: '昵称', example: '管理员', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 50, { message: '昵称长度不能超过50个字符' })
  nickname?: string;

  @ApiProperty({ description: '职务', example: '系统管理员', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 50, { message: '职务长度不能超过50个字符' })
  position?: string;

  @ApiProperty({ description: '手机号', example: '13800138000', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 20, { message: '手机号长度不能超过20个字符' })
  phone?: string;

  @ApiProperty({ description: '邮箱', example: 'admin@example.com', required: false })
  @IsOptional()
  @IsEmail({}, { message: '请输入正确的邮箱格式' })
  email?: string;

  @ApiProperty({ description: '角色ID', example: 'uuid-string', required: false })
  @IsOptional()
  @IsString()
  roleId?: string;

  @ApiProperty({ description: '备注', example: '系统管理员账户', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 255, { message: '备注长度不能超过255个字符' })
  remark?: string;

  @ApiProperty({ description: '状态', example: 1, required: false })
  @IsOptional()
  @IsInt({ message: '状态必须是整数' })
  @Min(0, { message: '状态不能小于0' })
  status?: number;
}

export class ResetPasswordDto {
  @ApiProperty({ description: '新密码', example: 'newpassword123' })
  @IsString()
  @IsNotEmpty({ message: '新密码不能为空' })
  @Length(6, 20, { message: '密码长度为6-20个字符' })
  newPassword: string;
}

export class QueryAdminDto {
  @ApiProperty({ description: '用户名', required: false })
  @IsOptional()
  @IsString()
  username?: string;

  @ApiProperty({ description: '昵称', required: false })
  @IsOptional()
  @IsString()
  nickname?: string;

  @ApiProperty({ description: '角色ID', required: false })
  @IsOptional()
  @IsString()
  roleId?: string;

  @ApiProperty({ description: '状态', required: false })
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

export class SalespersonData {
  @ApiProperty({ description: '用户名', example: 'sales001' })
  @IsString()
  @IsNotEmpty({ message: '用户名不能为空' })
  @Length(3, 50, { message: '用户名长度为3-50个字符' })
  username: string;

  @ApiProperty({ description: '密码', example: 'sales123456' })
  @IsString()
  @IsNotEmpty({ message: '密码不能为空' })
  @Length(6, 20, { message: '密码长度为6-20个字符' })
  password: string;

  @ApiProperty({ description: '昵称', example: '张三' })
  @IsString()
  @IsNotEmpty({ message: '昵称不能为空' })
  @Length(2, 50, { message: '昵称长度为2-50个字符' })
  nickname: string;

  @ApiProperty({ description: '手机号', example: '13800138000' })
  @IsString()
  @IsNotEmpty({ message: '手机号不能为空' })
  @Length(11, 11, { message: '手机号长度必须为11位' })
  phone: string;

  @ApiProperty({ description: '邮箱', example: 'sales@example.com', required: false })
  @IsOptional()
  @IsEmail({}, { message: '请输入正确的邮箱格式' })
  email?: string;

  @ApiProperty({ description: '备注', example: '业务员账户', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 255, { message: '备注长度不能超过255个字符' })
  remark?: string;
}

export class CreateSalespersonAccountsDto {
  @ApiProperty({ description: '业务员数据列表', type: [SalespersonData] })
  salespersonData: SalespersonData[];
}
