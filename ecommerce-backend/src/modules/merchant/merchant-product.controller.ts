import {
  Controller,
  Get,
  Post,
  Patch,
  Delete,
  Body,
  Param,
  Query,
  UseGuards,
  Request,
  ParseIntPipe,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { MerchantProductService } from './merchant-product.service';
import { SelectProductDto, UpdateProductPriceDto, UpdateProductStatusDto, QueryMerchantProductDto } from './dto/merchant-product.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('商家商品管理')
@Controller('merchant/products')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class MerchantProductController {
  constructor(private readonly merchantProductService: MerchantProductService) {}

  @Post()
  @ApiOperation({ summary: '商家选品' })
  async selectProduct(@Request() req: any, @Body() selectProductDto: SelectProductDto) {
    const merchantId = req.user.merchantId || req.user.id;
    return await this.merchantProductService.selectProduct(merchantId, selectProductDto);
  }

  @Get()
  @ApiOperation({ summary: '获取商家商品列表' })
  async getMerchantProducts(@Request() req: any, @Query() queryDto: QueryMerchantProductDto) {
    const merchantId = req.user.merchantId || req.user.id;
    return await this.merchantProductService.getMerchantProducts(merchantId, queryDto);
  }

  @Patch(':id/price')
  @ApiOperation({ summary: '更新商品价格' })
  async updateProductPrice(
    @Request() req: any,
    @Param('id', ParseIntPipe) merchantProductId: number,
    @Body() updateDto: UpdateProductPriceDto
  ) {
    const merchantId = req.user.merchantId || req.user.id;
    return await this.merchantProductService.updateProductPrice(merchantId, merchantProductId, updateDto);
  }

  @Patch(':id/status')
  @ApiOperation({ summary: '更新商品状态（上下架）' })
  async updateProductStatus(
    @Request() req: any,
    @Param('id', ParseIntPipe) merchantProductId: number,
    @Body() updateDto: UpdateProductStatusDto
  ) {
    const merchantId = req.user.merchantId || req.user.id;
    return await this.merchantProductService.updateProductStatus(merchantId, merchantProductId, updateDto);
  }

  @Delete(':id')
  @ApiOperation({ summary: '删除商家商品' })
  async deleteMerchantProduct(
    @Request() req: any,
    @Param('id', ParseIntPipe) merchantProductId: number
  ) {
    const merchantId = req.user.merchantId || req.user.id;
    return await this.merchantProductService.deleteMerchantProduct(merchantId, merchantProductId);
  }
}
