# Merchant Backend API Configuration

## API端口配置

本项目已更新为使用8081端口作为API服务端口。

### 配置说明

#### 1. 环境变量配置

创建 `.env` 文件并添加以下配置：

```env
# API Configuration
API_BASE_URL=http://localhost:8081/api
API_TIMEOUT=30
API_RETRY_ATTEMPTS=3
```

#### 2. 配置文件

API配置已添加到 `config/services.php`：

```php
'api' => [
    'base_url' => env('API_BASE_URL', 'http://localhost:8081/api'),
    'timeout' => env('API_TIMEOUT', 30),
    'retry_attempts' => env('API_RETRY_ATTEMPTS', 3),
],
```

#### 3. ApiClient服务

`app/Services/ApiClient.php` 已更新为使用配置文件：

```php
public function __construct()
{
    $this->baseUrl = rtrim(config('services.api.base_url', 'http://localhost:8081/api'), '/');
}
```

### 前端配置

Merchant前端的 `vite.config.ts` 已配置为代理到8081端口：

```typescript
proxy: {
  '/api': {
    target: 'http://127.0.0.1:8081',
    changeOrigin: true,
    secure: false
  }
}
```

### 启动说明

1. 确保API服务运行在8081端口
2. 启动Laravel后端服务：
   ```bash
   php artisan serve
   ```
3. 访问商家面板：http://localhost:8000/merchant/panel

### 测试API连接

可以通过以下方式测试API连接：

```bash
# 测试API端点
curl http://localhost:8081/api/merchant/profile

# 测试商家登录
curl -X POST http://localhost:8081/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant001","password":"123456"}'
```

### 故障排除

如果遇到API连接问题：

1. 检查API服务是否运行在8081端口
2. 检查防火墙设置
3. 查看Laravel日志：`storage/logs/laravel.log`
4. 确认环境变量配置正确

### 端口变更

如需更改API端口，请更新以下文件：

1. `.env` 文件中的 `API_BASE_URL`
2. `config/services.php` 中的默认值
3. `vite.config.ts` 中的代理目标（如果使用前端）
