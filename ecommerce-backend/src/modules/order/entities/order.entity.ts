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

  @Column({ name: 'order_status', type: 'smallint', default: 1, comment: '订单状态 1待支付 2待发货 3待收货 4已完成 5已取消' })
  orderStatus: number;

  @Column({ name: 'pay_status', type: 'smallint', default: 0, comment: '支付状态 0未支付 1已支付' })
  payStatus: number;

  @Column({ name: 'pay_type', type: 'smallint', nullable: true, comment: '支付方式 1微信 2支付宝' })
  payType: number;

  @Column({ name: 'pay_time', type: 'timestamp', nullable: true })
  payTime: Date;

  @Column({ name: 'ship_time', type: 'timestamp', nullable: true })
  shipTime: Date;

  @Column({ name: 'shipping_address', type: 'text', nullable: true })
  shippingAddress: string;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}

