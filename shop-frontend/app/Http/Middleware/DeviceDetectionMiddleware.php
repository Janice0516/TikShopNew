<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceDetectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 排除设备切换路由
        if ($request->is('switch-device/*')) {
            return $next($request);
        }
        
        $deviceType = $this->detectDevice($request);
        
        // 设置全局设备类型
        app()->instance('device_type', $deviceType);
        
        // 设置视图命名空间
        view()->addNamespace('desktop', resource_path('views/desktop'));
        view()->addNamespace('mobile', resource_path('views/mobile'));
        
        // 根据设备类型重定向
        if ($this->shouldRedirect($request, $deviceType)) {
            return $this->redirectToDevice($request, $deviceType);
        }
        
        return $next($request);
    }
    
    /**
     * 检测设备类型
     */
    private function detectDevice(Request $request): string
    {
        $currentPath = $request->path();
        
        // 1. 如果URL路径已经指定了设备类型，优先使用
        if (str_starts_with($currentPath, 'mobile/') || $currentPath === 'mobile') {
            return 'mobile';
        }
        if (str_starts_with($currentPath, 'desktop/') || $currentPath === 'desktop') {
            return 'desktop';
        }
        
        // 2. 检查URL参数
        if ($request->has('device')) {
            return $request->get('device') === 'mobile' ? 'mobile' : 'desktop';
        }
        
        // 3. 检查User-Agent
        $userAgent = $request->header('User-Agent', '');
        if ($this->isMobileUserAgent($userAgent)) {
            return 'mobile';
        }
        
        // 4. 检查屏幕尺寸Cookie
        $screenWidth = $request->cookie('screen_width');
        if ($screenWidth && $screenWidth <= 768) {
            return 'mobile';
        }
        
        // 5. 检查session中的设备类型
        if (session()->has('device_type')) {
            return session('device_type');
        }
        
        // 默认返回桌面端
        return 'desktop';
    }
    
    /**
     * 检查User-Agent是否为移动端
     */
    private function isMobileUserAgent(string $userAgent): bool
    {
        $mobilePatterns = [
            'android', 'iphone', 'ipad', 'ipod', 'blackberry',
            'windows phone', 'mobile', 'opera mini', 'iemobile'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 判断是否需要重定向
     */
    private function shouldRedirect(Request $request, string $deviceType): bool
    {
        $currentPath = $request->path();
        
        // 如果已经在正确的设备路径下，不需要重定向
        if (($deviceType === 'mobile' && str_starts_with($currentPath, 'mobile/')) ||
            ($deviceType === 'desktop' && str_starts_with($currentPath, 'desktop/'))) {
            return false;
        }
        
        // 如果路径就是设备名称本身，不需要重定向
        if ($currentPath === $deviceType) {
            return false;
        }
        
        // 如果路径包含错误的设备前缀，需要重定向
        if (str_starts_with($currentPath, 'mobile/') && $deviceType === 'desktop') {
            return true;
        }
        if (str_starts_with($currentPath, 'desktop/') && $deviceType === 'mobile') {
            return true;
        }
        
        // 如果不在任何设备路径下，需要重定向
        if (!str_starts_with($currentPath, 'mobile/') && !str_starts_with($currentPath, 'desktop/')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 重定向到对应的设备路径
     */
    private function redirectToDevice(Request $request, string $deviceType): Response
    {
        $currentPath = $request->path();
        
        // 移除错误的设备前缀
        if (str_starts_with($currentPath, 'mobile/')) {
            $currentPath = substr($currentPath, 7); // 移除 'mobile/'
        } elseif (str_starts_with($currentPath, 'desktop/')) {
            $currentPath = substr($currentPath, 8); // 移除 'desktop/'
        }
        
        // 如果路径为空或者是设备名称本身，重定向到首页
        if (empty($currentPath) || $currentPath === $deviceType) {
            $newPath = $deviceType . '/';
        } else {
            $newPath = $deviceType . '/' . $currentPath;
        }
        
        // 保存设备类型到session
        session(['device_type' => $deviceType]);
        
        return redirect($newPath);
    }
}
