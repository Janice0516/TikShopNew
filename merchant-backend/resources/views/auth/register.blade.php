<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商家注册 - Tiktok Shop Merchant</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-indigo-100">
                <i class="fas fa-store text-indigo-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                商家注册
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                创建您的商家账户
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="/merchant/register">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">注册失败</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">用户名</label>
                    <input id="username" 
                           name="username" 
                           type="text" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="请输入用户名"
                           value="{{ old('username') }}">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">邮箱</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="请输入邮箱地址"
                           value="{{ old('email') }}">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">密码</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="请输入密码">
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">确认密码</label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="请再次输入密码">
                </div>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">姓名</label>
                    <input id="name" 
                           name="name" 
                           type="text" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="请输入真实姓名"
                           value="{{ old('name') }}">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">电话</label>
                    <input id="phone" 
                           name="phone" 
                           type="tel" 
                           required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="请输入联系电话"
                           value="{{ old('phone') }}">
                </div>
            </div>

            <div class="flex items-center">
                <input id="agree-terms" 
                       name="agree_terms" 
                       type="checkbox" 
                       required
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="agree-terms" class="ml-2 block text-sm text-gray-900">
                    我同意 <a href="#" class="text-indigo-600 hover:text-indigo-500">服务条款</a> 和 <a href="#" class="text-indigo-600 hover:text-indigo-500">隐私政策</a>
                </label>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-indigo-500 group-hover:text-indigo-400"></i>
                    </span>
                    注册账户
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    已有账号？
                    <a href="/merchant/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                        立即登录
                    </a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
