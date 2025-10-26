# 管理端从 Vue 切换到 Laravel（Blade）完整替换教程

本教程指导你将已部署的 Vue 管理端切换为 Laravel 管理端（Blade 页面）。涵盖环境准备、后端部署、Web 服务器配置、域名流量切换、功能验证与回滚方案。

## 目标
- 将当前 `admin` 域名的服务切换为 `TikShopNew/admin-backend` 的 Laravel 站点。
- 使用 Blade 页面替代前端 SPA，不依赖前端构建与运行时。
- 保留可回滚路径，确保故障时能快速恢复到 Vue 版本。

## 先决条件
- 服务器环境：`PHP ≥ 8.1`、`Composer`、`MySQL/MariaDB`、`Nginx/Apache`。
- 代码位置：建议部署到如 `/var/www/admin-backend`。
- 数据库：沿用现有生产库或导入本项目的迁移/数据。

## 部署步骤
1) 安装依赖
```
cd /var/www/admin-backend
composer install --no-dev --optimize-autoloader
```

2) 配置环境变量
- 复制 `.env.example` 为 `.env`，填入关键项：
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://admin.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password

SESSION_DRIVER=file
CACHE_DRIVER=file
FILESYSTEM_DISK=public
```

3) 生成密钥与迁移数据
```
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
```

> 注意：如果已有生产数据，建议仅迁移缺失表。出现结构冲突时先备份数据库。

## Web 服务器配置
将站点根目录指向 `admin-backend/public`，启用 `index.php`。

- Nginx 参考配置：
```
server {
    server_name admin.yourdomain.com;
    root /var/www/admin-backend/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # 按你的 PHP-FPM 版本修改
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~* \.(jpg|jpeg|png|gif|css|js|svg|woff|woff2)$ {
        expires 7d;
        access_log off;
    }
}
```

- Apache（仅示意）：确保 `DocumentRoot` 指向 `admin-backend/public`，并启用 `AllowOverride All` 与 `mod_php`/`php-fpm`。

## 域名/流量切换
- 将 `admin` 域名从原 Vue 服务切换到上述站点（更新 Nginx/Apache 或反向代理）。
- 如使用 PM2/Node 作为前端静态服务，更新反向代理到 `admin-backend/public`。
- 建议先做灰度：准备新站点，内部验证通过后再切换 DNS/反代。

## 验证清单
- 登录页：访问 `https://admin.yourdomain.com/admin/login` 正常显示、可登录。
- 仪表盘：`/admin/dashboard` 打开页面，无 500 错误。
- 核心模块：
  - 商家：`/admin/merchants`
  - 订单：`/admin/orders`
  - 商品：`/admin/products`
  - 分类：`/admin/categories`
  - 用户：`/admin/users`
  - 邀请码：`/admin/invite-codes`
  - 提现：`/admin/withdrawals`
  - 充值审核：`/admin/recharge`
  - 信用评级：`/admin/credit-ratings`
  - 资金管理：`/admin/fund-management`
  - 特色商品：`/admin/featured-products`
- 静态资源：图片/CSS/JS 正常加载，无跨域/404。
- 上传：若涉及文件上传，`storage:link` 正常、`storage` 与 `public` 权限正确。

## 与原 Vue 版的差异
- 路径风格：Laravel 后台主要是 `/admin/...` 的 Blade 页面；Vue 版 API 有部分无 `admin` 前缀（例如 `/products`、`/withdrawal`）。
- 返回类型：Laravel 当前多为页面渲染。如果仍需前端 axios JSON 接口，可在后端新增 API 路由组或在控制器中返回 `response()->json(...)`。
- 功能模块：后端已覆盖大部分管理域；若需要 `role-permission/*`、`admin/settings/*`、`admin/order-remark/*`、`/admin/dashboard/stats`、`/health` 等纯 API，可按需补充。

## 常见问题与解决
- 500 错误：查看日志 `storage/logs/laravel.log`。
- 权限问题：
```
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache # 按你服务器用户
```
- 配置缓存：修改 `.env` 后执行 `php artisan config:clear && php artisan config:cache`。
- 路由不生效：`php artisan route:clear`。
- 静态资源 404：确认 `root` 指向 `public`，且前端文件不再占用该域名的反代。

## 回滚方案
- 若出现严重问题，临时将域名指回原 Vue 服务（恢复原 Nginx/Apache/反代配置）。
- Laravel 站点保留，离线修复后再切换回来。

## 快速命令清单
```
# 部署
cd /var/www/admin-backend
composer install --no-dev --optimize-autoloader
cp .env.example .env  # 并编辑数据库与域名
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan config:cache

# 故障排查
php artisan config:clear
php artisan route:clear
tail -f storage/logs/laravel.log
```

## 后续增强（可选）
- 同时支持前端 API：增加 `/admin/dashboard/stats`、`/health`、`admin/settings/*`、`role-permission/*`、`admin/order-remark/*` 等 JSON 接口。
- 路由分组：新增 `Route::prefix('api')->middleware('admin.auth')->group(...)` 提供纯 API，与 Blade 页面并存。

---
如需，我可以直接在 `admin-backend` 增加上述关键 JSON 接口的最小实现（控制器 + 路由），帮助你保留部分前端 axios 请求同时使用 Laravel 页面。