import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
} from 'typeorm';

@Entity('order_item')
export class OrderItem {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'order_id', type: 'bigint' })
  orderId: number;

  @Column({ name: 'product_id', type: 'bigint' })
  productId: number;

  @Column({ name: 'sku_id', type: 'bigint', nullable: true })
  skuId: number;

  @Column({ name: 'product_name', length: 200 })
  productName: string;

  @Column({ name: 'product_image', length: 255 })
  productImage: string;

  @Column({ name: 'sku_name', length: 100, nullable: true })
  skuName: string;

  @Column({ type: 'int' })
  quantity: number;

  @Column({ name: 'cost_price', type: 'decimal', precision: 10, scale: 2 })
  costPrice: number;

  @Column({ name: 'sale_price', type: 'decimal', precision: 10, scale: 2 })
  salePrice: number;

  @Column({ name: 'total_price', type: 'decimal', precision: 10, scale: 2 })
  totalPrice: number;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;
}

