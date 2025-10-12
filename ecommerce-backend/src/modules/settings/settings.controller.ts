import {
  Controller,
  Get,
  Post,
  Put,
  Body,
  UseGuards,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { SettingsService } from './settings.service';
import {
  BasicSettingsDto,
  BusinessSettingsDto,
  SecuritySettingsDto,
  NotificationSettingsDto,
  TestEmailDto,
  BackupRestoreDto
} from './dto/settings.dto';

@ApiTags('系统设置')
@Controller('settings')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class SettingsController {
  constructor(private readonly settingsService: SettingsService) {}

  @Get()
  @ApiOperation({ summary: '获取所有系统设置' })
  async getSystemSettings() {
    return this.settingsService.getSystemSettings();
  }

  @Put('basic')
  @ApiOperation({ summary: '更新基本设置' })
  async updateBasicSettings(@Body() data: BasicSettingsDto) {
    return this.settingsService.updateBasicSettings(data);
  }

  @Put('business')
  @ApiOperation({ summary: '更新业务设置' })
  async updateBusinessSettings(@Body() data: BusinessSettingsDto) {
    return this.settingsService.updateBusinessSettings(data);
  }

  @Put('security')
  @ApiOperation({ summary: '更新安全设置' })
  async updateSecuritySettings(@Body() data: SecuritySettingsDto) {
    return this.settingsService.updateSecuritySettings(data);
  }

  @Put('notification')
  @ApiOperation({ summary: '更新通知设置' })
  async updateNotificationSettings(@Body() data: NotificationSettingsDto) {
    return this.settingsService.updateNotificationSettings(data);
  }

  @Post('notification/test-email')
  @ApiOperation({ summary: '测试邮件设置' })
  async testEmailSettings(@Body() data: TestEmailDto) {
    return this.settingsService.testEmailSettings(data);
  }

  @Post('maintenance/clear-cache')
  @ApiOperation({ summary: '清理系统缓存' })
  async clearSystemCache() {
    return this.settingsService.clearSystemCache();
  }

  @Post('maintenance/clear-logs')
  @ApiOperation({ summary: '清理系统日志' })
  async clearSystemLogs() {
    return this.settingsService.clearSystemLogs();
  }

  @Post('maintenance/optimize-database')
  @ApiOperation({ summary: '优化数据库' })
  async optimizeDatabaseTables() {
    return this.settingsService.optimizeDatabaseTables();
  }

  @Post('maintenance/backup-database')
  @ApiOperation({ summary: '备份数据库' })
  async createDatabaseBackup() {
    return this.settingsService.createDatabaseBackup();
  }

  @Post('maintenance/backup-files')
  @ApiOperation({ summary: '备份文件' })
  async createFileBackup() {
    return this.settingsService.createFileBackup();
  }

  @Post('maintenance/restore-database')
  @ApiOperation({ summary: '从备份恢复数据库' })
  async restoreDatabaseFromBackup(@Body() data: BackupRestoreDto) {
    return this.settingsService.restoreDatabaseFromBackup(data);
  }

  @Get('system-info')
  @ApiOperation({ summary: '获取系统信息' })
  async getSystemInfo() {
    return this.settingsService.getSystemInfo();
  }
}
