#!/bin/bash

echo "🔍 测试静态资源MIME类型..."
echo "================================"

# 测试CSS文件
echo "1. 测试CSS文件:"
curl -s -I https://tiktokbusines.store/assets/index-LyVmV0no.css | grep -i content-type

# 测试JS文件
echo "2. 测试JS文件:"
curl -s -I https://tiktokbusines.store/assets/index-Db68PnkJ.js | grep -i content-type

# 测试vendor JS文件
echo "3. 测试vendor JS文件:"
curl -s -I https://tiktokbusines.store/assets/vendor-DY45WaHR.js | grep -i content-type

# 测试element JS文件
echo "4. 测试element JS文件:"
curl -s -I https://tiktokbusines.store/assets/element-dS3XB2Fo.js | grep -i content-type

echo "================================"
echo "✅ 静态资源测试完成！"
echo ""
echo "如果所有MIME类型都正确，用户前端应该能正常显示。"
