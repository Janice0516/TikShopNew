// 全局类型声明文件
declare module '@/utils/request' {
  const request: any
  export default request
}

declare module '@/api/merchant' {
  export const login: any
  export const getProfile: any
  export const getMerchantInfo: any
  export interface LoginForm {
    username: string
    password: string
  }
}

declare module '@/api/order' {
  export const getMerchantOrders: any
  export const getOrderDetail: any
  export const shipOrder: any
  export const getOrderStats: any
}

declare module '@/api/product' {
  export const getProducts: any
  export const getMerchantProducts: any
  export const getCategories: any
  export const updateProductPrice: any
  export const toggleProductStatus: any
  export const getPlatformProducts: any
  export const selectProduct: any
}

declare module '@/api/finance' {
  export const getFinanceStats: any
  export const getFundFlow: any
  export const getWithdrawHistory: any
  export const rechargeAccount: any
}

declare module '@/api/shop' {
  export const getShopInfo: any
  export const updateShopInfo: any
  export const updateShopAnnouncement: any
  export const uploadShopImage: any
}

declare module '@/api/withdrawal' {
  export const getMerchantBalance: any
  export const getMerchantWithdrawals: any
  export const createWithdrawal: any
}

declare module '@/api/credit-rating' {
  export const getMerchantCurrentRating: any
  export const getMerchantRatingHistory: any
}

declare module '@/stores/merchant' {
  export const useMerchantStore: any
}

declare module '@/components/LanguageSwitcher.vue' {
  const LanguageSwitcher: any
  export default LanguageSwitcher
}

declare module '@/i18n' {
  export const i18n: any
  export const languages: any
  export const setLanguage: any
}

declare module '@/views/login/index.vue' {
  const LoginView: any
  export default LoginView
}

declare module '@/views/register/index.vue' {
  const RegisterView: any
  export default RegisterView
}

declare module '@/layouts/index.vue' {
  const LayoutView: any
  export default LayoutView
}

declare module '@/views/dashboard/index.vue' {
  const DashboardView: any
  export default DashboardView
}

declare module '@/views/products/my-products.vue' {
  const MyProductsView: any
  export default MyProductsView
}

declare module '@/views/products/select-products.vue' {
  const SelectProductsView: any
  export default SelectProductsView
}

declare module '@/views/orders/pending.vue' {
  const PendingOrdersView: any
  export default PendingOrdersView
}

declare module '@/views/orders/all.vue' {
  const AllOrdersView: any
  export default AllOrdersView
}

declare module '@/views/orders/detail.vue' {
  const OrderDetailView: any
  export default OrderDetailView
}

declare module '@/views/finance/earnings.vue' {
  const EarningsView: any
  export default EarningsView
}

declare module '@/views/finance/recharge.vue' {
  const RechargeView: any
  export default RechargeView
}

declare module '@/views/withdrawal/index.vue' {
  const WithdrawalView: any
  export default WithdrawalView
}

declare module '@/views/shop/index.vue' {
  const ShopView: any
  export default ShopView
}

declare module '@/views/credit-rating/index.vue' {
  const CreditRatingView: any
  export default CreditRatingView
}

declare module '@/views/settings/index.vue' {
  const SettingsView: any
  export default SettingsView
}
