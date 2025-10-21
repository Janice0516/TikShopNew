import { Controller, Get, Param, Query } from '@nestjs/common';
import { ApiTags, ApiOperation } from '@nestjs/swagger';
import { PublicShopService } from './public-shop.service';

@ApiTags('公开商家店铺接口')
@Controller('shop/merchant')
export class PublicMerchantController {
  constructor(private readonly publicShopService: PublicShopService) {}

  @Get(':id')
  @ApiOperation({ summary: '获取商家店铺信息' })
  async getMerchantShop(@Param('id') merchantId: string) {
    try {
      const result = await this.publicShopService.getMerchantShopInfo(merchantId);
      return {
        code: 200,
        message: '获取成功',
        data: result,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '获取商家店铺信息失败',
        data: null,
      };
    }
  }

  @Get(':id/products')
  @ApiOperation({ summary: '获取商家商品列表' })
  async getMerchantProducts(
    @Param('id') merchantId: string,
    @Query('page') page: number = 1,
    @Query('pageSize') pageSize: number = 20,
    @Query('categoryId') categoryId?: string,
    @Query('keyword') keyword?: string
  ) {
    try {
      const result = await this.publicShopService.getMerchantProducts(
        merchantId,
        page,
        pageSize,
        categoryId,
        keyword
      );
      return {
        code: 200,
        message: '获取成功',
        data: result,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '获取商家商品失败',
        data: null,
      };
    }
  }

  @Get(':id/stats')
  @ApiOperation({ summary: '获取商家统计信息' })
  async getMerchantStats(@Param('id') merchantId: string) {
    try {
      const result = await this.publicShopService.getMerchantStats(merchantId);
      return {
        code: 200,
        message: '获取成功',
        data: result,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '获取商家统计信息失败',
        data: null,
      };
    }
  }
}
