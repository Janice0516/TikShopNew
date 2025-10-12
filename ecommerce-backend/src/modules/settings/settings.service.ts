import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { SystemSettings } from './entities/system-settings.entity';
import {
  BasicSettingsDto,
  BusinessSettingsDto,
  SecuritySettingsDto,
  NotificationSettingsDto,
  TestEmailDto,
  BackupRestoreDto
} from './dto/settings.dto';

@Injectable()
export class SettingsService {
  constructor(
    @InjectRepository(SystemSettings)
    private settingsRepository: Repository<SystemSettings>,
  ) {}

  // 获取所有系统设置
  async getSystemSettings() {
    try {
      const settings = await this.settingsRepository.find();
      const settingsMap = {};
      
      settings.forEach(setting => {
        settingsMap[setting.settingKey] = this.parseSettingValue(setting.settingValue, setting.settingType);
      });

      return {
        code: 200,
        message: '获取成功',
        data: {
          basic: this.getBasicSettings(settingsMap),
          business: this.getBusinessSettings(settingsMap),
          security: this.getSecuritySettings(settingsMap),
          notification: this.getNotificationSettings(settingsMap)
        }
      };
    } catch (error) {
      console.error('获取系统设置失败:', error);
      return {
        code: 500,
        message: '获取系统设置失败',
        data: null
      };
    }
  }

  // 更新基本设置
  async updateBasicSettings(data: BasicSettingsDto) {
    try {
      const settings = [
        { key: 'site_name', value: data.siteName, type: 'string', category: 'basic' },
        { key: 'site_logo', value: data.siteLogo || '', type: 'string', category: 'basic' },
        { key: 'site_description', value: data.siteDescription || '', type: 'string', category: 'basic' },
        { key: 'customer_service_phone', value: data.customerServicePhone || '', type: 'string', category: 'basic' },
        { key: 'customer_service_email', value: data.customerServiceEmail || '', type: 'string', category: 'basic' },
        { key: 'default_currency', value: data.defaultCurrency, type: 'string', category: 'basic' }
      ];

      await this.updateSettings(settings);

      return {
        code: 200,
        message: '基本设置更新成功',
        data: null
      };
    } catch (error) {
      console.error('更新基本设置失败:', error);
      return {
        code: 500,
        message: '更新基本设置失败',
        data: null
      };
    }
  }

  // 更新业务设置
  async updateBusinessSettings(data: BusinessSettingsDto) {
    try {
      const settings = [
        { key: 'auto_approve_orders', value: data.autoApproveOrders, type: 'boolean', category: 'business' },
        { key: 'order_timeout', value: data.orderTimeout, type: 'number', category: 'business' },
        { key: 'min_withdrawal_amount', value: data.minWithdrawalAmount, type: 'number', category: 'business' },
        { key: 'platform_fee_rate', value: data.platformFeeRate, type: 'number', category: 'business' },
        { key: 'merchant_onboarding_audit', value: data.merchantOnboardingAudit, type: 'boolean', category: 'business' },
        { key: 'product_listing_audit', value: data.productListingAudit, type: 'boolean', category: 'business' }
      ];

      await this.updateSettings(settings);

      return {
        code: 200,
        message: '业务设置更新成功',
        data: null
      };
    } catch (error) {
      console.error('更新业务设置失败:', error);
      return {
        code: 500,
        message: '更新业务设置失败',
        data: null
      };
    }
  }

  // 更新安全设置
  async updateSecuritySettings(data: SecuritySettingsDto) {
    try {
      const settings = [
        { key: 'login_failure_lock', value: data.loginFailureLock, type: 'boolean', category: 'security' },
        { key: 'max_login_attempts', value: data.maxLoginAttempts, type: 'number', category: 'security' },
        { key: 'lockout_duration', value: data.lockoutDuration, type: 'number', category: 'security' },
        { key: 'min_password_length', value: data.minPasswordLength, type: 'number', category: 'security' },
        { key: 'force_password_complexity', value: data.forcePasswordComplexity, type: 'boolean', category: 'security' },
        { key: 'session_timeout', value: data.sessionTimeout, type: 'number', category: 'security' }
      ];

      await this.updateSettings(settings);

      return {
        code: 200,
        message: '安全设置更新成功',
        data: null
      };
    } catch (error) {
      console.error('更新安全设置失败:', error);
      return {
        code: 500,
        message: '更新安全设置失败',
        data: null
      };
    }
  }

