import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { OrderService } from './order.service';
import { OrderController } from './order.controller';
import { AdminOrderController } from './admin-order.controller';
import { OrderRemarkController } from './controllers/order-remark.controller';
import { Order } from './entities/order.entity';
import { OrderItem } from './entities/order-item.entity';
import { ProductModule } from '../product/product.module';
import { UserModule } from '../user/user.module';
import { FundManagementModule } from '../fund-management/fund-management.module';

@Module({
  imports: [
    TypeOrmModule.forFeature([Order, OrderItem]),
    ProductModule,
    UserModule,
    FundManagementModule,
  ],
  controllers: [OrderController, AdminOrderController, OrderRemarkController],
  providers: [OrderService],
  exports: [OrderService],
})
export class OrderModule {}
