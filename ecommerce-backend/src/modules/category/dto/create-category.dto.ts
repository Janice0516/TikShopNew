import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNumber, IsOptional, IsInt, Min, Max } from 'class-validator';

export class CreateCategoryDto {
  @ApiProperty({ description: '父分类ID', example: 0 })
  @IsOptional()
  @IsInt()
  @Min(0)
  parentId?: number;

  @ApiProperty({ description: '分类名称', example: '电子产品' })
  @IsString()
  name: string;

  @ApiProperty({ description: '层级', example: 1 })
  @IsOptional()
  @IsInt()
  @Min(1)
  @Max(3)
  level?: number;

  @ApiProperty({ description: '排序', example: 0 })
  @IsOptional()
  @IsInt()
  @Min(0)
  sort?: number;

  @ApiProperty({ description: '图标', example: 'icon-electronics', required: false })
  @IsOptional()
  @IsString()
  icon?: string;

  @ApiProperty({ description: '状态', example: 1 })
  @IsOptional()
  @IsInt()
  @Min(0)
  @Max(1)
  status?: number;
}
