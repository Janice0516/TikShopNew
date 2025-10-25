import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/user'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: () => import('@/views/HomeModular.vue'),
      meta: { title: 'TikTok Shop' }
    },
    {
      path: '/mobile',
      name: 'MobileHome',
      component: () => import('@/views/MobileHome.vue'),
      meta: { title: 'TikTok Shop Mobile' }
    },
    // 地址管理路由
    {
      path: '/address/list',
      name: 'AddressList',
      component: () => import('@/views/AddressList.vue'),
      meta: { title: '地址管理', requiresAuth: true }
    },
    {
      path: '/address/add',
      name: 'AddressAdd',
      component: () => import('@/views/AddressForm.vue'),
      meta: { title: '添加地址', requiresAuth: true }
    },
    {
      path: '/address/edit/:id',
      name: 'AddressEdit',
      component: () => import('@/views/AddressForm.vue'),
      meta: { title: '编辑地址', requiresAuth: true }
    },
    // 移动端专用路由
    {
      path: '/mobile/login',
      name: 'MobileLogin',
      component: () => import('@/views/MobileLogin.vue'),
      meta: { title: '登录', hideForAuth: true }
    },
    {
      path: '/mobile/register',
      name: 'MobileRegister',
      component: () => import('@/views/MobileRegister.vue'),
      meta: { title: '注册', hideForAuth: true }
    },
    {
      path: '/mobile/cart',
      name: 'MobileCart',
      component: () => import('@/views/MobileCart.vue'),
      meta: { title: '购物车', requiresAuth: true }
    },
    {
      path: '/mobile/profile',
      name: 'MobileProfile',
      component: () => import('@/views/MobileProfile.vue'),
      meta: { title: '个人中心', requiresAuth: true }
    },
    {
      path: '/mobile/orders',
      name: 'MobileOrders',
      component: () => import('@/views/MobileOrders.vue'),
      meta: { title: '我的订单', requiresAuth: true }
    },
    {
      path: '/mobile/search',
      name: 'MobileSearch',
      component: () => import('@/views/MobileSearch.vue'),
      meta: { title: '搜索' }
    },
    {
      path: '/mobile/order/review/:orderId',
      name: 'MobileOrderReview',
      component: () => import('@/views/MobileOrderReview.vue'),
      meta: { title: '订单评价', requiresAuth: true }
    },
    {
      path: '/mobile/help',
      name: 'MobileHelp',
      component: () => import('@/views/MobileHelp.vue'),
      meta: { title: '帮助中心' }
    },
    {
      path: '/mobile/favorites',
      name: 'MobileFavorites',
      component: () => import('@/views/MobileFavorites.vue'),
      meta: { title: '我的收藏', requiresAuth: true }
    },
    {
      path: '/mobile/profile/edit',
      name: 'MobileProfileEdit',
      component: () => import('@/views/MobileProfileEdit.vue'),
      meta: { title: '编辑资料', requiresAuth: true }
    },
    {
      path: '/mobile/shop/:id',
      name: 'MobileShopDetail',
      component: () => import('@/views/MobileShopDetail.vue'),
      meta: { title: '商家店铺' }
    },
    {
      path: '/mobile/categories',
      name: 'MobileCategories',
      component: () => import('@/views/Categories.vue'),
      meta: { title: '所有分类' }
    },
    {
      path: '/mobile/category/:id',
      name: 'MobileCategoryDetail',
      component: () => import('@/views/CategoryDetail.vue'),
      meta: { title: '分类详情' }
    },
    {
      path: '/mobile/product/:id',
      name: 'MobileProductDetail',
      component: () => import('@/views/MobileProductDetail.vue'),
      meta: { title: '商品详情' }
    },
    {
      path: '/home-original',
      name: 'HomeOriginal',
      component: () => import('@/views/Home.vue'),
      meta: { title: 'TikTok Shop (Original)' }
    },
    {
      path: '/debug',
      name: 'Debug',
      component: () => import('@/views/Debug.vue'),
      meta: { title: '调试页面' }
    },
    {
      path: '/test',
      name: 'Test',
      component: () => import('@/views/Test.vue'),
      meta: { title: '测试页面' }
    },
    {
      path: '/simple',
      name: 'Simple',
      component: () => import('@/views/Simple.vue'),
      meta: { title: '简单测试' }
    },
    {
      path: '/minimal',
      name: 'Minimal',
      component: () => import('@/views/Minimal.vue'),
      meta: { title: '最小测试' }
    },
    {
      path: '/tiktok-shop',
      name: 'TikTokShop',
      component: () => import('@/views/TikTokShop.vue'),
      meta: { title: 'TikTok Shop' }
    },
    {
      path: '/data-debug',
      name: 'DataDebug',
      component: () => import('@/views/DataDebug.vue'),
      meta: { title: '数据调试' }
    },
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/Login.vue'),
      meta: { title: '登录', hideForAuth: true }
    },
    {
      path: '/register',
      name: 'Register',
      component: () => import('@/views/Register.vue'),
      meta: { title: '注册', hideForAuth: true }
    },
    {
      path: '/product/:id',
      name: 'ProductDetail',
      component: () => import('@/views/ProductDetail.vue'),
      meta: { title: '商品详情' }
    },
    {
      path: '/shop/:id',
      name: 'ShopDetail',
      component: () => import('@/views/ShopDetail.vue'),
      meta: { title: '商家店铺' }
    },
    {
      path: '/categories',
      name: 'Categories',
      component: () => import('@/views/Categories.vue'),
      meta: { title: '所有分类' }
    },
    {
      path: '/category/:id',
      name: 'CategoryDetail',
      component: () => import('@/views/CategoryDetail.vue'),
      meta: { title: '分类详情' }
    },
    {
      path: '/search',
      name: 'Search',
      component: () => import('@/views/Search.vue'),
      meta: { title: '搜索结果' }
    },
    {
      path: '/cart',
      name: 'Cart',
      component: () => import('@/views/Cart.vue'),
      meta: { title: '购物车', requiresAuth: true }
    },
    {
      path: '/order',
      name: 'Order',
      component: () => import('@/views/Order.vue'),
      meta: { title: '订单确认', requiresAuth: true }
    },
    {
      path: '/orders',
      name: 'Orders',
      component: () => import('@/views/Orders.vue'),
      meta: { title: '我的订单', requiresAuth: true }
    },
    {
      path: '/profile',
      name: 'Profile',
      component: () => import('@/views/Profile.vue'),
      meta: { title: '个人中心', requiresAuth: true }
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/views/NotFound.vue'),
      meta: { title: '页面不存在' }
    }
  ]
})

