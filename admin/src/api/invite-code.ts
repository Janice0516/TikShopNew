import request from '@/utils/request'

// 邀请码接口类型定义
export interface CreateInviteCodeData {
  salespersonName: string
  salespersonPhone?: string
  salespersonId?: string
  maxUsage?: number
  expireTime?: string
  remark?: string
}

export interface InviteCodeQuery {
  inviteCode?: string
  salespersonName?: string
  status?: number
  page?: number
  limit?: number
}

export interface InviteCodeStats {
  total: number
  active: number
  disabled: number
  totalUsed: number
}

export interface InviteCodeItem {
  id: string
  inviteCode: string
  salespersonName: string
  salespersonPhone?: string
  salespersonId?: string
  usedCount: number
  maxUsage: number
  status: number
  expireTime?: string
  remark?: string
  createTime: string
  updateTime: string
}

export interface InviteCodeListResponse {
  items: InviteCodeItem[]
  total: number
  page: number
  limit: number
  totalPages: number
}

// 创建邀请码
export function createInviteCode(data: CreateInviteCodeData) {
  return request({
    url: '/invite-code',
    method: 'post',
    data
  })
}

// 获取邀请码列表
export function getInviteCodeList(params: InviteCodeQuery) {
  return request({
    url: '/invite-code',
    method: 'get',
    params
  })
}

// 获取邀请码统计
export function getInviteCodeStats() {
  return request({
    url: '/invite-code/stats',
    method: 'get'
  })
}

// 获取邀请码详情
export function getInviteCodeById(id: string) {
  return request({
    url: `/invite-code/${id}`,
    method: 'get'
  })
}

// 更新邀请码状态
export function updateInviteCodeStatus(id: string, status: number) {
  return request({
    url: `/invite-code/${id}/status`,
    method: 'put',
    data: { status }
  })
}

// 删除邀请码
export function deleteInviteCode(id: string) {
  return request({
    url: `/invite-code/${id}`,
    method: 'delete'
  })
}

// 验证邀请码
export function validateInviteCode(inviteCode: string) {
  return request({
    url: '/invite-code/validate',
    method: 'post',
    data: { inviteCode }
  })
}
