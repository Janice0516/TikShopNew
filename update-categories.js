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

// 分类翻译映射
const categoryTranslations = {
  1: 'Fashion & Bags',
  2: 'Electronics & Appliances', 
  3: 'Food & Fresh',
  4: 'Beauty & Personal Care',
  5: 'Home & Living',
  6: 'Men\'s Clothing',
  7: 'Women\'s Clothing',
  8: 'Sports Shoes',
  9: 'Bags & Luggage',
  10: 'Mobile Phones',
  11: 'Computers',
  12: 'Home Appliances',
  13: 'Snacks',
  14: 'Fruits',
  15: 'Beverages',
  16: 'Skincare',
  17: 'Cosmetics',
  18: 'Home Textiles',
  19: 'Kitchenware'
};

async function updateCategories() {
  const connection = await mysql.createConnection(dbConfig);
  
  try {
    console.log('🔄 开始更新分类名称...');
    
    let successCount = 0;
    let errorCount = 0;
    
    for (const [id, englishName] of Object.entries(categoryTranslations)) {
      try {
        await connection.execute(
          'UPDATE category SET name = ? WHERE id = ?',
          [englishName, parseInt(id)]
        );
        
        successCount++;
        console.log(`✅ [${id}] ${englishName}`);
        
      } catch (error) {
        errorCount++;
        console.error(`❌ [${id}] ${englishName}: ${error.message}`);
      }
    }
    
    console.log('\n🎉 分类更新完成！');
    console.log(`✅ 成功更新: ${successCount} 个分类`);
    console.log(`❌ 失败: ${errorCount} 个分类`);
    
    // 查询更新后的结果
    console.log('\n📊 更新后的分类列表:');
    const [categories] = await connection.execute('SELECT id, name FROM category ORDER BY id');
    categories.forEach(cat => {
      console.log(`${cat.id}. ${cat.name}`);
    });
    
  } catch (error) {
    console.error('❌ 更新分类时发生错误:', error);
  } finally {
    await connection.end();
  }
}

// 运行脚本
updateCategories().catch(console.error);
