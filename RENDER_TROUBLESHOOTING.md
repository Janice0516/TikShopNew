# Render 部署问题排查指南

## 🚨 当前问题
Render部署失败，显示 "Exited with status 1 while building your code"

## 🔍 可能的原因

### 1. 环境变量缺失
确保在Render控制台中设置了以下环境变量：

```bash
NODE_ENV=production
DB_TYPE=postgres
DB_HOST=dpg-d0j8q8h2s78s73fq8hpg-a.oregon-postgres.render.com
DB_PORT=5432
DB_USERNAME=tiktokshop_slkz_user
DB_PASSWORD=U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn
DB_DATABASE=tiktokshop_slkz
JWT_SECRET=56dcb52ecafb60675a58d3472d7af4077f491c32e477372349f82f5ef3b12e4d7ff367b77c5f05bb0969843d1fbc3a647a69633dc6614d87ceea2d55c0ba31d6
PORT=10000
```

### 2. 构建命令问题
确保构建命令正确：
- **构建命令**: `npm install && npm run build`
- **启动命令**: `npm run start:prod`

### 3. 根目录设置
确保根目录设置为：`ecommerce-backend`

## 🛠️ 解决步骤

### 步骤1: 检查环境变量
1. 进入Render控制台
2. 选择 `tiktokshop-api` 服务
3. 点击 "Environment" 标签
4. 确保所有必需的环境变量都已设置

### 步骤2: 检查构建日志
1. 点击 "Logs" 标签
2. 查看详细的构建日志
3. 寻找具体的错误信息

### 步骤3: 手动触发部署
1. 点击 "Manual Deploy" 按钮
2. 选择 "Deploy latest commit"
3. 观察构建过程

## 🔧 常见问题修复

### 问题1: 数据库连接失败
如果看到数据库连接错误，检查：
- `DB_HOST` 是否正确
- `DB_PASSWORD` 是否包含特殊字符（需要URL编码）
- `DB_DATABASE` 名称是否正确

### 问题2: TypeScript编译错误
如果看到TypeScript错误，检查：
- 是否有语法错误
- 是否有类型定义缺失
- 是否有导入路径错误

### 问题3: 依赖安装失败
如果看到npm install失败，检查：
- package.json是否有语法错误
- 是否有不兼容的依赖版本
- 是否需要特定的Node.js版本

## 📋 检查清单

- [ ] 所有环境变量已设置
- [ ] 构建命令正确
- [ ] 启动命令正确
- [ ] 根目录设置正确
- [ ] GitHub仓库连接正常
- [ ] 分支设置为 `main`

## 🆘 如果问题仍然存在

1. 查看完整的构建日志
2. 检查是否有特定的错误信息
3. 尝试简化构建过程（分步骤构建）
4. 联系Render技术支持
