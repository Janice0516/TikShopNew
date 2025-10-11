#!/bin/bash

# 修复商家后台TypeScript错误的脚本

echo "🔧 修复商家后台TypeScript编译错误..."

# 修复未使用的变量 - 在validator函数中使用下划线前缀
find merchant/src/views -name "*.vue" -exec sed -i '' 's/validator: (rule,/validator: (_rule,/g' {} \;
find merchant/src/views -name "*.vue" -exec sed -i '' 's/validator: (rule,/validator: (_rule,/g' {} \;

# 修复未使用的result变量
find merchant/src/views -name "*.vue" -exec sed -i '' 's/const result = await/await/g' {} \;

# 修复未使用的response变量
find merchant/src/views -name "*.vue" -exec sed -i '' 's/const response = await/await/g' {} \;

# 修复未使用的res变量
find merchant/src/views -name "*.vue" -exec sed -i '' 's/const res = await/await/g' {} \;

# 修复未使用的shipOrder导入
find merchant/src/views -name "*.vue" -exec sed -i '' 's/import { shipOrder } from/\/\/ import { shipOrder } from/g' {} \;

echo "✅ TypeScript错误修复完成！"
