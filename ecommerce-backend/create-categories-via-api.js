#!/usr/bin/env node

// 通过API创建分类数据（临时解决方案）
const axios = require('axios');

const API_BASE_URL = 'http://localhost:3000/api';

async function createCategoriesViaAPI() {
  try {
    console.log('🔧 通过API创建分类数据（临时解决方案）...');
    
    // 1. 管理员登录
    console.log('🔐 管理员登录...');
    const adminLoginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const adminToken = adminLoginResponse.data.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 创建主分类
    console.log('📂 创建主分类...');
    const mainCategories = [
      { name: 'Electronics', parentId: 0, level: 1, sort: 1, status: 1 },
      { name: 'Fashion', parentId: 0, level: 1, sort: 2, status: 1 },
      { name: 'Home & Garden', parentId: 0, level: 1, sort: 3, status: 1 },
      { name: 'Sports & Outdoors', parentId: 0, level: 1, sort: 4, status: 1 },
      { name: 'Beauty & Health', parentId: 0, level: 1, sort: 5, status: 1 },
      { name: 'Books & Media', parentId: 0, level: 1, sort: 6, status: 1 },
      { name: 'Toys & Games', parentId: 0, level: 1, sort: 7, status: 1 },
      { name: 'Automotive', parentId: 0, level: 1, sort: 8, status: 1 }
    ];
    
    let categoryMap = {};
    let successCount = 0;
    let failCount = 0;
    
    for (const category of mainCategories) {
      try {
        const response = await axios.post(`${API_BASE_URL}/category`, category, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`   ✅ 创建主分类: ${category.name} (ID: ${response.data.data?.id || 'unknown'})`);
        categoryMap[category.name] = response.data.data?.id || successCount + 1;
        successCount++;
      } catch (error) {
        console.log(`   ❌ 创建主分类失败: ${category.name} - ${error.response?.data?.message || error.message}`);
        failCount++;
      }
    }
    
    // 3. 创建子分类
    console.log('📂 创建子分类...');
    const subCategories = [
      { name: 'Smartphones', parentId: categoryMap['Electronics'] || 1, level: 2, sort: 1, status: 1 },
      { name: 'Laptops', parentId: categoryMap['Electronics'] || 1, level: 2, sort: 2, status: 1 },
      { name: 'Audio', parentId: categoryMap['Electronics'] || 1, level: 2, sort: 3, status: 1 },
      { name: 'Cameras', parentId: categoryMap['Electronics'] || 1, level: 2, sort: 4, status: 1 },
      { name: 'Men\'s Clothing', parentId: categoryMap['Fashion'] || 2, level: 2, sort: 1, status: 1 },
      { name: 'Women\'s Clothing', parentId: categoryMap['Fashion'] || 2, level: 2, sort: 2, status: 1 },
      { name: 'Shoes', parentId: categoryMap['Fashion'] || 2, level: 2, sort: 3, status: 1 },
      { name: 'Accessories', parentId: categoryMap['Fashion'] || 2, level: 2, sort: 4, status: 1 },
      { name: 'Furniture', parentId: categoryMap['Home & Garden'] || 3, level: 2, sort: 1, status: 1 },
      { name: 'Kitchen & Dining', parentId: categoryMap['Home & Garden'] || 3, level: 2, sort: 2, status: 1 },
      { name: 'Garden Tools', parentId: categoryMap['Home & Garden'] || 3, level: 2, sort: 3, status: 1 },
      { name: 'Home Decor', parentId: categoryMap['Home & Garden'] || 3, level: 2, sort: 4, status: 1 }
    ];
    
    for (const subCategory of subCategories) {
      try {
        const response = await axios.post(`${API_BASE_URL}/category`, subCategory, {
          headers: { Authorization: `Bearer ${adminToken}` }
        });
        console.log(`   ✅ 创建子分类: ${subCategory.name} (ID: ${response.data.data?.id || 'unknown'})`);
        successCount++;
      } catch (error) {
        console.log(`   ❌ 创建子分类失败: ${subCategory.name} - ${error.response?.data?.message || error.message}`);
        failCount++;
      }
    }
    
    // 4. 测试分类列表
    console.log('📂 测试分类列表...');
    try {
      const categoriesResponse = await axios.get(`${API_BASE_URL}/category`, {
        headers: { Authorization: `Bearer ${adminToken}` }
      });
      console.log('✅ 分类列表API正常:', categoriesResponse.data);
    } catch (error) {
      console.log('❌ 分类列表API失败:', error.response?.data?.message || error.message);
    }
    
    console.log(`📊 分类创建结果: 成功 ${successCount} 个，失败 ${failCount} 个`);
    
    if (successCount > 0) {
      console.log('✅ 分类数据创建完成！');
      console.log('💡 提示：');
      console.log('   1. 分类数据已通过API创建');
      console.log('   2. 管理后台现在应该可以正常显示分类');
      console.log('   3. 可以继续创建商品数据');
    } else {
      console.log('❌ 分类创建失败，需要进一步调查API问题');
    }
    
  } catch (error) {
    console.error('❌ 分类创建失败:');
    console.error('   错误信息:', error.message);
    if (error.response) {
      console.error('   响应状态:', error.response.status);
      console.error('   响应数据:', error.response.data);
    }
  }
}

createCategoriesViaAPI();
