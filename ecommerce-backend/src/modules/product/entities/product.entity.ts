import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';

@Entity('platform_product')
export class Product {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'product_no', length: 50, nullable: true, unique: true })
  productNo: string;

  @Column({ length: 200 })
  name: string;

  @Column({ name: 'category_id', type: 'bigint' })
  categoryId: number;

  @Column({ length: 100, nullable: true })
  brand: string;

  @Column({ name: 'main_image', length: 255 })
  mainImage: string;

  @Column({ type: 'text', nullable: true, comment: '轮播图JSON数组' })
  images: string;

  @Column({ length: 255, nullable: true })
  video: string;

  @Column({ name: 'cost_price', type: 'decimal', precision: 10, scale: 2 })
  costPrice: number;

  @Column({ name: 'suggest_price', type: 'decimal', precision: 10, scale: 2, nullable: true })
  suggestPrice: number;

  @Column({ type: 'int', default: 0 })
  stock: number;

  @Column({ type: 'int', default: 0 })
  sales: number;

  @Column({ type: 'text', nullable: true, comment: '商品详情富文本' })
  description: string;

  @Column({ type: 'tinyint', default: 1, comment: '状态 1上架 0下架' })
  status: number;

  @Column({ type: 'int', default: 0 })
  sort: number;

  @CreateDateColumn({ name: 'create_time', type: 'datetime' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'datetime' })
  updateTime: Date;
}

