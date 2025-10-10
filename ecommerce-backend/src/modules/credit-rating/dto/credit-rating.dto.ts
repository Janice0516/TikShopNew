import { ApiProperty } from '@nestjs/swagger';
import { IsNumber, IsString, IsOptional, IsDateString, Min, Max, IsIn } from 'class-validator';

export class CreateCreditRatingDto {
  @ApiProperty({ description: '商户ID', example: 1 })
  @IsNumber()
  merchantId: number;

  @ApiProperty({ description: '信用评级 1-5星', example: 5 })
  @IsNumber()
  @Min(1)
  @Max(5)
  rating: number;

  @ApiProperty({ description: '信用分数 0-100', example: 95.5 })
  @IsNumber()
  @Min(0)
  @Max(100)
  score: number;

  @ApiProperty({ description: '信用等级', example: 'AAA', enum: ['AAA', 'AA', 'A', 'BBB', 'BB', 'B', 'C'] })
  @IsString()
  @IsIn(['AAA', 'AA', 'A', 'BBB', 'BB', 'B', 'C'])
  level: string;

  @ApiProperty({ description: '评级日期', example: '2024-01-08' })
  @IsDateString()
  evaluationDate: string;

  @ApiProperty({ description: '有效期至', example: '2024-12-31' })
  @IsDateString()
  validUntil: string;

  @ApiProperty({ description: '评级原因', example: '商户经营状况良好，无违规记录', required: false })
  @IsOptional()
  @IsString()
  evaluationReason?: string;
}

export class UpdateCreditRatingDto {
  @ApiProperty({ description: '信用评级 1-5星', example: 5, required: false })
  @IsOptional()
  @IsNumber()
  @Min(1)
  @Max(5)
  rating?: number;

  @ApiProperty({ description: '信用分数 0-100', example: 95.5, required: false })
  @IsOptional()
  @IsNumber()
  @Min(0)
  @Max(100)
  score?: number;

  @ApiProperty({ description: '信用等级', example: 'AAA', enum: ['AAA', 'AA', 'A', 'BBB', 'BB', 'B', 'C'], required: false })
  @IsOptional()
  @IsString()
  @IsIn(['AAA', 'AA', 'A', 'BBB', 'BB', 'B', 'C'])
  level?: string;

  @ApiProperty({ description: '评级日期', example: '2024-01-08', required: false })
  @IsOptional()
  @IsDateString()
  evaluationDate?: string;

  @ApiProperty({ description: '有效期至', example: '2024-12-31', required: false })
  @IsOptional()
  @IsDateString()
  validUntil?: string;

  @ApiProperty({ description: '评级原因', example: '商户经营状况良好，无违规记录', required: false })
  @IsOptional()
  @IsString()
  evaluationReason?: string;

  @ApiProperty({ description: '状态', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  @IsIn([0, 1])
  status?: number;
}

export class QueryCreditRatingDto {
  @ApiProperty({ description: '页码', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  page?: number;

  @ApiProperty({ description: '每页数量', example: 10, required: false })
  @IsOptional()
  @IsNumber()
  pageSize?: number;

  @ApiProperty({ description: '商户ID', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  merchantId?: number;

  @ApiProperty({ description: '信用等级', example: 'AAA', required: false })
  @IsOptional()
  @IsString()
  level?: string;

  @ApiProperty({ description: '状态', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  status?: number;
}
