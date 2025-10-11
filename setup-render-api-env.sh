#!/bin/bash

# Render API 环境变量设置脚本

echo "🚀 使用Render API设置环境变量..."

# 设置你的Render API Key
RENDER_API_KEY="your_render_api_key_here"
SERVICE_ID="your_service_id_here"

# 检查API Key是否设置
if [ "$RENDER_API_KEY" = "your_render_api_key_here" ]; then
    echo "❌ 请先设置RENDER_API_KEY"
    echo "🔑 获取API Key: https://dashboard.render.com/account/api-keys"
    exit 1
fi

# 设置环境变量的函数
set_env_var() {
    local key=$1
    local value=$2
    
    echo "📝 设置 $key = $value"
    
    curl -X PATCH "https://api.render.com/v1/services/$SERVICE_ID/env-vars" \
        -H "Authorization: Bearer $RENDER_API_KEY" \
        -H "Content-Type: application/json" \
        -d "{
            \"envVar\": {
                \"key\": \"$key\",
                \"value\": \"$value\"
            }
        }"
    
    echo ""
}

# 设置所有环境变量
set_env_var "VITE_API_BASE_URL" "https://tiktokshop-api.onrender.com/api"
set_env_var "VITE_APP_TITLE" "TikTok Shop"
set_env_var "VITE_APP_VERSION" "1.0.0"
set_env_var "VITE_NODE_ENV" "production"

echo "✅ 环境变量设置完成！"
echo ""
echo "📋 已设置的环境变量："
echo "   - VITE_API_BASE_URL: https://tiktokshop-api.onrender.com/api"
echo "   - VITE_APP_TITLE: TikTok Shop"
echo "   - VITE_APP_VERSION: 1.0.0"
echo "   - VITE_NODE_ENV: production"
echo ""
echo "🔄 服务将自动重新部署以应用新的环境变量"
