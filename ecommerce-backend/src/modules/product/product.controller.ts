import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  Query,
  UseGuards,
  Put,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { ProductService } from './product.service';
import { CreateProductDto } from './dto/create-product.dto';
import { UpdateProductDto } from './dto/update-product.dto';
import { QueryProductDto } from './dto/query-product.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { ProductValidationService } from '../../services/product-validation.service';

@ApiTags('商品模块')
@Controller('products')
export class ProductController {
  constructor(
    private readonly productService: ProductService,
    private readonly validationService: ProductValidationService
  ) {}

  @Post()
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '创建商品（平台管理员）' })
  create(@Body() createProductDto: CreateProductDto) {
    return this.productService.create(createProductDto);
  }

  @Get()
  @ApiOperation({ summary: '商品列表（支持分页、筛选、搜索）' })
  findAll(@Query() queryDto: QueryProductDto) {
    return this.productService.findAll(queryDto);
  }

  @Get('categories')
  @ApiOperation({ summary: '商品分类列表（树形结构）' })
  findAllCategories() {
    return this.productService.findAllCategories();
  }

  @Get(':id')
  @ApiOperation({ summary: '商品详情' })
  findOne(@Param('id') id: string) {
    return this.productService.findOne(id);
  }

  @Get('admin/:id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '商品详情（管理后台）' })
  findOneDirect(@Param('id') id: string) {
    return this.productService.findOneDirect(id);
  }

  @Get('shop/:id')
  @ApiOperation({ summary: '商品详情（用户端）' })
  findOneByMerchantProduct(@Param('id') id: string) {
    return this.productService.findOneByMerchantProduct(id);
  }

  @Put(':id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '更新商品' })
  update(@Param('id') id: string, @Body() updateProductDto: UpdateProductDto) {
    return this.productService.update(+id, updateProductDto);
  }

  @Delete(':id')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '删除商品（软删除）' })
  remove(@Param('id') id: string) {
    return this.productService.remove(+id);
  }

  @Patch(':id/status')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '上架/下架商品' })
  updateStatus(@Param('id') id: string, @Body('status') status: number) {
    return this.productService.updateStatus(+id, status);
  }

  @Post('validate-prices')
  @ApiOperation({ summary: '验证商品价格' })
  validatePrices(@Body() priceData: { costPrice: number; suggestPrice: number; salePrice?: number }) {
    const result = this.validationService.validateProductPrices(
      priceData.costPrice,
      priceData.suggestPrice,
      priceData.salePrice
    );
    return result;
  }

  @Post('price-suggestions')
  @ApiOperation({ summary: '获取价格建议' })
  getPriceSuggestions(@Body() data: { costPrice: number }) {
    return this.validationService.getPriceSuggestions(data.costPrice);
  }

  @Patch(':id/stock')
  @UseGuards(JwtAuthGuard)
  @ApiBearerAuth()
  @ApiOperation({ summary: '更新库存' })
  updateStock(
    @Param('id') id: string,
    @Body('quantity') quantity: number,
  ) {
    return this.productService.updateStock(id, quantity);
  }
}

