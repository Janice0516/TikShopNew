import { ApiProperty } from '@nestjs/swagger';
import {
  IsString,
  IsNotEmpty,
  IsNumber,
  IsOptional,
  Min,
  MaxLength,
} from 'class-validator';

export class CreateProductDto {
  @ApiProperty({ description: '商品名称', example: '纯棉T恤 经典款' })
  @IsString()
  @IsNotEmpty({ message: '商品名称不能为空' })
  @MaxLength(200, { message: '商品名称最多200个字符' })
  name: string;

  @ApiProperty({ description: '分类ID', example: 6 })
  @IsNumber()
  @IsNotEmpty({ message: '分类ID不能为空' })
  categoryId: number;

  @ApiProperty({ description: '品牌', example: '优衣库', required: false })
  @IsString()
  @IsOptional()
  @MaxLength(100)
  brand?: string;

  @ApiProperty({ description: '主图URL', example: '/images/products/tshirt.jpg' })
  @IsString()
  @IsNotEmpty({ message: '主图不能为空' })
  mainImage: string;

  @ApiProperty({
    description: '轮播图JSON数组',
    example: '["image1.jpg","image2.jpg"]',
    required: false,
  })
  @IsString()
  @IsOptional()
  images?: string;

  @ApiProperty({ description: '商品视频', required: false })
  @IsString()
  @IsOptional()
  video?: string;

  @ApiProperty({ description: '成本价', example: 39.0 })
  @IsNumber()
  @Min(0, { message: '成本价不能小于0' })
  costPrice: number;

  @ApiProperty({ description: '建议售价', example: 79.0, required: false })
  @IsNumber()
  @IsOptional()
  @Min(0)
  suggestPrice?: number;

  @ApiProperty({ description: '库存', example: 1000 })
  @IsNumber()
  @Min(0, { message: '库存不能小于0' })
  stock: number;

  @ApiProperty({ description: '商品详情', required: false })
  @IsString()
  @IsOptional()
  description?: string;
}

