import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { Merchant } from './merchant.entity';

@Entity('merchant_recharge')
export class MerchantRecharge {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: string;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: string;

  @ManyToOne(() => Merchant)
  @JoinColumn({ name: 'merchant_id' })
  merchant: Merchant;

  @Column({ name: 'amount', type: 'decimal', precision: 10, scale: 2 })
  amount: number;

  @Column({ name: 'payment_method', length: 50 })
  paymentMethod: string;

  @Column({ name: 'payment_reference', length: 200, nullable: true })
  paymentReference: string;

  @Column({ name: 'remark', type: 'text', nullable: true })
  remark: string;

  @Column({ 
    name: 'status', 
    type: 'smallint', 
    default: 0,
    comment: '状态 0待审核 1审核通过 2审核拒绝'
  })
  status: number;

  @Column({ name: 'admin_id', type: 'bigint', nullable: true })
  adminId: string;

  @Column({ name: 'admin_name', length: 100, nullable: true })
  adminName: string;

  @Column({ name: 'audit_reason', type: 'text', nullable: true })
  auditReason: string;

  @Column({ name: 'audit_time', type: 'timestamp', nullable: true })
  auditTime: Date;

  @CreateDateColumn({ name: 'created_at', type: 'timestamp' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at', type: 'timestamp' })
  updatedAt: Date;
}
