# 后端API环境变量更新

## 问题诊断
后端API仍然返回"relation 'merchant' does not exist"错误，说明后端还在使用旧的数据库连接配置。

## 解决方案
需要在Render Dashboard中更新后端API服务的数据库连接环境变量。

## 新的数据库连接信息
```
DB_HOST=dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com
DB_PORT=5432
DB_USERNAME=tiktokshop_slkz_user
DB_PASSWORD=U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn
DB_DATABASE=tiktokshop_slkz
DB_URL=postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz
```

## 更新步骤

1. **登录Render Dashboard**
   - 访问 https://dashboard.render.com
   - 找到 `TikShop-api` 后端服务

2. **更新环境变量**
   - 点击后端服务
   - 选择 "Environment" 标签
   - 更新以下环境变量：
     - `DB_HOST`: `dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com`
     - `DB_PORT`: `5432`
     - `DB_USERNAME`: `tiktokshop_slkz_user`
     - `DB_PASSWORD`: `U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn`
     - `DB_DATABASE`: `tikshop_slkz`
     - `DB_URL`: `postgresql://tiktokshop_slkz_user:U7WZHv0ETQfc8bPpQz3sCFlU6EnifRCn@dpg-d3kgpsd6ubrc73dvbjm0-a.singapore-postgres.render.com/tiktokshop_slkz`

3. **保存并重新部署**
   - 点击 "Save Changes"
   - 等待服务重新部署完成

## 验证修复

更新环境变量后，测试以下API：

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
- ✅ 不再有"relation 'merchant' does not exist"错误
