import { ApiProperty } from '@nestjs/swagger';
import { IsNumber, IsNotEmpty, Min, IsOptional, IsString } from 'class-validator';

export class SelectProductDto {
  @ApiProperty({ description: '商品ID', example: 1 })
  @IsNumber()
  @IsNotEmpty({ message: '商品ID不能为空' })
  productId: number;

  @ApiProperty({ description: '销售价格', example: 99.99 })
  @IsNumber()
  @IsNotEmpty({ message: '销售价格不能为空' })
  @Min(0.01, { message: '销售价格必须大于0' })
  salePrice: number;

  @ApiProperty({ description: '利润率', example: 20.5, required: false })
  @IsNumber()
  @IsOptional()
  profitMargin?: number;
}

export class UpdateProductPriceDto {
  @ApiProperty({ description: '销售价格', example: 99.99 })
  @IsNumber()
  @IsNotEmpty({ message: '销售价格不能为空' })
  @Min(0.01, { message: '销售价格必须大于0' })
  salePrice: number;
}

export class UpdateProductStatusDto {
  @ApiProperty({ description: '状态', example: 1 })
  @IsNumber()
  @IsNotEmpty({ message: '状态不能为空' })
  status: number;
}

export class QueryMerchantProductDto {
  @ApiProperty({ description: '页码', example: 1, required: false })
  @IsNumber()
  @IsOptional()
  page?: number;

  @ApiProperty({ description: '每页数量', example: 10, required: false })
  @IsNumber()
  @IsOptional()
  pageSize?: number;

  @ApiProperty({ description: '状态筛选', example: 1, required: false })
  @IsNumber()
  @IsOptional()
  status?: number;

  @ApiProperty({ description: '关键词搜索', example: 'iPhone', required: false })
  @IsString()
  @IsOptional()
  keyword?: string;
}
