#!/bin/bash

echo "🌐 测试域名访问..."
echo "================================"

# 测试主域名
echo "1. 测试主域名 (用户前端):"
curl -s -o /dev/null -w "状态码: %{http_code}, 响应时间: %{time_total}s\n" https://tiktokbusines.store

# 测试管理后台
echo "2. 测试管理后台:"
curl -s -o /dev/null -w "状态码: %{http_code}, 响应时间: %{time_total}s\n" https://tiktokbusines.store/admin/

# 测试商家后台
echo "3. 测试商家后台:"
curl -s -o /dev/null -w "状态码: %{http_code}, 响应时间: %{time_total}s\n" https://tiktokbusines.store/merchant/

# 测试API接口
echo "4. 测试API接口:"
curl -s -o /dev/null -w "状态码: %{http_code}, 响应时间: %{time_total}s\n" https://tiktokbusines.store/api/products

# 测试静态文件
echo "5. 测试静态文件:"
curl -s -o /dev/null -w "状态码: %{http_code}, 响应时间: %{time_total}s\n" https://tiktokbusines.store/uploads/images/test.jpg

echo "================================"
echo "✅ 域名测试完成！"
echo ""
echo "🌐 可访问的地址："
echo "   主域名: https://tiktokbusines.store"
echo "   管理后台: https://tiktokbusines.store/admin/"
echo "   商家后台: https://tiktokbusines.store/merchant/"
echo "   API接口: https://tiktokbusines.store/api/"
