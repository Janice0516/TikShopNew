import { ApiProperty } from '@nestjs/swagger';
import {
  IsArray,
  IsNotEmpty,
  IsNumber,
  IsString,
  IsOptional,
  ValidateNested,
  ArrayMinSize,
} from 'class-validator';
import { Type } from 'class-transformer';

export class OrderItemDto {
  @ApiProperty({ description: '商品ID', example: 1 })
  @IsNumber()
  @IsNotEmpty()
  productId: number;

  @ApiProperty({ description: 'SKU ID', required: false })
  @IsNumber()
  @IsOptional()
  skuId?: number;

  @ApiProperty({ description: '数量', example: 1 })
  @IsNumber()
  @IsNotEmpty()
  quantity: number;
}

export class CreateOrderDto {
  @ApiProperty({ description: '收货地址ID', example: 1 })
  @IsNumber()
  @IsNotEmpty({ message: '请选择收货地址' })
  addressId: number;

  @ApiProperty({ description: '订单商品列表', type: [OrderItemDto] })
  @IsArray()
  @ArrayMinSize(1, { message: '至少选择一件商品' })
  @ValidateNested({ each: true })
  @Type(() => OrderItemDto)
  items: OrderItemDto[];

  @ApiProperty({ description: '买家留言', required: false })
  @IsString()
  @IsOptional()
  buyerMessage?: string;
}

