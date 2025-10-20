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

// 商品数据
const products = [
  { brand: 'Maggi', nameZh: '鸡肉味速食面 5包', nameEn: 'Maggi Chicken Instant Noodles (5 packs)', price: 5.00, categoryId: 13 },
  { brand: 'Julie\'s', nameZh: '花生酱三明治饼干 200g', nameEn: 'Julie\'s Peanut Butter Sandwich Biscuits 200g', price: 4.90, categoryId: 13 },
  { brand: 'BOH', nameZh: '红茶袋装 50袋', nameEn: 'BOH Black Tea Bags (50 bags)', price: 9.10, categoryId: 15 },
  { brand: 'Nestlé', nameZh: '鲜奶 1L', nameEn: 'Nestlé Fresh Milk 1L', price: 5.60, categoryId: 15 },
  { brand: 'Dutch Lady', nameZh: 'UHT 牛奶 1L', nameEn: 'Dutch Lady UHT Milk 1L', price: 4.90, categoryId: 15 },
  { brand: 'Dettol', nameZh: '抗菌沐浴露 1L', nameEn: 'Dettol Body Wash 1L', price: 11.20, categoryId: 4 },
  { brand: 'Pantene', nameZh: '洗发水 750ml', nameEn: 'Pantene Shampoo 750ml', price: 13.25, categoryId: 4 },
  { brand: 'Colgate', nameZh: '牙膏 120g', nameEn: 'Colgate Toothpaste 120g', price: 4.80, categoryId: 4 },
  { brand: 'Dove', nameZh: '洗手液 500ml', nameEn: 'Dove Hand Wash 500ml', price: 8.40, categoryId: 4 },
  { brand: 'Downy', nameZh: '衣物柔顺剂 2L', nameEn: 'Downy Fabric Softener 2L', price: 14.00, categoryId: 5 },
  { brand: 'Attack', nameZh: '洗衣液 3L', nameEn: 'Attack Laundry Detergent 3L', price: 17.50, categoryId: 5 },
  { brand: 'Fa', nameZh: '香体沐浴露 500ml', nameEn: 'Fa Body Wash 500ml', price: 7.00, categoryId: 4 },
  { brand: '3M', nameZh: '厨房纸巾 6卷', nameEn: '3M Kitchen Towel (6 rolls)', price: 7.00, categoryId: 5 },
  { brand: 'Kleenex', nameZh: '面纸 100张', nameEn: 'Kleenex Facial Tissue (100 sheets)', price: 3.50, categoryId: 5 },
  { brand: 'Philips', nameZh: 'LED 台灯', nameEn: 'Philips LED Desk Lamp', price: 104.50, categoryId: 2 },
  { brand: 'Panasonic', nameZh: '电动剃须刀', nameEn: 'Panasonic Electric Shaver', price: 139.30, categoryId: 2 },
  { brand: 'Khind', nameZh: '7kg 全自动洗衣机', nameEn: 'Khind 7kg Washing Machine', price: 424.20, categoryId: 12 },
  { brand: 'Midea', nameZh: '6kg 全自动洗衣机', nameEn: 'Midea 6kg Washing Machine', price: 359.80, categoryId: 12 },
  { brand: 'IKEA', nameZh: '微波炉', nameEn: 'IKEA Microwave Oven', price: 230.30, categoryId: 12 },
  { brand: 'HOUZE', nameZh: '旋转拖把', nameEn: 'HOUZE Tornado Mop', price: 10.54, categoryId: 5 },
  { brand: 'Ajax', nameZh: '清洁剂 500ml', nameEn: 'Ajax Cleaner 500ml', price: 4.90, categoryId: 5 },
  { brand: 'Clorox', nameZh: '漂白水 2L', nameEn: 'Clorox Bleach 2L', price: 13.23, categoryId: 5 },
  { brand: 'Glad', nameZh: '垃圾袋 100个', nameEn: 'Glad Garbage Bags (100 pcs)', price: 8.40, categoryId: 5 },
  { brand: 'Eveready', nameZh: 'AA 电池 4粒', nameEn: 'Eveready AA Batteries (4 pcs)', price: 7.00, categoryId: 2 },
  { brand: 'Tefal', nameZh: '不粘平底锅 28cm', nameEn: 'Tefal Non-stick Frying Pan 28cm', price: 139.30, categoryId: 19 },
  { brand: 'Tupperware', nameZh: '食物储存盒套装', nameEn: 'Tupperware Food Container Set', price: 69.30, categoryId: 19 },
  { brand: 'Orient', nameZh: '电风扇 16寸', nameEn: 'Orient 16" Electric Fan', price: 83.30, categoryId: 12 },
  { brand: 'Sharp', nameZh: '微波炉', nameEn: 'Sharp Microwave Oven', price: 349.30, categoryId: 12 },
  { brand: 'Mistral', nameZh: '空气净化器', nameEn: 'Mistral Air Purifier', price: 279.30, categoryId: 12 },
  { brand: 'Bosch', nameZh: '电钻套装', nameEn: 'Bosch Drill Set', price: 389.30, categoryId: 2 },
  { brand: 'Lenovo', nameZh: '无线鼠标键盘套装', nameEn: 'Lenovo Wireless Mouse & Keyboard Set', price: 104.50, categoryId: 11 },
  { brand: 'Samsung', nameZh: '手机保护壳', nameEn: 'Samsung Phone Case', price: 27.30, categoryId: 10 },
  { brand: 'Xiaomi', nameZh: '无线蓝牙耳机', nameEn: 'Xiaomi Wireless Earbuds', price: 111.40, categoryId: 2 },
  { brand: 'Google', nameZh: '智能音箱', nameEn: 'Google Nest Speaker', price: 279.30, categoryId: 2 },
  { brand: 'Philips Avent', nameZh: '奶瓶 260ml', nameEn: 'Philips Avent Baby Bottle 260ml', price: 48.30, categoryId: 5 },
  { brand: 'Pampers', nameZh: '婴儿尿布 L号 60片', nameEn: 'Pampers Diapers L60', price: 55.20, categoryId: 5 },
  { brand: 'Huggies', nameZh: '婴儿湿巾 80片', nameEn: 'Huggies Baby Wipes 80 pcs', price: 9.80, categoryId: 5 },
  { brand: 'Gillette', nameZh: '剃须刀 1支装', nameEn: 'Gillette Razor 1 pc', price: 27.30, categoryId: 4 },
  { brand: 'Oral-B', nameZh: '电动牙刷头 2支装', nameEn: 'Oral-B Brush Heads 2 pcs', price: 41.20, categoryId: 4 },
  { brand: 'Panasonic Lumix', nameZh: '数码相机 12MP', nameEn: 'Panasonic Lumix Camera 12MP', price: 349.30, categoryId: 2 },
  { brand: 'Havit', nameZh: '蓝牙音箱', nameEn: 'Havit Bluetooth Speaker', price: 55.20, categoryId: 2 },
  { brand: 'X-Life', nameZh: '多功能料理机', nameEn: 'X-Life Food Processor', price: 139.30, categoryId: 19 },
  { brand: 'Tefal', nameZh: '空气炸锅 4.5L', nameEn: 'Tefal Air Fryer 4.5L', price: 279.30, categoryId: 12 },
  { brand: 'Dyson', nameZh: '吸尘器', nameEn: 'Dyson Vacuum Cleaner', price: 1389.30, categoryId: 12 },
  { brand: 'Mistral', nameZh: '油汀取暖器', nameEn: 'Mistral Oil Heater', price: 209.30, categoryId: 12 },
  { brand: 'Casio', nameZh: '数字手表', nameEn: 'Casio Digital Watch', price: 139.30, categoryId: 2 },
  { brand: 'Canon', nameZh: '彩色喷墨打印机', nameEn: 'Canon Colour Printer', price: 349.30, categoryId: 2 },
  { brand: 'Brother', nameZh: '激光多功能打印机', nameEn: 'Brother Laser Printer', price: 559.30, categoryId: 2 },
  { brand: 'Sekonic', nameZh: '光度计', nameEn: 'Sekonic Light Meter', price: 139.30, categoryId: 2 },
  { brand: 'Logitech', nameZh: '无线鼠标', nameEn: 'Logitech Wireless Mouse', price: 69.30, categoryId: 11 },
  { brand: 'Milo', nameZh: '美禄饮品 1kg', nameEn: 'Milo Chocolate Drink 1kg', price: 19.60, categoryId: 15 },
  { brand: 'Nescafé', nameZh: '速溶咖啡 200g', nameEn: 'Nescafé Classic 200g', price: 20.30, categoryId: 15 },
  { brand: 'F&N', nameZh: '炼乳 500g', nameEn: 'F&N Condensed Milk 500g', price: 4.90, categoryId: 15 },
  { brand: 'Ayam Brand', nameZh: '金枪鱼罐头 185g', nameEn: 'Ayam Brand Tuna 185g', price: 6.30, categoryId: 13 },
  { brand: 'Lee Kum Kee', nameZh: '蚝油 510g', nameEn: 'Lee Kum Kee Oyster Sauce 510g', price: 7.00, categoryId: 13 },
  { brand: 'Knife', nameZh: '食用油 5L', nameEn: 'Knife Cooking Oil 5L', price: 31.50, categoryId: 13 },
  { brand: 'Jasmine', nameZh: '白米 10kg', nameEn: 'Jasmine White Rice 10kg', price: 24.50, categoryId: 13 },
  { brand: 'Cap Rambutan', nameZh: '白砂糖 1kg', nameEn: 'Cap Rambutan Sugar 1kg', price: 2.50, categoryId: 13 },
  { brand: 'Cap Kapal ABC', nameZh: '食盐 1kg', nameEn: 'Cap Kapal ABC Salt 1kg', price: 1.25, categoryId: 13 },
  { brand: 'Gardenia', nameZh: '面包 400g', nameEn: 'Gardenia Bread 400g', price: 3.15, categoryId: 13 },
  { brand: 'Jacob\'s', nameZh: '全麦饼干 700g', nameEn: 'Jacob\'s Wheat Crackers 700g', price: 8.75, categoryId: 13 },
  { brand: 'Lifebuoy', nameZh: '香皂 4块装', nameEn: 'Lifebuoy Soap (4 pcs)', price: 7.00, categoryId: 4 },
  { brand: 'Breeze', nameZh: '洗衣粉 2kg', nameEn: 'Breeze Detergent Powder 2kg', price: 10.50, categoryId: 5 },
  { brand: 'Sunlight', nameZh: '洗碗液 1L', nameEn: 'Sunlight Dishwashing Liquid 1L', price: 5.55, categoryId: 5 },
  { brand: 'Ambi Pur', nameZh: '空气清新剂 275g', nameEn: 'Ambi Pur Air Freshener 275g', price: 9.80, categoryId: 5 },
  { brand: 'Shokubutsu', nameZh: '沐浴露 1L', nameEn: 'Shokubutsu Body Wash 1L', price: 9.10, categoryId: 4 },
  { brand: 'Anlene', nameZh: '高钙奶粉 1kg', nameEn: 'Anlene Milk Powder 1kg', price: 24.50, categoryId: 15 },
  { brand: 'Fernleaf', nameZh: '全脂奶粉 1kg', nameEn: 'Fernleaf Full Cream Milk Powder 1kg', price: 23.10, categoryId: 15 },
  { brand: 'Red Bull', nameZh: '红牛能量饮料 250ml', nameEn: 'Red Bull Energy Drink 250ml', price: 4.20, categoryId: 15 },
  { brand: '100PLUS', nameZh: '等渗运动饮料 500ml', nameEn: '100PLUS Isotonic Drink 500ml', price: 2.10, categoryId: 15 },
  { brand: 'Spritzer', nameZh: '矿泉水 1.5L', nameEn: 'Spritzer Mineral Water 1.5L', price: 1.40, categoryId: 15 },
  { brand: 'MamyPoko', nameZh: '婴儿尿布 M号 60片', nameEn: 'MamyPoko Diaper M60', price: 48.30, categoryId: 5 },
  { brand: 'Dettol', nameZh: '消毒液 500ml', nameEn: 'Dettol Antiseptic Liquid 500ml', price: 9.10, categoryId: 4 },
  { brand: 'Watsons', nameZh: '棉花棒 200支', nameEn: 'Watsons Cotton Buds 200 pcs', price: 3.50, categoryId: 4 },
  { brand: 'Oral-B', nameZh: '牙刷 3支装', nameEn: 'Oral-B Toothbrush 3 pcs', price: 9.10, categoryId: 4 },
  { brand: 'Sunsilk', nameZh: '护发素 350ml', nameEn: 'Sunsilk Conditioner 350ml', price: 7.70, categoryId: 4 },
  { brand: 'Kleenex', nameZh: '厕纸 10卷', nameEn: 'Kleenex Toilet Roll (10 rolls)', price: 8.40, categoryId: 5 },
  { brand: 'Ajax', nameZh: '厨房清洁剂 1L', nameEn: 'Ajax Kitchen Cleaner 1L', price: 7.00, categoryId: 5 },
  { brand: 'MamyPoko', nameZh: '婴儿湿巾 100片', nameEn: 'MamyPoko Baby Wipes 100 pcs', price: 7.00, categoryId: 5 },
  { brand: 'Energizer', nameZh: '电池 AAA 4粒', nameEn: 'Energizer AAA Batteries 4 pcs', price: 8.40, categoryId: 2 },
  { brand: 'Panasonic', nameZh: 'LED 灯泡 9W', nameEn: 'Panasonic LED Bulb 9W', price: 7.70, categoryId: 2 },
  { brand: 'IKEA', nameZh: '餐具套装 16件', nameEn: 'IKEA Cutlery Set 16 pcs', price: 27.30, categoryId: 19 },
  { brand: 'Samsung', nameZh: '便携充电宝 10000mAh', nameEn: 'Samsung Power Bank 10000mAh', price: 90.30, categoryId: 2 },
  { brand: 'Kingston', nameZh: '32GB U盘', nameEn: 'Kingston 32GB Flash Drive', price: 20.30, categoryId: 2 },
  { brand: 'Seagate', nameZh: '移动硬盘 1TB', nameEn: 'Seagate 1TB Portable HDD', price: 230.30, categoryId: 2 },
  { brand: 'Logitech', nameZh: '键盘', nameEn: 'Logitech Keyboard', price: 55.20, categoryId: 11 },
  { brand: 'Xiaomi', nameZh: '智能手环', nameEn: 'Xiaomi Smart Band', price: 139.30, categoryId: 2 },
  { brand: 'Realme', nameZh: '无线耳机', nameEn: 'Realme Wireless Earbuds', price: 111.30, categoryId: 2 },
  { brand: 'Philips', nameZh: '电水壶 1.7L', nameEn: 'Philips Electric Kettle 1.7L', price: 76.30, categoryId: 12 },
  { brand: 'Cornell', nameZh: '烤面包机', nameEn: 'Cornell Toaster', price: 62.30, categoryId: 12 },
  { brand: 'Sharp', nameZh: '电饭煲 1.8L', nameEn: 'Sharp Rice Cooker 1.8L', price: 90.30, categoryId: 12 },
  { brand: 'Midea', nameZh: '电磁炉', nameEn: 'Midea Induction Cooker', price: 139.30, categoryId: 12 },
  { brand: 'Elba', nameZh: '榨汁机', nameEn: 'Elba Juicer', price: 111.30, categoryId: 12 },
  { brand: 'Khind', nameZh: '电风扇 12寸', nameEn: 'Khind Fan 12"', price: 55.20, categoryId: 12 },
  { brand: 'Philips', nameZh: '吹风机 1600W', nameEn: 'Philips Hair Dryer 1600W', price: 76.30, categoryId: 4 },
  { brand: 'Tefal', nameZh: '电煮锅 1.5L', nameEn: 'Tefal Multi Cooker 1.5L', price: 139.30, categoryId: 12 },
  { brand: 'Dyson', nameZh: '吹风机', nameEn: 'Dyson Supersonic Hair Dryer', price: 1399.30, categoryId: 4 },
  { brand: 'Casio', nameZh: '科学计算器', nameEn: 'Casio Scientific Calculator', price: 41.20, categoryId: 2 },
  { brand: 'Huawei', nameZh: '蓝牙音箱', nameEn: 'Huawei Bluetooth Speaker', price: 111.30, categoryId: 2 },
  { brand: 'Acer', nameZh: '显示器 24寸', nameEn: 'Acer 24" Monitor', price: 559.30, categoryId: 11 }
];

