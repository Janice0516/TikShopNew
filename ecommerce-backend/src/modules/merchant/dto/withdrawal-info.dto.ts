import { ApiProperty } from '@nestjs/swagger';
import { IsNumber, IsString, IsOptional, IsBoolean, IsIn, Length, IsEmail } from 'class-validator';

export class CreateWithdrawalInfoDto {
  @ApiProperty({ description: '提款方式 1银行转账 2电子钱包 3现金提取', example: 1 })
  @IsNumber()
  @IsIn([1, 2, 3])
  withdrawalType: number;

  @ApiProperty({ description: '银行名称', example: 'Maybank', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  bankName?: string;

  @ApiProperty({ description: '银行代码', example: 'MBBEMYKL', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 20)
  bankCode?: string;

  @ApiProperty({ description: '账户持有人姓名', example: 'John Doe', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  accountHolderName?: string;

  @ApiProperty({ description: '账户号码', example: '1234567890', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50)
  accountNumber?: string;

  @ApiProperty({ description: 'SWIFT代码', example: 'MBBEMYKL', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 20)
  swiftCode?: string;

  @ApiProperty({ description: '电子钱包类型', example: 'TouchnGo', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50)
  walletType?: string;

  @ApiProperty({ description: '电子钱包账户', example: '+60123456789', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  walletAccount?: string;

  @ApiProperty({ description: '手机号码', example: '+60123456789', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 20)
  phoneNumber?: string;

  @ApiProperty({ description: '邮箱地址', example: 'john@example.com', required: false })
  @IsOptional()
  @IsEmail()
  @Length(1, 100)
  email?: string;

  @ApiProperty({ description: '地址', example: '123 Jalan Ampang', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 500)
  address?: string;

  @ApiProperty({ description: '城市', example: 'Kuala Lumpur', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  city?: string;

  @ApiProperty({ description: '州/省', example: 'Kuala Lumpur', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  state?: string;

  @ApiProperty({ description: '邮政编码', example: '50450', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 20)
  postalCode?: string;

  @ApiProperty({ description: '国家', example: 'Malaysia', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50)
  country?: string;

  @ApiProperty({ description: '是否为默认提款方式', example: false, required: false })
  @IsOptional()
  @IsBoolean()
  isDefault?: boolean;
}

export class UpdateWithdrawalInfoDto {
  @ApiProperty({ description: '提款方式 1银行转账 2电子钱包 3现金提取', example: 1, required: false })
  @IsOptional()
  @IsNumber()
  @IsIn([1, 2, 3])
  withdrawalType?: number;

  @ApiProperty({ description: '银行名称', example: 'Maybank', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  bankName?: string;

  @ApiProperty({ description: '银行代码', example: 'MBBEMYKL', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 20)
  bankCode?: string;

  @ApiProperty({ description: '账户持有人姓名', example: 'John Doe', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  accountHolderName?: string;

  @ApiProperty({ description: '账户号码', example: '1234567890', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50)
  accountNumber?: string;

  @ApiProperty({ description: 'SWIFT代码', example: 'MBBEMYKL', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 20)
  swiftCode?: string;

  @ApiProperty({ description: '电子钱包类型', example: 'TouchnGo', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50)
  walletType?: string;

  @ApiProperty({ description: '电子钱包账户', example: '+60123456789', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  walletAccount?: string;

  @ApiProperty({ description: '手机号码', example: '+60123456789', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 20)
  phoneNumber?: string;

  @ApiProperty({ description: '邮箱地址', example: 'john@example.com', required: false })
  @IsOptional()
  @IsEmail()
  @Length(1, 100)
  email?: string;

  @ApiProperty({ description: '地址', example: '123 Jalan Ampang', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 500)
  address?: string;

  @ApiProperty({ description: '城市', example: 'Kuala Lumpur', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  city?: string;

  @ApiProperty({ description: '州/省', example: 'Kuala Lumpur', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 100)
  state?: string;

  @ApiProperty({ description: '邮政编码', example: '50450', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 20)
  postalCode?: string;

  @ApiProperty({ description: '国家', example: 'Malaysia', required: false })
  @IsOptional()
  @IsString()
  @Length(1, 50)
  country?: string;

  @ApiProperty({ description: '是否为默认提款方式', example: false, required: false })
  @IsOptional()
  @IsBoolean()
  isDefault?: boolean;
}

export class SetDefaultWithdrawalInfoDto {
  @ApiProperty({ description: '提款信息ID', example: 1 })
  @IsNumber()
  id: number;
}

