import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Merchant } from '../merchant/entities/merchant.entity';

@Injectable()
export class BalanceService {
  constructor(
    @InjectRepository(Merchant)
    private merchantRepository: Repository<Merchant>,
  ) {}

  /**
   * 获取商户余额信息
   */
  async getMerchantBalance(merchantId: number) {
    const merchant = await this.merchantRepository.findOne({
      where: { id: String(merchantId) },
      select: ['id', 'merchantName', 'balance', 'frozenAmount'],
    });

    if (!merchant) {
      throw new Error('商户不存在');
    }

    // 这里可以添加更复杂的余额计算逻辑
    const availableBalance = (merchant.balance || 0) - (merchant.frozenAmount || 0);
    const frozenBalance = merchant.frozenAmount || 0;
    const totalBalance = merchant.balance || 0;

    return {
      code: 200,
      message: '获取成功',
      data: {
        merchantId: merchant.id,
        merchantName: merchant.merchantName,
        availableBalance: Math.max(0, availableBalance), // 可提现余额
        frozenBalance, // 冻结余额
        totalBalance, // 总余额
        minWithdrawalAmount: 100, // 最小提现金额
        maxWithdrawalAmount: 50000, // 最大提现金额
      },
    };
  }

  /**
   * 更新商户余额（内部方法，用于订单结算等）
   */
  async updateMerchantBalance(merchantId: number, amount: number, type: 'add' | 'subtract' | 'freeze' | 'unfreeze') {
    const merchant = await this.merchantRepository.findOne({
      where: { id: String(merchantId) },
    });

    if (!merchant) {
      throw new Error('商户不存在');
    }

    switch (type) {
      case 'add':
        merchant.balance = (merchant.balance || 0) + amount;
        break;
      case 'subtract':
        merchant.balance = Math.max(0, (merchant.balance || 0) - amount);
        break;
      case 'freeze':
        merchant.frozenAmount = (merchant.frozenAmount || 0) + amount;
        break;
      case 'unfreeze':
        merchant.frozenAmount = Math.max(0, (merchant.frozenAmount || 0) - amount);
        break;
    }

    await this.merchantRepository.save(merchant);
    return merchant;
  }
}
