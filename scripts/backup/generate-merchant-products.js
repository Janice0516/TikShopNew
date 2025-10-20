const axios = require('axios');

async function generateMerchantProducts() {
  try {
    console.log('🚀 生成商家产品数据...');

    // 获取产品列表
    const productsResponse = await axios.get('http://localhost:3000/api/products');
    const products = productsResponse.data.data.list;
    console.log(`📦 找到 ${products.length} 个产品`);

    // 模拟商家数据（基于我们之前创建的商家）
    const merchants = [
      { id: 1, username: 'merchant001', name: 'Apple Store' },
      { id: 2, username: 'merchant002', name: 'TechWorld Malaysia' },
      { id: 3, username: 'merchant003', name: 'Digital Dreams' }
    ];

    const merchantProducts = [];
    
    for (const merchant of merchants) {
      console.log(`\n🏪 为商家 ${merchant.username} 生成产品数据...`);
      
      // 为每个商家上架所有产品
      for (const product of products) {
        const basePrice = product.suggestPrice || product.costPrice;
        const salePrice = Math.round((basePrice * (0.9 + Math.random() * 0.2)) * 100) / 100;
        const profitMargin = ((salePrice - product.costPrice) / salePrice * 100);
        
        merchantProducts.push({
          merchantId: merchant.id,
          productId: parseInt(product.id),
          salePrice: salePrice,
          profitMargin: Math.round(profitMargin * 100) / 100,
          status: 1,
          sales: Math.floor(Math.random() * 100),
          merchantName: merchant.username,
          productName: product.name
        });
        
        console.log(`  ✅ ${product.name} - RM${salePrice} (利润率: ${Math.round(profitMargin * 100) / 100}%)`);
      }
    }

    // 保存数据到文件
    const fs = require('fs');
    fs.writeFileSync('merchant-products-data.json', JSON.stringify(merchantProducts, null, 2));
    console.log(`\n💾 已生成 ${merchantProducts.length} 条商家产品数据`);
    
    // 生成SQL插入语句
    console.log('\n📋 生成SQL插入语句...');
    let sqlStatements = `
-- 创建merchant_product表
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

-- 插入商家产品数据
`;

    for (const mp of merchantProducts) {
      sqlStatements += `INSERT INTO merchant_product (merchant_id, product_id, sale_price, profit_margin, status, sales) VALUES (${mp.merchantId}, ${mp.productId}, ${mp.salePrice}, ${mp.profitMargin}, ${mp.status}, ${mp.sales}) ON CONFLICT (merchant_id, product_id) DO NOTHING;\n`;
    }

    fs.writeFileSync('merchant-products.sql', sqlStatements);
    console.log('💾 已生成SQL文件: merchant-products.sql');
    
    console.log('\n🎉 商家产品数据生成完成！');
    console.log(`📊 总计: ${merchantProducts.length} 个商家产品关系`);
    console.log(`🏪 涉及商家: ${merchants.length} 个`);
    console.log(`📦 涉及产品: ${products.length} 个`);

  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  }
}

generateMerchantProducts();
