import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, Length, IsOptional, IsNumber } from 'class-validator';

export class CreateAddressDto {
  @ApiProperty({ description: '收货人姓名', example: '张三' })
  @IsString()
  @IsNotEmpty({ message: '收货人姓名不能为空' })
  @Length(1, 50, { message: '收货人姓名长度必须在1-50个字符之间' })
  receiverName: string;

  @ApiProperty({ description: '收货电话', example: '13800138000' })
  @IsString()
  @IsNotEmpty({ message: '收货电话不能为空' })
  @Length(11, 11, { message: '收货电话必须是11位数字' })
  phone: string;

  @ApiProperty({ description: '省份', example: '广东省' })
  @IsString()
  @IsNotEmpty({ message: '省份不能为空' })
  @Length(1, 50, { message: '省份长度必须在1-50个字符之间' })
  province: string;

  @ApiProperty({ description: '城市', example: '深圳市' })
  @IsString()
  @IsNotEmpty({ message: '城市不能为空' })
  @Length(1, 50, { message: '城市长度必须在1-50个字符之间' })
  city: string;

  @ApiProperty({ description: '区/县', example: '南山区' })
  @IsString()
  @IsNotEmpty({ message: '区/县不能为空' })
  @Length(1, 50, { message: '区/县长度必须在1-50个字符之间' })
  district: string;

  @ApiProperty({ description: '详细地址', example: '科技园南区某某大厦1号楼101室' })
  @IsString()
  @IsNotEmpty({ message: '详细地址不能为空' })
  @Length(1, 255, { message: '详细地址长度必须在1-255个字符之间' })
  detailAddress: string;

  @ApiProperty({ description: '是否设为默认地址', example: false, required: false })
  @IsOptional()
  @IsNumber()
  isDefault?: number;
}

export class UpdateAddressDto {
  @ApiProperty({ description: '收货人姓名', example: '张三', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50, { message: '收货人姓名长度必须在1-50个字符之间' })
  receiverName?: string;

  @ApiProperty({ description: '收货电话', example: '13800138000', required: false })
  @IsOptional()
  @IsString()
  @Length(11, 11, { message: '收货电话必须是11位数字' })
  phone?: string;

  @ApiProperty({ description: '省份', example: '广东省', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50, { message: '省份长度必须在1-50个字符之间' })
  province?: string;

  @ApiProperty({ description: '城市', example: '深圳市', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50, { message: '城市长度必须在1-50个字符之间' })
  city?: string;

  @ApiProperty({ description: '区/县', example: '南山区', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50, { message: '区/县长度必须在1-50个字符之间' })
  district?: string;

  @ApiProperty({ description: '详细地址', example: '科技园南区某某大厦1号楼101室', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 255, { message: '详细地址长度必须在1-255个字符之间' })
  detailAddress?: string;

  @ApiProperty({ description: '是否设为默认地址', example: false, required: false })
  @IsOptional()
  @IsNumber()
  isDefault?: number;
}