async function insertProducts() {
  const connection = await mysql.createConnection(dbConfig);
  
  try {
    console.log('🚀 开始插入商品数据...');
    
    // 检查是否已有商品数据
    const [existingProducts] = await connection.execute('SELECT COUNT(*) as count FROM platform_product');
    if (existingProducts[0].count > 0) {
      console.log(`⚠️  数据库中已有 ${existingProducts[0].count} 个商品，是否继续插入？`);
      console.log('💡 提示：新商品将追加到现有数据中');
    }
    
    let successCount = 0;
    let errorCount = 0;
    
    for (let i = 0; i < products.length; i++) {
      const product = products[i];
      
      try {
        // 生成商品编号
        const productNo = `PROD${String(i + 1).padStart(4, '0')}`;
        
        // 计算成本价（假设成本价是售价的70%）
        const costPrice = product.price * 0.7;
        
        // 计算建议售价（成本价 * 1.5）
        const suggestPrice = costPrice * 1.5;
        
        // 插入商品
        const [result] = await connection.execute(`
          INSERT INTO platform_product (
            product_no, 
            name, 
            category_id, 
            brand, 
            main_image, 
            cost_price, 
            suggest_price, 
            stock, 
            sales, 
            description, 
            status, 
            sort
          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        `, [
          productNo,
          `${product.nameZh} / ${product.nameEn}`, // 中英文名称组合
          product.categoryId,
          product.brand,
          '/static/default-product.jpg', // 默认图片
          costPrice,
          suggestPrice,
          Math.floor(Math.random() * 100) + 10, // 随机库存 10-110
          Math.floor(Math.random() * 50), // 随机销量 0-50
          `${product.nameZh} - ${product.nameEn}`, // 描述
          1, // 上架状态
          i + 1 // 排序
        ]);
        
        successCount++;
        console.log(`✅ [${i + 1}/${products.length}] ${product.brand} - ${product.nameZh} (ID: ${result.insertId})`);
        
      } catch (error) {
        errorCount++;
        console.error(`❌ [${i + 1}/${products.length}] ${product.brand} - ${product.nameZh}: ${error.message}`);
      }
    }
    
    console.log('\n🎉 商品插入完成！');
    console.log(`✅ 成功插入: ${successCount} 个商品`);
    console.log(`❌ 失败: ${errorCount} 个商品`);
    
    // 查询插入后的总数
    const [totalProducts] = await connection.execute('SELECT COUNT(*) as count FROM platform_product');
    console.log(`📊 数据库中总商品数: ${totalProducts[0].count}`);
    
  } catch (error) {
    console.error('❌ 插入商品时发生错误:', error);
  } finally {
    await connection.end();
  }
}

// 运行脚本
insertProducts().catch(console.error);
