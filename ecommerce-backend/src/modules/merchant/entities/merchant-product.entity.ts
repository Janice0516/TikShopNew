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

  @Column({ name: 'is_popular', type: 'boolean', default: false, comment: '是否推荐为热门商品' })
  isPopular: boolean;

  @Column({ name: 'is_top_deal', type: 'boolean', default: false, comment: '是否推荐为Top Deals' })
  isTopDeal: boolean;

  @Column({ name: 'recommend_reason', length: 255, nullable: true, comment: '推荐理由' })
  recommendReason: string;

  @Column({ name: 'recommend_priority', type: 'int', default: 0, comment: '推荐优先级' })
  recommendPriority: number;

  @Column({ name: 'recommend_start_time', type: 'timestamp', nullable: true, comment: '推荐开始时间' })
  recommendStartTime: Date;

  @Column({ name: 'recommend_end_time', type: 'timestamp', nullable: true, comment: '推荐结束时间' })
  recommendEndTime: Date;

  @Column({ name: 'discount_price', type: 'decimal', precision: 10, scale: 2, nullable: true, comment: '折扣价' })
  discountPrice: number;

  @Column({ name: 'discount_start_time', type: 'datetime', nullable: true, comment: '折扣开始时间' })
  discountStartTime: Date;

  @Column({ name: 'discount_end_time', type: 'datetime', nullable: true, comment: '折扣结束时间' })
  discountEndTime: Date;

  @Column({ name: 'is_discount_active', type: 'boolean', default: false, comment: '是否启用折扣' })
  isDiscountActive: boolean;

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
