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

@Entity('merchant_credit_rating')
export class MerchantCreditRating {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: number;

  @ManyToOne(() => Merchant)
  @JoinColumn({ name: 'merchant_id' })
  merchant: Merchant;

  @Column({ name: 'rating', type: 'smallint', comment: '信用评级 1-5星' })
  rating: number;

  @Column({ name: 'score', type: 'decimal', precision: 5, scale: 2, comment: '信用分数 0-100' })
  score: number;

  @Column({ name: 'level', type: 'varchar', length: 20, comment: '信用等级 AAA/AA/A/BBB/BB/B/C' })
  level: string;

  @Column({ name: 'evaluation_date', type: 'date', comment: '评级日期' })
  evaluationDate: Date;

  @Column({ name: 'valid_until', type: 'date', comment: '有效期至' })
  validUntil: Date;

  @Column({ name: 'evaluator_id', type: 'bigint', comment: '评估人ID' })
  evaluatorId: number;

  @Column({ name: 'evaluation_reason', type: 'text', nullable: true, comment: '评级原因' })
  evaluationReason: string;

  @Column({ name: 'status', type: 'smallint', default: 1, comment: '状态 0无效 1有效' })
  status: number;

  @CreateDateColumn({ name: 'create_time', type: 'datetime' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'datetime' })
  updateTime: Date;
}
