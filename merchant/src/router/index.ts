import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useMerchantStore } from '@/stores/merchant'

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/login/index.vue'),
    meta: { title: 'login.title', requiresAuth: false }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/register/index.vue'),
    meta: { title: 'register.title', requiresAuth: false }
  },
  {
    path: '/',
    component: () => import('@/layouts/index.vue'),
    redirect: '/dashboard',
    children: [
      {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('@/views/dashboard/index.vue'),
        meta: { title: 'nav.dashboard', icon: 'DataAnalysis' }
      },
      {
        path: '/products',
        name: 'Products',
        redirect: '/products/select-products',
        meta: { title: 'nav.products', icon: 'Goods' },
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
            meta: { title: 'nav.selectProducts' }
          }
        ]
      },
      {
        path: '/orders',
        name: 'Orders',
        redirect: '/orders/pending',
        meta: { title: 'nav.orders', icon: 'Document' },
        children: [
          {
            path: 'pending',
            name: 'PendingOrders',
            component: () => import('@/views/orders/pending.vue'),
            meta: { title: 'nav.pendingOrders' }
          },
          {
            path: 'all',
            name: 'AllOrders',
            component: () => import('@/views/orders/all.vue'),
            meta: { title: 'nav.allOrders' }
          },
          {
            path: ':id',
            name: 'OrderDetail',
            component: () => import('@/views/orders/detail.vue'),
            meta: { title: 'orders.orderDetails', hidden: true }
          }
        ]
      },
      {
        path: '/finance',
        name: 'Finance',
        redirect: '/finance/earnings',
        meta: { title: 'nav.finance', icon: 'Money' },
        children: [
          {
            path: 'earnings',
            name: 'Earnings',
            component: () => import('@/views/finance/earnings.vue'),
            meta: { title: 'nav.earnings' }
          },
          {
            path: 'recharge',
            name: 'Recharge',
            component: () => import('@/views/finance/recharge.vue'),
            meta: { title: 'nav.recharge' }
          },
          {
            path: 'withdraw',
            name: 'Withdraw',
            component: () => import('@/views/withdrawal/index.vue'),
            meta: { title: 'nav.withdraw' }
          }
        ]
      },
      {
        path: '/shop',
        name: 'Shop',
        component: () => import('@/views/shop/index.vue'),
        meta: { title: 'nav.shop', icon: 'Shop' }
      },
      {
        path: '/credit-rating',
        name: 'CreditRating',
        component: () => import('@/views/credit-rating/index.vue'),
        meta: { title: 'nav.creditRating', icon: 'Star' }
      },
      {
        path: '/settings',
        name: 'Settings',
        component: () => import('@/views/settings/index.vue'),
        meta: { title: 'nav.settings', icon: 'Setting' }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// 路由守卫
router.beforeEach((to, from, next) => {
  const merchantStore = useMerchantStore()
  
  // 如果不需要认证，直接通过
  if (to.meta.requiresAuth === false) {
    next()
    return
  }
  
  // 检查是否已登录
  if (!merchantStore.token) {
    next('/login')
    return
  }
  
  next()
})

export default router

