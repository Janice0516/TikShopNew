import { ApiProperty } from '@nestjs/swagger';
import { IsOptional, IsString, IsNumber, IsNotEmpty } from 'class-validator';

export class UpdateMerchantDto {
  @ApiProperty({ description: '商家名称', required: false })
  @IsOptional()
  @IsString()
  @IsNotEmpty()
  merchantName?: string;

  @ApiProperty({ description: '店铺名称', required: false })
  @IsOptional()
  @IsString()
  @IsNotEmpty()
  shopName?: string;

  @ApiProperty({ description: '联系人', required: false })
  @IsOptional()
  @IsString()
  @IsNotEmpty()
  contactName?: string;

  @ApiProperty({ description: '联系电话', required: false })
  @IsOptional()
  @IsString()
  @IsNotEmpty()
  contactPhone?: string;

  @ApiProperty({ description: '商家状态', required: false })
  @IsOptional()
  @IsNumber()
  status?: number;
}
