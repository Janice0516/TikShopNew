import { Controller, Get, Query } from '@nestjs/common';
import { ApiTags, ApiOperation } from '@nestjs/swagger';
import { PublicShopService } from './public-shop.service';
import { QueryShopProductDto } from './dto/query-shop-product.dto';

@ApiTags('公开商城接口')
@Controller('shop')
export class PublicShopController {
  constructor(private readonly publicShopService: PublicShopService) {}

  @Get('products')
  @ApiOperation({ summary: '获取商城商品列表（所有商家上架的商品）' })
  async getShopProducts(@Query() queryDto: QueryShopProductDto) {
    return await this.publicShopService.getShopProducts(queryDto);
  }

  @Get('categories')
  @ApiOperation({ summary: '获取商品分类列表' })
  async getCategories() {
    return await this.publicShopService.getCategories();
  }

  @Get('top-deals')
  @ApiOperation({ summary: '获取Top Deals商品列表' })
  async getTopDeals(@Query('limit') limit?: number) {
    try {
      const result = await this.publicShopService.getTopDeals(limit || 10);
      return {
        code: 200,
        message: '获取成功',
        list: result,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '获取Top Deals失败',
        list: [],
      };
    }
  }

  @Get('popular-items')
  @ApiOperation({ summary: '获取热门商品列表' })
  async getPopularItems(@Query('limit') limit?: number) {
    try {
      const result = await this.publicShopService.getPopularItems(limit || 10);
      return {
        code: 200,
        message: '获取成功',
        list: result,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '获取热门商品失败',
        list: [],
      };
    }
  }
}
