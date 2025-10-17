const axios = require('axios');

async function testMerchantProductAPI() {
  try {
    console.log('🧪 测试商家产品上架API...');

    // 1. 商家登录
    console.log('1. 商家登录...');
    const loginResponse = await axios.post('https://tiktokshop-api.onrender.com/api/merchant/login', {
      username: 'merchant001',
      password: 'password123'
    });
    
    const token = loginResponse.data.data.data.token;
    console.log('✅ 登录成功，Token:', token.substring(0, 20) + '...');

    // 2. 获取产品列表
    console.log('\n2. 获取产品列表...');
    const productsResponse = await axios.get('https://tiktokshop-api.onrender.com/api/products');
    const products = productsResponse.data.data.list;
    console.log(`✅ 找到 ${products.length} 个产品`);

    // 3. 测试上架第一个产品
    console.log('\n3. 测试上架产品...');
    const firstProduct = products[0];
    console.log(`尝试上架产品: ${firstProduct.name} (ID: ${firstProduct.id})`);

    try {
      const addResponse = await axios.post('https://tiktokshop-api.onrender.com/api/merchant/products', {
        productId: parseInt(firstProduct.id),
        salePrice: 100.00
      }, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      
      console.log('✅ 产品上架成功:', addResponse.data);
      
    } catch (error) {
      console.log('❌ 产品上架失败:');
      console.log('   状态码:', error.response?.status);
      console.log('   错误信息:', error.response?.data?.message || error.message);
      console.log('   完整响应:', JSON.stringify(error.response?.data, null, 2));
    }

    // 4. 测试获取商家产品列表
    console.log('\n4. 测试获取商家产品列表...');
    try {
      const listResponse = await axios.get('https://tiktokshop-api.onrender.com/api/merchant/products', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      });
      
      console.log('✅ 获取商家产品列表成功:', listResponse.data);
      
    } catch (error) {
      console.log('❌ 获取商家产品列表失败:');
      console.log('   状态码:', error.response?.status);
      console.log('   错误信息:', error.response?.data?.message || error.message);
    }

  } catch (error) {
    console.error('❌ 测试失败:', error.message);
  }
}

testMerchantProductAPI();
