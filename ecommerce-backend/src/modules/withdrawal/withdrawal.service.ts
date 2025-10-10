import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { MerchantWithdrawal } from './entities/merchant-withdrawal.entity';
import { CreateWithdrawalDto, UpdateWithdrawalStatusDto } from './dto/withdrawal.dto';

@Injectable()
export class WithdrawalService {
  constructor(
    @InjectRepository(MerchantWithdrawal)
    private withdrawalRepository: Repository<MerchantWithdrawal>,
  ) {}

  /**
   * 创建提现申请
   */
  async createWithdrawal(merchantId: number, createWithdrawalDto: CreateWithdrawalDto) {
    const { withdrawalAmount, bankName, bankAccount, accountHolder, remark } = createWithdrawalDto;

    // 检查是否有待审核的提现申请
    const pendingWithdrawal = await this.withdrawalRepository.findOne({
      where: { merchantId, status: 0 },
    });

    if (pendingWithdrawal) {
      throw new BadRequestException('您有未处理的提现申请，请等待审核完成后再申请');
    }

    // 检查提现金额（这里可以添加更多业务逻辑，比如检查商户余额）
    if (withdrawalAmount < 100) {
      throw new BadRequestException('提现金额不能少于100元');
    }

    if (withdrawalAmount > 50000) {
      throw new BadRequestException('单次提现金额不能超过50000元');
    }

    // 验证银行账号格式
    if (!/^[0-9]{16,25}$/.test(bankAccount)) {
      throw new BadRequestException('银行账号格式不正确，请输入16-25位数字');
    }

    // 验证账户持有人姓名
    if (!/^[\u4e00-\u9fa5a-zA-Z\s]{2,20}$/.test(accountHolder)) {
      throw new BadRequestException('账户持有人姓名格式不正确');
    }

    const withdrawal = this.withdrawalRepository.create({
      merchantId,
      withdrawalAmount,
      bankName,
      bankAccount,
      accountHolder,
      remark: remark || '',
      status: 0, // 待审核
    });

    const savedWithdrawal = await this.withdrawalRepository.save(withdrawal);
    return {
      code: 200,
      message: '提现申请提交成功',
      data: savedWithdrawal,
    };
  }

  /**
   * 获取提现列表（管理员）
   */
  async getWithdrawalList(params: any) {
    const { page = 1, pageSize = 10, status, merchantId, startDate, endDate } = params;
    
    const queryBuilder = this.withdrawalRepository
      .createQueryBuilder('withdrawal')
      .leftJoinAndSelect('withdrawal.merchant', 'merchant')
      .orderBy('withdrawal.createTime', 'DESC');

    // 状态筛选
    if (status !== undefined && status !== '') {
      queryBuilder.andWhere('withdrawal.status = :status', { status });
    }

    // 商户筛选
    if (merchantId) {
      queryBuilder.andWhere('withdrawal.merchantId = :merchantId', { merchantId });
    }

    // 日期筛选
    if (startDate) {
      queryBuilder.andWhere('withdrawal.createTime >= :startDate', { startDate });
    }
    if (endDate) {
      queryBuilder.andWhere('withdrawal.createTime <= :endDate', { endDate });
    }

    const [withdrawals, total] = await queryBuilder
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    return {
      code: 200,
      message: '获取成功',
      data: {
        list: withdrawals,
        total,
        page,
        pageSize,
      },
    };
  }

  /**
   * 获取提现详情
   */
  async getWithdrawalDetail(id: number) {
    const withdrawal = await this.withdrawalRepository.findOne({
      where: { id },
      relations: ['merchant'],
    });

    if (!withdrawal) {
      throw new NotFoundException('提现记录不存在');
    }

    return {
      code: 200,
      message: '获取成功',
      data: withdrawal,
    };
  }

  /**
   * 更新提现状态（管理员）
   */
  async updateWithdrawalStatus(id: number, updateStatusDto: UpdateWithdrawalStatusDto, adminId: number) {
    const { status, adminRemark } = updateStatusDto;

    const withdrawal = await this.withdrawalRepository.findOne({
      where: { id },
    });

    if (!withdrawal) {
      throw new NotFoundException('提现记录不存在');
    }

    if (withdrawal.status !== 0) {
      throw new BadRequestException('该提现申请已处理，无法修改状态');
    }

    withdrawal.status = status;
    withdrawal.adminRemark = adminRemark || '';
    withdrawal.processedBy = adminId;
    withdrawal.processedAt = new Date();

    await this.withdrawalRepository.save(withdrawal);

    return {
      code: 200,
      message: '状态更新成功',
      data: withdrawal,
    };
  }

  /**
   * 获取商户的提现记录
   */
  async getMerchantWithdrawals(merchantId: number, params: any) {
    const { page = 1, pageSize = 10, status } = params;
    
    const queryBuilder = this.withdrawalRepository
      .createQueryBuilder('withdrawal')
      .where('withdrawal.merchantId = :merchantId', { merchantId })
      .orderBy('withdrawal.createTime', 'DESC');

    if (status !== undefined && status !== '') {
      queryBuilder.andWhere('withdrawal.status = :status', { status });
    }

    const [withdrawals, total] = await queryBuilder
      .skip((page - 1) * pageSize)
      .take(pageSize)
      .getManyAndCount();

    return {
      code: 200,
      message: '获取成功',
      data: {
        list: withdrawals,
        total,
        page,
        pageSize,
      },
    };
  }
}
