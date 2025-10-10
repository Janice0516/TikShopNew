import { ApiProperty, PartialType } from '@nestjs/swagger';
import { CreateProductDto } from './create-product.dto';
import { IsOptional, IsNumber, Min } from 'class-validator';

export class UpdateProductDto extends PartialType(CreateProductDto) {
  @ApiProperty({ description: '排序', required: false })
  @IsNumber()
  @IsOptional()
  @Min(0)
  sort?: number;

  @ApiProperty({ description: '状态 1上架 0下架', required: false })
  @IsNumber()
  @IsOptional()
  status?: number;
}