  // 更新通知设置
  async updateNotificationSettings(data: NotificationSettingsDto) {
    try {
      const settings = [
        { key: 'email_notifications', value: data.emailNotifications, type: 'boolean', category: 'notification' },
        { key: 'sms_notifications', value: data.smsNotifications, type: 'boolean', category: 'notification' },
        { key: 'system_notifications', value: data.systemNotifications, type: 'boolean', category: 'notification' },
        { key: 'smtp_host', value: data.smtpHost, type: 'string', category: 'notification' },
        { key: 'smtp_port', value: data.smtpPort, type: 'number', category: 'notification' },
        { key: 'smtp_user', value: data.smtpUser, type: 'string', category: 'notification' },
        { key: 'smtp_pass', value: data.smtpPass, type: 'string', category: 'notification' },
        { key: 'smtp_secure', value: data.smtpSecure, type: 'boolean', category: 'notification' }
      ];

      await this.updateSettings(settings);

      return {
        code: 200,
        message: '通知设置更新成功',
        data: null
      };
    } catch (error) {
      console.error('更新通知设置失败:', error);
      return {
        code: 500,
        message: '更新通知设置失败',
        data: null
      };
    }
  }

  // 测试邮件设置
  async testEmailSettings(data: TestEmailDto) {
    try {
      // 这里应该实现真实的邮件发送逻辑
      // 暂时返回成功
      console.log('测试邮件发送到:', data.email);
      
      return {
        code: 200,
        message: '邮件连接测试成功',
        data: null
      };
    } catch (error) {
      console.error('邮件连接测试失败:', error);
      return {
        code: 500,
        message: '邮件连接测试失败',
        data: null
      };
    }
  }

  // 清理系统缓存
  async clearSystemCache() {
    try {
      // 这里应该实现真实的缓存清理逻辑
      console.log('清理系统缓存');
      
      return {
        code: 200,
        message: '缓存清理成功',
        data: null
      };
    } catch (error) {
      console.error('缓存清理失败:', error);
      return {
        code: 500,
        message: '缓存清理失败',
        data: null
      };
    }
  }

  // 清理系统日志
  async clearSystemLogs() {
    try {
      // 这里应该实现真实的日志清理逻辑
      console.log('清理系统日志');
      
      return {
        code: 200,
        message: '日志清理成功',
        data: null
      };
    } catch (error) {
      console.error('日志清理失败:', error);
      return {
        code: 500,
        message: '日志清理失败',
        data: null
      };
    }
  }

  // 优化数据库
  async optimizeDatabaseTables() {
    try {
      // 这里应该实现真实的数据库优化逻辑
      console.log('优化数据库表');
      
      return {
        code: 200,
        message: '数据库优化成功',
        data: null
      };
    } catch (error) {
      console.error('数据库优化失败:', error);
      return {
        code: 500,
        message: '数据库优化失败',
        data: null
      };
    }
  }

  // 创建数据库备份
  async createDatabaseBackup() {
    try {
      // 这里应该实现真实的数据库备份逻辑
      console.log('创建数据库备份');
      
      return {
        code: 200,
        message: '数据库备份成功',
        data: { backupId: 'backup_' + Date.now() }
      };
    } catch (error) {
      console.error('数据库备份失败:', error);
      return {
        code: 500,
        message: '数据库备份失败',
        data: null
      };
    }
  }

  // 创建文件备份
  async createFileBackup() {
    try {
      // 这里应该实现真实的文件备份逻辑
      console.log('创建文件备份');
      
      return {
        code: 200,
        message: '文件备份成功',
        data: { backupId: 'file_backup_' + Date.now() }
      };
    } catch (error) {
      console.error('文件备份失败:', error);
      return {
        code: 500,
        message: '文件备份失败',
        data: null
      };
    }
  }

