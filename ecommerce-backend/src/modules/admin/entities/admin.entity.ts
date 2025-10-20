import { Entity, PrimaryGeneratedColumn, Column, CreateDateColumn, UpdateDateColumn, ManyToOne, JoinColumn } from 'typeorm';
import { Role } from '../../auth/entities/role.entity';

@Entity('admin')
export class Admin {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ unique: true, length: 50 })
  username: string;

  @Column({ length: 100 })
  password: string;

  @Column({ length: 50, nullable: true })
  nickname: string;

  @Column({ length: 255, nullable: true })
  avatar: string;

  @Column({ length: 20, default: 'admin' })
  role: string;

  @Column({ name: 'role_id', nullable: true, comment: '角色ID' })
  roleId: string;

  @ManyToOne(() => Role, { nullable: true })
  @JoinColumn({ name: 'role_id' })
  roleEntity: Role;

  @Column({ length: 50, nullable: true, comment: '职务' })
  position: string;

  @Column({ length: 20, nullable: true, comment: '手机号' })
  phone: string;

  @Column({ length: 100, nullable: true, comment: '邮箱' })
  email: string;

  @Column({ length: 255, nullable: true, comment: '备注' })
  remark: string;

  @Column({ default: 1 })
  status: number;

  // Align with MySQL schema: create_time/update_time
  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}
