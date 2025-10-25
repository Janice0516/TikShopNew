import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { UserAddress } from './entities/user-address.entity';
import { CreateAddressDto, UpdateAddressDto } from './dto/address.dto';

@Injectable()
export class AddressService {
  constructor(
    @InjectRepository(UserAddress)
    private addressRepository: Repository<UserAddress>,
  ) {}

  /**
   * 获取用户地址列表
   */
  async getUserAddresses(userId: string): Promise<UserAddress[]> {
    return this.addressRepository.find({
      where: { userId },
      order: { isDefault: 'DESC', createTime: 'DESC' },
    });
  }

  /**
   * 获取用户默认地址
   */
  async getDefaultAddress(userId: string): Promise<UserAddress | null> {
    return this.addressRepository.findOne({
      where: { userId, isDefault: 1 },
    });
  }

  /**
   * 根据ID获取地址
   */
  async getAddressById(id: string, userId: string): Promise<UserAddress> {
    const address = await this.addressRepository.findOne({
      where: { id, userId },
    });

    if (!address) {
      throw new NotFoundException('地址不存在');
    }

    return address;
  }

  /**
   * 创建地址
   */
  async createAddress(userId: string, createAddressDto: CreateAddressDto): Promise<UserAddress> {
    const { isDefault, ...addressData } = createAddressDto;

    // 如果设置为默认地址，先取消其他默认地址
    if (isDefault === 1) {
      await this.addressRepository.update(
        { userId, isDefault: 1 },
        { isDefault: 0 }
      );
    }

    const address = this.addressRepository.create({
      userId,
      ...addressData,
      isDefault: isDefault || 0,
    });

    return this.addressRepository.save(address);
  }

  /**
   * 更新地址
   */
  async updateAddress(id: string, userId: string, updateAddressDto: UpdateAddressDto): Promise<UserAddress> {
    const address = await this.getAddressById(id, userId);
    
    const { isDefault, ...updateData } = updateAddressDto;

    // 如果设置为默认地址，先取消其他默认地址
    if (isDefault === 1) {
      await this.addressRepository.update(
        { userId, isDefault: 1 },
        { isDefault: 0 }
      );
    }

    await this.addressRepository.update(id, {
      ...updateData,
      ...(isDefault !== undefined && { isDefault }),
    });

    return this.getAddressById(id, userId);
  }

  /**
   * 删除地址
   */
  async deleteAddress(id: string, userId: string): Promise<void> {
    const address = await this.getAddressById(id, userId);
    await this.addressRepository.remove(address);
  }

  /**
   * 设置默认地址
   */
  async setDefaultAddress(id: string, userId: string): Promise<UserAddress> {
    // 先取消所有默认地址
    await this.addressRepository.update(
      { userId, isDefault: 1 },
      { isDefault: 0 }
    );

    // 设置新的默认地址
    await this.addressRepository.update(id, { isDefault: 1 });

    return this.getAddressById(id, userId);
  }
}

