import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';
import { Exclude } from 'class-transformer';

@Entity('merchant')
export class Merchant {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'merchant_uid', length: 20, unique: true, comment: '商家唯一标识符' })
  merchantUid: string;

  @Column({ length: 50, unique: true })
  username: string;

  @Column({ length: 100 })
  @Exclude()
  password: string;

  @Column({ name: 'merchant_name', length: 100 })
  merchantName: string;

  @Column({ name: 'contact_name', length: 50, nullable: true })
  contactName: string;

  @Column({ name: 'contact_phone', length: 11, nullable: true })
  contactPhone: string;

  @Column({ name: 'business_license', length: 255, nullable: true })
  businessLicense: string;

  @Column({ name: 'id_card_front', length: 255, nullable: true })
  idCardFront: string;

  @Column({ name: 'id_card_back', length: 255, nullable: true })
  idCardBack: string;

  @Column({ type: 'smallint', default: 0, comment: '状态 0待审核 1已通过 2已拒绝 3已禁用' })
  status: number;

  @Column({ name: 'reject_reason', length: 255, nullable: true })
  rejectReason: string;

  @Column({ name: 'shop_name', length: 100, nullable: true })
  shopName: string;

  @Column({ name: 'shop_logo', length: 255, nullable: true })
  shopLogo: string;

  @Column({ name: 'shop_banner', type: 'text', nullable: true })
  shopBanner: string;

  @Column({ name: 'shop_description', length: 500, nullable: true })
  shopDescription: string;

  @Column({ type: 'decimal', precision: 10, scale: 2, default: 0 })
  balance: number;

  @Column({ name: 'frozen_amount', type: 'decimal', precision: 10, scale: 2, default: 0 })
  frozenAmount: number;

  @Column({ name: 'total_income', type: 'decimal', precision: 10, scale: 2, default: 0 })
  totalIncome: number;

  @Column({ name: 'total_withdraw', type: 'decimal', precision: 10, scale: 2, default: 0 })
  totalWithdraw: number;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}

