<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? '登录' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: '#5b8def',
            brand2: '#7b6ef6',
            accent: '#19c37d',
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-slate-100 to-slate-200">
  <div class="container mx-auto px-4 py-10">
    <div class="max-w-md mx-auto">
      <div class="text-center mb-6">
        <a href="/merchant" class="inline-flex items-center gap-2">
          <span class="inline-block w-10 h-10 rounded-xl bg-gradient-to-tr from-brand to-brand2"></span>
          <span class="text-xl font-semibold">Tiktok Shop Merchant</span>
        </a>
      </div>
      <div class="rounded-2xl shadow-2xl bg-white overflow-hidden">
        <div class="bg-gradient-to-r from-brand to-brand2 p-6">
          <h1 class="text-white text-lg font-semibold">{{ $title ?? '登录' }}</h1>
          <p class="text-white/80 text-sm mt-1">欢迎回来，请输入账号和密码</p>
        </div>
        <div class="p-6">
          @yield('content')
        </div>
      </div>
      <p class="text-center text-slate-500 text-sm mt-6">© Tiktok Shop Merchant</p>
    </div>
  </div>
</body>
</html>