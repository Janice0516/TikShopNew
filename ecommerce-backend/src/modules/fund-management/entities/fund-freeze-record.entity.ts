import { Entity, PrimaryGeneratedColumn, Column, CreateDateColumn, UpdateDateColumn } from 'typeorm';

@Entity('fund_freeze_record')
export class FundFreezeRecord {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ type: 'bigint' })
  merchantId: string;

  @Column({ type: 'bigint' })
  orderId: string;

  @Column({ type: 'decimal', precision: 10, scale: 2 })
  freezeAmount: number;

  @Column({ type: 'smallint', default: 1 })
  freezeType: number; // 1订单冻结 2其他冻结

  @Column({ type: 'smallint', default: 1 })
  freezeStatus: number; // 1已冻结 2已解冻 3已取消

  @Column({ type: 'varchar', length: 255, default: '订单资金冻结' })
  freezeReason: string;

  @Column({ type: 'timestamp', nullable: true })
  unfreezeTime: Date;

  @Column({ type: 'varchar', length: 255, nullable: true })
  unfreezeReason: string;

  @CreateDateColumn()
  createTime: Date;

  @UpdateDateColumn()
  updateTime: Date;
}
