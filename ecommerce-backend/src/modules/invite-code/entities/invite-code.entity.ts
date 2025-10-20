import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  Index,
} from 'typeorm';

@Entity('invite_code')
export class InviteCode {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: string;

  @Column({ name: 'invite_code', length: 20, unique: true, comment: '邀请码' })
  @Index()
  inviteCode: string;

  @Column({ name: 'salesperson_name', length: 100, comment: '业务员姓名' })
  salespersonName: string;

  @Column({ name: 'salesperson_phone', length: 20, nullable: true, comment: '业务员电话' })
  salespersonPhone: string;

  @Column({ name: 'salesperson_id', length: 50, nullable: true, comment: '业务员ID' })
  salespersonId: string;

  @Column({ name: 'used_count', type: 'int', default: 0, comment: '已使用次数' })
  usedCount: number;

  @Column({ name: 'max_usage', type: 'int', default: 0, comment: '最大使用次数，0表示无限制' })
  maxUsage: number;

  @Column({ type: 'smallint', default: 1, comment: '状态 0禁用 1启用' })
  status: number;

  @Column({ name: 'expire_time', type: 'timestamp', nullable: true, comment: '过期时间' })
  expireTime: Date;

  @Column({ name: 'remark', length: 255, nullable: true, comment: '备注' })
  remark: string;

  @CreateDateColumn({ name: 'create_time', type: 'timestamp' })
  createTime: Date;

  @UpdateDateColumn({ name: 'update_time', type: 'timestamp' })
  updateTime: Date;
}
