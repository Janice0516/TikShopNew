import {
  Controller,
  Get,
  Post,
  Body,
  Param,
  Query,
  UseGuards,
  Request,
  Patch,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('管理员充值审核')
@Controller('admin/recharge-audit')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class AdminRechargeAuditController {
  constructor() {}

  @Get()
  @ApiOperation({ summary: '获取充值审核列表（管理员）' })
  async findAll(@Query() query: any) {
    try {
      const { page = 1, pageSize = 10, status, merchantId, startDate, endDate } = query;
      
      // 这里应该连接数据库查询充值记录
      // 暂时返回模拟数据
      const mockData = {
        list: [
          {
            id: 1,
            merchantId: 1,
            merchantName: '测试商家',
            rechargeAmount: 1000.00,
            paymentMethod: '银行转账',
            paymentReference: 'REF001',
            status: 0,
            adminRemark: null,
            processedBy: null,
            processedAt: null,
            createTime: new Date().toISOString(),
            updateTime: new Date().toISOString(),
          },
          {
            id: 2,
            merchantId: 2,
            merchantName: '精品服饰',
            rechargeAmount: 2000.00,
            paymentMethod: '支付宝',
            paymentReference: 'REF002',
            status: 1,
            adminRemark: '审核通过',
            processedBy: 1,
            processedAt: new Date().toISOString(),
            createTime: new Date().toISOString(),
            updateTime: new Date().toISOString(),
          }
        ],
        total: 2,
        page: parseInt(page),
        pageSize: parseInt(pageSize),
        totalPages: 1,
      };

      return {
        code: 200,
        message: '获取成功',
        data: mockData,
      };
    } catch (error) {
      console.error('获取充值审核列表失败:', error);
      return {
        code: 500,
        message: '获取充值审核列表失败',
        data: null,
      };
    }
  }

  @Get('count')
  @ApiOperation({ summary: '统计充值审核数量（管理员）' })
  async countRecharges() {
    try {
      // 模拟统计数据
      const mockStats = {
        totalRecharges: 10,
        pendingRecharges: 3,
        approvedRecharges: 5,
        rejectedRecharges: 2,
        totalAmount: 50000.00,
        pendingAmount: 15000.00,
      };

      return {
        code: 200,
        message: '获取成功',
        data: mockStats,
      };
    } catch (error) {
      console.error('统计充值审核数量失败:', error);
      return {
        code: 500,
        message: '统计失败',
        data: null,
      };
    }
  }

  @Get(':id')
  @ApiOperation({ summary: '获取充值审核详情（管理员）' })
  async findOne(@Param('id') id: string) {
    try {
      // 模拟数据
      const mockRecharge = {
        id: parseInt(id),
        merchantId: 1,
        merchantName: '测试商家',
        rechargeAmount: 1000.00,
        paymentMethod: '银行转账',
        paymentReference: 'REF001',
        status: 0,
        adminRemark: null,
        processedBy: null,
        processedAt: null,
        createTime: new Date().toISOString(),
        updateTime: new Date().toISOString(),
      };

      return {
        code: 200,
        message: '获取成功',
        data: mockRecharge,
      };
    } catch (error) {
      console.error('获取充值审核详情失败:', error);
      return {
        code: 500,
        message: '获取充值审核详情失败',
        data: null,
      };
    }
  }

  @Patch(':id/approve')
  @ApiOperation({ summary: '审核通过充值（管理员）' })
  async approve(@Param('id') id: string, @Body('adminRemark') adminRemark: string, @Request() req) {
    try {
      const adminId = req.user.adminId;
      
      // 这里应该更新数据库记录
      console.log(`管理员 ${adminId} 审核通过充值 ${id}，备注：${adminRemark}`);

      return {
        code: 200,
        message: '审核通过成功',
        data: null,
      };
    } catch (error) {
      console.error('审核通过失败:', error);
      return {
        code: 500,
        message: '审核通过失败',
        data: null,
      };
    }
  }

  @Patch(':id/reject')
  @ApiOperation({ summary: '审核拒绝充值（管理员）' })
  async reject(@Param('id') id: string, @Body('adminRemark') adminRemark: string, @Request() req) {
    try {
      const adminId = req.user.adminId;
      
      // 这里应该更新数据库记录
      console.log(`管理员 ${adminId} 审核拒绝充值 ${id}，备注：${adminRemark}`);

      return {
        code: 200,
        message: '审核拒绝成功',
        data: null,
      };
    } catch (error) {
      console.error('审核拒绝失败:', error);
      return {
        code: 500,
        message: '审核拒绝失败',
        data: null,
      };
    }
  }
}
