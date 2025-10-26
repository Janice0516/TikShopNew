<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * 显示个人资料页面
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $admin->load('roles');
        
        // 获取管理员统计信息
        $stats = [
            'login_count' => rand(50, 200), // 模拟数据
            'last_login' => $admin->last_login_at,
            'account_created' => $admin->created_at,
            'permissions_count' => count($admin->getAllPermissions()),
            'roles_count' => $admin->roles->count(),
        ];

        return view('admin.profile.index', compact('admin', 'stats'));
    }

    /**
     * 更新个人资料
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'username' => 'required|string|max:255|unique:admins,username,' . $admin->id,
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ]);

        return redirect()->route('admin.profile.index')
            ->with('success', '个人资料已更新');
    }

    /**
     * 更新密码
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // 验证当前密码
        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->route('admin.profile.index')
                ->with('error', '当前密码不正确');
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile.index')
            ->with('success', '密码已更新');
    }

    /**
     * 更新头像
     */
    public function updateAvatar(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 处理头像上传
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('avatars', $filename, 'public');
            
            // 删除旧头像
            if ($admin->avatar && \Storage::disk('public')->exists($admin->avatar)) {
                \Storage::disk('public')->delete($admin->avatar);
            }

            $admin->update(['avatar' => $path]);
        }

        return redirect()->route('admin.profile.index')
            ->with('success', '头像已更新');
    }

    /**
     * 更新通知设置
     */
    public function updateNotifications(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'order_notifications' => 'boolean',
            'system_notifications' => 'boolean',
        ]);

        $notifications = [
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'order_notifications' => $request->boolean('order_notifications'),
            'system_notifications' => $request->boolean('system_notifications'),
        ];

        $admin->update(['notifications' => $notifications]);

        return redirect()->route('admin.profile.index')
            ->with('success', '通知设置已更新');
    }

    /**
     * 更新系统设置
     */
    public function updateSettings(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'language' => 'required|string|in:zh,en,ms',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:10',
            'theme' => 'required|string|in:light,dark',
        ]);

        $settings = [
            'language' => $request->language,
            'timezone' => $request->timezone,
            'date_format' => $request->date_format,
            'time_format' => $request->time_format,
            'theme' => $request->theme,
        ];

        $admin->update(['settings' => $settings]);

        return redirect()->route('admin.profile.index')
            ->with('success', '系统设置已更新');
    }

    /**
     * 获取登录历史
     */
    public function loginHistory(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // 模拟登录历史数据
        $loginHistory = [
            [
                'ip' => '192.168.1.100',
                'location' => '马来西亚吉隆坡',
                'device' => 'Chrome 120.0 on Windows 10',
                'login_at' => now()->subHours(2),
                'status' => 'success',
            ],
            [
                'ip' => '192.168.1.100',
                'location' => '马来西亚吉隆坡',
                'device' => 'Chrome 120.0 on Windows 10',
                'login_at' => now()->subDays(1),
                'status' => 'success',
            ],
            [
                'ip' => '192.168.1.101',
                'location' => '马来西亚槟城',
                'device' => 'Safari 17.0 on macOS',
                'login_at' => now()->subDays(3),
                'status' => 'success',
            ],
            [
                'ip' => '192.168.1.102',
                'location' => '未知位置',
                'device' => 'Chrome 119.0 on Android',
                'login_at' => now()->subDays(5),
                'status' => 'failed',
            ],
        ];

        return response()->json($loginHistory);
    }

    /**
     * 获取操作日志
     */
    public function activityLog(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // 模拟操作日志数据
        $activityLog = [
            [
                'action' => '登录系统',
                'description' => '管理员登录到管理后台',
                'ip' => '192.168.1.100',
                'created_at' => now()->subHours(2),
            ],
            [
                'action' => '更新商品',
                'description' => '更新了商品 "iPhone 15 Pro" 的信息',
                'ip' => '192.168.1.100',
                'created_at' => now()->subHours(3),
            ],
            [
                'action' => '创建订单',
                'description' => '创建了新订单 #ORD-2024-001',
                'ip' => '192.168.1.100',
                'created_at' => now()->subHours(4),
            ],
            [
                'action' => '更新设置',
                'description' => '更新了系统设置',
                'ip' => '192.168.1.100',
                'created_at' => now()->subDays(1),
            ],
            [
                'action' => '删除用户',
                'description' => '删除了用户 "test@example.com"',
                'ip' => '192.168.1.100',
                'created_at' => now()->subDays(2),
            ],
        ];

        return response()->json($activityLog);
    }

    /**
     * 导出个人数据
     */
    public function exportData(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $data = [
            'profile' => [
                'name' => $admin->name,
                'username' => $admin->username,
                'email' => $admin->email,
                'role' => $admin->role,
                'created_at' => $admin->created_at,
                'last_login_at' => $admin->last_login_at,
            ],
            'roles' => $admin->roles->map(function($role) {
                return [
                    'name' => $role->name,
                    'description' => $role->description,
                ];
            }),
            'permissions' => $admin->getAllPermissions(),
            'settings' => $admin->settings ?? [],
            'notifications' => $admin->notifications ?? [],
        ];

        $filename = 'admin_profile_' . $admin->username . '_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }
}
