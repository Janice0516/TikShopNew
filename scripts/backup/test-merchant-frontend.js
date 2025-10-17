const axios = require('axios');

async function testMerchantFrontend() {
  console.log('🔍 详细测试商家后台问题\n');

  try {
    // 1. 测试页面访问
    console.log('1. 测试页面访问...');
    const pageResponse = await axios.get('https://tikshop-merchant.onrender.com');
    console.log('   ✅ 页面可访问');
    console.log('   状态码:', pageResponse.status);
    console.log('   内容长度:', pageResponse.data.length);

    // 2. 检查页面内容
    console.log('\n2. 检查页面内容...');
    const html = pageResponse.data;
    
    // 检查是否有JavaScript文件
    const jsMatch = html.match(/src="([^"]*\.js)"/);
    if (jsMatch) {
      console.log('   JavaScript文件:', jsMatch[1]);
      
      // 测试JavaScript文件是否可访问
      try {
        const jsResponse = await axios.get(`https://tikshop-merchant.onrender.com${jsMatch[1]}`);
        console.log('   ✅ JavaScript文件可访问');
        console.log('   JS文件大小:', jsResponse.data.length);
      } catch (jsError) {
        console.log('   ❌ JavaScript文件无法访问:', jsError.message);
      }
    }

    // 检查是否有CSS文件
    const cssMatch = html.match(/href="([^"]*\.css)"/);
    if (cssMatch) {
      console.log('   CSS文件:', cssMatch[1]);
      
      // 测试CSS文件是否可访问
      try {
        const cssResponse = await axios.get(`https://tikshop-merchant.onrender.com${cssMatch[1]}`);
        console.log('   ✅ CSS文件可访问');
        console.log('   CSS文件大小:', cssResponse.data.length);
      } catch (cssError) {
        console.log('   ❌ CSS文件无法访问:', cssError.message);
      }
    }

    // 3. 测试API连接
    console.log('\n3. 测试API连接...');
    const apiResponse = await axios.get('https://tiktokshop-api.onrender.com/api/health');
    console.log('   ✅ API健康检查通过');
    console.log('   API响应:', apiResponse.data);

  } catch (error) {
    console.error('❌ 测试失败:', error.response?.status, error.response?.data?.message || error.message);
  }
}

testMerchantFrontend();
