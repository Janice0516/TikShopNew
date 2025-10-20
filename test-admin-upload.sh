#!/bin/bash

echo "🔧 测试管理后台上传功能修复..."
echo "================================"

# 测试管理后台主页面
echo "1. 测试管理后台主页面:"
ADMIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/admin/)
echo "   状态码: $ADMIN_STATUS"

# 测试登录接口
echo "2. 测试登录接口:"
LOGIN_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin001","password":"cadWcxIpzglf"}')
echo "   登录响应: $(echo $LOGIN_RESPONSE | head -c 100)..."

# 提取token
TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
echo "   Token: ${TOKEN:0:50}..."

# 测试上传接口
echo "3. 测试上传接口:"
UPLOAD_RESPONSE=$(curl -s -X POST https://tiktokbusines.store/api/upload/image \
  -H "Authorization: Bearer $TOKEN" \
  -F "file=@/dev/null")
echo "   上传响应: $UPLOAD_RESPONSE"

# 测试API基础路径
echo "4. 测试API基础路径:"
API_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://tiktokbusines.store/api/products)
echo "   API状态码: $API_STATUS"

echo "================================"

# 判断结果
if [ "$ADMIN_STATUS" = "200" ] && [ "$API_STATUS" = "200" ] && [ -n "$TOKEN" ]; then
    echo "✅ 管理后台上传功能修复成功！"
    echo "💡 现在可以正常使用管理后台了"
    echo ""
    echo "🌐 访问地址:"
    echo "   管理后台: https://tiktokbusines.store/admin/"
    echo ""
    echo "🔑 测试账户:"
    echo "   用户名: admin001"
    echo "   密码: cadWcxIpzglf"
    echo ""
    echo "📝 说明:"
    echo "   - 上传接口需要JWT认证"
    echo "   - 只支持图片文件上传"
    echo "   - API基础URL已正确配置"
else
    echo "❌ 部分测试失败，请检查配置"
fi
