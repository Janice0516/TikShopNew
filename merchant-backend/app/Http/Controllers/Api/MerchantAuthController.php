<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MerchantProfile;
use App\Models\MerchantToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MerchantAuthController extends Controller
{
    /**
     * 商家注册
     */
    public function register(Request $request)
    {
        $data = $request->only([
            'username', 'password', 'merchantName', 'contactName', 'contactPhone', 'shopName', 'inviteCode', 'email'
        ]);

        $validator = Validator::make($data, [
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:merchant_profiles,username'],
            'password' => ['required', 'string', 'min:6'],
            'merchantName' => ['required', 'string', 'max:100'],
            'contactName' => ['nullable', 'string', 'max:50'],
            'contactPhone' => ['nullable', 'regex:/^(?:\+?60|0)?1[0-9]{8,9}$/'],
            'shopName' => ['nullable', 'string', 'max:100'],
            'inviteCode' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 422, 'message' => $validator->errors()->first()], 422);
        }

        // 创建用户
        $user = User::create([
            'name' => $data['username'] ?? $data['merchantName'] ?? ($data['email'] ?? 'merchant'),
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        // 创建商家档案
        $profile = MerchantProfile::create([
            'user_id' => $user->id,
            'merchant_name' => $data['merchantName'],
            'username' => $data['username'],
            'contact_name' => $data['contactName'] ?? null,
            'contact_phone' => $data['contactPhone'] ?? null,
            'shop_name' => $data['shopName'] ?? null,
            'invite_code' => $data['inviteCode'] ?? null,
        ]);

        // 生成令牌
        $token = Str::random(64);
        MerchantToken::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        return response()->json([
            'code' => 200,
            'data' => [
                'token' => $token,
                'merchantInfo' => [
                    'id' => $user->id,
                    'merchantName' => $profile->merchant_name,
                    'username' => $profile->username,
                    'contactName' => $profile->contact_name,
                    'contactPhone' => $profile->contact_phone,
                    'shopName' => $profile->shop_name,
                    'inviteCode' => $profile->invite_code,
                    'email' => $user->email,
                ],
            ],
        ]);
    }

    /**
     * 商家登录
     */
    public function login(Request $request)
    {
        $data = $request->only(['username', 'password']);
        $validator = Validator::make($data, [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 422, 'message' => $validator->errors()->first()], 422);
        }

        $profile = MerchantProfile::where('username', $data['username'])->first();
        if (!$profile) {
            return response()->json(['code' => 401, 'message' => 'Invalid credentials'], 401);
        }
        $user = $profile->user;
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['code' => 401, 'message' => 'Invalid credentials'], 401);
        }

        // 发放新令牌
        $token = Str::random(64);
        MerchantToken::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        return response()->json([
            'code' => 200,
            'data' => [
                'token' => $token,
                'merchantInfo' => [
                    'id' => $user->id,
                    'merchantName' => $profile->merchant_name,
                    'username' => $profile->username,
                    'contactName' => $profile->contact_name,
                    'contactPhone' => $profile->contact_phone,
                    'shopName' => $profile->shop_name,
                    'inviteCode' => $profile->invite_code,
                    'email' => $user->email,
                ],
            ],
        ]);
    }

    /**
     * 获取商家资料（需 Bearer Token）
     */
    public function profile(Request $request)
    {
        $auth = $request->header('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return response()->json(['code' => 401, 'message' => 'Unauthorized'], 401);
        }
        $token = substr($auth, 7);
        $tokenRow = MerchantToken::where('token', $token)->first();
        if (!$tokenRow) {
            return response()->json(['code' => 401, 'message' => 'Unauthorized'], 401);
        }
        $user = $tokenRow->user;
        $profile = MerchantProfile::where('user_id', $user->id)->first();

        return response()->json([
            'code' => 200,
            'data' => [
                'id' => $user->id,
                'merchantName' => $profile->merchant_name ?? null,
                'username' => $profile->username ?? null,
                'contactName' => $profile->contact_name ?? null,
                'contactPhone' => $profile->contact_phone ?? null,
                'shopName' => $profile->shop_name ?? null,
                'inviteCode' => $profile->invite_code ?? null,
                'email' => $user->email,
            ],
        ]);
    }
}