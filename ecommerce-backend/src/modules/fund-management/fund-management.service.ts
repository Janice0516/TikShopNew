import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { FundOperation } from './entities/fund-operation.entity';
import { Merchant } from '../merchant/entities/merchant.entity';
import { 
  IncreaseFundDto,
  FreezeFundDto, 
  UnfreezeFundDto, 
  DeductFundDto, 
  RefundFundDto, 
  QueryFundOperationDto,
  MerchantFundInfoDto 
} from './dto/fund-management.dto';

@Injectable()
export class FundManagementService {
  constructor(
    @InjectRepository(FundOperation)
    private fundOperationRepository: Repository<FundOperation>,
    @InjectRepository(Merchant)
    private merchantRepository: Repository<Merchant>,
  ) {}

  /**
   * 增加商户资金
   */
  async increaseFund(adminId: number, adminName: string, increaseFundDto: IncreaseFundDto) {
    const { merchantId, amount, reason, remark } = increaseFundDto;

    const merchant = await this.merchantRepository.findOne({ where: { id: String(merchantId) } });
    if (!merchant) {
      throw new NotFoundException('商户不存在');
    }

    // 更新商户余额
    const beforeBalance = merchant.balance;
    const afterBalance = parseFloat((beforeBalance + amount).toFixed(2));
    merchant.balance = afterBalance;

    await this.merchantRepository.save(merchant);

    // 记录资金操作
    const fundOperation = this.fundOperationRepository.create({
      merchantId,
      operationType: 1, // 1表示充值/增加资金
      amount,
      balanceBefore: beforeBalance,
      balanceAfter: afterBalance,
      frozenBefore: merchant.frozenAmount,
      frozenAfter: merchant.frozenAmount,
      adminId,
      adminName,
      reason,
      remark,
    });

    await this.fundOperationRepository.save(fundOperation);

    return {
      code: 200,
      message: '资金增加成功',
      data: fundOperation,
    };
  }

  /**
   * 冻结商户资金
   */
  async freezeFund(adminId: number, adminName: string, freezeFundDto: FreezeFundDto) {
    const { merchantId, amount, reason, remark } = freezeFundDto;

    const merchant = await this.merchantRepository.findOne({ where: { id: String(merchantId) } });
    if (!merchant) {
      throw new NotFoundException('商户不存在');
    }

    if (amount > merchant.balance) {
      throw new BadRequestException('冻结金额不能超过可用余额');
    }

    if (amount <= 0) {
      throw new BadRequestException('冻结金额必须大于0');
    }

    const balanceBefore = merchant.balance;
    const frozenBefore = merchant.frozenAmount;
    const balanceAfter = balanceBefore - amount;
    const frozenAfter = frozenBefore + amount;

    // 更新商户资金
    await this.merchantRepository.update(merchantId, {
      balance: balanceAfter,
      frozenAmount: frozenAfter,
    });

    // 记录操作日志
    const operation = this.fundOperationRepository.create({
      merchantId,
      operationType: 3, // 冻结
      amount,
      balanceBefore,
      balanceAfter,
      frozenBefore,
      frozenAfter,
      adminId,
      adminName,
      reason,
      remark,
    });

    await this.fundOperationRepository.save(operation);

    return {
      code: 200,
      message: '资金冻结成功',
      data: {
        merchantId,
        amount,
        balanceAfter,
        frozenAfter,
      },
    };
  }

  /**
   * 解冻商户资金
   */
  async unfreezeFund(adminId: number, adminName: string, unfreezeFundDto: UnfreezeFundDto) {
    const { merchantId, amount, reason, remark } = unfreezeFundDto;

    const merchant = await this.merchantRepository.findOne({ where: { id: String(merchantId) } });
    if (!merchant) {
      throw new NotFoundException('商户不存在');
    }

    if (amount > merchant.frozenAmount) {
      throw new BadRequestException('解冻金额不能超过冻结金额');
    }

    if (amount <= 0) {
      throw new BadRequestException('解冻金额必须大于0');
    }

    const balanceBefore = merchant.balance;
    const frozenBefore = merchant.frozenAmount;
    const balanceAfter = balanceBefore + amount;
    const frozenAfter = frozenBefore - amount;

    // 更新商户资金
    await this.merchantRepository.update(merchantId, {
      balance: balanceAfter,
      frozenAmount: frozenAfter,
    });

    // 记录操作日志
    const operation = this.fundOperationRepository.create({
      merchantId,
      operationType: 4, // 解冻
      amount,
      balanceBefore,
      balanceAfter,
      frozenBefore,
      frozenAfter,
      adminId,
      adminName,
      reason,
      remark,
    });

    await this.fundOperationRepository.save(operation);

    return {
      code: 200,
      message: '资金解冻成功',
      data: {
        merchantId,
        amount,
        balanceAfter,
        frozenAfter,
      },
    };
  }

