<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * 显示系统设置页面
     */
    public function index(Request $request)
    {
        $group = $request->get('group', 'general');
        
        // 获取所有分组
        $groups = Setting::distinct()->pluck('group')->toArray();
        $defaultGroups = ['general', 'payment', 'email', 'sms', 'notification', 'security'];
        $allGroups = array_unique(array_merge($defaultGroups, $groups));
        
        // 获取当前分组的设置
        $settings = Setting::where('group', $group)->get();
        
        return view('admin.settings.index', compact('group', 'allGroups', 'settings'));
    }

    /**
     * 更新设置
     */
    public function update(Request $request)
    {
        $group = $request->input('group', 'general');
        
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            Setting::set($key, $value, 'string', $group);
        }

        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('success', '设置已保存');
    }

    /**
     * 更新单个设置
     */
    public function updateSingle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
            'type' => 'nullable|string',
            'group' => 'nullable|string',
        ]);

        Setting::set(
            $request->key,
            $request->value,
            $request->type ?? 'string',
            $request->group ?? 'general'
        );

        return response()->json(['success' => true]);
    }

    /**
     * 重置设置
     */
    public function reset(Request $request)
    {
        $group = $request->input('group', 'general');
        
        // 删除该分组的所有设置
        Setting::where('group', $group)->delete();
        
        // 重新初始化默认设置
        $this->initializeDefaultSettings($group);

        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('success', '设置已重置为默认值');
    }

    /**
     * 初始化默认设置
     */
    private function initializeDefaultSettings($group = null)
    {
        $defaultSettings = [
            'general' => [
                'site_name' => ['value' => 'TikShop 管理平台', 'description' => '网站名称'],
                'site_description' => ['value' => '专业的电商管理平台', 'description' => '网站描述'],
                'site_logo' => ['value' => '', 'description' => '网站Logo'],
                'default_currency' => ['value' => 'MYR', 'description' => '默认货币'],
                'timezone' => ['value' => 'Asia/Kuala_Lumpur', 'description' => '时区'],
                'language' => ['value' => 'zh', 'description' => '默认语言'],
                'maintenance_mode' => ['value' => '0', 'type' => 'boolean', 'description' => '维护模式'],
            ],
            'payment' => [
                'payment_enabled' => ['value' => '1', 'type' => 'boolean', 'description' => '启用支付'],
                'default_payment_method' => ['value' => 'credit_card', 'description' => '默认支付方式'],
                'payment_timeout' => ['value' => '30', 'type' => 'integer', 'description' => '支付超时时间(分钟)'],
                'refund_enabled' => ['value' => '1', 'type' => 'boolean', 'description' => '启用退款'],
                'refund_timeout' => ['value' => '7', 'type' => 'integer', 'description' => '退款处理时间(天)'],
            ],
            'email' => [
                'smtp_host' => ['value' => '', 'description' => 'SMTP服务器'],
                'smtp_port' => ['value' => '587', 'type' => 'integer', 'description' => 'SMTP端口'],
                'smtp_username' => ['value' => '', 'description' => 'SMTP用户名'],
                'smtp_password' => ['value' => '', 'description' => 'SMTP密码'],
                'smtp_encryption' => ['value' => 'tls', 'description' => '加密方式'],
                'from_email' => ['value' => 'noreply@tiktokshop.com', 'description' => '发件人邮箱'],
                'from_name' => ['value' => 'TikShop', 'description' => '发件人名称'],
            ],
            'sms' => [
                'sms_enabled' => ['value' => '0', 'type' => 'boolean', 'description' => '启用短信'],
                'sms_provider' => ['value' => 'twilio', 'description' => '短信服务商'],
                'sms_api_key' => ['value' => '', 'description' => 'API密钥'],
                'sms_api_secret' => ['value' => '', 'description' => 'API密钥'],
                'sms_from_number' => ['value' => '', 'description' => '发送号码'],
            ],
            'notification' => [
                'email_notifications' => ['value' => '1', 'type' => 'boolean', 'description' => '邮件通知'],
                'sms_notifications' => ['value' => '0', 'type' => 'boolean', 'description' => '短信通知'],
                'push_notifications' => ['value' => '1', 'type' => 'boolean', 'description' => '推送通知'],
                'order_notifications' => ['value' => '1', 'type' => 'boolean', 'description' => '订单通知'],
                'payment_notifications' => ['value' => '1', 'type' => 'boolean', 'description' => '支付通知'],
            ],
            'security' => [
                'login_attempts' => ['value' => '5', 'type' => 'integer', 'description' => '登录尝试次数限制'],
                'lockout_duration' => ['value' => '15', 'type' => 'integer', 'description' => '锁定时间(分钟)'],
                'password_min_length' => ['value' => '8', 'type' => 'integer', 'description' => '密码最小长度'],
                'password_require_special' => ['value' => '1', 'type' => 'boolean', 'description' => '密码需要特殊字符'],
                'session_timeout' => ['value' => '120', 'type' => 'integer', 'description' => '会话超时时间(分钟)'],
                'two_factor_auth' => ['value' => '0', 'type' => 'boolean', 'description' => '双因子认证'],
            ],
        ];

        if ($group) {
            $settings = $defaultSettings[$group] ?? [];
        } else {
            $settings = $defaultSettings;
        }

        foreach ($settings as $key => $config) {
            Setting::set(
                $key,
                $config['value'],
                $config['type'] ?? 'string',
                $group ?? 'general',
                $config['description'] ?? null
            );
        }
    }

    /**
     * 获取设置值
     */
    public function get(Request $request)
    {
        $key = $request->input('key');
        $group = $request->input('group');
        
        if ($key) {
            $value = Setting::get($key);
            return response()->json(['value' => $value]);
        }
        
        if ($group) {
            $values = Setting::getGroup($group);
            return response()->json($values);
        }
        
        return response()->json(['error' => 'Missing key or group parameter'], 400);
    }
}
