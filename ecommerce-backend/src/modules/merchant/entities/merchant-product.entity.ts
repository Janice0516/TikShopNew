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
import { Product } from '../../product/entities/product.entity';

@Entity('merchant_product')
export class MerchantProduct {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: number;

  @Column({ name: 'platform_product_id', type: 'bigint' })
  productId: number;

  @Column({ name: 'sale_price', type: 'decimal', precision: 10, scale: 2 })
  salePrice: number;

  @Column({ name: 'profit_margin', type: 'decimal', precision: 10, scale: 2, nullable: true })
  profitMargin: number;

  @Column({ type: 'smallint', default: 1, comment: '状态 1上架 0下架' })
  status: number;

  @Column({ type: 'int', default: 0, comment: '销量' })
  sales: number;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;

  // 关联关系
  @ManyToOne(() => Merchant)
  @JoinColumn({ name: 'merchant_id' })
  merchant: Merchant;

  @ManyToOne(() => Product)
  @JoinColumn({ name: 'platform_product_id' })
  product: Product;
}
