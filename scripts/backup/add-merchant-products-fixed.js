const axios = require('axios');

// 获取现有产品列表
async function getProducts() {
  try {
    const response = await axios.get('http://localhost:3000/api/products');
    return response.data.data.list;
  } catch (error) {
    console.error('获取产品列表失败:', error.message);
    return [];
  }
}

// 商家登录获取token
async function loginMerchant(username, password) {
  try {
    const response = await axios.post('http://localhost:3000/api/merchant/login', {
      username,
      password
    });
    // 处理嵌套的数据结构
    return response.data.data.data.token;
  } catch (error) {
    console.error(`商家 ${username} 登录失败:`, error.message);
    return null;
  }
}

// 为商家上架产品
async function addProductToMerchant(token, productId, salePrice) {
  try {
    const response = await axios.post('http://localhost:3000/api/merchant/products', {
      productId,
      salePrice
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    return response.data;
  } catch (error) {
    console.error(`上架产品 ${productId} 失败:`, error.response?.data?.message || error.message);
    return null;
  }
}

// 主函数
async function main() {
  try {
    console.log('🚀 开始为商家上架产品...');

    // 获取产品列表
    const products = await getProducts();
    if (products.length === 0) {
      console.log('❌ 没有找到产品');
      return;
    }
    console.log(`✅ 找到 ${products.length} 个产品`);

    // 商家列表（使用我们之前创建的商家）
    const merchants = [
      { username: 'merchant001', password: 'password123' },
      { username: 'merchant002', password: 'password123' },
      { username: 'merchant003', password: 'password123' }
    ];

    // 为每个商家上架产品
    for (const merchant of merchants) {
      console.log(`\n📦 为商家 ${merchant.username} 上架产品...`);
      
      // 登录获取token
      const token = await loginMerchant(merchant.username, merchant.password);
      if (!token) {
        console.log(`⚠️  商家 ${merchant.username} 登录失败，跳过`);
        continue;
      }

      console.log(`✅ 商家 ${merchant.username} 登录成功`);

      // 为商家上架前10个产品（如果产品数量不足10个，则上架所有产品）
      const productsToAdd = products.slice(0, Math.min(10, products.length));
      let successCount = 0;

      for (const product of productsToAdd) {
        // 设置销售价格（建议售价的90%-110%之间）
        const basePrice = product.suggestPrice || product.costPrice;
        const salePrice = Math.round((basePrice * (0.9 + Math.random() * 0.2)) * 100) / 100;

        const result = await addProductToMerchant(token, parseInt(product.id), salePrice);
        if (result) {
          successCount++;
          console.log(`  ✅ 上架产品: ${product.name} - RM${salePrice}`);
        } else {
          console.log(`  ⚠️  跳过产品: ${product.name}`);
        }

        // 避免请求过快
        await new Promise(resolve => setTimeout(resolve, 500));
      }

      console.log(`📊 商家 ${merchant.username} 成功上架 ${successCount} 个产品`);
    }

    console.log('\n🎉 商家产品上架完成！');

  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  }
}

main();
