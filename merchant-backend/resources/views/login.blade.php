@extends('layouts.auth')

@section('content')
  @if($errors->any())
    <div class="rounded-xl bg-red-50 border border-red-200 text-red-600 px-4 py-3 mb-4">{{ $errors->first() }}</div>
  @endif

  <form method="post" action="/merchant/login" class="space-y-4" autocomplete="off">
    @csrf
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">邮箱</label>
      <div class="relative">
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 pr-10 focus:ring-2 focus:ring-brand focus:border-brand outline-none" />
        <svg class="absolute right-3 top-2.5 w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M1.5 6.75A3.75 3.75 0 015.25 3h13.5A3.75 3.75 0 0122.5 6.75v10.5A3.75 3.75 0 0118.75 21H5.25A3.75 3.75 0 011.5 17.25V6.75zm3.3-.45A2.25 2.25 0 003.75 6.75v.662l7.637 4.582a.75.75 0 00.772 0L19.8 7.412V6.75A2.25 2.25 0 0017.55 6.3H4.8z"/></svg>
      </div>
    </div>
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">密码</label>
      <div class="relative">
        <input type="password" name="password" required
               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 pr-10 focus:ring-2 focus:ring-brand2 focus:border-brand2 outline-none" />
        <svg class="absolute right-3 top-2.5 w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3h-.75A2.25 2.25 0 003.75 12v7.5A2.25 2.25 0 006 21.75h12a2.25 2.25 0 002.25-2.25V12a2.25 2.25 0 00-2.25-2.25h-.75v-3A5.25 5.25 0 0012 1.5zm-3.75 8.25v-3a3.75 3.75 0 017.5 0v3h-7.5z"/></svg>
      </div>
    </div>

    <div class="flex items-center justify-between">
      <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand to-brand2 text-white px-4 py-2.5 shadow hover:opacity-95 transition">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zm.75 5.25a.75.75 0 10-1.5 0v4.5a.75.75 0 00.356.636l3 1.8a.75.75 0 10.788-1.272l-2.644-1.587V7.5z"/></svg>
        登录
      </button>
      <a href="/merchant/logout" class="text-slate-500 hover:text-slate-700 text-sm">退出</a>
    </div>
  </form>
@endsection