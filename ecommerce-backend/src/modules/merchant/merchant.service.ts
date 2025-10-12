import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { JwtService } from '@nestjs/jwt';
import * as bcrypt from 'bcrypt';
import { Merchant } from './entities/merchant.entity';
import { RegisterMerchantDto } from './dto/register-merchant.dto';
import { LoginMerchantDto } from './dto/login-merchant.dto';
import { UidGenerator } from '../../common/utils/uid-generator.util';

@Injectable()
export class MerchantService {
  constructor(
    @InjectRepository(Merchant)
    private merchantRepository: Repository<Merchant>,
    private jwtService: JwtService,
  ) {}

  /**
   * 商家注册
   */
  async register(registerDto: RegisterMerchantDto) {
    const { username, password, ...rest } = registerDto;

    // 检查用户名是否已存在
    const existMerchant = await this.merchantRepository.findOne({
      where: { username },
    });
    if (existMerchant) {
      throw new HttpException('该账号已被注册', HttpStatus.BAD_REQUEST);
    }

    // 生成唯一的商家UID
    let merchantUid: string;
    let isUnique = false;
    let attempts = 0;
    const maxAttempts = 10;

    while (!isUnique && attempts < maxAttempts) {
      merchantUid = UidGenerator.generateMerchantUid();
      const existingUid = await this.merchantRepository.findOne({
        where: { merchantUid },
      });
      if (!existingUid) {
        isUnique = true;
      }
      attempts++;
    }

    if (!isUnique) {
      throw new HttpException('生成商家标识失败，请重试', HttpStatus.INTERNAL_SERVER_ERROR);
    }

    // 密码加密
    const hashedPassword = await bcrypt.hash(password, 10);

    // 创建商家（默认待审核状态）
    const merchant = this.merchantRepository.create({
      username,
      password: hashedPassword,
      merchantUid,
      ...rest,
      status: 0, // 待审核
    });

    await this.merchantRepository.save(merchant);

    return {
      message: '注册成功，请等待审核',
      merchantId: merchant.id,
      merchantUid: merchant.merchantUid,
    };
  }

  /**
   * 商家登录
   */
  async login(loginDto: LoginMerchantDto) {
    const { username, password } = loginDto;

    // 查找商家
    const merchant = await this.merchantRepository.findOne({
      where: { username },
    });
    if (!merchant) {
      throw new HttpException('账号或密码错误', HttpStatus.UNAUTHORIZED);
    }

    // 验证密码
    const isPasswordValid = await bcrypt.compare(password, merchant.password);
    if (!isPasswordValid) {
      throw new HttpException('账号或密码错误', HttpStatus.UNAUTHORIZED);
    }

    // 检查状态 - 允许审核中的商家登录
    if (merchant.status === 2) {
      throw new HttpException(
        `账号审核未通过：${merchant.rejectReason || '请联系客服'}`,
        HttpStatus.FORBIDDEN,
      );
    }
    if (merchant.status === 3) {
      throw new HttpException('账号已被禁用', HttpStatus.FORBIDDEN);
    }

    // 生成Token
    const token = this.generateToken(merchant.id);

    const { password: _, ...merchantInfo } = merchant;

    return {
      token,
      merchantInfo,
    };
  }

  /**
   * 获取商家信息
   */
  async findById(id: number) {
    const merchant = await this.merchantRepository.findOne({ where: { id: String(id) } });
    if (!merchant) {
      throw new HttpException('商家不存在', HttpStatus.NOT_FOUND);
    }

    const { password, ...result } = merchant;
    return result;
  }

  /**
   * 更新店铺信息
   */
  async updateShop(id: number, shopData: any) {
    await this.findById(id);
    await this.merchantRepository.update(id, shopData);
    return await this.findById(id);
  }

  /**
   * 简化查询商家列表（平台管理用）
   */
  async findAllSimple() {
    try {
      console.log('执行简化查询...');
      
      // 直接查询所有商家
      const merchants = await this.merchantRepository.find({
        order: { id: 'DESC' },
      });

      console.log('查询到的商家数量:', merchants.length);

      return merchants;
    } catch (error) {
      console.error('简化商家列表查询错误:', error);
      throw error;
    }
  }

