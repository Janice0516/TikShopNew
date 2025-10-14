import { Controller, Post, Body } from '@nestjs/common';
import { ApiTags, ApiOperation } from '@nestjs/swagger';
import { ProductService } from './product.service';
import { CreateProductDto } from './dto/create-product.dto';

@ApiTags('公开商品管理')
@Controller('public-products')
export class PublicProductController {
  constructor(private readonly productService: ProductService) {}

  @Post()
  @ApiOperation({ summary: '创建商品（公开API，用于数据初始化）' })
  async createProduct(@Body() createProductDto: CreateProductDto) {
    try {
      const result = await this.productService.create(createProductDto);
      return {
        code: 200,
        message: '商品创建成功',
        data: result
      };
    } catch (error) {
      return {
        code: 500,
        message: error.message || '商品创建失败',
        data: null
      };
    }
  }
}
