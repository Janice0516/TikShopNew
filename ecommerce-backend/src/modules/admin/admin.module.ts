import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Admin } from './entities/admin.entity';
import { Product } from '../product/entities/product.entity';
import { Merchant } from '../merchant/entities/merchant.entity';
import { Order } from '../order/entities/order.entity';
import { User } from '../user/entities/user.entity';
import { MerchantProduct } from '../merchant/entities/merchant-product.entity';
import { Role } from '../auth/entities/role.entity';
import { Permission } from '../auth/entities/permission.entity';
import { AdminController } from './admin.controller';
import { AdminService } from './admin.service';
import { TestAdminController } from './test-admin.controller';
import { RecommendProductController } from './recommend-product.controller';
import { RecommendProductService } from './recommend-product.service';
import { AdminRechargeAuditController } from './admin-recharge-audit.controller';
import { AdminAccountController } from './controllers/admin-account.controller';
import { RolePermissionModule } from '../auth/modules/role-permission.module';

@Module({
  imports: [
    TypeOrmModule.forFeature([Admin, Product, Merchant, Order, User, MerchantProduct, Role, Permission]),
    RolePermissionModule
  ],
  controllers: [AdminController, TestAdminController, RecommendProductController, AdminRechargeAuditController, AdminAccountController],
  providers: [AdminService, RecommendProductService],
  exports: [AdminService, RecommendProductService],
})
export class AdminModule {}
