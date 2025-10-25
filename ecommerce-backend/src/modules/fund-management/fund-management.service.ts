import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, DataSource } from 'typeorm';
import { Merchant } from '../merchant/entities/merchant.entity';
import { Order } from '../order/entities/order.entity';
import { Product } from '../product/entities/product.entity';
import { FundFreezeRecord } from './entities/fund-freeze-record.entity';
import { FundTransaction } from './entities/fund-transaction.entity';

@Injectable()
export class FundManagementService {
  constructor(
    @InjectRepository(Merchant)
    private merchantRepository: Repository<Merchant>,
    @InjectRepository(Order)
    private orderRepository: Repository<Order>,
    @InjectRepository(Product)
    private productRepository: Repository<Product>,
    @InjectRepository(FundFreezeRecord)
    private fundFreezeRepository: Repository<FundFreezeRecord>,
    @InjectRepository(FundTransaction)
    private fundTransactionRepository: Repository<FundTransaction>,
    private dataSource: DataSource,
  ) {}

  /**
   * 订单创建时冻结资金
   */
  async freezeFundsOnOrder(orderId: string): Promise<void> {
    const queryRunner = this.dataSource.createQueryRunner();
    await queryRunner.connect();
    await queryRunner.startTransaction();

    try {
      // 0. 幂等性：若已存在未解冻记录，则直接返回
      const existingFreeze = await queryRunner.manager.query(
        'SELECT id FROM fund_freeze_record WHERE order_id = $1 AND freeze_status = 1',
        [orderId]
      );
      if (existingFreeze.length > 0) {
        await queryRunner.rollbackTransaction();
        return; // 已冻结则不重复扣款、不重复记录
      }

      // 1. 获取订单信息
      const order = await queryRunner.manager.findOne(Order, {
        where: { id: orderId },
      });

      if (!order) {
        throw new Error('订单不存在');
      }

      // 2. 获取订单商品信息
      const orderItems = await queryRunner.manager.query(
        'SELECT * FROM order_item WHERE order_id = $1',
        [orderId]
      );

      // 3. 计算总成本价（平台提供商品的价格）
      let totalCostPrice = 0;
      for (const item of orderItems) {
        const productResult = await queryRunner.manager.query(
          'SELECT cost_price FROM platform_product WHERE id = $1',
          [item.product_id]
        );
        if (productResult.length > 0) {
          const costPrice = parseFloat(productResult[0].cost_price);
          totalCostPrice += costPrice * item.quantity;
        }
      }

      // 4. 更新订单成本价
      await queryRunner.manager.query(
        'UPDATE "order" SET cost_amount = $1 WHERE id = $2',
        [totalCostPrice, orderId]
      );

      // 5. 获取商家信息
      const merchant = await queryRunner.manager.findOne(Merchant, {
        where: { id: order.merchantId },
      });

      if (!merchant) {
        throw new Error('商家不存在');
      }

      // 6. 冻结资金（允许负数）
      const currentBalance = typeof merchant.balance === 'string' ? parseFloat(merchant.balance) : merchant.balance || 0;
      const currentFrozen = typeof merchant.frozenAmount === 'string' ? parseFloat(merchant.frozenAmount) : merchant.frozenAmount || 0;
      const newFrozen = currentFrozen + totalCostPrice;
      const newBalance = currentBalance - totalCostPrice; // 允许负数

      await queryRunner.manager.query(
        'UPDATE merchant SET balance = $1, frozen_amount = $2 WHERE id = $3',
        [newBalance.toString(), newFrozen.toString(), order.merchantId]
      );

      // 7. 创建冻结记录
      await queryRunner.manager.query(
        `INSERT INTO fund_freeze_record (merchant_id, order_id, freeze_amount, freeze_type, freeze_status, freeze_reason)
         VALUES ($1, $2, $3, $4, $5, $6)`,
        [order.merchantId, orderId, totalCostPrice, 1, 1, '订单成本价冻结']
      );

      // 8. 记录资金流水
      await queryRunner.manager.query(
        `INSERT INTO fund_transaction (merchant_id, order_id, transaction_type, amount, balance_before, balance_after, frozen_before, frozen_after, description, remark)
         VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)`,
        [
          order.merchantId, orderId, 1, totalCostPrice,
          currentBalance, newBalance, currentFrozen, newFrozen,
          `订单${orderId}成本价冻结`, `冻结金额: ${totalCostPrice}`
        ]
      );

      await queryRunner.commitTransaction();
      console.log(`✅ 订单${orderId}成本价冻结成功，冻结金额: ${totalCostPrice}`);
    } catch (error) {
      await queryRunner.rollbackTransaction();
      console.error('❌ 资金冻结失败:', error);
      throw error;
    } finally {
      await queryRunner.release();
    }
  }

