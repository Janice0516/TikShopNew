# 移动端子域名配置

## 域名结构

### 主域名（桌面端）
- **URL**: `https://tikshop-user.onrender.com`
- **用途**: 桌面端用户访问
- **界面**: 桌面端布局（侧边栏+主内容区）

### 移动端子域名
- **URL**: `https://m.tikshop-user.onrender.com`
- **用途**: 移动端用户访问
- **界面**: 移动端布局（垂直滚动）

## 自动跳转逻辑

### 桌面端访问主域名
```
用户访问: https://tikshop-user.onrender.com
    ↓
检测设备类型
    ↓
移动设备？
    ↓ 是
跳转到: https://m.tikshop-user.onrender.com
    ↓ 否
保持桌面端界面
```

### 移动端访问主域名
```
用户访问: https://tikshop-user.onrender.com
    ↓
检测设备类型
    ↓
移动设备？
    ↓ 是
跳转到: https://m.tikshop-user.onrender.com
    ↓ 否
保持桌面端界面
```

## Render部署配置

### 1. 主域名服务
- **服务名**: `tikshop-user`
- **域名**: `tikshop-user.onrender.com`
- **构建命令**: `npm run build`
- **发布目录**: `dist`

### 2. 移动端子域名服务
- **服务名**: `tikshop-mobile`
- **域名**: `m.tikshop-user.onrender.com`
- **构建命令**: `npm run build`
- **发布目录**: `dist`

## 环境变量配置

### 主域名环境变量
```bash
VITE_API_BASE_URL=https://tikshop-backend.onrender.com
VITE_APP_TITLE=TikTok Shop Desktop
VITE_APP_DOMAIN=tikshop-user.onrender.com
```

### 移动端子域名环境变量
```bash
VITE_API_BASE_URL=https://tikshop-backend.onrender.com
VITE_APP_TITLE=TikTok Shop Mobile
VITE_APP_DOMAIN=m.tikshop-user.onrender.com
```

## 跳转代码实现

### 设备检测逻辑
```javascript
const checkDeviceAndRedirect = () => {
  try {
    const systemInfo = uni.getSystemInfoSync()
    const screenWidth = systemInfo.screenWidth || 0
    const platform = systemInfo.platform || ''
    
    const isMobile = screenWidth < 1024 || 
                    platform === 'ios' || 
                    platform === 'android'
    
    if (isMobile) {
      // 跳转到移动端子域名
      const currentUrl = window.location.href
      const mobileUrl = currentUrl.replace('tikshop-user.onrender.com', 'm.tikshop-user.onrender.com')
      window.location.href = mobileUrl
    }
  } catch (error) {
    console.error('设备检测失败:', error)
  }
}
```

## 部署步骤

### 1. 创建移动端服务
1. 在Render Dashboard中创建新服务
2. 选择 "Web Service"
3. 连接GitHub仓库
4. 配置服务名称为 `tikshop-mobile`

### 2. 配置域名
1. 在服务设置中添加自定义域名
2. 设置域名为 `m.tikshop-user.onrender.com`
3. 配置SSL证书

### 3. 配置环境变量
1. 设置 `VITE_API_BASE_URL`
2. 设置 `VITE_APP_TITLE`
3. 设置 `VITE_APP_DOMAIN`

### 4. 部署
1. 触发自动部署
2. 等待构建完成
3. 测试域名访问

## 测试验证

### 桌面端测试
1. 在桌面浏览器中访问 `https://tikshop-user.onrender.com`
2. 应该显示桌面端界面
3. 不应该跳转到移动端子域名

### 移动端测试
1. 在移动浏览器中访问 `https://tikshop-user.onrender.com`
2. 应该自动跳转到 `https://m.tikshop-user.onrender.com`
3. 应该显示移动端界面

### 直接访问移动端子域名
1. 直接访问 `https://m.tikshop-user.onrender.com`
2. 应该显示移动端界面
3. 不应该跳转回主域名

## 优势

### 1. 标准做法
- ✅ 符合行业标准
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
