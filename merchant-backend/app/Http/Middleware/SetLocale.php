<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 获取语言设置
        $locale = $this->getLocale($request);
        
        // 设置应用语言
        App::setLocale($locale);
        
        return $next($request);
    }

    /**
     * 获取语言设置
     */
    private function getLocale(Request $request): string
    {
        // 1. 优先使用URL参数
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if ($this->isValidLocale($locale)) {
                Session::put('locale', $locale);
                return $locale;
            }
        }

        // 2. 使用Session中保存的语言
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        // 3. 使用用户设置的语言
        if (auth()->check()) {
            $userSettings = auth()->user()->settings ?? [];
            if (isset($userSettings['language']) && $this->isValidLocale($userSettings['language'])) {
                Session::put('locale', $userSettings['language']);
                return $userSettings['language'];
            }
        }

        // 4. 使用浏览器语言
        $browserLocale = $this->getBrowserLocale($request);
        if ($browserLocale && $this->isValidLocale($browserLocale)) {
            Session::put('locale', $browserLocale);
            return $browserLocale;
        }

        // 5. 默认使用马来西亚马来语
        $defaultLocale = 'ms';
        Session::put('locale', $defaultLocale);
        return $defaultLocale;
    }

    /**
     * 检查语言是否有效
     */
    private function isValidLocale(string $locale): bool
    {
        return in_array($locale, ['zh', 'en', 'ms']);
    }

    /**
     * 获取浏览器语言
     */
    private function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }

        $languages = explode(',', $acceptLanguage);
        foreach ($languages as $language) {
            $locale = trim(explode(';', $language)[0]);
            
            // 检查完整语言代码
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
            
            // 检查语言前缀
            $prefix = substr($locale, 0, 2);
            if ($this->isValidLocale($prefix)) {
                return $prefix;
            }
        }

        return null;
    }
}