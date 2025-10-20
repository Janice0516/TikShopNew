const mysql = require('mysql2/promise');

// 数据库配置
const dbConfig = {
  host: '127.0.0.1',
  port: 3306,
  user: 'tikshop',
  password: 'TikShop_MySQL_#2025!9pQwXz',
  database: 'ecommerce'
};

// 商家类型和对应的商品分类
const merchantTypes = [
  {
    type: 'electronics',
    name: '电子产品',
    categories: [1, 2, 3], // 电子产品、手机数码、电脑办公
    shopNames: ['科技数码城', '智能生活馆', '电子世界', '数码先锋', '科技前沿']
  },
  {
    type: 'fashion',
    name: '时尚服装',
    categories: [4, 5, 6], // 服装鞋帽、箱包配饰、珠宝首饰
    shopNames: ['时尚潮流', '精品服饰', '潮流前线', '时尚达人', '美衣阁']
  },
  {
    type: 'home',
    name: '家居生活',
    categories: [7, 8, 9], // 家居家装、厨具餐具、母婴用品
    shopNames: ['温馨家居', '生活美学', '居家好物', '品质生活', '家居优选']
  },
  {
    type: 'beauty',
    name: '美妆护肤',
    categories: [10, 11], // 美妆护肤、个护清洁
    shopNames: ['美丽工坊', '护肤专家', '美妆达人', '美丽人生', '护肤小铺']
  },
  {
    type: 'sports',
    name: '运动户外',
    categories: [12, 13], // 运动户外、汽车用品
    shopNames: ['运动天地', '户外探险', '健身达人', '运动先锋', '活力无限']
  },
  {
    type: 'books',
    name: '图书文具',
    categories: [14, 15], // 图书音像、办公文具
    shopNames: ['书香门第', '知识宝库', '文具世界', '阅读时光', '学习天地']
  },
  {
    type: 'food',
    name: '食品饮料',
    categories: [16, 17], // 食品饮料、生鲜食品
    shopNames: ['美食天地', '味蕾工坊', '新鲜生活', '美食达人', '味觉享受']
  },
  {
    type: 'health',
    name: '健康保健',
    categories: [18, 19], // 健康保健、宠物用品
    shopNames: ['健康生活', '养生专家', '健康守护', '活力健康', '养生小铺']
  }
];

