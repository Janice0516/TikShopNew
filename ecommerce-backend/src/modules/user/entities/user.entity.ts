import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';
import { Exclude } from 'class-transformer';

@Entity('user')
export class User {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: string;

  @Column({ length: 50, unique: true })
  username: string;

  @Column({ length: 100 })
  @Exclude() // 序列化时排除密码字段
  password: string;

  @Column({ length: 50, nullable: true })
  nickname: string;

  @Column({ length: 100, nullable: true })
  email: string;

  @Column({ length: 20, nullable: true })
  phone: string;

  @Column({ length: 255, nullable: true })
  avatar: string;

  @Column({ type: 'smallint', default: 0, comment: '性别 0未知 1男 2女' })
  gender: number;

  @Column({ type: 'smallint', default: 1, comment: '状态 1正常 0禁用' })
  status: number;

  @Column({ type: 'timestamp', nullable: true })
  lastLoginTime: Date;

  @CreateDateColumn({ type: 'timestamp', name: 'create_time' })
  createTime: Date;

  @UpdateDateColumn({ type: 'timestamp', name: 'update_time' })
  updateTime: Date;
}

