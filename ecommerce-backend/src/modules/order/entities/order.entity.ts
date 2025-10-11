import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';

@Entity('order_main')
export class Order {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'order_no', length: 32, unique: true })
  orderNo: string;

  @Column({ name: 'user_id', type: 'bigint' })
  userId: number;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: number;

  @Column({ name: 'total_amount', type: 'decimal', precision: 10, scale: 2 })
  totalAmount: number;

  @Column({ name: 'cost_amount', type: 'decimal', precision: 10, scale: 2 })
  costAmount: number;

  @Column({ name: 'merchant_profit', type: 'decimal', precision: 10, scale: 2 })
  merchantProfit: number;

  @Column({ name: 'platform_profit', type: 'decimal', precision: 10, scale: 2, default: 0 })
  platformProfit: number;

  @Column({ type: 'decimal', precision: 10, scale: 2, default: 0 })
  freight: number;

  @Column({ name: 'discount_amount', type: 'decimal', precision: 10, scale: 2, default: 0 })
  discountAmount: number;

  @Column({ name: 'pay_amount', type: 'decimal', precision: 10, scale: 2 })
  payAmount: number;

  @Column({ name: 'receiver_name', length: 50 })
  receiverName: string;

  @Column({ name: 'receiver_phone', length: 11 })
  receiverPhone: string;

  @Column({ name: 'receiver_province', length: 50 })
  receiverProvince: string;

  @Column({ name: 'receiver_city', length: 50 })
  receiverCity: string;

  @Column({ name: 'receiver_district', length: 50 })
  receiverDistrict: string;

  @Column({ name: 'receiver_address', length: 255 })
  receiverAddress: string;

  @Column({ name: 'order_status', type: 'smallint', default: 1, comment: '1待付款 2待发货 3待收货 4已完成 5已取消' })
  orderStatus: number;

  @Column({ name: 'pay_status', type: 'smallint', default: 0, comment: '0未支付 1已支付 2已退款' })
  payStatus: number;

  @Column({ name: 'pay_type', type: 'smallint', nullable: true, comment: '1微信 2支付宝' })
  payType: number;

  @Column({ name: 'pay_time', type: 'timestamp', nullable: true })
  payTime: Date;

  @Column({ name: 'transaction_id', length: 100, nullable: true })
  transactionId: string;

  @Column({ name: 'ship_time', type: 'timestamp', nullable: true })
  shipTime: Date;

  @Column({ name: 'receive_time', type: 'timestamp', nullable: true })
  receiveTime: Date;

  @Column({ name: 'finish_time', type: 'timestamp', nullable: true })
  finishTime: Date;

  @Column({ name: 'cancel_time', type: 'timestamp', nullable: true })
  cancelTime: Date;

  @Column({ name: 'cancel_reason', length: 255, nullable: true })
  cancelReason: string;

  @Column({ name: 'buyer_message', length: 255, nullable: true })
  buyerMessage: string;

  @Column({ length: 500, nullable: true })
  remark: string;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}

