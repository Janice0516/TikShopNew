const { DataSource } = require('typeorm');
const { Merchant } = require('./dist/modules/merchant/entities/merchant.entity');

async function debugMerchant() {
  const AppDataSource = new DataSource({
    type: 'mysql',
    host: 'localhost',
    port: 3306,
    username: 'root',
    password: '',
    database: 'ecommerce',
    entities: [Merchant],
    synchronize: false,
    logging: true,
  });

  try {
    await AppDataSource.initialize();
    console.log('✅ 数据库连接成功');

    const merchantRepository = AppDataSource.getRepository(Merchant);
    
    // 测试创建商家
    const testMerchant = {
      username: 'debugmerchant001',
      password: 'hashedpassword123',
      merchantName: '调试商家001',
      contactName: '调试用户',
      contactPhone: '012-3456789',
      status: 0
    };

    console.log('🧪 测试创建商家...');
    const merchant = merchantRepository.create(testMerchant);
    const savedMerchant = await merchantRepository.save(merchant);
    console.log('✅ 商家创建成功:', savedMerchant);

    // 查询商家列表
    console.log('📋 查询商家列表...');
    const merchants = await merchantRepository.find({
      select: ['id', 'username', 'merchantName', 'contactName', 'contactPhone', 'status', 'createTime']
    });
    console.log('📋 商家列表:', merchants);

  } catch (error) {
    console.error('❌ 错误:', error);
  } finally {
    if (AppDataSource.isInitialized) {
      await AppDataSource.destroy();
    }
  }
}

debugMerchant();
