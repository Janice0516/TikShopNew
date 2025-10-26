<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * 显示登录页面
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * 处理登录请求
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)
            ->where('is_active', true)
            ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'username' => ['用户名或密码错误'],
            ]);
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));

        // 更新最后登录时间
        $admin->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect()->intended('/admin/dashboard');
    }

    /**
     * 处理登出请求
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
