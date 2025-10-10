import { ApiProperty } from '@nestjs/swagger';
import { IsOptional, IsNumber, IsString, Min } from 'class-validator';
import { Type } from 'class-transformer';

export class QueryProductDto {
  @ApiProperty({ description: '页码', example: 1, required: false })
  @IsNumber()
  @IsOptional()
  @Min(1)
  @Type(() => Number)
  page?: number = 1;

  @ApiProperty({ description: '每页数量', example: 10, required: false })
  @IsNumber()
  @IsOptional()
  @Min(1)
  @Type(() => Number)
  pageSize?: number = 10;

  @ApiProperty({ description: '分类ID', required: false })
  @IsNumber()
  @IsOptional()
  @Type(() => Number)
  categoryId?: number;

  @ApiProperty({ description: '商品名称（模糊搜索）', required: false })
  @IsString()
  @IsOptional()
  keyword?: string;

  @ApiProperty({ description: '状态 1上架 0下架', required: false })
  @IsNumber()
  @IsOptional()
  @Type(() => Number)
  status?: number;
}

