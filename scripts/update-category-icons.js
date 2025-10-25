const axios = require('axios');
const fs = require('fs');
const path = require('path');

// 配置
const API_BASE_URL = 'https://tiktokbusines.store/api';
const ICONS_DIR = path.join(__dirname, '../Public/category-icons');
const TOKEN = process.env.ADMIN_TOKEN || 'your-admin-token'; // 从环境变量获取token

// 分类名称到图标文件的映射（基于您上传的文件）
const categoryIconMapping = {
  'Jewelry Accessories & Derivatives': 'Jewelry.jpg',
  'Food & Beverages': 'Food.jpeg',
  'Collectibles': 'Book.jpg', // 使用Book.jpg作为收藏品图标
  'Pre-Owned': 'LuggageNBags.jpeg', // 使用LuggageNBags.jpeg作为二手商品图标
  'Electronics & Appliances': 'AutomotiveNMotorcycle.jpeg', // 使用AutomotiveNMotorcycle.jpeg作为电子产品图标
  'Fashion & Bags': 'LuggageNBags.jpeg',
  'Beauty & Personal Care': 'Pet.jpg', // 使用Pet.jpg作为美妆个护图标
  'Home & Living': 'ToysNHobbies.png', // 使用ToysNHobbies.png作为家居用品图标
  'Sports Shoes': 'Sports NOutdoor.jpg',
  'Books, Magazines & Audio': 'Book.jpg',
  'Womenswear & Underwear': 'LuggageNBags.jpeg',
  'Phones & Electronics': 'AutomotiveNMotorcycle.jpeg',
  'Fashion Accessories': 'LuggageNBags.jpeg',
  'Menswear & Underwear': 'LuggageNBags.jpeg',
  'Home Supplies': 'ToysNHobbies.png',
  'Shoes': 'Sports NOutdoor.jpg',
  'Sports & Outdoor': 'Sports NOutdoor.jpg',
  'Luggage & Bags': 'LuggageNBags.jpeg',
  'Toys & Hobbies': 'ToysNHobbies.png',
  'Automotive & Motorcycle': 'AutomotiveNMotorcycle.jpeg',
  'Kids Fashion': 'LuggageNBags.jpeg',
  'Kitchenware': 'ToysNHobbies.png',
  'Computers & Office Equipment': 'AutomotiveNMotorcycle.jpeg',
  'Baby & Maternity': 'LuggageNBags.jpeg',
  'Tools & Hardware': 'AutomotiveNMotorcycle.jpeg',
  'Textiles & Soft Furnishings': 'ToysNHobbies.png',
  'Pet Supplies': 'Pet.jpg',
  'Home Improvement': 'ToysNHobbies.png',
  'Muslim Fashion': 'LuggageNBags.jpeg',
  'Household Appliances': 'AutomotiveNMotorcycle.jpeg',
  'Health': 'Pet.jpg',
  'Furniture': 'ToysNHobbies.png'
};

async function updateCategoryIcons() {
  try {
    console.log('🚀 开始批量更新分类图标...');
    
    // 1. 检查图标文件夹是否存在
    if (!fs.existsSync(ICONS_DIR)) {
      console.log(`❌ 图标文件夹不存在: ${ICONS_DIR}`);
      return;
    }
    
    // 2. 列出所有图标文件
    const iconFiles = fs.readdirSync(ICONS_DIR);
    console.log(`📁 找到 ${iconFiles.length} 个图标文件:`, iconFiles);
    
    // 3. 获取所有分类
    console.log('📋 获取分类列表...');
    const categoriesResponse = await axios.get(`${API_BASE_URL}/products/categories`, {
      headers: { Authorization: `Bearer ${TOKEN}` }
    });
    
    const categories = categoriesResponse.data.data || categoriesResponse.data;
    console.log(`📊 找到 ${categories.length} 个分类`);
    
    // 4. 批量更新图标
    let successCount = 0;
    let errorCount = 0;
    
    for (const category of categories) {
      const iconFileName = categoryIconMapping[category.name];
      
      if (iconFileName) {
        // 检查图标文件是否存在
        const iconPath = path.join(ICONS_DIR, iconFileName);
        if (fs.existsSync(iconPath)) {
          try {
            // 构建完整的图标URL，处理文件名中的空格
            const encodedFileName = encodeURIComponent(iconFileName);
            const iconUrl = `${API_BASE_URL.replace('/api', '')}/Public/category-icons/${encodedFileName}`;
            
            // 更新分类图标
            await axios.put(`${API_BASE_URL}/category/${category.id}/image`, {
              image_url: iconUrl,
              name: category.name
            }, {
              headers: { Authorization: `Bearer ${TOKEN}` }
            });
            
            console.log(`✅ 更新分类 "${category.name}" 图标成功: ${iconUrl}`);
            successCount++;
          } catch (error) {
            console.error(`❌ 更新分类 "${category.name}" 图标失败:`, error.response?.data || error.message);
            errorCount++;
          }
        } else {
          console.warn(`⚠️  图标文件不存在: ${iconPath}`);
          errorCount++;
        }
      } else {
        console.warn(`⚠️  未找到分类 "${category.name}" 的图标映射`);
      }
    }
    
    console.log('\n🎉 批量更新完成！');
    console.log(`✅ 成功: ${successCount} 个`);
    console.log(`❌ 失败: ${errorCount} 个`);
    
  } catch (error) {
    console.error('💥 批量更新失败:', error.response?.data || error.message);
  }
}

// 运行脚本
if (require.main === module) {
  updateCategoryIcons();
}

module.exports = { updateCategoryIcons };
