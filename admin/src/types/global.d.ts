// 全局类型声明文件
declare module '@/utils/request' {
  const request: any
  export default request
}

declare module '@/api/admin' {
  export const login: any
  export const getUserInfo: any
  export const getDashboardStats: any
}

declare module '@/api/user' {
  export const getUserList: any
  export const getUserDetail: any
  export const updateUserStatus: any
}

declare module '@/api/order' {
  export const getOrderList: any
  export const getOrderDetail: any
  export const updateOrderStatus: any
}

declare module '@/api/product' {
  export const getProductList: any
  export const getProductDetail: any
  export const updateProductStatus: any
  export const createProduct: any
}

declare module '@/api/recharge' {
  export const getRechargeList: any
  export const auditRecharge: any
}

declare module '@/api/withdrawal' {
  export const getWithdrawalList: any
  export const updateWithdrawalStatus: any
}

declare module '@/api/credit-rating' {
  export const getCreditRatingList: any
  export const calculateMerchantRating: any
  export const getCreditRatingStats: any
  export const recalculateAllMerchantRatings: any
}

declare module '@/stores/user' {
  export const useUserStore: any
}

declare module '@/views/login/index.vue' {
  const LoginView: any
  export default LoginView
}

declare module '@/views/dashboard/index.vue' {
  const DashboardView: any
  export default DashboardView
}

declare module '@/views/users/index.vue' {
  const UsersView: any
  export default UsersView
}

declare module '@/views/merchants/index.vue' {
  const MerchantsView: any
  export default MerchantsView
}

declare module '@/views/products/index.vue' {
  const ProductsView: any
  export default ProductsView
}

declare module '@/views/products/add.vue' {
  const AddProductView: any
  export default AddProductView
}

declare module '@/views/orders/index.vue' {
  const OrdersView: any
  export default OrdersView
}

declare module '@/views/orders/detail.vue' {
  const OrderDetailView: any
  export default OrderDetailView
}

declare module '@/views/recharge-audit/index.vue' {
  const RechargeAuditView: any
  export default RechargeAuditView
}

declare module '@/views/withdrawal/index.vue' {
  const WithdrawalView: any
  export default WithdrawalView
}

declare module '@/views/profile/index.vue' {
  const ProfileView: any
  export default ProfileView
}

declare module '@/views/credit-rating/index.vue' {
  const CreditRatingView: any
  export default CreditRatingView
}
