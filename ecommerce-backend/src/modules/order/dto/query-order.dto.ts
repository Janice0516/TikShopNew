import { ApiProperty } from '@nestjs/swagger';
import { IsOptional, IsNumber, IsString, Min } from 'class-validator';
import { Type } from 'class-transformer';

export class QueryOrderDto {
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

  @ApiProperty({ description: '订单状态', required: false })
  @IsNumber()
  @IsOptional()
  @Type(() => Number)
  orderStatus?: number;

  @ApiProperty({ description: '订单号', required: false })
  @IsString()
  @IsOptional()
  orderNo?: string;
}

