import { Module } from '@nestjs/common';
import { AdminController } from './admin.controller';
import { AdminService } from './admin.service';
import { TestAdminController } from './test-admin.controller';

@Module({
  controllers: [AdminController, TestAdminController],
  providers: [AdminService],
  exports: [AdminService],
})
export class AdminModule {}
