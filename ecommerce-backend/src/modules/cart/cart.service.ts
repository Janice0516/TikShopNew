import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Cart } from './entities/cart.entity';
import { AddCartDto } from './dto/add-cart.dto';
import { ProductService } from '../product/product.service';

@Injectable()
export class CartService {
  constructor(
    @InjectRepository(Cart)
    private cartRepository: Repository<Cart>,
    private productService: ProductService,
  ) {}

  /**
   * 添加到购物车
   */
  async add(userId: number, addCartDto: AddCartDto) {
    const { productId, skuId, quantity } = addCartDto;

    // 验证商品是否存在
    const product = await this.productService.findOne(productId);
    if (product.status !== 1) {
      throw new HttpException('商品已下架', HttpStatus.BAD_REQUEST);
    }

    // 检查购物车中是否已存在该商品
    const existCart = await this.cartRepository.findOne({
      where: {
        userId,
        productId,
        skuId: skuId || null,
      },
    });

    if (existCart) {
      // 如果已存在，更新数量
      const newQuantity = existCart.quantity + quantity;
      if (newQuantity > product.stock) {
        throw new HttpException('库存不足', HttpStatus.BAD_REQUEST);
      }
      await this.cartRepository.update(existCart.id, {
        quantity: newQuantity,
      });
      return { message: '已更新购物车数量' };
    } else {
      // 否则，新增购物车项
      if (quantity > product.stock) {
        throw new HttpException('库存不足', HttpStatus.BAD_REQUEST);
      }

      // TODO: 获取merchantId，暂时用商品ID
      const cart = this.cartRepository.create({
        userId,
        merchantId: 1, // 暂时写死
        productId,
        skuId: skuId || null,
        quantity,
        selected: 1,
      });

      await this.cartRepository.save(cart);
      return { message: '已添加到购物车' };
    }
  }

  /**
   * 获取购物车列表
   */
  async findAll(userId: number) {
    const cartItems = await this.cartRepository
      .createQueryBuilder('cart')
      .leftJoinAndSelect('platform_product', 'product', 'cart.product_id = product.id')
      .where('cart.user_id = :userId', { userId })
      .select([
        'cart.id as id',
        'cart.product_id as productId',
        'cart.sku_id as skuId',
        'cart.quantity as quantity',
        'cart.selected as selected',
        'product.name as productName',
        'product.main_image as mainImage',
        'product.cost_price as costPrice',
        'product.stock as stock',
        'product.status as status',
      ])
      .getRawMany();

    // 计算总价
    const totalAmount = cartItems
      .filter((item) => item.selected === 1 && item.status === 1)
      .reduce((sum, item) => sum + item.costPrice * item.quantity, 0);

    return {
      list: cartItems,
      totalAmount: Number(totalAmount.toFixed(2)),
    };
  }

  /**
   * 更新购物车数量
   */
  async updateQuantity(id: number, userId: number, quantity: number) {
    const cart = await this.cartRepository.findOne({
      where: { id, userId },
    });
    if (!cart) {
      throw new HttpException('购物车项不存在', HttpStatus.NOT_FOUND);
    }

    if (quantity < 1) {
      throw new HttpException('数量至少为1', HttpStatus.BAD_REQUEST);
    }

    // 验证库存
    const product = await this.productService.findOne(cart.productId);
    if (quantity > product.stock) {
      throw new HttpException('库存不足', HttpStatus.BAD_REQUEST);
    }

    await this.cartRepository.update(id, { quantity });
    return { message: '更新成功' };
  }

  /**
   * 切换选中状态
   */
  async toggleSelected(id: number, userId: number, selected: number) {
    const cart = await this.cartRepository.findOne({
      where: { id, userId },
    });
    if (!cart) {
      throw new HttpException('购物车项不存在', HttpStatus.NOT_FOUND);
    }

    await this.cartRepository.update(id, { selected });
    return { message: '更新成功' };
  }

  /**
   * 删除购物车项
   */
  async remove(id: number, userId: number) {
    const cart = await this.cartRepository.findOne({
      where: { id, userId },
    });
    if (!cart) {
      throw new HttpException('购物车项不存在', HttpStatus.NOT_FOUND);
    }

    await this.cartRepository.delete(id);
    return { message: '删除成功' };
  }

  /**
   * 清空购物车
   */
  async clear(userId: number) {
    await this.cartRepository.delete({ userId });
    return { message: '购物车已清空' };
  }
}

