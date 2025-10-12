import {
  Entity,
  Column,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
} from 'typeorm';

@Entity('system_settings')
export class SystemSettings {
  @PrimaryGeneratedColumn('increment', { type: 'bigint' })
  id: string;

  @Column({ name: 'setting_key', length: 100, unique: true })
  settingKey: string;

  @Column({ name: 'setting_value', type: 'text' })
  settingValue: string;

  @Column({ name: 'setting_type', length: 50, default: 'string' })
  settingType: string;

  @Column({ name: 'category', length: 50, default: 'general' })
  category: string;

  @Column({ name: 'description', length: 255, nullable: true })
  description: string;

  @CreateDateColumn({ type: 'timestamp', name: 'create_time' })
  createTime: Date;

  @UpdateDateColumn({ type: 'timestamp', name: 'update_time' })
  updateTime: Date;
}
