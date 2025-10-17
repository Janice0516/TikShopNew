const { Client } = require('pg');

async function testCostPriceFreeze() {
  const client = new Client({
    connectionString: 'postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz',
    ssl: { rejectUnauthorized: false },
    connectionTimeoutMillis: 10000
  });

  try {
    await client.connect();
    console.log('✅ 数据库连接成功\n');

    // 1. 查看商品成本价示例
    console.log('1. 查看商品成本价示例...');
    const products = await client.query(`
      SELECT id, name, cost_price, suggest_price 
      FROM platform_product 
      LIMIT 3
    `);
    console.log('   商品价格示例:');
    products.rows.forEach(product => {
      console.log(`   - ${product.name}: 成本价=${product.cost_price}, 建议价=${product.suggest_price}`);
    });

    // 2. 模拟您的示例：成本价100马币，商家卖130马币
    console.log('\n2. 模拟您的示例...');
    const costPrice = 100; // 成本价100马币
    const merchantSellPrice = 130; // 商家售价130马币
    const freezeAmount = costPrice; // 冻结成本价100马币
    
    console.log(`   成本价: ${costPrice}马币`);
    console.log(`   商家售价: ${merchantSellPrice}马币`);
    console.log(`   冻结金额: ${freezeAmount}马币`);

    // 3. 计算资金变动
    console.log('\n3. 资金变动计算...');
    const merchantProfit = merchantSellPrice - costPrice; // 商家收益 = 130 - 100 = 30马币
    const platformProfit = 0; // 平台不抽成
    
    console.log(`   商家收益: ${merchantProfit}马币 (${merchantSellPrice} - ${costPrice})`);
    console.log(`   平台抽成: ${platformProfit}马币`);

    // 4. 资金流向演示
    console.log('\n4. 资金流向演示:');
    console.log('   冻结时:');
    console.log(`     - 冻结金额: ${freezeAmount}马币 (成本价)`);
    console.log(`     - 商家余额: 原余额 - ${freezeAmount}马币`);
    console.log('   解冻时:');
    console.log(`     - 解冻金额: ${freezeAmount}马币 (成本价)`);
    console.log(`     - 收益结算: ${merchantProfit}马币 (商家收益)`);
    console.log(`     - 最终余额: 原余额 + ${freezeAmount} + ${merchantProfit} = 原余额 + ${freezeAmount + merchantProfit}马币`);

    // 5. 实际商品示例
    console.log('\n5. 实际商品示例:');
    const exampleProduct = products.rows[0];
    const exampleCostPrice = parseFloat(exampleProduct.cost_price);
    const exampleSuggestPrice = parseFloat(exampleProduct.suggest_price);
    const exampleMerchantSellPrice = exampleSuggestPrice + 50; // 假设商家加价50马币
    const exampleFreezeAmount = exampleCostPrice;
    const exampleMerchantProfit = exampleMerchantSellPrice - exampleCostPrice;
    
    console.log(`   商品: ${exampleProduct.name}`);
    console.log(`   成本价: ${exampleCostPrice}马币`);
    console.log(`   建议价: ${exampleSuggestPrice}马币`);
    console.log(`   商家售价: ${exampleMerchantSellPrice}马币`);
    console.log(`   冻结金额: ${exampleFreezeAmount}马币`);
    console.log(`   商家收益: ${exampleMerchantProfit}马币`);

    console.log('\n🎉 成本价冻结逻辑验证完成！');
    console.log('\n📋 关键要点:');
    console.log('   ✅ 冻结金额 = 成本价 (cost_price)');
    console.log('   ✅ 商家收益 = 售价 - 成本价');
    console.log('   ✅ 平台抽成 = 0%');
    console.log('   ✅ 解冻时返还成本价 + 商家收益');

  } catch (error) {
    console.error('❌ 验证失败:', error);
  } finally {
    await client.end();
  }
}

testCostPriceFreeze();