// 生成商家数据
async function generateMerchants() {
  const connection = await mysql.createConnection(dbConfig);
  
  try {
    console.log('🚀 开始生成50个商家...');
    
    // 获取所有商品
    const [products] = await connection.execute('SELECT * FROM platform_product');
    console.log(`📦 找到 ${products.length} 个商品`);
    
    // 获取所有分类
    const [categories] = await connection.execute('SELECT * FROM category');
    console.log(`📂 找到 ${categories.length} 个分类`);
    
    const merchants = [];
    const merchantProducts = [];
    
    // 生成50个商家
    for (let i = 1; i <= 50; i++) {
      const merchantType = merchantTypes[i % merchantTypes.length];
      const shopNameIndex = Math.floor(i / merchantTypes.length) % merchantType.shopNames.length;
      const shopName = `${merchantType.shopNames[shopNameIndex]}${i}`;
      
      const merchant = {
        merchant_uid: `MERCHANT_${String(i).padStart(3, '0')}`,
        username: `merchant${i}`,
        password: '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        merchant_name: shopName,
        contact_name: `联系人${i}`,
        contact_phone: `138${String(i).padStart(8, '0')}`,
        business_license: `LICENSE_${i}`,
        id_card_front: `front_${i}.jpg`,
        id_card_back: `back_${i}.jpg`,
        status: 1, // 已审核
        reject_reason: null,
        shop_name: shopName,
        shop_logo: `logo_${i}.jpg`,
        shop_banner: `banner_${i}.jpg`,
        shop_description: `${merchantType.name}专业店铺，提供优质商品和服务`,
        balance: Math.floor(Math.random() * 10000) + 1000, // 1000-11000
        frozen_amount: Math.floor(Math.random() * 1000), // 0-1000
        total_income: Math.floor(Math.random() * 50000) + 5000, // 5000-55000
        total_withdraw: Math.floor(Math.random() * 20000) + 1000, // 1000-21000
        create_time: new Date(),
        update_time: new Date()
      };
      
      merchants.push(merchant);
      
      // 为每个商家分配5-15个商品
      const productCount = Math.floor(Math.random() * 11) + 5; // 5-15个商品
      const merchantCategoryProducts = products.filter(p => 
        merchantType.categories.includes(p.category_id)
      );
      
      // 随机选择商品
      const selectedProducts = [];
      for (let j = 0; j < productCount && j < merchantCategoryProducts.length; j++) {
        const randomIndex = Math.floor(Math.random() * merchantCategoryProducts.length);
        const product = merchantCategoryProducts[randomIndex];
        
        // 避免重复选择
        if (!selectedProducts.find(p => p.id === product.id)) {
          selectedProducts.push(product);
        }
      }
      
      // 为选中的商品创建商家商品记录
      selectedProducts.forEach(product => {
        const salePrice = Math.floor(product.suggest_price * (0.8 + Math.random() * 0.4)); // 80%-120%的建议价格
        const costPrice = product.cost_price || product.suggest_price * 0.6; // 成本价
        const profitMargin = salePrice - costPrice;
        const profitRate = (profitMargin / salePrice) * 100;
        
        merchantProducts.push({
          merchant_id: i,
          platform_product_id: product.id,
          sale_price: salePrice,
          profit_margin: profitMargin,
          profit_rate: profitRate,
          stock: Math.floor(Math.random() * 100) + 10, // 10-110库存
          sales: Math.floor(Math.random() * 50), // 0-50销量
          status: Math.random() > 0.1 ? 1 : 0, // 90%概率上架
          create_time: new Date(),
          update_time: new Date()
        });
      });
      
      console.log(`✅ 生成商家 ${i}: ${shopName} (${merchantType.name}) - ${selectedProducts.length}个商品`);
    }
    
    // 插入商家数据
    console.log('\n📝 插入商家数据...');
    for (const merchant of merchants) {
      await connection.execute(`
        INSERT INTO merchant (
          merchant_uid, username, password, merchant_name, contact_name, contact_phone,
          business_license, id_card_front, id_card_back, status, reject_reason,
          shop_name, shop_logo, shop_banner, shop_description,
          balance, frozen_amount, total_income, total_withdraw,
          create_time, update_time
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `, [
        merchant.merchant_uid, merchant.username, merchant.password, merchant.merchant_name,
        merchant.contact_name, merchant.contact_phone, merchant.business_license,
        merchant.id_card_front, merchant.id_card_back, merchant.status, merchant.reject_reason,
        merchant.shop_name, merchant.shop_logo, merchant.shop_banner, merchant.shop_description,
        merchant.balance, merchant.frozen_amount, merchant.total_income, merchant.total_withdraw,
        merchant.create_time, merchant.update_time
      ]);
    }
    
    // 插入商家商品数据
    console.log('\n📦 插入商家商品数据...');
    for (const mp of merchantProducts) {
      await connection.execute(`
        INSERT INTO merchant_product (
          merchant_id, platform_product_id, sale_price, profit_margin, profit_rate, 
          stock, sales, status, create_time, update_time
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `, [
        mp.merchant_id, mp.platform_product_id, mp.sale_price, mp.profit_margin, mp.profit_rate,
        mp.stock, mp.sales, mp.status, mp.create_time, mp.update_time
      ]);
    }
    
    console.log('\n🎉 商家生成完成！');
    console.log(`📊 统计信息:`);
    console.log(`• 生成商家数量: ${merchants.length}`);
    console.log(`• 分配商品数量: ${merchantProducts.length}`);
    console.log(`• 平均每个商家商品数: ${(merchantProducts.length / merchants.length).toFixed(1)}`);
    
    // 按类型统计
    const typeStats = {};
    merchants.forEach(m => {
      const type = merchantTypes.find(t => m.shop_name.includes(t.name.split('')[0]));
      if (type) {
        typeStats[type.name] = (typeStats[type.name] || 0) + 1;
      }
    });
    
    console.log('\n📈 商家类型分布:');
    Object.entries(typeStats).forEach(([type, count]) => {
      console.log(`• ${type}: ${count}个商家`);
    });
    
  } catch (error) {
    console.error('❌ 生成商家时出错:', error);
  } finally {
    await connection.end();
  }
}

// 运行脚本
generateMerchants();
