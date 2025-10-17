import { Entity, PrimaryGeneratedColumn, Column, CreateDateColumn } from 'typeorm';

@Entity('fund_transaction')
export class FundTransaction {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ type: 'bigint' })
  merchantId: string;

  @Column({ type: 'bigint', nullable: true })
  orderId: string;

  @Column({ type: 'smallint' })
  transactionType: number; // 1冻结 2解冻 3佣金结算 4提现 5充值

  @Column({ type: 'decimal', precision: 10, scale: 2 })
  amount: number;

  @Column({ type: 'decimal', precision: 10, scale: 2 })
  balanceBefore: number;

  @Column({ type: 'decimal', precision: 10, scale: 2 })
  balanceAfter: number;

  @Column({ type: 'decimal', precision: 10, scale: 2 })
  frozenBefore: number;

  @Column({ type: 'decimal', precision: 10, scale: 2 })
  frozenAfter: number;

  @Column({ type: 'varchar', length: 255 })
  description: string;

  @Column({ type: 'varchar', length: 500, nullable: true })
  remark: string;

  @CreateDateColumn()
  createTime: Date;
}
