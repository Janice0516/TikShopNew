// 设备检测工具函数
export function detectDevice() {
  // 获取用户代理字符串
  const userAgent = navigator.userAgent.toLowerCase()
  
  // 检测移动设备
  const mobileRegex = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i
  const isMobile = mobileRegex.test(userAgent)
  
  // 检测平板设备
  const tabletRegex = /ipad|android(?!.*mobile)|kindle|silk|playbook/i
  const isTablet = tabletRegex.test(userAgent)
  
  // 检测桌面设备
  const isDesktop = !isMobile && !isTablet
  
  // 检测屏幕尺寸
  const screenWidth = window.innerWidth || document.documentElement.clientWidth
  const screenHeight = window.innerHeight || document.documentElement.clientHeight
  
  // 根据屏幕尺寸进一步判断
  const isLargeScreen = screenWidth >= 1024
  const isMediumScreen = screenWidth >= 768 && screenWidth < 1024
  const isSmallScreen = screenWidth < 768
  
  return {
    isMobile,
    isTablet,
    isDesktop,
    isLargeScreen,
    isMediumScreen,
    isSmallScreen,
    screenWidth,
    screenHeight,
    userAgent
  }
}

// 获取设备类型
export function getDeviceType() {
  const device = detectDevice()
  
  if (device.isMobile) {
    return 'mobile'
  } else if (device.isTablet) {
    return 'tablet'
  } else if (device.isDesktop) {
    return 'desktop'
  } else {
    return 'unknown'
  }
}

// 判断是否应该显示桌面端
export function shouldShowDesktop() {
  const device = detectDevice()
  
  // 桌面设备且屏幕宽度大于768px
  return device.isDesktop && device.screenWidth >= 768
}

// 判断是否应该显示移动端
export function shouldShowMobile() {
  const device = detectDevice()
  
  // 移动设备或屏幕宽度小于768px
  return device.isMobile || device.screenWidth < 768
}
