import { IsString, IsOptional, IsBoolean, IsNumber, IsEmail } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class BasicSettingsDto {
  @ApiProperty({ description: '网站名称' })
  @IsString()
  siteName: string;

  @ApiProperty({ description: '网站Logo', required: false })
  @IsOptional()
  @IsString()
  siteLogo?: string;

  @ApiProperty({ description: '网站描述', required: false })
  @IsOptional()
  @IsString()
  siteDescription?: string;

  @ApiProperty({ description: '客服电话', required: false })
  @IsOptional()
  @IsString()
  customerServicePhone?: string;

  @ApiProperty({ description: '客服邮箱', required: false })
  @IsOptional()
  @IsEmail()
  customerServiceEmail?: string;

  @ApiProperty({ description: '默认货币' })
  @IsString()
  defaultCurrency: string;
}

export class BusinessSettingsDto {
  @ApiProperty({ description: '自动审核订单' })
  @IsBoolean()
  autoApproveOrders: boolean;

  @ApiProperty({ description: '订单超时时间(分钟)' })
  @IsNumber()
  orderTimeout: number;

  @ApiProperty({ description: '最低提现金额' })
  @IsNumber()
  minWithdrawalAmount: number;

  @ApiProperty({ description: '平台手续费率(%)' })
  @IsNumber()
  platformFeeRate: number;

  @ApiProperty({ description: '商家入驻审核' })
  @IsBoolean()
  merchantOnboardingAudit: boolean;

  @ApiProperty({ description: '商品上架审核' })
  @IsBoolean()
  productListingAudit: boolean;
}

export class SecuritySettingsDto {
  @ApiProperty({ description: '登录失败锁定' })
  @IsBoolean()
  loginFailureLock: boolean;

  @ApiProperty({ description: '最大失败次数' })
  @IsNumber()
  maxLoginAttempts: number;

  @ApiProperty({ description: '锁定时间(分钟)' })
  @IsNumber()
  lockoutDuration: number;

  @ApiProperty({ description: '密码最小长度' })
  @IsNumber()
  minPasswordLength: number;

  @ApiProperty({ description: '强制密码复杂度' })
  @IsBoolean()
  forcePasswordComplexity: boolean;

  @ApiProperty({ description: '会话超时时间(分钟)' })
  @IsNumber()
  sessionTimeout: number;
}

export class NotificationSettingsDto {
  @ApiProperty({ description: '邮件通知' })
  @IsBoolean()
  emailNotifications: boolean;

  @ApiProperty({ description: '短信通知' })
  @IsBoolean()
  smsNotifications: boolean;

  @ApiProperty({ description: '系统通知' })
  @IsBoolean()
  systemNotifications: boolean;

  @ApiProperty({ description: 'SMTP服务器' })
  @IsString()
  smtpHost: string;

  @ApiProperty({ description: 'SMTP端口' })
  @IsNumber()
  smtpPort: number;

  @ApiProperty({ description: 'SMTP用户名' })
  @IsString()
  smtpUser: string;

  @ApiProperty({ description: 'SMTP密码' })
  @IsString()
  smtpPass: string;

  @ApiProperty({ description: '使用SSL/TLS' })
  @IsBoolean()
  smtpSecure: boolean;
}

export class TestEmailDto {
  @ApiProperty({ description: '测试邮箱地址' })
  @IsEmail()
  email: string;
}

export class BackupRestoreDto {
  @ApiProperty({ description: '备份ID' })
  @IsString()
  backupId: string;
}
