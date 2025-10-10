import { Entity, PrimaryGeneratedColumn, Column, CreateDateColumn, UpdateDateColumn } from 'typeorm';

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

  @Column({ default: 1 })
  status: number;

  @CreateDateColumn({ name: 'created_at' })
  createTime: Date;

  @UpdateDateColumn({ name: 'updated_at' })
  updateTime: Date;
}
