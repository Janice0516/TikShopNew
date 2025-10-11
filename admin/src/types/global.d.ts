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
  export const login: any
  export const getUserInfo: any
  export const testConnection: any
  export interface LoginForm {
    username: string
    password: string
  }
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
  export const getCategoryList: any
  export const updateProduct: any
  export const deleteProduct: any
}

declare module '@/api/recharge' {
  export const getRechargeList: any
  export const auditRecharge: any
  export const getRechargeStats: any
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
  export const createCreditRating: any
  export const updateCreditRating: any
  export const deleteCreditRating: any
  export const getCreditRatingDetail: any
}

declare module '@/api/category' {
  export const getCategoryList: any
  export const createCategory: any
  export const updateCategory: any
  export const deleteCategory: any
  export const getCategoryTree: any
  export const updateCategoryStatus: any
}

declare module '@/api/fund-management' {
  export const getFundOperations: any
  export const createFundOperation: any
  export const increaseFund: any
  export const freezeFund: any
  export const unfreezeFund: any
  export const deductFund: any
  export const refundFund: any
  export const getFundOperationList: any
}

declare module '@/api/merchant' {
  export const getMerchantList: any
  export const getMerchantDetail: any
  export const updateMerchantStatus: any
  export const auditMerchant: any
  export const updateMerchant: any
  export const resetMerchantPassword: any
}

declare module '@/utils/format' {
  export const formatCurrency: any
  export const formatDate: any
  export const formatTime: any
}

declare module '@/router' {
  export const router: any
  export const push: any
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

declare module '@/views/categories/index.vue' {
  const CategoriesView: any
  export default CategoriesView
}

declare module '@/views/fund-management/index.vue' {
  const FundManagementView: any
  export default FundManagementView
}

declare module '@/components/FileUpload.vue' {
  const FileUpload: any
  export default FileUpload
}

declare module '@/components/RichTextEditor.vue' {
  const RichTextEditor: any
  export default RichTextEditor
}
