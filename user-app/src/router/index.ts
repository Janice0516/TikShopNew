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
      path: '/category/:id',
      name: 'Category',
      component: () => import('@/views/Category.vue'),
      meta: { title: '商品分类' }
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

// 路由守卫
router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()
  
  // 设置页面标题
  document.title = to.meta.title as string || 'TikTok Shop'
  
  // 检查是否需要登录
  if (to.meta.requiresAuth && !userStore.isLoggedIn) {
    next('/login')
    return
  }
  
  // 检查是否已登录用户访问登录/注册页面
  if (to.meta.hideForAuth && userStore.isLoggedIn) {
    next('/')
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