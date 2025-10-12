import request from '@/utils/request'

// 获取系统设置
export function getSystemSettings() {
  return request({
    url: '/admin/settings',
    method: 'get'
  })
}

// 更新基本设置
export function updateBasicSettings(data: any) {
  return request({
    url: '/admin/settings/basic',
    method: 'put',
    data
  })
}

// 更新业务设置
export function updateBusinessSettings(data: any) {
  return request({
    url: '/admin/settings/business',
    method: 'put',
    data
  })
}

// 更新安全设置
export function updateSecuritySettings(data: any) {
  return request({
    url: '/admin/settings/security',
    method: 'put',
    data
  })
}

// 更新通知设置
export function updateNotificationSettings(data: any) {
  return request({
    url: '/admin/settings/notification',
    method: 'put',
    data
  })
}

// 测试邮件连接
export function testEmailSettings(data: any) {
  return request({
    url: '/admin/settings/test-email',
    method: 'post',
    data
  })
}

// 清理系统缓存
export function clearSystemCache() {
  return request({
    url: '/admin/maintenance/clear-cache',
    method: 'post'
  })
}

// 清理系统日志
export function clearSystemLogs() {
  return request({
    url: '/admin/maintenance/clear-logs',
    method: 'post'
  })
}

// 优化数据库
export function optimizeDatabaseTables() {
  return request({
    url: '/admin/maintenance/optimize-database',
    method: 'post'
  })
}

// 备份数据库
export function createDatabaseBackup() {
  return request({
    url: '/admin/maintenance/backup-database',
    method: 'post'
  })
}

// 备份文件
export function createFileBackup() {
  return request({
    url: '/admin/maintenance/backup-files',
    method: 'post'
  })
}

// 恢复数据库
export function restoreDatabaseFromBackup(data: any) {
  return request({
    url: '/admin/maintenance/restore-database',
    method: 'post',
    data
  })
}

// 获取系统信息
export function getSystemInfo() {
  return request({
    url: '/admin/system/info',
    method: 'get'
  })
}
