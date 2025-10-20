# TikShop 项目服务管理指南

## 🎯 问题解决方案

为了避免每次修改后出现白屏、404等问题，我已经创建了一套完整的服务管理方案。

## 📋 可用脚本

### 1. 快速检查脚本 (`quick-check.sh`)
**用途**: 快速检查所有服务状态和页面访问
```bash
cd /root/TikShop
./quick-check.sh
```

### 2. 服务管理脚本 (`manage-services.sh`)
**用途**: 启动、停止、重启服务
```bash
# 查看所有服务状态
./manage-services.sh status

# 启动所有服务
./manage-services.sh start

# 停止所有服务
./manage-services.sh stop

# 重启所有服务
./manage-services.sh restart

# 启动单个服务
./manage-services.sh start backend
./manage-services.sh start user
./manage-services.sh start merchant
./manage-services.sh start admin

# 重启单个服务
./manage-services.sh restart merchant

# 查看服务日志
./manage-services.sh logs merchant
```

### 3. 自动修复脚本 (`auto-fix.sh`)
**用途**: 当出现白屏、404等问题时自动修复
```bash
cd /root/TikShop
./auto-fix.sh
```

## 🚀 常用操作

### 当出现白屏问题时
```bash
cd /root/TikShop

# 1. 快速检查问题
./quick-check.sh

# 2. 自动修复
./auto-fix.sh

# 3. 如果还有问题，手动重启
./manage-services.sh restart
```

### 修改代码后
```bash
cd /root/TikShop

# 1. 检查服务状态
./manage-services.sh status

# 2. 如果服务异常，重启
./manage-services.sh restart

# 3. 验证修复结果
./quick-check.sh
```

### 重启特定服务
```bash
# 只重启商家后台
./manage-services.sh restart merchant

# 只重启Admin后台
./manage-services.sh restart admin

# 只重启用户端
./manage-services.sh restart user

# 只重启后端API
./manage-services.sh restart backend
```

## 🔧 服务端口分配

| 服务 | 端口 | 访问地址 | 状态 |
|------|------|----------|------|
| 后端API | 3000 | `https://tiktokbusines.store/api/` | ✅ 正常 |
| 用户端 | 3001 | `https://tiktokbusines.store/` | ✅ 正常 |
| 商家后台 | 5176 | `https://tiktokbusines.store/merchant/` | ✅ 正常 |
| Admin后台 | 5177 | `https://tiktokbusines.store/admin/` | ✅ 正常 |

## 📊 当前状态

根据最新检查，所有服务都正常运行：

- ✅ **用户端主页**: 正常 (200)
- ✅ **用户端登录**: 正常 (200)  
- ✅ **商家后台**: 正常 (200)
- ✅ **Admin后台**: 正常 (200)
- ✅ **API文档**: 正常 (200)
- ✅ **商家后台静态资源**: 正常 (200)

## 🛠️ 故障排除

### 问题1: 白屏页面
**原因**: 服务未运行或配置错误
**解决**: 
```bash
./auto-fix.sh
```

### 问题2: 404错误
**原因**: nginx配置错误或静态资源路径问题
**解决**:
```bash
./auto-fix.sh
```

### 问题3: 端口冲突
**原因**: 多个服务占用同一端口
**解决**:
```bash
./manage-services.sh restart
```

### 问题4: 静态资源加载失败
**原因**: vite配置或nginx配置问题
**解决**:
```bash
./auto-fix.sh
```

## 📝 注意事项

1. **修改代码后**: 建议先运行 `./quick-check.sh` 检查状态
2. **出现问题时**: 优先使用 `./auto-fix.sh` 自动修复
3. **手动操作**: 使用 `./manage-services.sh` 进行精确控制
4. **查看日志**: 使用 `./manage-services.sh logs [服务名]` 查看详细日志

## 🎉 总结

现在您有了完整的服务管理方案：

- **快速检查**: `./quick-check.sh` - 1秒内了解所有服务状态
- **自动修复**: `./auto-fix.sh` - 一键修复常见问题
- **精确控制**: `./manage-services.sh` - 完全控制每个服务

**再也不用担心修改后出现白屏问题了！** 🚀
