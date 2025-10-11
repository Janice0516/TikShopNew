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

@Entity('merchant_withdrawal')
export class MerchantWithdrawal {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: number;

  @ManyToOne(() => Merchant)
  @JoinColumn({ name: 'merchant_id' })
  merchant: Merchant;

  @Column({ name: 'withdrawal_amount', type: 'decimal', precision: 10, scale: 2 })
  withdrawalAmount: number;

  @Column({ name: 'bank_name', length: 100 })
  bankName: string;

  @Column({ name: 'bank_account', length: 50 })
  bankAccount: string;

  @Column({ name: 'account_holder', length: 50 })
  accountHolder: string;

  @Column({ name: 'status', type: 'smallint', default: 0, comment: '状态 0待审核 1已通过 2已拒绝 3已打款' })
  status: number;

  @Column({ name: 'remark', length: 500, nullable: true })
  remark: string;

  @Column({ name: 'admin_remark', length: 500, nullable: true })
  adminRemark: string;

  @Column({ name: 'processed_by', type: 'bigint', nullable: true })
  processedBy: number;

  @Column({ name: 'processed_at', type: 'timestamp', nullable: true })
  processedAt: Date;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}
