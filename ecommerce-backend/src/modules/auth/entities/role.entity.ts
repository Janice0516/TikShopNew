import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToMany,
  JoinTable,
} from 'typeorm';
import { Permission } from './permission.entity';

@Entity('roles')
export class Role {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: string;

  @Column({ length: 50, unique: true, comment: '角色代码' })
  code: string;

  @Column({ length: 100, comment: '角色名称' })
  name: string;

  @Column({ length: 255, nullable: true, comment: '角色描述' })
  description: string;

  @Column({ type: 'smallint', default: 1, comment: '状态 0禁用 1启用' })
  status: number;

  @Column({ type: 'smallint', default: 0, comment: '是否系统角色 0否 1是' })
  isSystem: number;

  @ManyToMany(() => Permission, permission => permission.roles)
  @JoinTable({
    name: 'role_permissions',
    joinColumn: { name: 'role_id', referencedColumnName: 'id' },
    inverseJoinColumn: { name: 'permission_id', referencedColumnName: 'id' },
  })
  permissions: Permission[];

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}