  /**
   * 查询商家列表（平台管理用）
   */
  async findAll(queryDto: any) {
    try {
      const { page = 1, pageSize = 10, status, username, merchantName, contactName, contactPhone } = queryDto;
      
      console.log('查询参数:', queryDto);
      
      // 构建查询条件
      const where: any = {};
      
      // 状态筛选
      if (status !== undefined && status !== null && status !== 'all') {
        where.status = status;
      }
      
      // 用户名搜索 - 使用模糊搜索
      if (username && username.trim()) {
        where.username = username.trim();
      }
      
      // 商家名称搜索 - 使用模糊搜索
      if (merchantName && merchantName.trim()) {
        where.merchantName = merchantName.trim();
      }
      
      // 联系人搜索 - 使用模糊搜索
      if (contactName && contactName.trim()) {
        where.contactName = contactName.trim();
      }
      
      // 联系电话搜索 - 使用模糊搜索
      if (contactPhone && contactPhone.trim()) {
        where.contactPhone = contactPhone.trim();
      }

      console.log('查询条件:', where);

      // 查询数据
      const [merchants, total] = await this.merchantRepository.findAndCount({
        where,
        skip: (page - 1) * pageSize,
        take: pageSize,
        order: { id: 'DESC' },
      });

      console.log('查询到的商家数量:', merchants.length);

      // 格式化返回数据
      const list = merchants.map(merchant => ({
        id: merchant.id,
        merchantUid: merchant.merchantUid,
        username: merchant.username,
        merchantName: merchant.merchantName,
        contactName: merchant.contactName,
        contactPhone: merchant.contactPhone,
        shopName: merchant.shopName,
        status: merchant.status,
        createTime: merchant.createTime,
      }));

      return { list, total, page, pageSize };
    } catch (error) {
      console.error('商家列表查询错误:', error);
      throw error;
    }
  }

  /**
   * 审核商家（平台管理）
   */
  async audit(id: number, status: number, rejectReason?: string) {
    const merchant = await this.findById(id);

    if (merchant.status !== 0) {
      throw new HttpException('该商家已审核', HttpStatus.BAD_REQUEST);
    }

    if (![1, 2].includes(status)) {
      throw new HttpException('状态值错误', HttpStatus.BAD_REQUEST);
    }

    if (status === 2 && !rejectReason) {
      throw new HttpException('请填写拒绝原因', HttpStatus.BAD_REQUEST);
    }

    await this.merchantRepository.update(id, {
      status,
      rejectReason: status === 2 ? rejectReason : null,
      shopName: status === 1 ? merchant.merchantName : null,
    });

    return { message: status === 1 ? '审核通过' : '已拒绝' };
  }

  /**
   * 更新商家信息（平台管理）
   */
  async updateMerchant(id: number, updateData: any) {
    const merchant = await this.findById(id);

    // 构建更新数据
    const updateFields: any = {};
    
    if (updateData.merchantName !== undefined) {
      updateFields.merchantName = updateData.merchantName;
    }
    if (updateData.shopName !== undefined) {
      updateFields.shopName = updateData.shopName;
    }
    if (updateData.contactName !== undefined) {
      updateFields.contactName = updateData.contactName;
    }
    if (updateData.contactPhone !== undefined) {
      updateFields.contactPhone = updateData.contactPhone;
    }
    if (updateData.status !== undefined) {
      updateFields.status = updateData.status;
    }

    await this.merchantRepository.update(id, updateFields);

    return { message: '商家信息更新成功' };
  }

  /**
   * 重置商家密码（平台管理）
   */
  async resetPassword(id: number, newPassword: string) {
    const merchant = await this.findById(id);

    // 加密新密码
    const hashedPassword = await bcrypt.hash(newPassword, 10);

    await this.merchantRepository.update(id, {
      password: hashedPassword,
    });

    return { message: '密码重置成功' };
  }

  /**
   * 生成JWT Token
   */
  private generateToken(merchantId: string): string {
    const payload = { merchantId: merchantId, type: 'merchant' };
    return this.jwtService.sign(payload);
  }
}

