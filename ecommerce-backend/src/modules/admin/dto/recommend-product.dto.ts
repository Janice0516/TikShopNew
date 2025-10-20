import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { IsOptional, IsBoolean, IsString, IsNumber, IsDateString, Min, Max } from 'class-validator';

export class UpdateRecommendProductDto {
  @ApiProperty({ description: '是否推荐为热门商品' })
  @IsBoolean()
  isPopular: boolean;

  @ApiProperty({ description: '是否推荐为Top Deals' })
  @IsBoolean()
  isTopDeal: boolean;

  @ApiPropertyOptional({ description: '推荐理由' })
  @IsOptional()
  @IsString()
  recommendReason?: string;

  @ApiPropertyOptional({ description: '推荐优先级 (0-100)', minimum: 0, maximum: 100 })
  @IsOptional()
  @IsNumber()
  @Min(0)
  @Max(100)
  recommendPriority?: number;

  @ApiPropertyOptional({ description: '推荐开始时间' })
  @IsOptional()
  @IsDateString()
  recommendStartTime?: string;

  @ApiPropertyOptional({ description: '推荐结束时间' })
  @IsOptional()
  @IsDateString()
  recommendEndTime?: string;
}

export class QueryRecommendProductsDto {
  @ApiPropertyOptional({ description: '页码', default: 1 })
  @IsOptional()
  @IsNumber()
  page?: number = 1;

  @ApiPropertyOptional({ description: '每页数量', default: 10 })
  @IsOptional()
  @IsNumber()
  pageSize?: number = 10;

  @ApiPropertyOptional({ description: '推荐类型', enum: ['popular', 'top_deal', 'all'] })
  @IsOptional()
  @IsString()
  recommendType?: 'popular' | 'top_deal' | 'all' = 'all';

  @ApiPropertyOptional({ description: '商家ID' })
  @IsOptional()
  @IsNumber()
  merchantId?: number;

  @ApiPropertyOptional({ description: '商品名称关键词' })
  @IsOptional()
  @IsString()
  keyword?: string;

  @ApiPropertyOptional({ description: '推荐状态', enum: ['active', 'expired', 'all'] })
  @IsOptional()
  @IsString()
  status?: 'active' | 'expired' | 'all' = 'all';
}
