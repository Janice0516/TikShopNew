import { createRouter, createWebHistory } from 'vue-router'
import { useMerchantStore } from '@/stores/merchant'

const router = createRouter({
  history: createWebHistory('/merchant/'),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/login/index.vue'),
      meta: { title: 'nav.login', requiresAuth: false }
    },
    {
      path: '',
      redirect: '/dashboard'
    },
    {
      path: '/',
      component: () => import('@/layouts/index.vue'),
      children: [
        {
          path: 'dashboard',
          name: 'Dashboard',
          component: () => import('@/views/dashboard/index.vue'),
          meta: { title: 'nav.dashboard' }
        },
        {
          path: 'products',
          children: [
            {
              path: 'my-products',
              name: 'MyProducts',
              component: () => import('@/views/products/my-products.vue'),
              meta: { title: 'nav.myProducts' }
            },
            {
              path: 'select-products',
              name: 'SelectProducts',
              component: () => import('@/views/products/select-products.vue'),
              meta: { title: 'products.selectFromPlatform' }
            }
          ]
        },
        {
          path: 'orders',
          children: [
            {
              path: 'pending',
              name: 'PendingOrders',
              component: () => import('@/views/orders/pending.vue'),
              meta: { title: 'orders.pending' }
            },
            {
              path: 'all',
              name: 'AllOrders',
              component: () => import('@/views/orders/all.vue'),
              meta: { title: 'orders.all' }
            },
            {
              path: ':id',
              name: 'OrderDetail',
              component: () => import('@/views/orders/detail.vue'),
              meta: { title: 'orders.detail' }
            }
          ]
        },
        {
          path: 'finance',
          children: [
            {
              path: 'earnings',
              name: 'Earnings',
              component: () => import('@/views/finance/earnings.vue'),
              meta: { title: 'finance.earnings' }
            },
            {
              path: 'recharge',
              name: 'Recharge',
              component: () => import('@/views/finance/recharge.vue'),
              meta: { title: 'finance.recharge' }
            },
            {
              path: 'withdraw',
              name: 'Withdraw',
              component: () => import('@/views/finance/withdraw.vue'),
              meta: { title: 'finance.withdraw' }
            }
          ]
        },
        {
          path: 'shop',
          name: 'Shop',
          component: () => import('@/views/shop/index.vue'),
          meta: { title: 'nav.shop' }
        },
        {
          path: 'credit-rating',
          name: 'CreditRating',
          component: () => import('@/views/credit-rating/index.vue'),
          meta: { title: 'nav.creditRating' }
        },
        {
          path: 'settings',
          name: 'Settings',
          component: () => import('@/views/settings/index.vue'),
          meta: { title: 'nav.settings' }
        }
      ]
    }
  ]
})

router.beforeEach((to, from, next) => {
  const merchantStore = useMerchantStore()
  
  // 防止重复重定向
  if (from.path === to.path) {
    next()
    return
  }
  
  // 检查是否需要认证
  if (to.meta.requiresAuth !== false && !merchantStore.token) {
    next('/login')
    return
  }
  
  // 如果已登录且访问登录页，重定向到仪表板
  if (to.path === '/login' && merchantStore.token) {
    next('/dashboard')
    return
  }
  
  next()
})

export default router
