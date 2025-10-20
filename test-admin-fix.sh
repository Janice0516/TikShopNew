#!/bin/bash

echo "🔧 测试管理后台修复结果..."
echo "================================"

# 测试管理后台主页面
echo "1. 测试管理后台主页面:"
ADMIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/admin/)
echo "   状态码: $ADMIN_STATUS"

# 测试CSS文件
echo "2. 测试CSS文件:"
CSS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/admin/assets/index-Dqv8VrO9.css)
CSS_TYPE=$(curl -s -I https://tiktokbusines.store/admin/assets/index-Dqv8VrO9.css | grep -i content-type | cut -d: -f2 | tr -d ' \r\n')
echo "   CSS状态码: $CSS_STATUS"
echo "   CSS类型: $CSS_TYPE"

# 测试JS文件
echo "3. 测试JS文件:"
JS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/admin/assets/index-B0Vfd4x6.js)
JS_TYPE=$(curl -s -I https://tiktokbusines.store/admin/assets/index-B0Vfd4x6.js | grep -i content-type | cut -d: -f2 | tr -d ' \r\n')
echo "   JS状态码: $JS_STATUS"
echo "   JS类型: $JS_TYPE"

# 测试SVG文件
echo "4. 测试SVG文件:"
SVG_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/admin/vite.svg)
echo "   SVG状态码: $SVG_STATUS"

echo "================================"

# 判断结果
if [ "$ADMIN_STATUS" = "200" ] && [ "$CSS_STATUS" = "200" ] && [ "$JS_STATUS" = "200" ] && [ "$SVG_STATUS" = "200" ] && [ "$CSS_TYPE" = "text/css" ] && [ "$JS_TYPE" = "application/javascript" ]; then
    echo "✅ 管理后台修复成功！"
    echo "💡 现在可以正常访问管理后台了"
    echo ""
    echo "🌐 访问地址:"
    echo "   管理后台: https://tiktokbusines.store/admin/"
    echo ""
    echo "🔑 测试账户:"
    echo "   用户名: admin001"
    echo "   密码: cadWcxIpzglf"
else
    echo "❌ 部分测试失败，请检查配置"
fi
