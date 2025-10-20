import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { RolePermissionService } from '../services/role-permission.service';
import { RolePermissionController } from '../controllers/role-permission.controller';
import { Role } from '../entities/role.entity';
import { Permission } from '../entities/permission.entity';

@Module({
  imports: [TypeOrmModule.forFeature([Role, Permission])],
  providers: [RolePermissionService],
  controllers: [RolePermissionController],
  exports: [RolePermissionService],
})
export class RolePermissionModule {}