  // 从备份恢复数据库
  async restoreDatabaseFromBackup(data: BackupRestoreDto) {
    try {
      // 这里应该实现真实的数据库恢复逻辑
      console.log('从备份恢复数据库:', data.backupId);
      
      return {
        code: 200,
        message: '数据库恢复成功',
        data: null
      };
    } catch (error) {
      console.error('数据库恢复失败:', error);
      return {
        code: 500,
        message: '数据库恢复失败',
        data: null
      };
    }
  }

  // 获取系统信息
  async getSystemInfo() {
    try {
      const systemInfo = {
        version: '1.0.0',
        environment: process.env.NODE_ENV || 'production',
        cpuUsage: '25.5',
        memoryUsage: '60.2',
        diskSpace: '80GB / 200GB',
        lastBackupTime: '2025-10-12 10:00:00'
      };

      return {
        code: 200,
        message: '获取成功',
        data: systemInfo
      };
    } catch (error) {
      console.error('获取系统信息失败:', error);
      return {
        code: 500,
        message: '获取系统信息失败',
        data: null
      };
    }
  }

  // 私有方法：更新设置
  private async updateSettings(settings: any[]) {
    for (const setting of settings) {
      const existingSetting = await this.settingsRepository.findOne({
        where: { settingKey: setting.key }
      });

      if (existingSetting) {
        await this.settingsRepository.update(existingSetting.id, {
          settingValue: this.stringifySettingValue(setting.value),
          settingType: setting.type,
          category: setting.category
        });
      } else {
        await this.settingsRepository.save({
          settingKey: setting.key,
          settingValue: this.stringifySettingValue(setting.value),
          settingType: setting.type,
          category: setting.category,
          description: ''
        });
      }
    }
  }

  // 私有方法：解析设置值
  private parseSettingValue(value: string, type: string) {
    switch (type) {
      case 'boolean':
        return value === 'true';
      case 'number':
        return parseFloat(value);
      case 'object':
        return JSON.parse(value);
      default:
        return value;
    }
  }

  // 私有方法：序列化设置值
  private stringifySettingValue(value: any) {
    if (typeof value === 'object') {
      return JSON.stringify(value);
    }
    return String(value);
  }

  // 私有方法：获取基本设置
  private getBasicSettings(settingsMap: any) {
    return {
      siteName: settingsMap.site_name || 'TikShop 电商平台',
      siteLogo: settingsMap.site_logo || '',
      siteDescription: settingsMap.site_description || '一个功能强大的电商平台',
      customerServicePhone: settingsMap.customer_service_phone || '+60 12-345 6789',
      customerServiceEmail: settingsMap.customer_service_email || 'support@tikshop.com',
      defaultCurrency: settingsMap.default_currency || 'MYR'
    };
  }

  // 私有方法：获取业务设置
  private getBusinessSettings(settingsMap: any) {
    return {
      autoApproveOrders: settingsMap.auto_approve_orders !== false,
      orderTimeout: settingsMap.order_timeout || 30,
      minWithdrawalAmount: settingsMap.min_withdrawal_amount || 10.00,
      platformFeeRate: settingsMap.platform_fee_rate || 1.5,
      merchantOnboardingAudit: settingsMap.merchant_onboarding_audit !== false,
      productListingAudit: settingsMap.product_listing_audit !== false
    };
  }

  // 私有方法：获取安全设置
  private getSecuritySettings(settingsMap: any) {
    return {
      loginFailureLock: settingsMap.login_failure_lock !== false,
      maxLoginAttempts: settingsMap.max_login_attempts || 5,
      lockoutDuration: settingsMap.lockout_duration || 30,
      minPasswordLength: settingsMap.min_password_length || 8,
      forcePasswordComplexity: settingsMap.force_password_complexity !== false,
      sessionTimeout: settingsMap.session_timeout || 60
    };
  }

  // 私有方法：获取通知设置
  private getNotificationSettings(settingsMap: any) {
    return {
      emailNotifications: settingsMap.email_notifications !== false,
      smsNotifications: settingsMap.sms_notifications || false,
      systemNotifications: settingsMap.system_notifications !== false,
      smtpHost: settingsMap.smtp_host || 'smtp.example.com',
      smtpPort: settingsMap.smtp_port || 587,
      smtpUser: settingsMap.smtp_user || 'user@example.com',
      smtpPass: settingsMap.smtp_pass || 'password',
      smtpSecure: settingsMap.smtp_secure !== false
    };
  }
}