  /**
   * 订单完成时解冻资金并结算佣金
   */
  async unfreezeFundsOnCompletion(orderId: string): Promise<void> {
    const queryRunner = this.dataSource.createQueryRunner();
    await queryRunner.connect();
    await queryRunner.startTransaction();

    try {
      // 0. 幂等性：若已解冻过，则直接返回
      const freezeRecords = await queryRunner.manager.query(
        'SELECT * FROM fund_freeze_record WHERE order_id = $1 AND freeze_status IN (1,2)',
        [orderId]
      );
      const alreadyUnfrozen = freezeRecords.some((r: any) => r.freeze_status === 2);
      if (alreadyUnfrozen) {
        await queryRunner.rollbackTransaction();
        return; // 已解冻，则不重复结算
      }

      // 1. 获取订单信息
      const order = await queryRunner.manager.findOne(Order, {
        where: { id: orderId },
      });

      if (!order) {
        throw new Error('订单不存在');
      }

      // 2. 获取冻结记录（仍处于已冻结状态）
      const activeFreeze = await queryRunner.manager.query(
        'SELECT * FROM fund_freeze_record WHERE order_id = $1 AND freeze_status = 1',
        [orderId]
      );

      if (activeFreeze.length === 0) {
        throw new Error('未找到冻结记录');
      }

      const freezeRecord = activeFreeze[0];

      // 3. 计算商家收益（无平台抽成）
      const totalAmount = parseFloat(order.totalAmount.toString());
      const costPrice = parseFloat(freezeRecord.freeze_amount);
      const merchantProfit = totalAmount - costPrice; // 商家收益 = 订单售价 - 成本价
      const platformProfit = 0; // 平台不抽成
      const finalMerchantProfit = merchantProfit; // 商家获得全部收益

      // 4. 更新订单佣金信息
      await queryRunner.manager.query(
        'UPDATE "order" SET merchant_profit = $1, platform_profit = $2 WHERE id = $3',
        [finalMerchantProfit, platformProfit, orderId]
      );

      // 5. 获取商家信息
      const merchant = await queryRunner.manager.findOne(Merchant, {
        where: { id: order.merchantId },
      });

      if (!merchant) {
        throw new Error('商家不存在');
      }

      // 6. 解冻资金并结算收益
      const currentBalance = typeof merchant.balance === 'string' ? parseFloat(merchant.balance) : merchant.balance || 0;
      const currentFrozen = typeof merchant.frozenAmount === 'string' ? parseFloat(merchant.frozenAmount) : merchant.frozenAmount || 0;
      const unfreezeAmount = parseFloat(freezeRecord.freeze_amount);
      const newFrozen = currentFrozen - unfreezeAmount;
      const newBalance = currentBalance + unfreezeAmount + finalMerchantProfit; // 解冻成本价 + 商家收益

      await queryRunner.manager.query(
        'UPDATE merchant SET balance = $1, frozen_amount = $2 WHERE id = $3',
        [newBalance.toString(), newFrozen.toString(), order.merchantId]
      );

      // 7. 更新冻结记录状态
      await queryRunner.manager.query(
        'UPDATE fund_freeze_record SET freeze_status = 2, unfreeze_time = NOW(), unfreeze_reason = $1 WHERE id = $2',
        ['订单完成，解冻成本价并结算收益', freezeRecord.id]
      );

      // 8. 记录解冻流水
      await queryRunner.manager.query(
        `INSERT INTO fund_transaction (merchant_id, order_id, transaction_type, amount, balance_before, balance_after, frozen_before, frozen_after, description, remark)
         VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)`,
        [
          order.merchantId, orderId, 2, unfreezeAmount,
          currentBalance, currentBalance + unfreezeAmount, currentFrozen, newFrozen,
          `订单${orderId}成本价解冻`, `解冻金额: ${unfreezeAmount}`
        ]
      );

      // 9. 记录收益结算流水
      await queryRunner.manager.query(
        `INSERT INTO fund_transaction (merchant_id, order_id, transaction_type, amount, balance_before, balance_after, frozen_before, frozen_after, description, remark)
         VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)`,
        [
          order.merchantId, orderId, 3, finalMerchantProfit,
          currentBalance + unfreezeAmount, newBalance, newFrozen, newFrozen,
          `订单${orderId}收益结算`, `收益: ${finalMerchantProfit}, 平台抽成: 0`
        ]
      );

      await queryRunner.commitTransaction();
      console.log(`✅ 订单${orderId}成本价解冻成功，解冻金额: ${unfreezeAmount}，商家收益: ${finalMerchantProfit}`);
    } catch (error) {
      await queryRunner.rollbackTransaction();
      console.error('❌ 资金解冻失败:', error);
      throw error;
    } finally {
      await queryRunner.release();
    }
  }

