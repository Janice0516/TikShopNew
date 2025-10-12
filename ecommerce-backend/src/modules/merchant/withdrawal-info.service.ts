import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { MerchantWithdrawalInfo } from './entities/merchant-withdrawal-info.entity';
import { CreateWithdrawalInfoDto, UpdateWithdrawalInfoDto, SetDefaultWithdrawalInfoDto } from './dto/withdrawal-info.dto';

@Injectable()
export class WithdrawalInfoService {
  constructor(
    @InjectRepository(MerchantWithdrawalInfo)
    private withdrawalInfoRepository: Repository<MerchantWithdrawalInfo>,
  ) {}

  /**
   * 创建提款信息
   */
  async createWithdrawalInfo(merchantId: number, createWithdrawalInfoDto: CreateWithdrawalInfoDto) {
    const { withdrawalType, isDefault } = createWithdrawalInfoDto;

    // 验证必填字段
    this.validateWithdrawalInfo(createWithdrawalInfoDto);

    // 如果设置为默认，先取消其他默认设置
    if (isDefault) {
      await this.withdrawalInfoRepository.update(
        { merchantId: String(merchantId), isDefault: true },
        { isDefault: false }
      );
    }

    const withdrawalInfo = this.withdrawalInfoRepository.create({
      merchantId: String(merchantId),
      ...createWithdrawalInfoDto,
    });

    await this.withdrawalInfoRepository.save(withdrawalInfo);

    return {
      code: 200,
      message: '提款信息创建成功',
      data: withdrawalInfo,
    };
  }

  /**
   * 获取商户的提款信息列表
   */
  async getWithdrawalInfoList(merchantId: number) {
    const list = await this.withdrawalInfoRepository.find({
      where: { merchantId: String(merchantId) },
      order: { isDefault: 'DESC', createdAt: 'DESC' },
    });

    return {
      code: 200,
      message: '获取成功',
      data: list,
    };
  }

  /**
   * 获取提款信息详情
   */
  async getWithdrawalInfoDetail(id: number, merchantId: number) {
    const withdrawalInfo = await this.withdrawalInfoRepository.findOne({
      where: { id: String(id), merchantId: String(merchantId) },
    });

    if (!withdrawalInfo) {
      throw new NotFoundException('提款信息不存在');
    }

    return {
      code: 200,
      message: '获取成功',
      data: withdrawalInfo,
    };
  }

  /**
   * 更新提款信息
   */
  async updateWithdrawalInfo(id: number, merchantId: number, updateWithdrawalInfoDto: UpdateWithdrawalInfoDto) {
    const withdrawalInfo = await this.withdrawalInfoRepository.findOne({
      where: { id: String(id), merchantId: String(merchantId) },
    });

    if (!withdrawalInfo) {
      throw new NotFoundException('提款信息不存在');
    }

    const { isDefault } = updateWithdrawalInfoDto;

    // 如果设置为默认，先取消其他默认设置
    if (isDefault) {
      await this.withdrawalInfoRepository.update(
        { merchantId: String(merchantId), isDefault: true },
        { isDefault: false }
      );
    }

    // 验证更新后的数据
    const updatedData = { ...withdrawalInfo, ...updateWithdrawalInfoDto };
    this.validateWithdrawalInfo(updatedData);

    await this.withdrawalInfoRepository.update(id, updateWithdrawalInfoDto);

    const updatedWithdrawalInfo = await this.withdrawalInfoRepository.findOne({
      where: { id: String(id) },
    });

    return {
      code: 200,
      message: '提款信息更新成功',
      data: updatedWithdrawalInfo,
    };
  }

  /**
   * 删除提款信息
   */
  async deleteWithdrawalInfo(id: number, merchantId: number) {
    const withdrawalInfo = await this.withdrawalInfoRepository.findOne({
      where: { id: String(id), merchantId: String(merchantId) },
    });

    if (!withdrawalInfo) {
      throw new NotFoundException('提款信息不存在');
    }

    await this.withdrawalInfoRepository.delete(id);

    return {
      code: 200,
      message: '提款信息删除成功',
    };
  }

