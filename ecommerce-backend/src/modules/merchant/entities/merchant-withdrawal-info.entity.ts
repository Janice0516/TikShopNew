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

@Entity('merchant_withdrawal_info')
export class MerchantWithdrawalInfo {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: number;

  @ManyToOne(() => Merchant)
  @JoinColumn({ name: 'merchant_id' })
  merchant: Merchant;

  @Column({ name: 'withdrawal_type', type: 'smallint', comment: '提款方式 1银行转账 2电子钱包 3现金提取' })
  withdrawalType: number;

  @Column({ name: 'bank_name', length: 100, nullable: true, comment: '银行名称' })
  bankName: string;

  @Column({ name: 'bank_code', length: 20, nullable: true, comment: '银行代码' })
  bankCode: string;

  @Column({ name: 'account_holder_name', length: 100, nullable: true, comment: '账户持有人姓名' })
  accountHolderName: string;

  @Column({ name: 'account_number', length: 50, nullable: true, comment: '账户号码' })
  accountNumber: string;

  @Column({ name: 'swift_code', length: 20, nullable: true, comment: 'SWIFT代码' })
  swiftCode: string;

  @Column({ name: 'wallet_type', length: 50, nullable: true, comment: '电子钱包类型 (TouchnGo, GrabPay, Boost等)' })
  walletType: string;

  @Column({ name: 'wallet_account', length: 100, nullable: true, comment: '电子钱包账户' })
  walletAccount: string;

  @Column({ name: 'phone_number', length: 20, nullable: true, comment: '手机号码' })
  phoneNumber: string;

  @Column({ name: 'email', length: 100, nullable: true, comment: '邮箱地址' })
  email: string;

  @Column({ name: 'address', length: 500, nullable: true, comment: '地址' })
  address: string;

  @Column({ name: 'city', length: 100, nullable: true, comment: '城市' })
  city: string;

  @Column({ name: 'state', length: 100, nullable: true, comment: '州/省' })
  state: string;

  @Column({ name: 'postal_code', length: 20, nullable: true, comment: '邮政编码' })
  postalCode: string;

  @Column({ name: 'country', length: 50, default: 'Malaysia', comment: '国家' })
  country: string;

  @Column({ name: 'is_default', type: 'boolean', default: false, comment: '是否为默认提款方式' })
  isDefault: boolean;

  @Column({ name: 'is_verified', type: 'boolean', default: false, comment: '是否已验证' })
  isVerified: boolean;

  @Column({ name: 'verification_status', type: 'smallint', default: 0, comment: '验证状态 0待验证 1验证中 2验证通过 3验证失败' })
  verificationStatus: number;

  @Column({ name: 'verification_remark', length: 500, nullable: true, comment: '验证备注' })
  verificationRemark: string;

  @CreateDateColumn({ name: 'created_at', type: 'timestamp' })
  createdAt: Date;

  @UpdateDateColumn({ name: 'updated_at', type: 'timestamp' })
  updatedAt: Date;
}

