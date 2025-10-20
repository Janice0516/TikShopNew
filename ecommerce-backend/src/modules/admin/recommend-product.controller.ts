import { Controller, Get, Post, Put, Body, Param, Query, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth } from '@nestjs/swagger';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RecommendProductService } from './recommend-product.service';
import { UpdateRecommendProductDto, QueryRecommendProductsDto } from './dto/recommend-product.dto';

@ApiTags('推荐商品管理')
@Controller('admin/recommend-products')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class RecommendProductController {
  constructor(private readonly recommendProductService: RecommendProductService) {}

  @Get()
  @ApiOperation({ summary: '获取推荐商品列表' })
  @ApiResponse({ status: 200, description: '获取成功' })
  async getRecommendProducts(@Query() queryDto: QueryRecommendProductsDto) {
    try {
      const result = await this.recommendProductService.getRecommendProducts(queryDto);
      return {
        code: 200,
        message: '获取成功',
        data: result,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '获取推荐商品失败',
        data: null,
      };
    }
  }

  @Put(':id')
  @ApiOperation({ summary: '更新推荐商品设置' })
  @ApiResponse({ status: 200, description: '更新成功' })
  async updateRecommendProduct(
    @Param('id') id: number,
    @Body() updateDto: UpdateRecommendProductDto,
  ) {
    try {
      const result = await this.recommendProductService.updateRecommendProduct(id, updateDto);
      return {
        code: 200,
        message: '更新成功',
        data: result,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '更新推荐商品失败',
        data: null,
      };
    }
  }

  @Post('batch-update')
  @ApiOperation({ summary: '批量更新推荐商品设置' })
  @ApiResponse({ status: 200, description: '批量更新成功' })
  async batchUpdateRecommendProducts(
    @Body() body: { updates: Array<{ id: number; data: UpdateRecommendProductDto }> },
  ) {
    try {
      const result = await this.recommendProductService.batchUpdateRecommendProducts(body.updates);
      return {
        code: 200,
        message: '批量更新完成',
        data: result,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '批量更新失败',
        data: null,
      };
    }
  }

  @Get('stats')
  @ApiOperation({ summary: '获取推荐商品统计' })
  @ApiResponse({ status: 200, description: '获取成功' })
  async getRecommendStats() {
    try {
      const stats = await this.recommendProductService.getRecommendStats();
      return {
        code: 200,
        message: '获取成功',
        data: stats,
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '获取统计失败',
        data: null,
      };
    }
  }
}
