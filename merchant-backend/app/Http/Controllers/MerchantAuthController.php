<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiClient;

class MerchantAuthController extends Controller
{
    protected ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    public function showLogin()
    {
        return view('login', ['title' => '商家登录']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $data = $request->only(['username', 'password']);
        
        // 直接调用API控制器而不是通过HTTP
        $apiController = new \App\Http\Controllers\Api\MerchantAuthController();
        $apiRequest = new \Illuminate\Http\Request();
        $apiRequest->merge($data);
        
        try {
            $response = $apiController->login($apiRequest);
            $res = json_decode($response->getContent(), true);
            
            // 调试信息
            \Log::info('Login API Response:', $res);
            
            if ($res['code'] !== 200) {
                \Log::error('Login failed:', $res);
                return back()->withErrors(['username' => '登录失败，请检查账号或密码'])->withInput();
            }
            
            $token = $res['data']['token'] ?? null;
            if (!$token) {
                \Log::error('No token in response:', $res);
                return back()->withErrors(['username' => '登录失败，请检查账号或密码'])->withInput();
            }

            session(['merchant_token' => $token]);
            session(['merchant_info' => $res['data']['merchantInfo'] ?? []]);

            return redirect('/merchant/dashboard');
        } catch (\Exception $e) {
            \Log::error('Login error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['username' => '登录失败: ' . $e->getMessage()])->withInput();
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'agree_terms' => 'required|accepted',
        ]);

        $payload = [
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
        ];

        try {
            $res = $this->api->post('merchant/register', $payload);
            
            if (isset($res['data']['token'])) {
                session(['merchant_token' => $res['data']['token']]);
                session(['merchant_info' => $res['data']['user'] ?? []]);
                return redirect('/merchant/dashboard')->with('success', '注册成功！');
            } else {
                return back()->withErrors(['username' => $res['message'] ?? '注册失败'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['username' => '注册失败: ' . $e->getMessage()])->withInput();
        }
    }

    public function logout()
    {
        session()->forget(['merchant_token', 'merchant_info']);
        return redirect('/merchant/login');
    }
}