import { ApiProperty } from '@nestjs/swagger';
import { IsNumber, IsNotEmpty, Min, IsOptional } from 'class-validator';

export class AddCartDto {
  @ApiProperty({ description: '商品ID', example: 1 })
  @IsNumber()
  @IsNotEmpty({ message: '商品ID不能为空' })
  productId: number;

  @ApiProperty({ description: 'SKU ID', required: false })
  @IsNumber()
  @IsOptional()
  skuId?: number;

  @ApiProperty({ description: '数量', example: 1 })
  @IsNumber()
  @Min(1, { message: '数量至少为1' })
  quantity: number;
}

