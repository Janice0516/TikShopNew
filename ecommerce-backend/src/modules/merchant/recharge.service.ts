import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, DataSource } from 'typeorm';
import { MerchantRecharge } from './entities/merchant-recharge.entity';
import { Merchant } from './entities/merchant.entity';
import { CreateRechargeDto, AuditRechargeDto, QueryRechargeDto } from './dto/recharge.dto';

@Injectable()
export class RechargeService {
  constructor(
    @InjectRepository(MerchantRecharge)
    private rechargeRepository: Repository<MerchantRecharge>,
    @InjectRepository(Merchant)
    private merchantRepository: Repository<Merchant>,
    private dataSource: DataSource,
  ) {}

  // 商家申请充值
  async createRecharge(merchantId: number, createDto: CreateRechargeDto) {
    const recharge = this.rechargeRepository.create({
      merchantId: String(merchantId),
      ...createDto,
      status: 0, // 待审核
    });
    
    const result = await this.rechargeRepository.save(recharge);
    return {
      code: 200,
      message: '充值申请提交成功',
      data: result,
    };
  }

  // 获取充值记录列表（商家）
  async getMerchantRecharges(merchantId: number, query: QueryRechargeDto) {
    const { page = 1, pageSize = 10, status, paymentMethod, startDate, endDate } = query;
    
    const queryBuilder = this.rechargeRepository.createQueryBuilder('recharge')
      .where('recharge.merchantId = :merchantId', { merchantId })
      .orderBy('recharge.createdAt', 'DESC');

    if (status !== undefined) {
      queryBuilder.andWhere('recharge.status = :status', { status });
    }

    if (paymentMethod) {
      queryBuilder.andWhere('recharge.paymentMethod = :paymentMethod', { paymentMethod });
    }

    if (startDate) {
      queryBuilder.andWhere('DATE(recharge.createdAt) >= :startDate', { startDate });
    }

    if (endDate) {
      queryBuilder.andWhere('DATE(recharge.createdAt) <= :endDate', { endDate });
    }

    const [list, total] = await queryBuilder
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    return {
      code: 200,
      message: '获取成功',
      data: { list, total, page, pageSize },
    };
  }

  // 获取充值记录列表（管理员）
  async getRechargeList(query: QueryRechargeDto) {
    const { 
      page = 1, 
      pageSize = 10, 
      merchantId, 
      status, 
      paymentMethod, 
      startDate, 
      endDate 
    } = query;
    
    const queryBuilder = this.rechargeRepository.createQueryBuilder('recharge')
      .leftJoinAndSelect('recharge.merchant', 'merchant')
      .orderBy('recharge.createdAt', 'DESC');

    if (merchantId) {
      queryBuilder.andWhere('recharge.merchantId = :merchantId', { merchantId });
    }

    if (status !== undefined) {
      queryBuilder.andWhere('recharge.status = :status', { status });
    }

    if (paymentMethod) {
      queryBuilder.andWhere('recharge.paymentMethod = :paymentMethod', { paymentMethod });
    }

    if (startDate) {
      queryBuilder.andWhere('DATE(recharge.createdAt) >= :startDate', { startDate });
    }

    if (endDate) {
      queryBuilder.andWhere('DATE(recharge.createdAt) <= :endDate', { endDate });
    }

    const [list, total] = await queryBuilder
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    return {
      code: 200,
      message: '获取成功',
      data: { list, total, page, pageSize },
    };
  }

  // 获取充值详情
  async getRechargeDetail(id: number) {
    const recharge = await this.rechargeRepository.findOne({
      where: { id: String(id) },
      relations: ['merchant'],
    });

    if (!recharge) {
      throw new NotFoundException('充值记录不存在');
    }

    return {
      code: 200,
      message: '获取成功',
      data: recharge,
    };
  }

  // 审核充值
  async auditRecharge(adminId: number, adminName: string, auditDto: AuditRechargeDto) {
    const { id, status, auditReason } = auditDto;

    const recharge = await this.rechargeRepository.findOne({
      where: { id: String(id) },
      relations: ['merchant'],
    });

    if (!recharge) {
      throw new NotFoundException('充值记录不存在');
    }

    if (recharge.status !== 0) {
      throw new BadRequestException('该充值记录已审核');
    }

    const queryRunner = this.dataSource.createQueryRunner();
    await queryRunner.connect();
    await queryRunner.startTransaction();

    try {
      // 更新充值记录
      recharge.status = status;
      recharge.adminId = String(adminId);
      recharge.adminName = adminName;
      recharge.auditReason = auditReason;
      recharge.auditTime = new Date();

      await queryRunner.manager.save(recharge);

      // 如果审核通过，更新商户余额
      if (status === 1) {
        const merchant = await queryRunner.manager.findOne(Merchant, {
          where: { id: String(recharge.merchantId) },
        });

        if (merchant) {
          merchant.balance = parseFloat((merchant.balance + recharge.amount).toFixed(2));
          await queryRunner.manager.save(merchant);
        }
      }

      await queryRunner.commitTransaction();

      return {
        code: 200,
        message: status === 1 ? '审核通过' : '审核拒绝',
        data: recharge,
      };
    } catch (error) {
      await queryRunner.rollbackTransaction();
      throw error;
    } finally {
      await queryRunner.release();
    }
  }

  // 获取充值统计
  async getRechargeStats() {
    const totalCount = await this.rechargeRepository.count();
    const pendingCount = await this.rechargeRepository.count({ where: { status: 0 } });
    const approvedCount = await this.rechargeRepository.count({ where: { status: 1 } });
    const rejectedCount = await this.rechargeRepository.count({ where: { status: 2 } });

    const totalAmount = await this.rechargeRepository
      .createQueryBuilder('recharge')
      .select('SUM(recharge.amount)', 'total')
      .where('recharge.status = :status', { status: 1 })
      .getRawOne();

    return {
      code: 200,
      message: '获取成功',
      data: {
        totalCount,
        pendingCount,
        approvedCount,
        rejectedCount,
        totalAmount: totalAmount?.total || 0,
      },
    };
  }
}