// 检测是否为移动设备
const isMobile = () => {
  return window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
}

// 路由守卫
router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()
  
  // 设置页面标题
  document.title = to.meta.title as string || 'TikTok Shop'
  
  // 移动端自动跳转逻辑
  if (isMobile()) {
    // 如果是桌面端路由且是移动设备，跳转到移动端
    if (!to.path.startsWith('/mobile') && to.path !== '/mobile') {
      // 特殊处理：根路径直接跳转到移动端首页
      if (to.path === '/') {
        next('/mobile')
        return
      }
      // 其他桌面端路由跳转到对应的移动端路由
      const mobilePath = `/mobile${to.path}`
      next(mobilePath)
      return
    }
  } else {
    // 如果是移动端路由且是桌面设备，跳转到桌面端
    if (to.path.startsWith('/mobile')) {
      const desktopPath = to.path.replace('/mobile', '') || '/'
      next(desktopPath)
      return
    }
  }
  
  // 检查是否需要登录
  if (to.meta.requiresAuth && !userStore.isLoggedIn) {
    const loginPath = isMobile() ? '/mobile/login' : '/login'
    next(loginPath)
    return
  }
  
  // 检查是否已登录用户访问登录/注册页面
  if (to.meta.hideForAuth && userStore.isLoggedIn) {
    const homePath = isMobile() ? '/mobile' : '/'
    next(homePath)
    return
  }
  
  // 初始化用户信息
  if (userStore.isLoggedIn && !userStore.userInfo) {
    try {
      await userStore.initUserInfo()
    } catch (error) {
      console.error('初始化用户信息失败:', error)
    }
  }
  
  next()
})

export default router