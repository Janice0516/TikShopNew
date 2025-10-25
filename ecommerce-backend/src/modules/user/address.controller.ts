import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  UseGuards,
  Request,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { AddressService } from './address.service';
import { CreateAddressDto, UpdateAddressDto } from './dto/address.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('用户地址管理')
@Controller('user/addresses')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class AddressController {
  constructor(private readonly addressService: AddressService) {}

  @Get()
  @ApiOperation({ summary: '获取用户地址列表' })
  async getUserAddresses(@Request() req) {
    const userId = req.user.userId;
    const addresses = await this.addressService.getUserAddresses(String(userId));
    return {
      code: 200,
      message: 'success',
      data: addresses,
    };
  }

  @Get('default')
  @ApiOperation({ summary: '获取用户默认地址' })
  async getDefaultAddress(@Request() req) {
    const userId = req.user.userId;
    const address = await this.addressService.getDefaultAddress(String(userId));
    return {
      code: 200,
      message: 'success',
      data: address,
    };
  }

  @Get(':id')
  @ApiOperation({ summary: '根据ID获取地址' })
  async getAddressById(@Param('id') id: string, @Request() req) {
    const userId = req.user.userId;
    const address = await this.addressService.getAddressById(id, String(userId));
    return {
      code: 200,
      message: 'success',
      data: address,
    };
  }

  @Post()
  @ApiOperation({ summary: '创建地址' })
  async createAddress(@Body() createAddressDto: CreateAddressDto, @Request() req) {
    const userId = req.user.userId;
    const address = await this.addressService.createAddress(String(userId), createAddressDto);
    return {
      code: 200,
      message: '地址创建成功',
      data: address,
    };
  }

  @Put(':id')
  @ApiOperation({ summary: '更新地址' })
  async updateAddress(
    @Param('id') id: string,
    @Body() updateAddressDto: UpdateAddressDto,
    @Request() req,
  ) {
    const userId = req.user.userId;
    const address = await this.addressService.updateAddress(id, String(userId), updateAddressDto);
    return {
      code: 200,
      message: '地址更新成功',
      data: address,
    };
  }

  @Delete(':id')
  @ApiOperation({ summary: '删除地址' })
  async deleteAddress(@Param('id') id: string, @Request() req) {
    const userId = req.user.userId;
    await this.addressService.deleteAddress(id, String(userId));
    return {
      code: 200,
      message: '地址删除成功',
      data: null,
    };
  }

  @Put(':id/default')
  @ApiOperation({ summary: '设置默认地址' })
  async setDefaultAddress(@Param('id') id: string, @Request() req) {
    const userId = req.user.userId;
    const address = await this.addressService.setDefaultAddress(id, String(userId));
    return {
      code: 200,
      message: '默认地址设置成功',
      data: address,
    };
  }
}

