<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * 切换语言
     */
    public function switch(Request $request)
    {
        $request->validate([
            'lang' => 'required|in:zh,en,ms',
        ]);

        $locale = $request->input('lang');
        
        // 保存到Session
        Session::put('locale', $locale);
        
        // 如果用户已登录，保存到用户设置
        if (auth()->check()) {
            $user = auth()->user();
            $userSettings = $user->settings ?? [];
            $userSettings['language'] = $locale;
            $user->update(['settings' => $userSettings]);
        }

        // 设置应用语言
        App::setLocale($locale);

        return response()->json([
            'success' => true,
            'message' => __('merchant.success.settings_saved'),
            'locale' => $locale,
        ]);
    }

    /**
     * 获取当前语言
     */
    public function current()
    {
        $locale = App::getLocale();
        
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'language_name' => $this->getLanguageName($locale),
        ]);
    }

    /**
     * 获取支持的语言列表
     */
    public function supported()
    {
        $languages = [
            'zh' => [
                'code' => 'zh',
                'name' => '中文',
                'native_name' => '中文',
                'flag' => '🇨🇳',
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇺🇸',
            ],
            'ms' => [
                'code' => 'ms',
                'name' => 'Bahasa Malaysia',
                'native_name' => 'Bahasa Malaysia',
                'flag' => '🇲🇾',
            ],
        ];

        return response()->json([
            'success' => true,
            'languages' => $languages,
        ]);
    }

    /**
     * 获取语言名称
     */
    private function getLanguageName(string $locale): string
    {
        $names = [
            'zh' => '中文',
            'en' => 'English',
            'ms' => 'Bahasa Malaysia',
        ];

        return $names[$locale] ?? 'Unknown';
    }
}