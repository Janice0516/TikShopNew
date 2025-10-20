import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToMany,
} from 'typeorm';
import { Role } from './role.entity';

@Entity('permissions')
export class Permission {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: string;

  @Column({ length: 50, unique: true, comment: '权限代码' })
  code: string;

  @Column({ length: 100, comment: '权限名称' })
  name: string;

  @Column({ length: 255, nullable: true, comment: '权限描述' })
  description: string;

  @Column({ length: 100, comment: '权限分组' })
  group: string;

  @Column({ type: 'smallint', default: 1, comment: '状态 0禁用 1启用' })
  status: number;

  @Column({ type: 'smallint', default: 0, comment: '是否系统权限 0否 1是' })
  isSystem: number;

  @ManyToMany(() => Role, role => role.permissions)
  roles: Role[];

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}
