import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';

@Entity('category')
export class Category {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: number;

  @Column({ name: 'parent_id', type: 'bigint', default: 0 })
  parentId: number;

  @Column({ length: 50 })
  name: string;

  @Column({ type: 'smallint', default: 1, comment: '层级 1一级 2二级 3三级' })
  level: number;

  @Column({ type: 'int', default: 0 })
  sort: number;

  @Column({ length: 255, nullable: true })
  icon: string;

  @Column({ name: 'image_url', length: 500, nullable: true, comment: '分类图片URL' })
  imageUrl: string;

  @Column({ name: 'icon_class', length: 100, nullable: true, comment: '图标CSS类名' })
  iconClass: string;

  @Column({ type: 'smallint', default: 1, comment: '状态 1启用 0禁用' })
  status: number;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}
