const mysql = require('mysql2/promise');

// 数据库配置
const dbConfig = {
  host: '127.0.0.1',
  port: 3306,
  user: 'tikshop',
  password: 'TikShop_MySQL_#2025!9pQwXz',
  database: 'ecommerce',
  charset: 'utf8mb4'
};

async function distributeProductsToMerchants() {
  const connection = await mysql.createConnection(dbConfig);
  
  try {
    console.log('🚀 开始商品分配...\n');
    
    // 1. 获取所有商家
    const [merchants] = await connection.execute('SELECT id, merchant_name FROM merchant ORDER BY id');
    console.log(`📊 找到 ${merchants.length} 个商家`);
    
    // 2. 获取所有平台商品（按分类分组）
    const [products] = await connection.execute(`
      SELECT p.id, p.name, p.category_id, p.cost_price, p.suggest_price, c.name as category_name
      FROM platform_product p
      LEFT JOIN category c ON p.category_id = c.id
      ORDER BY p.category_id, p.id
    `);
    console.log(`🛍️ 找到 ${products.length} 个平台商品`);
    
    // 3. 按分类分组商品
    const productsByCategory = {};
    products.forEach(product => {
      const categoryId = product.category_id;
      if (!productsByCategory[categoryId]) {
        productsByCategory[categoryId] = {
          name: product.category_name,
          products: []
        };
      }
      productsByCategory[categoryId].products.push(product);
    });
    
    console.log('\n📂 分类商品分布:');
    Object.keys(productsByCategory).forEach(categoryId => {
      const category = productsByCategory[categoryId];
      console.log(`   ${category.name}: ${category.products.length} 个商品`);
    });
    
    // 4. 计算每个商家应该分配的商品数量
    const totalProducts = products.length;
    const totalMerchants = merchants.length;
    const productsPerMerchant = Math.floor(totalProducts / totalMerchants);
    const extraProducts = totalProducts % totalMerchants;
    
    console.log(`\n📈 分配策略:`);
    console.log(`   每个商家基础分配: ${productsPerMerchant} 个商品`);
    console.log(`   额外商品: ${extraProducts} 个`);
    
    // 5. 为每个商家分配商品
    let productIndex = 0;
    let successCount = 0;
    let skipCount = 0;
    
    for (let i = 0; i < merchants.length; i++) {
      const merchant = merchants[i];
      const merchantId = merchant.id;
      const merchantName = merchant.merchant_name;
      
      // 计算这个商家应该分配的商品数量
      let productsToAssign = productsPerMerchant;
      if (i < extraProducts) {
        productsToAssign += 1; // 前几个商家多分配一个
      }
      
      console.log(`\n🏪 为商家 ${merchantName} (ID: ${merchantId}) 分配 ${productsToAssign} 个商品:`);
      
      // 为这个商家分配商品
      for (let j = 0; j < productsToAssign && productIndex < products.length; j++) {
        const product = products[productIndex];
        productIndex++;
        
        // 检查这个商品是否已经被这个商家上架
        const [existing] = await connection.execute(
          'SELECT id FROM merchant_product WHERE merchant_id = ? AND platform_product_id = ?',
          [merchantId, product.id]
        );
        
        if (existing.length > 0) {
          console.log(`   ⚠️ 跳过已存在: ${product.name}`);
          skipCount++;
          continue;
        }
        
        // 计算售价（成本价 + 20-50% 利润）
        const costPrice = parseFloat(product.cost_price);
        const suggestPrice = parseFloat(product.suggest_price);
        const profitMargin = 0.2 + Math.random() * 0.3; // 20-50% 利润
        const salePrice = costPrice * (1 + profitMargin);
        
        // 插入商家商品记录
        try {
          await connection.execute(`
            INSERT INTO merchant_product (
              merchant_id, platform_product_id, sale_price, profit_margin, 
              status, sales, is_popular, is_top_deal, 
              discount_price, is_discount_active,
              create_time, update_time
            ) VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?, ?, NOW(), NOW())
          `, [
            merchantId,
            product.id,
            salePrice.toFixed(2),
            (profitMargin * 100).toFixed(2),
            Math.floor(Math.random() * 100), // 随机销量
            Math.random() > 0.8, // 20% 概率设为热门
            Math.random() > 0.9, // 10% 概率设为Top Deal
            Math.random() > 0.7 ? (salePrice * 0.8).toFixed(2) : null, // 30% 概率有折扣
            Math.random() > 0.7 // 30% 概率启用折扣
          ]);
          
          console.log(`   ✅ ${product.name} - 售价: RM${salePrice.toFixed(2)} (${(profitMargin * 100).toFixed(1)}% 利润)`);
          successCount++;
          
        } catch (error) {
          console.log(`   ❌ 插入失败: ${product.name} - ${error.message}`);
        }
      }
    }
    
    // 6. 检查最终结果
    console.log('\n📊 分配完成统计:');
    console.log(`   ✅ 成功分配: ${successCount} 个商品`);
    console.log(`   ⚠️ 跳过重复: ${skipCount} 个商品`);
    console.log(`   📦 总处理: ${successCount + skipCount} 个商品`);
    
    // 7. 验证每个商家的商品数量
    console.log('\n🏪 商家商品分布验证:');
    const [merchantStats] = await connection.execute(`
      SELECT m.merchant_name, COUNT(mp.id) as product_count 
      FROM merchant m 
      LEFT JOIN merchant_product mp ON m.id = mp.merchant_id AND mp.status = 1
      GROUP BY m.id, m.merchant_name 
      ORDER BY product_count DESC
    `);
    
    merchantStats.forEach(stat => {
      console.log(`   ${stat.merchant_name}: ${stat.product_count} 个商品`);
    });
    
    // 8. 验证分类商品分布
    console.log('\n📂 分类商品分布验证:');
    const [categoryStats] = await connection.execute(`
      SELECT c.name, COUNT(mp.id) as merchant_product_count 
      FROM category c 
      LEFT JOIN platform_product p ON c.id = p.category_id
      LEFT JOIN merchant_product mp ON p.id = mp.platform_product_id AND mp.status = 1
      GROUP BY c.id, c.name 
      ORDER BY merchant_product_count DESC
    `);
    
    categoryStats.forEach(stat => {
      console.log(`   ${stat.name}: ${stat.merchant_product_count} 个已上架商品`);
    });
    
    console.log('\n🎉 商品分配完成！');
    console.log('💡 现在每个分类都应该有足够的商品显示了');
    
  } catch (error) {
    console.error('❌ 分配失败:', error.message);
  } finally {
    await connection.end();
  }
}

// 运行分配脚本
distributeProductsToMerchants();
