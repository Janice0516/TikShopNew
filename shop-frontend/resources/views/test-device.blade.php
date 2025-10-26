<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Separation Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <h1>设备分离测试页面</h1>
    
    <div class="test-section">
        <h2>当前设备类型</h2>
        <p>设备类型: {{ app('device_type') ?? '未设置' }}</p>
        <p>当前路径: {{ request()->path() }}</p>
        <p>User-Agent: {{ request()->header('User-Agent') }}</p>
    </div>
    
    <div class="test-section">
        <h2>测试链接</h2>
        <p><a href="/desktop">桌面端首页</a></p>
        <p><a href="/mobile">移动端首页</a></p>
        <p><a href="/switch-device/desktop">切换到桌面端</a></p>
        <p><a href="/switch-device/mobile">切换到移动端</a></p>
    </div>
    
    <div class="test-section">
        <h2>设备检测测试</h2>
        <p>Session设备类型: {{ session('device_type') ?? '未设置' }}</p>
        <p>屏幕宽度Cookie: {{ request()->cookie('screen_width') ?? '未设置' }}</p>
    </div>
</body>
</html>
