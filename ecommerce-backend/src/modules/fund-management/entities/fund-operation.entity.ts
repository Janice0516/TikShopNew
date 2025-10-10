import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { Merchant } from '../../merchant/entities/merchant.entity';

@Entity('fund_operation')
export class FundOperation {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: number;

  @ManyToOne(() => Merchant)
  @JoinColumn({ name: 'merchant_id' })
  merchant: Merchant;

  @Column({ name: 'operation_type', type: 'tinyint', comment: '操作类型 1充值 2提现 3冻结 4解冻 5扣款 6退款' })
  operationType: number;

  @Column({ name: 'amount', type: 'decimal', precision: 10, scale: 2, comment: '操作金额' })
  amount: number;

  @Column({ name: 'balance_before', type: 'decimal', precision: 10, scale: 2, comment: '操作前余额' })
  balanceBefore: number;

  @Column({ name: 'balance_after', type: 'decimal', precision: 10, scale: 2, comment: '操作后余额' })
  balanceAfter: number;

  @Column({ name: 'frozen_before', type: 'decimal', precision: 10, scale: 2, comment: '操作前冻结金额' })
  frozenBefore: number;

  @Column({ name: 'frozen_after', type: 'decimal', precision: 10, scale: 2, comment: '操作后冻结金额' })
  frozenAfter: number;

  @Column({ name: 'admin_id', type: 'bigint', nullable: true, comment: '操作管理员ID' })
  adminId: number;

  @Column({ name: 'admin_name', length: 50, nullable: true, comment: '操作管理员姓名' })
  adminName: string;

  @Column({ name: 'reason', length: 500, nullable: true, comment: '操作原因' })
  reason: string;

  @Column({ name: 'remark', length: 500, nullable: true, comment: '备注' })
  remark: string;

  @Column({ name: 'order_id', type: 'bigint', nullable: true, comment: '关联订单ID' })
  orderId: number;

  @Column({ name: 'withdrawal_id', type: 'bigint', nullable: true, comment: '关联提现ID' })
  withdrawalId: number;

  @CreateDateColumn({ name: 'create_time', type: 'datetime' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'datetime' })
  updateTime: Date;
}
