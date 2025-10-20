import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, Length, IsOptional, IsInt, Min, IsDateString } from 'class-validator';

export class CreateInviteCodeDto {
  @ApiProperty({ description: '业务员姓名', example: '张三' })
  @IsString()
  @IsNotEmpty({ message: '业务员姓名不能为空' })
  @Length(2, 50, { message: '业务员姓名长度为2-50个字符' })
  salespersonName: string;

  @ApiProperty({ description: '业务员电话', example: '012-3456789', required: false })
  @IsString()
  @IsOptional()
  salespersonPhone?: string;

  @ApiProperty({ description: '业务员ID', example: 'SALES001', required: false })
  @IsString()
  @IsOptional()
  salespersonId?: string;

  @ApiProperty({ description: '最大使用次数，0表示无限制', example: 100, required: false })
  @IsInt()
  @Min(0)
  @IsOptional()
  maxUsage?: number;

  @ApiProperty({ description: '过期时间', example: '2024-12-31T23:59:59Z', required: false })
  @IsDateString()
  @IsOptional()
  expireTime?: Date;

  @ApiProperty({ description: '备注', example: 'VIP客户专用', required: false })
  @IsString()
  @IsOptional()
  remark?: string;
}

export class ValidateInviteCodeDto {
  @ApiProperty({ description: '邀请码', example: 'INVITE123456' })
  @IsString()
  @IsNotEmpty({ message: '邀请码不能为空' })
  inviteCode: string;
}

export class QueryInviteCodeDto {
  @ApiProperty({ description: '邀请码', required: false })
  @IsString()
  @IsOptional()
  inviteCode?: string;

  @ApiProperty({ description: '业务员姓名', required: false })
  @IsString()
  @IsOptional()
  salespersonName?: string;

  @ApiProperty({ description: '状态', required: false })
  @IsInt()
  @IsOptional()
  status?: number;

  @ApiProperty({ description: '页码', example: 1, required: false })
  @IsInt()
  @Min(1)
  @IsOptional()
  page?: number;

  @ApiProperty({ description: '每页数量', example: 10, required: false })
  @IsInt()
  @Min(1)
  @IsOptional()
  limit?: number;
}