  /**
   * 扣除商户资金
   */
  async deductFund(adminId: number, adminName: string, deductFundDto: DeductFundDto) {
    const { merchantId, amount, reason, remark } = deductFundDto;

    const merchant = await this.merchantRepository.findOne({ where: { id: String(merchantId) } });
    if (!merchant) {
      throw new NotFoundException('商户不存在');
    }

    if (amount > merchant.balance) {
      throw new BadRequestException('扣款金额不能超过可用余额');
    }

    if (amount <= 0) {
      throw new BadRequestException('扣款金额必须大于0');
    }

    const balanceBefore = merchant.balance;
    const frozenBefore = merchant.frozenAmount;
    const balanceAfter = balanceBefore - amount;
    const frozenAfter = frozenBefore;

    // 更新商户资金
    await this.merchantRepository.update(merchantId, {
      balance: balanceAfter,
    });

    // 记录操作日志
    const operation = this.fundOperationRepository.create({
      merchantId,
      operationType: 5, // 扣款
      amount,
      balanceBefore,
      balanceAfter,
      frozenBefore,
      frozenAfter,
      adminId,
      adminName,
      reason,
      remark,
    });

    await this.fundOperationRepository.save(operation);

    return {
      code: 200,
      message: '资金扣除成功',
      data: {
        merchantId,
        amount,
        balanceAfter,
        frozenAfter,
      },
    };
  }

  /**
   * 退还商户资金
   */
  async refundFund(adminId: number, adminName: string, refundFundDto: RefundFundDto) {
    const { merchantId, amount, reason, orderId, remark } = refundFundDto;

    const merchant = await this.merchantRepository.findOne({ where: { id: String(merchantId) } });
    if (!merchant) {
      throw new NotFoundException('商户不存在');
    }

    if (amount <= 0) {
      throw new BadRequestException('退款金额必须大于0');
    }

    const balanceBefore = merchant.balance;
    const frozenBefore = merchant.frozenAmount;
    const balanceAfter = balanceBefore + amount;
    const frozenAfter = frozenBefore;

    // 更新商户资金
    await this.merchantRepository.update(merchantId, {
      balance: balanceAfter,
    });

    // 记录操作日志
    const operation = this.fundOperationRepository.create({
      merchantId,
      operationType: 6, // 退款
      amount,
      balanceBefore,
      balanceAfter,
      frozenBefore,
      frozenAfter,
      adminId,
      adminName,
      reason,
      remark,
      orderId,
    });

    await this.fundOperationRepository.save(operation);

    return {
      code: 200,
      message: '资金退还成功',
      data: {
        merchantId,
        amount,
        balanceAfter,
        frozenAfter,
      },
    };
  }

  /**
   * 获取商户资金信息
   */
  async getMerchantFundInfo(merchantId: number): Promise<MerchantFundInfoDto> {
    const merchant = await this.merchantRepository.findOne({ 
      where: { id: String(merchantId) },
      select: ['id', 'merchantName', 'balance', 'frozenAmount', 'totalIncome', 'totalWithdraw']
    });

    if (!merchant) {
      throw new NotFoundException('商户不存在');
    }

    return {
      merchantId: Number(merchant.id),
      merchantName: merchant.merchantName,
      availableBalance: parseFloat((merchant.balance - merchant.frozenAmount).toFixed(2)),
      frozenAmount: parseFloat(merchant.frozenAmount.toFixed(2)),
      totalBalance: parseFloat(merchant.balance.toFixed(2)),
      totalIncome: parseFloat(merchant.totalIncome.toFixed(2)),
      totalWithdraw: parseFloat(merchant.totalWithdraw.toFixed(2)),
    };
  }

  /**
   * 获取资金操作记录
   */
  async getFundOperationList(query: QueryFundOperationDto) {
    const { page = 1, pageSize = 10, merchantId, operationType, adminId, startDate, endDate } = query;

    const queryBuilder = this.fundOperationRepository
      .createQueryBuilder('operation')
      .leftJoinAndSelect('operation.merchant', 'merchant')
      .orderBy('operation.createTime', 'DESC');

    if (merchantId) {
      queryBuilder.andWhere('operation.merchantId = :merchantId', { merchantId });
    }
    if (operationType) {
      queryBuilder.andWhere('operation.operationType = :operationType', { operationType });
    }
    if (adminId) {
      queryBuilder.andWhere('operation.adminId = :adminId', { adminId });
    }
    if (startDate) {
      queryBuilder.andWhere('operation.createTime >= :startDate', { startDate });
    }
    if (endDate) {
      queryBuilder.andWhere('operation.createTime <= :endDate', { endDate });
    }

    const [list, total] = await queryBuilder
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    return {
      code: 200,
      message: '获取成功',
      data: {
        list,
        total,
        page,
        pageSize,
      },
    };
  }

  /**
   * 获取操作类型名称
   */
  getOperationTypeName(type: number): string {
    const typeMap: Record<number, string> = {
      1: '充值',
      2: '提现',
      3: '冻结',
      4: '解冻',
      5: '扣款',
      6: '退款',
    };
    return typeMap[type] || '未知';
  }
}
