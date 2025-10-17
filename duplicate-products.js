const axios = require('axios');

async function duplicateProducts() {
  try {
    console.log('🚀 开始复制现有产品...');

    // 获取现有产品
    const response = await axios.get('https://tiktokshop-api.onrender.com/api/products');
    const products = response.data.data.list;
    console.log(`📦 找到 ${products.length} 个现有产品`);

    // 产品名称变体
    const nameVariants = [
      'Pro', 'Max', 'Ultra', 'Plus', 'Elite', 'Premium', 'Advanced', 'Deluxe',
      'Standard', 'Basic', 'Mini', 'Compact', 'Large', 'XL', 'XXL'
    ];

    const brandVariants = [
      'Apple', 'Samsung', 'Sony', 'Canon', 'Nikon', 'Dell', 'HP', 'Lenovo',
      'Bose', 'JBL', 'Beats', 'Nike', 'Adidas', 'Puma', 'Reebok'
    ];

    let newProducts = [];
    let productId = 9; // 从9开始，因为现有8个产品

    // 为每个现有产品创建多个变体
    for (const product of products) {
      for (let i = 0; i < 4; i++) { // 每个产品创建4个变体
        const variant = nameVariants[Math.floor(Math.random() * nameVariants.length)];
        const brand = brandVariants[Math.floor(Math.random() * brandVariants.length)];
        
        const newProduct = {
          id: productId++,
          name: `${product.name} ${variant}`,
          categoryId: product.categoryId,
          brand: brand,
          mainImage: product.mainImage,
          costPrice: Math.round((product.costPrice * (0.8 + Math.random() * 0.4)) * 100) / 100,
          suggestPrice: Math.round((product.suggestPrice * (0.8 + Math.random() * 0.4)) * 100) / 100,
          stock: Math.floor(Math.random() * 200) + 10,
          description: `${product.description} - ${variant}版本`,
          status: 1
        };
        
        newProducts.push(newProduct);
      }
    }

    console.log(`📋 生成了 ${newProducts.length} 个新产品变体`);

    // 保存到文件
    const fs = require('fs');
    fs.writeFileSync('duplicated-products.json', JSON.stringify(newProducts, null, 2));
    
    // 生成SQL插入语句
    let sqlContent = `-- 插入${newProducts.length}个产品变体\n`;
    for (const product of newProducts) {
      sqlContent += `INSERT INTO platform_product (name, category_id, brand, main_image, cost_price, suggest_price, stock, description, status) VALUES ('${product.name}', ${product.categoryId}, '${product.brand}', '${product.mainImage}', ${product.costPrice}, ${product.suggestPrice}, ${product.stock}, '${product.description}', 1);\n`;
    }
    
    fs.writeFileSync('duplicated-products.sql', sqlContent);
    
    console.log('💾 已生成文件:');
    console.log('  - duplicated-products.json (JSON格式)');
    console.log('  - duplicated-products.sql (SQL格式)');
    
    console.log('\n📊 产品变体示例:');
    newProducts.slice(0, 5).forEach(p => {
      console.log(`  ✅ ${p.name} - RM${p.suggestPrice} (${p.brand})`);
    });

  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  }
}

duplicateProducts();
