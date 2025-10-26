<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '商家后台' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/merchant">Tiktok Shop Merchant</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBar" aria-controls="navBar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navBar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant') ? 'active' : '' }}" href="/merchant"><i class="bi bi-speedometer me-1"></i>仪表盘</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant/products') ? 'active' : '' }}" href="/merchant/products"><i class="bi bi-box-seam me-1"></i>商品</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant/orders') ? 'active' : '' }}" href="/merchant/orders"><i class="bi bi-receipt-cutoff me-1"></i>订单</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant/finance') ? 'active' : '' }}" href="/merchant/finance"><i class="bi bi-cash-coin me-1"></i>财务</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant/withdrawals') ? 'active' : '' }}" href="/merchant/withdrawals"><i class="bi bi-wallet2 me-1"></i>提现</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant/shop') ? 'active' : '' }}" href="/merchant/shop"><i class="bi bi-shop me-1"></i>店铺</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant/credit-rating') ? 'active' : '' }}" href="/merchant/credit-rating"><i class="bi bi-shield-check me-1"></i>信用评级</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant/categories') ? 'active' : '' }}" href="/merchant/categories"><i class="bi bi-grid-3x3-gap me-1"></i>分类</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('merchant/settings') ? 'active' : '' }}" href="/merchant/settings"><i class="bi bi-gear me-1"></i>设置</a></li>
      </ul>
      <div class="d-flex">
        @if(session()->has('merchant_token'))
          <a class="btn btn-outline-light btn-sm" href="/merchant/logout"><i class="bi bi-box-arrow-right"></i> 退出</a>
        @else
          <a class="btn btn-outline-light btn-sm" href="/merchant/login"><i class="bi bi-person"></i> 登录</a>
        @endif
      </div>
    </div>
  </div>
</nav>
<div class="container mb-4">
    @yield('content')
</div>
<footer class="container pb-4"><div class="footer-muted">© Tiktok Shop Merchant · 提升效率与体验</div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>