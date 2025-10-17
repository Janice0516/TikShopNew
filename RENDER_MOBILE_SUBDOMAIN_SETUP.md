# Render移动端子域名配置指南

## 概述

现在代码已经支持子域名跳转机制：
- **主域名**: `tikshop-user.onrender.com` (桌面端)
- **移动端子域名**: `m.tikshop-user.onrender.com` (移动端)

## 当前状态

### ✅ 已完成的配置
1. **设备检测逻辑**: 移动设备自动跳转到 `m.` 子域名
2. **跳转代码**: 使用 `window.location.href` 进行域名跳转
3. **错误处理**: 包含备用检测机制

### ⏳ 需要配置的部分
1. **移动端服务**: 在Render上创建 `m.tikshop-user.onrender.com` 服务
2. **域名绑定**: 将移动端子域名绑定到新服务
3. **环境变量**: 配置移动端服务的环境变量

## Render配置步骤

### 1. 创建移动端服务

1. **登录Render Dashboard**
   - 访问 https://dashboard.render.com
   - 登录您的账户

2. **创建新服务**
   - 点击 "New +" 按钮
   - 选择 "Web Service"
   - 选择 "Build and deploy from a Git repository"

3. **连接GitHub仓库**
   - 选择您的GitHub仓库: `Janice0516/TikShop`
   - 选择分支: `main`
   - 选择根目录: `user-app`

4. **配置服务设置**
   - **Name**: `tikshop-mobile`
   - **Environment**: `Node`
   - **Build Command**: `npm install && npm run build`
   - **Start Command**: `npm run preview`
   - **Publish Directory**: `dist`

### 2. 配置环境变量

在移动端服务的环境变量中添加：

```bash
VITE_API_BASE_URL=https://tikshop-backend.onrender.com
VITE_APP_TITLE=TikTok Shop Mobile
VITE_APP_DOMAIN=m.tikshop-user.onrender.com
```

### 3. 配置自定义域名

1. **添加自定义域名**
   - 在服务设置中找到 "Custom Domains"
   - 点击 "Add Custom Domain"
   - 输入域名: `m.tikshop-user.onrender.com`

2. **配置DNS**
   - Render会自动提供DNS配置
   - 按照提示配置DNS记录

3. **启用SSL**
   - Render会自动为自定义域名配置SSL证书
   - 等待SSL证书生效

## 测试验证

### 1. 桌面端测试
- 访问: `https://tikshop-user.onrender.com`
- 预期: 显示桌面端界面，不跳转

### 2. 移动端测试
- 访问: `https://tikshop-user.onrender.com`
- 预期: 自动跳转到 `https://m.tikshop-user.onrender.com`
- 预期: 显示移动端界面

### 3. 直接访问移动端子域名
- 访问: `https://m.tikshop-user.onrender.com`
- 预期: 显示移动端界面

## 当前跳转逻辑

### 设备检测
```javascript
const isMobile = screenWidth < 1024 || 
                platform === 'ios' || 
                platform === 'android'
```

### 跳转逻辑
```javascript
if (isMobile) {
  const currentUrl = window.location.href
  const mobileUrl = currentUrl.replace('tikshop-user.onrender.com', 'm.tikshop-user.onrender.com')
  window.location.href = mobileUrl
}
```

## 优势

### 1. 标准做法
- ✅ 符合行业标准（如淘宝、京东等）
- ✅ 用户体验一致
- ✅ SEO友好

### 2. 性能优化
- ✅ 移动端和桌面端独立优化
- ✅ 减少不必要的代码加载
- ✅ 更好的缓存策略

### 3. 维护便利
- ✅ 独立的部署流程
- ✅ 独立的配置管理
- ✅ 独立的错误处理

## 注意事项

### 1. 域名配置
- 确保两个域名都正确配置
- 确保SSL证书正常工作
- 确保DNS解析正确

### 2. 跳转逻辑
- 避免无限跳转循环
- 处理跳转失败的情况
- 提供手动切换选项

### 3. 用户体验
- 跳转过程要流畅
- 提供加载提示
- 保持用户状态

## 下一步

1. **创建移动端服务**: 按照上述步骤在Render上创建服务
2. **配置域名**: 设置 `m.tikshop-user.onrender.com` 域名
3. **测试跳转**: 验证设备检测和跳转功能
4. **优化体验**: 根据需要调整跳转逻辑

## 技术支持

如果在配置过程中遇到问题，请提供：
1. **错误信息**: 具体的错误日志
2. **配置截图**: Render服务配置截图
3. **测试结果**: 跳转测试的结果

这样我可以进一步协助您完成配置。
