#!/bin/bash

echo "🚀 最终测试 - TikTok Shop 电商平台"
echo "=================================="

# 测试主页面
echo "1. 主页面测试:"
MAIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store)
echo "   状态码: $MAIN_STATUS"

# 测试静态资源
echo "2. 静态资源测试:"
CSS_TYPE=$(curl -s -I https://tiktokbusines.store/assets/index-LyVmV0no.css | grep -i content-type | cut -d: -f2 | tr -d ' \r\n')
JS_TYPE=$(curl -s -I https://tiktokbusines.store/assets/index-Db68PnkJ.js | grep -i content-type | cut -d: -f2 | tr -d ' \r\n')
echo "   CSS类型: $CSS_TYPE"
echo "   JS类型: $JS_TYPE"

# 测试API
echo "3. API测试:"
API_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/api/products)
echo "   API状态码: $API_STATUS"

# 测试管理后台
echo "4. 管理后台测试:"
ADMIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/admin/)
echo "   管理后台状态码: $ADMIN_STATUS"

# 测试商家后台
echo "5. 商家后台测试:"
MERCHANT_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/merchant/)
echo "   商家后台状态码: $MERCHANT_STATUS"

echo "=================================="

# 判断结果
if [ "$MAIN_STATUS" = "200" ] && [ "$CSS_TYPE" = "text/css" ] && [ "$JS_TYPE" = "application/javascript" ] && [ "$API_STATUS" = "200" ] && [ "$ADMIN_STATUS" = "200" ] && [ "$MERCHANT_STATUS" = "200" ]; then
    echo "✅ 所有测试通过！用户前端应该能正常显示。"
    echo ""
    echo "🌐 访问地址："
    echo "   用户商城: https://tiktokbusines.store"
    echo "   管理后台: https://tiktokbusines.store/admin/"
    echo "   商家后台: https://tiktokbusines.store/merchant/"
else
    echo "❌ 部分测试失败，请检查配置。"
fi
