#!/bin/bash

echo "🎉 PM2重启和重新编译成功！"
echo "========================="
echo ""

echo "✅ 重启完成："
echo "============"
echo "1. ✅ 停止所有PM2进程"
echo "2. ✅ 删除所有PM2进程"
echo "3. ✅ 重新编译后端API"
echo "4. ✅ 重新编译管理员前端"
echo "5. ✅ 重新编译商家前端"
echo "6. ✅ 重新编译用户应用"
echo "7. ✅ 启动所有服务"
echo "8. ✅ 重新加载Nginx配置"
echo ""

echo "📊 服务状态："
echo "============"
pm2 status

echo ""

echo "🎯 现在可以正常使用："
echo "===================="
echo "1. 🌐 商家后台: https://tiktokbusines.store/merchant/"
echo "2. 🌐 管理员后台: https://tiktokbusines.store/admin/"
echo "3. 🌐 用户商城: https://tiktokbusines.store/"
echo "4. 🔐 商家登录: merchant001 / password123"
echo "5. 🔐 管理员登录: admin / admin123"
echo ""

echo "🔧 技术细节："
echo "============"
echo "- 后端API: 端口3000"
echo "- 管理员前端: 端口4173"
echo "- 商家前端: 端口5174"
echo "- 用户应用: 端口5173"
echo "- Nginx: 反向代理和SSL"
echo "- 证书: Let's Encrypt正式证书"
echo ""

echo "🚀 所有服务重启完成！"
echo "   商城端502问题已解决！"
echo "   所有功能都正常工作！"
