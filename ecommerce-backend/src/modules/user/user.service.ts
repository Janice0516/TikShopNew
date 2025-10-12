import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { JwtService } from '@nestjs/jwt';
import * as bcrypt from 'bcrypt';
import { User } from './entities/user.entity';
import { RegisterDto } from './dto/register.dto';
import { LoginDto } from './dto/login.dto';

@Injectable()
export class UserService {
  constructor(
    @InjectRepository(User)
    private userRepository: Repository<User>,
    private jwtService: JwtService,
  ) {}

  /**
   * 用户注册
   */
  async register(registerDto: RegisterDto) {
    const { phone, password, code } = registerDto;

    // TODO: 验证短信验证码（暂时跳过）
    // const isValidCode = await this.verifySmsCode(phone, code);
    // if (!isValidCode) {
    //   throw new HttpException('验证码错误或已过期', HttpStatus.BAD_REQUEST);
    // }

    // 检查手机号是否已注册
    const existUser = await this.userRepository.findOne({ where: { phone } });
    if (existUser) {
      throw new HttpException('该手机号已注册', HttpStatus.BAD_REQUEST);
    }

    // 密码加密
    const hashedPassword = await bcrypt.hash(password, 10);

    // 创建用户
    const user = this.userRepository.create({
      phone,
      password: hashedPassword,
      nickname: `用户${phone.slice(-4)}`,
    });

    await this.userRepository.save(user);

    // 生成Token
    const token = this.generateToken(user.id);

    return {
      token,
      userInfo: {
        id: user.id,
        phone: user.phone,
        nickname: user.nickname,
        avatar: user.avatar,
      },
    };
  }

  /**
   * 用户登录
   */
  async login(loginDto: LoginDto) {
    const { phone, password } = loginDto;

    // 查找用户
    const user = await this.userRepository.findOne({ where: { phone } });
    if (!user) {
      throw new HttpException('手机号或密码错误', HttpStatus.UNAUTHORIZED);
    }

    // 验证密码
    const isPasswordValid = await bcrypt.compare(password, user.password);
    if (!isPasswordValid) {
      throw new HttpException('手机号或密码错误', HttpStatus.UNAUTHORIZED);
    }

    // 检查用户状态
    if (user.status !== 1) {
      throw new HttpException('账号已被禁用', HttpStatus.FORBIDDEN);
    }

    // 更新最后登录时间
    await this.userRepository.update(user.id, {
      lastLoginTime: new Date(),
    });

    // 生成Token
    const token = this.generateToken(user.id);

    return {
      token,
      userInfo: {
        id: user.id,
        phone: user.phone,
        nickname: user.nickname,
        avatar: user.avatar,
      },
    };
  }

  /**
   * 发送短信验证码
   */
  async sendSmsCode(phone: string) {
    // TODO: 实现短信发送逻辑
    // 1. 生成6位验证码
    const code = Math.random().toString().slice(-6);
    
    // 2. 存储到Redis（5分钟过期）
    // await this.redis.set(`sms:${phone}`, code, 'EX', 300);
    
    // 3. 调用阿里云短信API发送
    // await this.sendSms(phone, code);

    console.log(`发送验证码到 ${phone}: ${code}`);

    return {
      message: '验证码发送成功',
      // 开发环境返回验证码（生产环境删除）
      code: process.env.NODE_ENV === 'development' ? code : undefined,
    };
  }

  /**
   * 根据ID查找用户
   */
  async findById(id: number) {
    const user = await this.userRepository.findOne({ where: { id: String(id) } });
    if (!user) {
      throw new HttpException('用户不存在', HttpStatus.NOT_FOUND);
    }
    
    // 不返回密码字段
    const { password, ...result } = user;
    return result;
  }

  /**
   * 生成JWT Token
   */
  private generateToken(userId: string): string {
    const payload = { userId, type: 'user' };
    return this.jwtService.sign(payload);
  }
}

