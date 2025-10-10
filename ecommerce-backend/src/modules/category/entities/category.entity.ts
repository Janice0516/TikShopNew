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

  @Column({ type: 'tinyint', default: 1, comment: '层级 1一级 2二级 3三级' })
  level: number;

  @Column({ type: 'int', default: 0 })
  sort: number;

  @Column({ length: 255, nullable: true })
  icon: string;

  @Column({ type: 'tinyint', default: 1, comment: '状态 1启用 0禁用' })
  status: number;

  @CreateDateColumn({ name: 'create_time', type: 'datetime' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'datetime' })
  updateTime: Date;
}
