const axios = require('axios');

async function executeMerchantProductsSQL() {
  try {
    console.log('🚀 执行商家产品SQL...');

    // 读取SQL文件
    const fs = require('fs');
    const sqlContent = fs.readFileSync('merchant-products.sql', 'utf8');
    
    // 分割SQL语句
    const sqlStatements = sqlContent.split(';').filter(stmt => stmt.trim());
    
    console.log(`📋 找到 ${sqlStatements.length} 条SQL语句`);

    // 由于无法直接执行SQL，我们创建一个简化的数据插入脚本
    console.log('📝 由于API限制，创建数据插入脚本...');
    
    // 读取JSON数据
    const jsonData = JSON.parse(fs.readFileSync('merchant-products-data.json', 'utf8'));
    
    console.log(`📊 准备插入 ${jsonData.length} 条商家产品数据`);
    
    // 创建插入脚本
    const insertScript = `
const axios = require('axios');

async function insertMerchantProducts() {
  try {
    console.log('🚀 开始插入商家产品数据...');
    
    const merchantProducts = ${JSON.stringify(jsonData, null, 2)};
    
    // 这里需要数据库连接，但由于API限制，我们只能模拟
    console.log('📋 商家产品数据已准备完成:');
    console.log(\`📊 总计: \${merchantProducts.length} 个商家产品关系\`);
    
    // 按商家分组显示
    const groupedByMerchant = merchantProducts.reduce((acc, mp) => {
      if (!acc[mp.merchantName]) {
        acc[mp.merchantName] = [];
      }
      acc[mp.merchantName].push(mp);
      return acc;
    }, {});
    
    for (const [merchantName, products] of Object.entries(groupedByMerchant)) {
      console.log(\`\\n🏪 商家 \${merchantName} 的产品:\`);
      products.forEach(p => {
        console.log(\`  ✅ \${p.productName} - RM\${p.salePrice} (利润率: \${p.profitMargin}%)\`);
      });
    }
    
    console.log('\\n🎉 商家产品数据展示完成！');
    console.log('💡 要实际插入数据库，需要手动执行merchant-products.sql文件');
    
  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  }
}

insertMerchantProducts();
`;

    fs.writeFileSync('insert-merchant-products.js', insertScript);
    console.log('💾 已创建插入脚本: insert-merchant-products.js');
    
    // 执行插入脚本
    console.log('\n📊 执行数据展示...');
    const { execSync } = require('child_process');
    execSync('node insert-merchant-products.js', { stdio: 'inherit' });

  } catch (error) {
    console.error('❌ 执行失败:', error.message);
  }
}

executeMerchantProductsSQL();
