import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useUserStore } from '@/stores/user'

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/login/index.vue'),
    meta: { title: '登录', titleKey: 'login', requiresAuth: false }
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
        meta: { title: '数据概览', titleKey: 'dashboard', icon: 'DataAnalysis' }
      },
      {
        path: '/products',
        name: 'Products',
        component: () => import('@/views/products/index.vue'),
        meta: { title: '商品管理', titleKey: 'products', icon: 'Goods' }
      },
      {
        path: '/recommend-products',
        name: 'RecommendProducts',
        component: () => import('@/views/recommend-products.vue'),
        meta: { title: '推荐商品管理', titleKey: 'recommendProducts', icon: 'Star' }
      },
      {
        path: '/products/add',
        name: 'ProductAdd',
        component: () => import('@/views/products/add.vue'),
        meta: { title: '添加商品', titleKey: 'addProduct', hidden: true }
      },
      {
        path: '/categories',
        name: 'Categories',
        component: () => import('@/views/categories/index.vue'),
        meta: { title: '分类管理', titleKey: 'categories', icon: 'Menu' }
      },
      {
        path: '/merchants',
        name: 'Merchants',
        component: () => import('@/views/merchants/index.vue'),
        meta: { title: '商家管理', titleKey: 'merchants', icon: 'Shop' }
      },
      {
        path: '/invite-code',
        name: 'InviteCode',
        component: () => import('@/views/invite-code/index.vue'),
        meta: { title: '邀请码管理', titleKey: 'inviteCode', icon: 'Ticket' }
      },
      {
        path: '/withdrawal',
        name: 'Withdrawal',
        component: () => import('@/views/withdrawal/index.vue'),
        meta: { title: '提现管理', titleKey: 'withdrawal', icon: 'CreditCard' }
      },
      {
        path: '/recharge-audit',
        name: 'RechargeAudit',
        component: () => import('@/views/recharge-audit/index.vue'),
        meta: { title: '充值审核', titleKey: 'rechargeAudit', icon: 'Money' }
      },
      {
        path: '/credit-rating',
        name: 'CreditRating',
        component: () => import('@/views/credit-rating/index.vue'),
        meta: { title: '信用评级', titleKey: 'creditRating', icon: 'Star' }
      },
      {
        path: '/fund-management',
        name: 'FundManagement',
        component: () => import('@/views/fund-management/index.vue'),
        meta: { title: '资金管理', titleKey: 'fundManagement', icon: 'Wallet' }
      },
      {
        path: '/orders',
        name: 'Orders',
        component: () => import('@/views/orders/index.vue'),
        meta: { title: '订单管理', titleKey: 'orders', icon: 'Document' }
      },
      {
        path: '/orders/:id',
        name: 'OrderDetail',
        component: () => import('@/views/orders/detail.vue'),
        meta: { title: '订单详情', titleKey: 'orderDetail', hidden: true }
      },
      {
        path: '/users',
        name: 'Users',
        component: () => import('@/views/users/index.vue'),
        meta: { title: '用户管理', titleKey: 'users', icon: 'UserFilled' }
      },
      {
        path: '/profile',
        name: 'Profile',
        component: () => import('@/views/profile/index.vue'),
        meta: { title: '个人中心', titleKey: 'profile', icon: 'User' }
      },
      {
        path: '/settings',
        name: 'Settings',
        component: () => import('@/views/settings/index.vue'),
        meta: { title: '系统设置', titleKey: 'settings', icon: 'Setting' }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// 路由守卫
router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()
  
  // 设置页面标题
  document.title = to.meta.title ? `${to.meta.title} - 电商管理后台` : '电商管理后台'
  
  // 如果不需要认证，直接通过
  if (to.meta.requiresAuth === false) {
    next()
    return
  }
  
  // 检查是否已登录
  if (!userStore.token) {
    next('/login')
    return
  }
  
  next()
})

export default router

