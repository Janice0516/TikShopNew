import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, Length, Matches, IsOptional } from 'class-validator';

export class RegisterMerchantDto {
  @ApiProperty({ description: '登录账号', example: 'merchant002' })
  @IsString()
  @IsNotEmpty({ message: '账号不能为空' })
  @Length(5, 50, { message: '账号长度为5-50个字符' })
  username: string;

  @ApiProperty({ description: '密码', example: '123456' })
  @IsString()
  @IsNotEmpty({ message: '密码不能为空' })
  @Length(6, 20, { message: '密码长度为6-20个字符' })
  password: string;

  @ApiProperty({ description: '商家名称', example: '优品小店' })
  @IsString()
  @IsNotEmpty({ message: '商家名称不能为空' })
  @Length(2, 100, { message: '商家名称长度为2-100个字符' })
  merchantName: string;

  @ApiProperty({ description: '联系人', example: '张三' })
  @IsString()
  @IsNotEmpty({ message: '联系人不能为空' })
  contactName: string;

  @ApiProperty({ description: '联系电话', example: '012-3456789' })
  @IsString()
  @IsNotEmpty({ message: '联系电话不能为空' })
  @Matches(/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/, { message: '请输入有效的马来西亚手机号' })
  contactPhone: string;

  @ApiProperty({ description: '店铺名称', required: false })
  @IsString()
  @IsOptional()
  shopName?: string;
}
