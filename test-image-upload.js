#!/usr/bin/env node

// 测试管理端商品照片上传功能
const axios = require('axios');
const FormData = require('form-data');
const fs = require('fs');

const API_BASE_URL = 'http://localhost:3000/api';

async function testImageUpload() {
  try {
    console.log('🚀 开始测试商品照片上传功能...');
    
    // 1. 管理员登录
    console.log('🔐 管理员登录...');
    const loginResponse = await axios.post(`${API_BASE_URL}/admin/login`, {
      username: 'admin',
      password: 'admin123'
    });
    
    const token = loginResponse.data.data.token;
    console.log('✅ 管理员登录成功');
    
    // 2. 测试图片上传
    console.log('📸 测试图片上传...');
    const formData = new FormData();
    formData.append('file', fs.createReadStream('/root/TikShop/ecommerce-backend/uploads/images/test-product.jpg'));
    
    const uploadResponse = await axios.post(`${API_BASE_URL}/upload/image`, formData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        ...formData.getHeaders()
      }
    });
    
    console.log('✅ 图片上传成功:', uploadResponse.data);
    
    // 3. 测试图片访问
    console.log('🔍 测试图片访问...');
    const imageUrl = `http://localhost:3000${uploadResponse.data.url}`;
    const imageResponse = await axios.get(imageUrl);
    
    if (imageResponse.status === 200) {
      console.log('✅ 图片访问成功');
      console.log('📷 图片URL:', imageUrl);
    } else {
      console.log('❌ 图片访问失败');
    }
    
    // 4. 测试创建商品
    console.log('🛍️ 测试创建商品...');
    const productData = {
      name: '测试商品 - 无线耳机',
      categoryId: 1,
      brand: 'TestBrand',
      mainImage: uploadResponse.data.url,
      costPrice: 50.00,
      suggestPrice: 80.00,
      stock: 100,
      description: '这是一个测试商品，用于验证图片上传功能'
    };
    
    const productResponse = await axios.post(`${API_BASE_URL}/products`, productData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    
    console.log('✅ 商品创建成功:', productResponse.data);
    
    console.log('🎉 所有测试通过！商品照片上传功能正常工作');
    
  } catch (error) {
    console.error('❌ 测试失败:', error.response?.data || error.message);
  }
}

testImageUpload();
