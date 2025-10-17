const axios = require('axios');

// 马来西亚生活用品产品数据
const dailyProducts = [
  // 饮料类
  { name: 'Milo Original 1.5kg', categoryId: 3, brand: 'Milo', mainImage: '/static/products/milo-original-1.5kg.jpg', costPrice: 25.00, suggestPrice: 35.00, stock: 200, description: 'Original Milo malt drink powder, perfect for breakfast and snacks.' },
  { name: 'Milo 3-in-1 Original 20s', categoryId: 3, brand: 'Milo', mainImage: '/static/products/milo-3in1-original-20s.jpg', costPrice: 8.50, suggestPrice: 12.00, stock: 500, description: 'Convenient 3-in-1 Milo sachets with milk powder and sugar.' },
  { name: 'Nescafe 3-in-1 Original 20s', categoryId: 3, brand: 'Nescafe', mainImage: '/static/products/nescafe-3in1-original-20s.jpg', costPrice: 7.50, suggestPrice: 11.00, stock: 400, description: 'Classic 3-in-1 coffee mix with creamer and sugar.' },
  { name: 'Nescafe Gold 3-in-1 20s', categoryId: 3, brand: 'Nescafe', mainImage: '/static/products/nescafe-gold-3in1-20s.jpg', costPrice: 12.00, suggestPrice: 18.00, stock: 300, description: 'Premium 3-in-1 coffee with rich aroma and smooth taste.' },
  { name: 'Old Town White Coffee 3-in-1 20s', categoryId: 3, brand: 'Old Town', mainImage: '/static/products/old-town-white-coffee-3in1-20s.jpg', costPrice: 9.50, suggestPrice: 14.00, stock: 350, description: 'Famous Malaysian white coffee with authentic taste.' },
  { name: 'Aik Cheong Coffee 3-in-1 20s', categoryId: 3, brand: 'Aik Cheong', mainImage: '/static/products/aik-cheong-coffee-3in1-20s.jpg', costPrice: 6.50, suggestPrice: 10.00, stock: 250, description: 'Traditional Malaysian coffee blend with rich flavor.' },
  
  // 食品类
  { name: 'Maggi Instant Noodles Curry 5s', categoryId: 3, brand: 'Maggi', mainImage: '/static/products/maggi-curry-5s.jpg', costPrice: 3.50, suggestPrice: 5.50, stock: 1000, description: 'Popular curry flavored instant noodles, quick and delicious meal.' },
  { name: 'Maggi Instant Noodles Chicken 5s', categoryId: 3, brand: 'Maggi', mainImage: '/static/products/maggi-chicken-5s.jpg', costPrice: 3.50, suggestPrice: 5.50, stock: 800, description: 'Classic chicken flavored instant noodles.' },
  { name: 'Indomie Mi Goreng 5s', categoryId: 3, brand: 'Indomie', mainImage: '/static/products/indomie-mi-goreng-5s.jpg', costPrice: 4.00, suggestPrice: 6.50, stock: 600, description: 'Indonesian fried noodles with authentic seasoning.' },
  { name: 'Cintan Chicken Noodles 5s', categoryId: 3, brand: 'Cintan', mainImage: '/static/products/cintan-chicken-5s.jpg', costPrice: 3.00, suggestPrice: 5.00, stock: 500, description: 'Local favorite chicken flavored instant noodles.' },
  
  // 调味料
  { name: 'Lee Kum Kee Soy Sauce 500ml', categoryId: 3, brand: 'Lee Kum Kee', mainImage: '/static/products/lee-kum-kee-soy-sauce-500ml.jpg', costPrice: 8.50, suggestPrice: 12.00, stock: 200, description: 'Premium soy sauce for cooking and dipping.' },
  { name: 'Knorr Chicken Stock Cubes 8s', categoryId: 3, brand: 'Knorr', mainImage: '/static/products/knorr-chicken-stock-8s.jpg', costPrice: 4.50, suggestPrice: 7.00, stock: 300, description: 'Chicken stock cubes for enhancing soup and dish flavors.' },
  { name: 'Ajinomoto MSG 200g', categoryId: 3, brand: 'Ajinomoto', mainImage: '/static/products/ajinomoto-msg-200g.jpg', costPrice: 3.50, suggestPrice: 5.50, stock: 400, description: 'Umami seasoning to enhance food taste.' },
  
  // 清洁用品
  { name: 'Dettol Antiseptic Liquid 500ml', categoryId: 5, brand: 'Dettol', mainImage: '/static/products/dettol-antiseptic-500ml.jpg', costPrice: 12.00, suggestPrice: 18.00, stock: 150, description: 'Antiseptic liquid for first aid and cleaning wounds.' },
  { name: 'Dettol Hand Sanitizer 250ml', categoryId: 5, brand: 'Dettol', mainImage: '/static/products/dettol-hand-sanitizer-250ml.jpg', costPrice: 8.50, suggestPrice: 13.00, stock: 200, description: 'Alcohol-based hand sanitizer for hygiene protection.' },
  { name: 'Lifebuoy Body Wash 400ml', categoryId: 4, brand: 'Lifebuoy', mainImage: '/static/products/lifebuoy-body-wash-400ml.jpg', costPrice: 6.50, suggestPrice: 10.00, stock: 300, description: 'Antibacterial body wash for daily hygiene.' },
  { name: 'Colgate Total Toothpaste 150g', categoryId: 4, brand: 'Colgate', mainImage: '/static/products/colgate-total-toothpaste-150g.jpg', costPrice: 7.50, suggestPrice: 12.00, stock: 250, description: 'Complete protection toothpaste for oral health.' },
  
  // 个人护理
  { name: 'Vaseline Petroleum Jelly 100g', categoryId: 4, brand: 'Vaseline', mainImage: '/static/products/vaseline-petroleum-jelly-100g.jpg', costPrice: 5.50, suggestPrice: 8.50, stock: 180, description: 'Multi-purpose petroleum jelly for skin care.' },
  { name: 'Johnson Baby Powder 200g', categoryId: 7, brand: 'Johnson', mainImage: '/static/products/johnson-baby-powder-200g.jpg', costPrice: 8.00, suggestPrice: 12.50, stock: 120, description: 'Gentle baby powder for sensitive skin.' },
  { name: 'Pampers Baby Diapers Size M 50s', categoryId: 7, brand: 'Pampers', mainImage: '/static/products/pampers-diapers-m-50s.jpg', costPrice: 35.00, suggestPrice: 55.00, stock: 80, description: 'Premium baby diapers with superior absorption.' },
  
  // 厨房用品
  { name: 'Tupperware Food Container Set 5pcs', categoryId: 5, brand: 'Tupperware', mainImage: '/static/products/tupperware-container-set-5pcs.jpg', costPrice: 45.00, suggestPrice: 75.00, stock: 50, description: 'BPA-free food storage containers for kitchen organization.' },
  { name: 'Lock & Lock Food Container 1L', categoryId: 5, brand: 'Lock & Lock', mainImage: '/static/products/lock-lock-container-1l.jpg', costPrice: 15.00, suggestPrice: 25.00, stock: 100, description: 'Airtight food container for fresh food storage.' },
  { name: 'Tefal Non-stick Pan 24cm', categoryId: 5, brand: 'Tefal', mainImage: '/static/products/tefal-nonstick-pan-24cm.jpg', costPrice: 65.00, suggestPrice: 95.00, stock: 30, description: 'Non-stick frying pan for healthy cooking.' },
  
  // 文具用品
  { name: 'Faber-Castell Pencil Set 12pcs', categoryId: 9, brand: 'Faber-Castell', mainImage: '/static/products/faber-castell-pencil-set-12pcs.jpg', costPrice: 8.50, suggestPrice: 15.00, stock: 200, description: 'High-quality graphite pencils for writing and drawing.' },
  { name: 'Staedtler Eraser 2pcs', categoryId: 9, brand: 'Staedtler', mainImage: '/static/products/staedtler-eraser-2pcs.jpg', costPrice: 3.50, suggestPrice: 6.00, stock: 300, description: 'Soft erasers that erase cleanly without smudging.' },
  { name: 'Uni-ball Pen Black 0.5mm', categoryId: 9, brand: 'Uni-ball', mainImage: '/static/products/uni-ball-pen-black-0.5mm.jpg', costPrice: 4.50, suggestPrice: 8.00, stock: 150, description: 'Smooth writing gel pen with precise 0.5mm tip.' },
  
  // 汽车用品
  { name: 'Shell Helix Ultra Engine Oil 4L', categoryId: 8, brand: 'Shell', mainImage: '/static/products/shell-helix-ultra-4l.jpg', costPrice: 85.00, suggestPrice: 125.00, stock: 40, description: 'Premium synthetic engine oil for better engine performance.' },
  { name: 'Castrol GTX Engine Oil 4L', categoryId: 8, brand: 'Castrol', mainImage: '/static/products/castrol-gtx-4l.jpg', costPrice: 75.00, suggestPrice: 110.00, stock: 35, description: 'High-quality engine oil for all car types.' },
  
  // 宠物用品
  { name: 'Whiskas Adult Cat Food 1.2kg', categoryId: 10, brand: 'Whiskas', mainImage: '/static/products/whiskas-adult-cat-food-1.2kg.jpg', costPrice: 18.00, suggestPrice: 28.00, stock: 120, description: 'Complete nutrition for adult cats with meat and fish.' },
  { name: 'Friskies Adult Cat Food 1.2kg', categoryId: 10, brand: 'Friskies', mainImage: '/static/products/friskies-adult-cat-food-1.2kg.jpg', costPrice: 16.00, suggestPrice: 25.00, stock: 100, description: 'Balanced cat food with essential nutrients.' },
  
  // 运动用品
  { name: 'Wilson Tennis Ball 3pcs', categoryId: 6, brand: 'Wilson', mainImage: '/static/products/wilson-tennis-ball-3pcs.jpg', costPrice: 12.00, suggestPrice: 20.00, stock: 80, description: 'Professional tennis balls for training and matches.' },
  { name: 'Yonex Badminton Shuttlecock 12pcs', categoryId: 6, brand: 'Yonex', mainImage: '/static/products/yonex-shuttlecock-12pcs.jpg', costPrice: 25.00, suggestPrice: 40.00, stock: 60, description: 'High-quality feather shuttlecocks for badminton.' }
];

