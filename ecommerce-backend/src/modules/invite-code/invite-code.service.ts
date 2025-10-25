import { Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { InviteCode } from './entities/invite-code.entity';
import { CreateInviteCodeDto, ValidateInviteCodeDto, QueryInviteCodeDto } from './dto/invite-code.dto';

@Injectable()
export class InviteCodeService {
  constructor(
    @InjectRepository(InviteCode)
    private inviteCodeRepository: Repository<InviteCode>,
  ) {}

  /**
   * 生成邀请码
   */
  private generateInviteCode(): string {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let result = '';
    for (let i = 0; i < 12; i++) {
      result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
  }

  /**
   * 创建邀请码
   */
  async createInviteCode(createDto: CreateInviteCodeDto) {
    // 生成唯一邀请码
    let inviteCode: string;
    let isUnique = false;
    let attempts = 0;
    const maxAttempts = 10;

    while (!isUnique && attempts < maxAttempts) {
      inviteCode = this.generateInviteCode();
      const existingCode = await this.inviteCodeRepository.findOne({
        where: { inviteCode },
      });
      if (!existingCode) {
        isUnique = true;
      }
      attempts++;
    }

    if (!isUnique) {
      throw new HttpException('生成邀请码失败，请重试', HttpStatus.INTERNAL_SERVER_ERROR);
    }

    const inviteCodeEntity = this.inviteCodeRepository.create({
      inviteCode,
      ...createDto,
      expireTime: createDto.expireTime ? new Date(createDto.expireTime) : null,
      status: 1, // 默认启用
    });

    const savedCode = await this.inviteCodeRepository.save(inviteCodeEntity);
    return savedCode;
  }

  /**
   * 验证邀请码
   */
  async validateInviteCode(validateDto: ValidateInviteCodeDto) {
    const { inviteCode } = validateDto;

    const code = await this.inviteCodeRepository.findOne({
      where: { inviteCode },
    });

    if (!code) {
      throw new HttpException('邀请码不存在', HttpStatus.NOT_FOUND);
    }

    if (code.status === 0) {
      throw new HttpException('邀请码已禁用', HttpStatus.BAD_REQUEST);
    }

    if (code.expireTime && new Date() > code.expireTime) {
      throw new HttpException('邀请码已过期', HttpStatus.BAD_REQUEST);
    }

    if (code.maxUsage > 0 && code.usedCount >= code.maxUsage) {
      throw new HttpException('邀请码使用次数已达上限', HttpStatus.BAD_REQUEST);
    }

    return {
      valid: true,
      salespersonName: code.salespersonName,
      salespersonPhone: code.salespersonPhone,
      salespersonId: code.salespersonId,
      usedCount: code.usedCount,
      maxUsage: code.maxUsage,
    };
  }

  /**
   * 使用邀请码（增加使用次数）
   */
  async useInviteCode(inviteCode: string) {
    const code = await this.inviteCodeRepository.findOne({
      where: { inviteCode },
    });

    if (!code) {
      throw new HttpException('邀请码不存在', HttpStatus.NOT_FOUND);
    }

    // 增加使用次数
    code.usedCount += 1;
    await this.inviteCodeRepository.save(code);

    return {
      salespersonName: code.salespersonName,
      salespersonPhone: code.salespersonPhone,
      salespersonId: code.salespersonId,
    };
  }

  /**
   * 查询邀请码列表
   */
  async queryInviteCodes(queryDto: QueryInviteCodeDto) {
    const { page = 1, limit = 10, inviteCode, salespersonName, status } = queryDto;
    const skip = (page - 1) * limit;

    const queryBuilder = this.inviteCodeRepository.createQueryBuilder('inviteCode');

    if (inviteCode) {
      queryBuilder.andWhere('inviteCode.inviteCode LIKE :inviteCode', {
        inviteCode: `%${inviteCode}%`,
      });
    }

    if (salespersonName) {
      queryBuilder.andWhere('inviteCode.salespersonName LIKE :salespersonName', {
        salespersonName: `%${salespersonName}%`,
      });
    }

    if (status !== undefined) {
      queryBuilder.andWhere('inviteCode.status = :status', { status });
    }

    const [items, total] = await queryBuilder
      .orderBy('inviteCode.createTime', 'DESC')
      .skip(skip)
      .take(limit)
      .getManyAndCount();

    return {
      items,
      total,
      page,
      limit,
      totalPages: Math.ceil(total / limit),
    };
  }

  /**
   * 获取邀请码详情
   */
  async getInviteCodeById(id: string) {
    const code = await this.inviteCodeRepository.findOne({
      where: { id },
    });

    if (!code) {
      throw new HttpException('邀请码不存在', HttpStatus.NOT_FOUND);
    }

    return code;
  }

  /**
   * 更新邀请码状态
   */
  async updateInviteCodeStatus(id: string, status: number) {
    const code = await this.inviteCodeRepository.findOne({
      where: { id },
    });

    if (!code) {
      throw new HttpException('邀请码不存在', HttpStatus.NOT_FOUND);
    }

    code.status = status;
    await this.inviteCodeRepository.save(code);

    return code;
  }

  /**
   * 删除邀请码
   */
  async deleteInviteCode(id: string) {
    const code = await this.inviteCodeRepository.findOne({
      where: { id },
    });

    if (!code) {
      throw new HttpException('邀请码不存在', HttpStatus.NOT_FOUND);
    }

    await this.inviteCodeRepository.remove(code);
    return { message: '删除成功' };
  }

  /**
   * 获取邀请码统计
   */
  async getInviteCodeStats() {
    const total = await this.inviteCodeRepository.count();
    const active = await this.inviteCodeRepository.count({ where: { status: 1 } });
    const disabled = await this.inviteCodeRepository.count({ where: { status: 0 } });
    
    const totalUsed = await this.inviteCodeRepository
      .createQueryBuilder('inviteCode')
      .select('SUM(inviteCode.usedCount)', 'totalUsed')
      .getRawOne();

    return {
      total,
      active,
      disabled,
      totalUsed: parseInt(totalUsed.totalUsed) || 0,
    };
  }
}
