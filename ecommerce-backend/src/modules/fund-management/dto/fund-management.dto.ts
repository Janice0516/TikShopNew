import { ApiProperty } from '@nestjs/swagger';
import { IsNumber, IsString, IsOptional, IsDecimal, Min, Max, IsIn } from 'class-validator';

export class IncreaseFundDto {
  @ApiProperty({ description: '商户ID', example: 1 })
  @IsNumber()
  merchantId: number;

  @ApiProperty({ description: '增加金额', example: 1000.00 })
  @IsNumber()
  @Min(0.01)
  amount: number;

  @ApiProperty({ description: '增加原因', example: '充值' })
  @IsString()
  reason: string;

  @ApiProperty({ description: '备注', example: '管理员手动充值', required: false })
  @IsOptional()
  @IsString()
  remark?: string;
}

export class FreezeFundDto {
  @ApiProperty({ description: '商户ID', example: 1 })
  @IsNumber()
  merchantId: number;

  @ApiProperty({ description: '冻结金额', example: 1000.00 })
  @IsNumber()
  @Min(0.01)
  amount: number;

  @ApiProperty({ description: '冻结原因', example: '违规处理' })
  @IsString()
  reason: string;

  @ApiProperty({ description: '备注', example: '因违规行为冻结资金', required: false })
  @IsOptional()
  @IsString()
  remark?: string;
}

export class UnfreezeFundDto {
  @ApiProperty({ description: '商户ID', example: 1 })
  @IsNumber()
  merchantId: number;

  @ApiProperty({ description: '解冻金额', example: 1000.00 })
  @IsNumber()
  @Min(0.01)
  amount: number;

  @ApiProperty({ description: '解冻原因', example: '问题已解决' })
  @IsString()
  reason: string;

  @ApiProperty({ description: '备注', example: '违规问题已处理完毕', required: false })
  @IsOptional()
  @IsString()
  remark?: string;
}

export class DeductFundDto {
  @ApiProperty({ description: '商户ID', example: 1 })
  @IsNumber()
  merchantId: number;

  @ApiProperty({ description: '扣款金额', example: 500.00 })
  @IsNumber()
  @Min(0.01)
  amount: number;

  @ApiProperty({ description: '扣款原因', example: '违规罚款' })
  @IsString()
  reason: string;

  @ApiProperty({ description: '备注', example: '因违规行为扣除保证金', required: false })
  @IsOptional()
  @IsString()
  remark?: string;
}

export class RefundFundDto {
  @ApiProperty({ description: '商户ID', example: 1 })
  @IsNumber()
  merchantId: number;

  @ApiProperty({ description: '退款金额', example: 200.00 })
  @IsNumber()
  @Min(0.01)
  amount: number;

  @ApiProperty({ description: '退款原因', example: '订单取消退款' })
  @IsString()
  reason: string;

  @ApiProperty({ description: '关联订单ID', example: 123, required: false })
  @IsOptional()
  @IsNumber()
  orderId?: number;

  @ApiProperty({ description: '备注', example: '订单取消，退还保证金', required: false })
  @IsOptional()
  @IsString()
  remark?: string;
}

export class QueryFundOperationDto {
  @ApiProperty({ description: '商户ID', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  merchantId?: number;

  @ApiProperty({ description: '操作类型', example: 3, required: false })
  @IsOptional()
  @IsNumber()
  @IsIn([1, 2, 3, 4, 5, 6])
  operationType?: number;

  @ApiProperty({ description: '操作管理员ID', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  adminId?: number;

  @ApiProperty({ description: '开始日期', example: '2024-01-01', required: false })
  @IsOptional()
  @IsString()
  startDate?: string;

  @ApiProperty({ description: '结束日期', example: '2024-12-31', required: false })
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

export class MerchantFundInfoDto {
  @ApiProperty({ description: '商户ID', example: 1 })
  merchantId: number;

  @ApiProperty({ description: '商户名称', example: '测试商户' })
  merchantName: string;

  @ApiProperty({ description: '可用余额', example: 5000.00 })
  availableBalance: number;

  @ApiProperty({ description: '冻结金额', example: 1000.00 })
  frozenAmount: number;

  @ApiProperty({ description: '总余额', example: 6000.00 })
  totalBalance: number;

  @ApiProperty({ description: '总收入', example: 10000.00 })
  totalIncome: number;

  @ApiProperty({ description: '总提现', example: 4000.00 })
  totalWithdraw: number;
}
