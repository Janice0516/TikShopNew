import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';

@Entity('cart')
export class Cart {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'user_id', type: 'bigint' })
  userId: number;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: number;

  @Column({ name: 'product_id', type: 'bigint' })
  productId: number;

  @Column({ name: 'sku_id', type: 'bigint', nullable: true })
  skuId: number;

  @Column({ type: 'int', default: 1 })
  quantity: number;

  @Column({ type: 'tinyint', default: 1, comment: '是否选中 1是 0否' })
  selected: number;

  @CreateDateColumn({ name: 'create_time', type: 'datetime' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'datetime' })
  updateTime: Date;
}

