import { ApiProperty } from '@nestjs/swagger';
import { IsNumber, IsString, IsOptional, IsDecimal, Min, Max } from 'class-validator';

export class CreateWithdrawalDto {
  @ApiProperty({ description: '提现金额', example: 1000.00 })
  @IsNumber()
  @Min(0.01)
  withdrawalAmount: number;

  @ApiProperty({ description: '银行名称', example: '中国银行' })
  @IsString()
  bankName: string;

  @ApiProperty({ description: '银行账号', example: '6217000000000000000' })
  @IsString()
  bankAccount: string;

  @ApiProperty({ description: '账户持有人', example: '张三' })
  @IsString()
  accountHolder: string;

  @ApiProperty({ description: '备注', example: '提现申请', required: false })
  @IsOptional()
  @IsString()
  remark?: string;
}

export class UpdateWithdrawalStatusDto {
  @ApiProperty({ description: '状态', example: 1 })
  @IsNumber()
  @Min(0)
  @Max(3)
  status: number;

  @ApiProperty({ description: '管理员备注', example: '审核通过', required: false })
  @IsOptional()
  @IsString()
  adminRemark?: string;
}
