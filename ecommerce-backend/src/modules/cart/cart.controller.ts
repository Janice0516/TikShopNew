import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  UseGuards,
  Request,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiBearerAuth } from '@nestjs/swagger';
import { CartService } from './cart.service';
import { AddCartDto } from './dto/add-cart.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@ApiTags('购物车模块')
@Controller('cart')
@UseGuards(JwtAuthGuard)
@ApiBearerAuth()
export class CartController {
  constructor(private readonly cartService: CartService) {}

  @Post()
  @ApiOperation({ summary: '添加到购物车' })
  add(@Request() req, @Body() addCartDto: AddCartDto) {
    return this.cartService.add(req.user.userId, addCartDto);
  }

  @Get()
  @ApiOperation({ summary: '获取购物车列表' })
  findAll(@Request() req) {
    return this.cartService.findAll(req.user.userId);
  }

  @Patch(':id/quantity')
  @ApiOperation({ summary: '更新购物车数量' })
  updateQuantity(
    @Param('id') id: string,
    @Request() req,
    @Body('quantity') quantity: number,
  ) {
    return this.cartService.updateQuantity(+id, req.user.userId, quantity);
  }

  @Patch(':id/selected')
  @ApiOperation({ summary: '切换选中状态' })
  toggleSelected(
    @Param('id') id: string,
    @Request() req,
    @Body('selected') selected: number,
  ) {
    return this.cartService.toggleSelected(+id, req.user.userId, selected);
  }

  @Delete(':id')
  @ApiOperation({ summary: '删除购物车项' })
  remove(@Param('id') id: string, @Request() req) {
    return this.cartService.remove(+id, req.user.userId);
  }

  @Delete()
  @ApiOperation({ summary: '清空购物车' })
  clear(@Request() req) {
    return this.cartService.clear(req.user.userId);
  }
}

