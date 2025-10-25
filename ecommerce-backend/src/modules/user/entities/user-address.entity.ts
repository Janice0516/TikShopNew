import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { User } from './user.entity';

@Entity('user_address')
export class UserAddress {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: string;

  @Column({ name: 'user_id', type: 'bigint' })
  userId: string;

  @Column({ name: 'receiver_name', length: 50 })
  receiverName: string;

  @Column({ name: 'phone', length: 11 })
  phone: string;

  @Column({ name: 'province', length: 50 })
  province: string;

  @Column({ name: 'city', length: 50 })
  city: string;

  @Column({ name: 'district', length: 50 })
  district: string;

  @Column({ name: 'detail_address', length: 255 })
  detailAddress: string;

  @Column({ name: 'is_default', type: 'tinyint', default: 0 })
  isDefault: number;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;

  // 关联用户
  @ManyToOne(() => User)
  @JoinColumn({ name: 'user_id' })
  user: User;
}

