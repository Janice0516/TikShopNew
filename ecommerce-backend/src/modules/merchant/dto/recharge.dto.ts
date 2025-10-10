import { ApiProperty } from '@nestjs/swagger';
import { IsNumber, IsString, IsOptional, IsIn, Min } from 'class-validator';

export class CreateRechargeDto {
  @ApiProperty({ description: '充值金额', example: 1000.00 })
  @IsNumber()
  @Min(0.01)
  amount: number;

  @ApiProperty({ description: '支付方式', example: 'bank_transfer' })
  @IsString()
  paymentMethod: string;

  @ApiProperty({ description: '支付凭证号', example: 'REF123456789', required: false })
  @IsOptional()
  @IsString()
  paymentReference?: string;

  @ApiProperty({ description: '备注', example: '银行转账充值', required: false })
  @IsOptional()
  @IsString()
  remark?: string;
}

export class AuditRechargeDto {
  @ApiProperty({ description: '充值记录ID', example: 1 })
  @IsNumber()
  id: number;

  @ApiProperty({ description: '审核状态 1通过 2拒绝', example: 1 })
  @IsNumber()
  @IsIn([1, 2])
  status: number;

  @ApiProperty({ description: '审核原因', example: '审核通过', required: false })
  @IsOptional()
  @IsString()
  auditReason?: string;
}

export class QueryRechargeDto {
  @ApiProperty({ description: '商户ID', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  merchantId?: number;

  @ApiProperty({ description: '状态 0待审核 1通过 2拒绝', example: 0, required: false })
  @IsOptional()
  @IsNumber()
  @IsIn([0, 1, 2])
  status?: number;

  @ApiProperty({ description: '支付方式', example: 'bank_transfer', required: false })
  @IsOptional()
  @IsString()
  paymentMethod?: string;

  @ApiProperty({ description: '开始时间', example: '2025-01-01', required: false })
  @IsOptional()
  @IsString()
  startDate?: string;

  @ApiProperty({ description: '结束时间', example: '2025-01-31', required: false })
  @IsOptional()
  @IsString()
  endDate?: string;

  @ApiProperty({ description: '页码', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  page?: number;

  @ApiProperty({ description: '每页数量', example: 10, required: false })
  @IsOptional()
  @IsNumber()
  pageSize?: number;
}
