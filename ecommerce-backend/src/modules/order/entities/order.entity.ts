import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';

@Entity('"order"')
export class Order {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'order_no', length: 50, unique: true })
  orderNo: string;

  @Column({ name: 'user_id', type: 'bigint' })
  userId: number;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: number;

  @Column({ name: 'product_id', type: 'bigint' })
  productId: number;

  @Column({ type: 'int' })
  quantity: number;

  @Column({ name: 'total_amount', type: 'decimal', precision: 10, scale: 2 })
  totalAmount: number;

  @Column({ type: 'smallint', default: 1 })
  status: number;

  @Column({ name: 'shipping_address', type: 'text', nullable: true })
  shippingAddress: string;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}

