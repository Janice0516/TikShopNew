# 后端API环境变量更新（修正版）

## 问题诊断
后端API无法连接数据库，错误信息显示 `ECONNREFUSED`。经过测试发现：
- ✅ 数据库连接信息正确
- ❌ 数据库名称错误：应该是 `tiktokshop_slkz`，不是 `tikshop_slkz`

## 正确的环境变量配置

请在Render Dashboard中设置以下环境变量：

```
DB_TYPE=postgres
DB_HOST=dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com
DB_PORT=5432
DB_USERNAME=tiktokshop_slkz_user
DB_PASSWORD=U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn
DB_DATABASE=tiktokshop_slkz
NODE_ENV=production
```

## 更新步骤

1. **登录Render Dashboard**
   - 访问 https://dashboard.render.com
   - 找到 `TikShop-api` 后端服务

2. **更新环境变量**
   - 点击后端服务
   - 选择 "Environment" 标签
   - 删除旧的环境变量（如果有）
   - 添加上述所有环境变量
   - **特别注意**：`DB_DATABASE` 必须是 `tiktokshop_slkz`

3. **保存并重新部署**
   - 点击 "Save Changes"
   - 等待服务重新部署完成

## 验证修复

更新环境变量后，检查部署日志应该看到：
- ✅ 数据库连接成功
- ✅ 应用启动成功
- ❌ 不再有 `ECONNREFUSED` 错误

然后测试API：
```bash
# 测试商家登录
curl -X POST https://tiktokshop-api.onrender.com/api/merchant/login \
  -H "Content-Type: application/json" \
  -d '{"username":"merchant001","password":"password123"}'

# 测试提现列表
curl https://tiktokshop-api.onrender.com/api/withdrawal/list
```

## 预期结果

修复后应该看到：
- ✅ 商家登录成功，返回token
- ✅ 提现列表API返回数据
- ✅ 管理后台显示提现记录
- ✅ 不再有数据库连接错误