  /**
   * 设置默认提款信息
   */
  async setDefaultWithdrawalInfo(merchantId: number, setDefaultDto: SetDefaultWithdrawalInfoDto) {
    const { id } = setDefaultDto;

    const withdrawalInfo = await this.withdrawalInfoRepository.findOne({
      where: { id: String(id), merchantId: String(merchantId) },
    });

    if (!withdrawalInfo) {
      throw new NotFoundException('提款信息不存在');
    }

    // 先取消所有默认设置
    await this.withdrawalInfoRepository.update(
      { merchantId: String(merchantId), isDefault: true },
      { isDefault: false }
    );

    // 设置新的默认
    await this.withdrawalInfoRepository.update(id, { isDefault: true });

    return {
      code: 200,
      message: '默认提款信息设置成功',
    };
  }

  /**
   * 获取马来西亚银行列表
   */
  async getMalaysianBanks() {
    const banks = [
      { code: 'MBBEMYKL', name: 'Maybank', nameCn: '马来亚银行' },
      { code: 'CITIMYKX', name: 'Citibank', nameCn: '花旗银行' },
      { code: 'HLBBMYKL', name: 'Hong Leong Bank', nameCn: '丰隆银行' },
      { code: 'RHBAMYKL', name: 'RHB Bank', nameCn: '兴业银行' },
      { code: 'PBBEMYKL', name: 'Public Bank', nameCn: '大众银行' },
      { code: 'AMBKMYKL', name: 'AmBank', nameCn: '大马银行' },
      { code: 'CIMBMYKL', name: 'CIMB Bank', nameCn: '联昌银行' },
      { code: 'OCBCMYKL', name: 'OCBC Bank', nameCn: '华侨银行' },
      { code: 'UOVBMYKL', name: 'UOB Bank', nameCn: '大华银行' },
      { code: 'AFFBMYKL', name: 'Affin Bank', nameCn: '艾芬银行' },
    ];

    return {
      code: 200,
      message: '获取成功',
      data: banks,
    };
  }

  /**
   * 获取马来西亚电子钱包列表
   */
  async getMalaysianWallets() {
    const wallets = [
      { code: 'touchngo', name: 'TouchnGo eWallet', nameCn: '一触即通电子钱包' },
      { code: 'grabpay', name: 'GrabPay', nameCn: 'GrabPay' },
      { code: 'boost', name: 'Boost', nameCn: 'Boost' },
      { code: 'bigpay', name: 'BigPay', nameCn: 'BigPay' },
      { code: 'fave', name: 'Fave', nameCn: 'Fave' },
      { code: 'shopee', name: 'ShopeePay', nameCn: '虾皮支付' },
      { code: 'lazada', name: 'Lazada Wallet', nameCn: 'Lazada钱包' },
    ];

    return {
      code: 200,
      message: '获取成功',
      data: wallets,
    };
  }

  /**
   * 验证提款信息
   */
  private validateWithdrawalInfo(data: any) {
    const { withdrawalType } = data;

    switch (withdrawalType) {
      case 1: // 银行转账
        if (!data.bankName || !data.accountHolderName || !data.accountNumber) {
          throw new BadRequestException('银行转账方式需要填写银行名称、账户持有人姓名和账户号码');
        }
        break;
      case 2: // 电子钱包
        if (!data.walletType || !data.walletAccount) {
          throw new BadRequestException('电子钱包方式需要填写钱包类型和钱包账户');
        }
        break;
      case 3: // 现金提取
        if (!data.address || !data.city || !data.state) {
          throw new BadRequestException('现金提取方式需要填写完整地址信息');
        }
        break;
      default:
        throw new BadRequestException('无效的提款方式');
    }
  }
}

