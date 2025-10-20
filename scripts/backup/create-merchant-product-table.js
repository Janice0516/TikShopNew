const axios = require('axios');

async function createMerchantProductTable() {
  try {
    console.log('🚀 创建merchant_product表和数据...');

    // 使用管理员登录获取token
    const loginResponse = await axios.post('http://localhost:3000/api/admin/login', {
      username: 'admin',
      password: 'admin123'
    });
    
    const token = loginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');

    // 创建一个临时的API端点来执行SQL
    // 由于没有直接的SQL执行API，我们通过其他方式
    
    console.log('📋 由于API限制，我们将通过其他方式创建数据...');
    
    // 获取商家列表
    const merchantsResponse = await axios.get('http://localhost:3000/api/merchant/list', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    
    const merchants = merchantsResponse.data.data.list || [];
    console.log(`📊 找到 ${merchants.length} 个商家`);

    // 获取产品列表
    const productsResponse = await axios.get('http://localhost:3000/api/products');
    const products = productsResponse.data.data.list;
    console.log(`📦 找到 ${products.length} 个产品`);

    // 由于无法直接创建表，我们创建一个简单的数据文件
    const merchantProducts = [];
    
    for (let i = 0; i < Math.min(3, merchants.length); i++) {
      const merchant = merchants[i];
      console.log(`\n🏪 为商家 ${merchant.username} 准备产品数据...`);
      
      for (let j = 0; j < Math.min(10, products.length); j++) {
        const product = products[j];
        const basePrice = product.suggestPrice || product.costPrice;
        const salePrice = Math.round((basePrice * (0.9 + Math.random() * 0.2)) * 100) / 100;
        const profitMargin = ((salePrice - product.costPrice) / salePrice * 100);
        
        merchantProducts.push({
          merchantId: merchant.id,
          productId: product.id,
          salePrice: salePrice,
          profitMargin: profitMargin,
          status: 1,
          merchantName: merchant.username,
          productName: product.name
        });
        
        console.log(`  ✅ 准备产品: ${product.name} - RM${salePrice}`);
      }
    }

    // 保存数据到文件
    const fs = require('fs');
    fs.writeFileSync('merchant-products-data.json', JSON.stringify(merchantProducts, null, 2));
    console.log(`\n💾 已保存 ${merchantProducts.length} 条商家产品数据到 merchant-products-data.json`);
    
    console.log('\n📋 接下来需要手动在数据库中创建merchant_product表并插入这些数据');
    console.log('SQL创建表语句:');
    console.log(`
CREATE TABLE IF NOT EXISTS merchant_product (
  id BIGSERIAL PRIMARY KEY,
  merchant_id BIGINT NOT NULL,
  product_id BIGINT NOT NULL,
  sale_price DECIMAL(10,2) NOT NULL,
  profit_margin DECIMAL(5,2) DEFAULT 0,
  status SMALLINT DEFAULT 1,
  sales INTEGER DEFAULT 0,
  create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  update_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(merchant_id, product_id)
);
    `);

  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  }
}

createMerchantProductTable();