async function createDailyProducts() {
  try {
    console.log('🚀 开始创建马来西亚生活用品...');
    console.log(`📦 准备创建 ${dailyProducts.length} 个生活用品`);

    // 保存到文件
    const fs = require('fs');
    fs.writeFileSync('daily-products.json', JSON.stringify(dailyProducts, null, 2));
    
    // 生成SQL插入语句
    let sqlContent = `-- 插入${dailyProducts.length}个马来西亚生活用品\n`;
    for (const product of dailyProducts) {
      sqlContent += `INSERT INTO platform_product (name, category_id, brand, main_image, cost_price, suggest_price, stock, description, status) VALUES ('${product.name}', ${product.categoryId}, '${product.brand}', '${product.mainImage}', ${product.costPrice}, ${product.suggestPrice}, ${product.stock}, '${product.description}', 1);\n`;
    }
    
    fs.writeFileSync('daily-products.sql', sqlContent);
    
    console.log('💾 已生成文件:');
    console.log('  - daily-products.json (JSON格式)');
    console.log('  - daily-products.sql (SQL格式)');
    
    console.log('\n📊 生活用品分类统计:');
    const categoryStats = dailyProducts.reduce((acc, product) => {
      acc[product.categoryId] = (acc[product.categoryId] || 0) + 1;
      return acc;
    }, {});
    
    const categoryNames = {
      3: '食品饮料',
      4: '美妆个护', 
      5: '家居生活',
      7: '母婴用品',
      8: '汽车用品',
      9: '图书文具',
      10: '宠物用品',
      6: '运动户外'
    };
    
    Object.entries(categoryStats).forEach(([categoryId, count]) => {
      console.log(`  📂 ${categoryNames[categoryId] || `分类${categoryId}`}: ${count} 个产品`);
    });
    
    console.log('\n📋 产品示例:');
    dailyProducts.slice(0, 10).forEach((product, index) => {
      console.log(`  ${index + 1}. ${product.name} - ${product.brand} - RM${product.suggestPrice}`);
    });

  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  }
}

createDailyProducts();
