import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { JwtModule } from '@nestjs/jwt';
import { UserService } from './user.service';
import { UserController } from './user.controller';
import { AdminUserController } from './admin-user.controller';
import { AddressController } from './address.controller';
import { AddressService } from './address.service';
import { User } from './entities/user.entity';
import { UserAddress } from './entities/user-address.entity';

@Module({
  imports: [
    TypeOrmModule.forFeature([User, UserAddress]),
    JwtModule.register({
      secret: process.env.JWT_SECRET || 'your-secret-key',
      signOptions: { expiresIn: '7d' },
    }),
  ],
  controllers: [UserController, AdminUserController, AddressController],
  providers: [UserService, AddressService],
  exports: [UserService, AddressService],
})
export class UserModule {}

