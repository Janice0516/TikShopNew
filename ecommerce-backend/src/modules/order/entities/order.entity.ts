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
  id: string;

  @Column({ name: 'order_no', length: 50, unique: true })
  orderNo: string;

  @Column({ name: 'user_id', type: 'bigint' })
  userId: string;

  @Column({ name: 'merchant_id', type: 'bigint' })
  merchantId: string;

  @Column({ name: 'product_id', type: 'bigint' })
  productId: string;

  @Column({ type: 'int' })
  quantity: number;

  @Column({ name: 'total_amount', type: 'decimal', precision: 10, scale: 2 })
  totalAmount: number;

  @Column({ name: 'cost_amount', type: 'decimal', precision: 10, scale: 2, nullable: true })
  costAmount: number;

  @Column({ name: 'merchant_profit', type: 'decimal', precision: 10, scale: 2, nullable: true })
  merchantProfit: number;

  @Column({ name: 'platform_profit', type: 'decimal', precision: 10, scale: 2, nullable: true })
  platformProfit: number;

  @Column({ name: 'freight', type: 'decimal', precision: 10, scale: 2, default: 0 })
  freight: number;

  @Column({ name: 'discount_amount', type: 'decimal', precision: 10, scale: 2, default: 0 })
  discountAmount: number;

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

  @Column({ name: 'receive_time', type: 'timestamp', nullable: true })
  receiveTime: Date;

  @Column({ name: 'finish_time', type: 'timestamp', nullable: true })
  finishTime: Date;

  @Column({ name: 'cancel_time', type: 'timestamp', nullable: true })
  cancelTime: Date;

  @Column({ name: 'cancel_reason', type: 'text', nullable: true })
  cancelReason: string;

  @Column({ name: 'transaction_id', type: 'varchar', length: 100, nullable: true })
  transactionId: string;

  @Column({ name: 'pay_amount', type: 'decimal', precision: 10, scale: 2, nullable: true })
  payAmount: number;

  @Column({ name: 'receiver_name', type: 'varchar', length: 50, nullable: true })
  receiverName: string;

  @Column({ name: 'receiver_phone', type: 'varchar', length: 20, nullable: true })
  receiverPhone: string;

  @Column({ name: 'receiver_province', type: 'varchar', length: 50, nullable: true })
  receiverProvince: string;

  @Column({ name: 'receiver_city', type: 'varchar', length: 50, nullable: true })
  receiverCity: string;

  @Column({ name: 'receiver_district', type: 'varchar', length: 50, nullable: true })
  receiverDistrict: string;

  @Column({ name: 'receiver_address', type: 'varchar', length: 200, nullable: true })
  receiverAddress: string;

  @Column({ name: 'buyer_message', type: 'text', nullable: true })
  buyerMessage: string;

  @Column({ name: 'shipping_address', type: 'text', nullable: true })
  shippingAddress: string;

  @Column({ name: 'admin_remark', type: 'text', nullable: true, comment: '管理员备注' })
  adminRemark: string;

  @Column({ name: 'logistics_status', type: 'varchar', length: 100, nullable: true, comment: '物流状态' })
  logisticsStatus: string;

  @Column({ name: 'tracking_number', type: 'varchar', length: 50, nullable: true, comment: '快递单号' })
  trackingNumber: string;

  @Column({ name: 'logistics_company', type: 'varchar', length: 50, nullable: true, comment: '物流公司' })
  logisticsCompany: string;

  @Column({ name: 'logistics_update_time', type: 'timestamp', nullable: true, comment: '物流更新时间' })
  logisticsUpdateTime: Date;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}