  /**
   * 获取商家资金概览
   */
  async getMerchantFundOverview(merchantId: string) {
    const merchant = await this.merchantRepository.findOne({
      where: { id: merchantId },
    });

    if (!merchant) {
      throw new Error('商家不存在');
    }

    // 统计冻结中的订单
    const frozenOrders = await this.fundFreezeRepository.count({
      where: { merchantId, freezeStatus: 1 },
    });

    // 统计总冻结金额
    const totalFrozenResult = await this.dataSource.query(
      'SELECT SUM(freeze_amount) as total FROM fund_freeze_record WHERE merchant_id = $1 AND freeze_status = 1',
      [merchantId]
    );

    return {
      balance: typeof merchant.balance === 'string' ? parseFloat(merchant.balance) : merchant.balance || 0,
      frozenAmount: typeof merchant.frozenAmount === 'string' ? parseFloat(merchant.frozenAmount) : merchant.frozenAmount || 0,
      availableBalance: (typeof merchant.balance === 'string' ? parseFloat(merchant.balance) : merchant.balance || 0) + (typeof merchant.frozenAmount === 'string' ? parseFloat(merchant.frozenAmount) : merchant.frozenAmount || 0),
      frozenOrdersCount: frozenOrders,
      totalFrozenAmount: parseFloat(totalFrozenResult[0]?.total || '0'),
    };
  }

  /**
   * 获取商家资金流水
   */
  async getMerchantFundTransactions(
    merchantId: string,
    page: number = 1,
    pageSize: number = 10,
  ) {
    const [transactions, total] = await this.fundTransactionRepository.findAndCount({
      where: { merchantId },
      order: { createTime: 'DESC' },
      skip: (page - 1) * pageSize,
      take: pageSize,
    });

    return {
      list: transactions,
      total,
      page,
      pageSize,
      totalPages: Math.ceil(total / pageSize),
    };
  }

  /**
   * 获取商家冻结记录
   */
  async getMerchantFreezeRecords(
    merchantId: string,
    page: number = 1,
    pageSize: number = 10,
  ) {
    const [records, total] = await this.fundFreezeRepository.findAndCount({
      where: { merchantId },
      order: { createTime: 'DESC' },
      skip: (page - 1) * pageSize,
      take: pageSize,
    });

    return {
      list: records,
      total,
      page,
      pageSize,
      totalPages: Math.ceil(total / pageSize),
    };
  }
}