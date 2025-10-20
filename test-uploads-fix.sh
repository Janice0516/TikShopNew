#!/bin/bash

echo "🔧 测试uploads路径修复结果..."
echo "================================"

# 测试uploads路径
echo "1. 测试uploads路径:"
UPLOADS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/uploads/images/e51cd3e39cf5d8b8433410410c9653b2fb.jpg)
echo "   状态码: $UPLOADS_STATUS"

# 测试图片内容类型
echo "2. 测试图片内容类型:"
CONTENT_TYPE=$(curl -s -I https://tiktokbusines.store/uploads/images/e51cd3e39cf5d8b8433410410c9653b2fb.jpg | grep -i content-type | cut -d: -f2 | tr -d ' \r\n' | head -c 20)
echo "   内容类型: $CONTENT_TYPE"

# 测试图片大小
echo "3. 测试图片大小:"
CONTENT_LENGTH=$(curl -s -I https://tiktokbusines.store/uploads/images/e51cd3e39cf5d8b8433410410c9653b2fb.jpg | grep -i content-length | cut -d: -f2 | tr -d ' \r\n')
echo "   文件大小: $CONTENT_LENGTH bytes"

# 测试其他图片文件
echo "4. 测试其他图片文件:"
OTHER_IMAGE=$(ls /root/TikShop/ecommerce-backend/uploads/images/ | head -1)
if [ -n "$OTHER_IMAGE" ]; then
    OTHER_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "https://tiktokbusines.store/uploads/images/$OTHER_IMAGE")
    echo "   其他图片状态码: $OTHER_STATUS"
else
    echo "   没有找到其他图片文件"
fi

echo "================================"

# 判断结果
if [ "$UPLOADS_STATUS" = "200" ] && [ "$CONTENT_LENGTH" -gt 0 ]; then
    echo "✅ uploads路径修复成功！"
    echo "💡 现在可以正常访问上传的图片了"
    echo ""
    echo "🌐 测试地址:"
    echo "   图片URL: https://tiktokbusines.store/uploads/images/e51cd3e39cf5d8b8433410410c9653b2fb.jpg"
    echo ""
    echo "📝 说明:"
    echo "   - 修复了Nginx配置中的正则表达式冲突"
    echo "   - uploads路径现在正确代理到后端服务"
    echo "   - 图片文件可以正常访问和显示"
else
    echo "❌ 部分测试失败，请检查配置"
fi
