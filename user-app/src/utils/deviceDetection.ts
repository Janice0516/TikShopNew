// 设备检测工具函数
export function detectDevice() {
  // 在UniApp环境中获取系统信息
  const systemInfo = uni.getSystemInfoSync()
  
  // 获取用户代理字符串（如果可用）
  const userAgent = typeof navigator !== 'undefined' ? navigator.userAgent.toLowerCase() : ''
  
  // 检测移动设备
  const mobileRegex = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i
  const isMobile = mobileRegex.test(userAgent) || systemInfo.platform === 'ios' || systemInfo.platform === 'android'
  
  // 检测平板设备
  const tabletRegex = /ipad|android(?!.*mobile)|kindle|silk|playbook/i
  const isTablet = tabletRegex.test(userAgent) || (systemInfo.platform === 'ios' && systemInfo.screenWidth >= 768)
  
  // 检测桌面设备
  const isDesktop = !isMobile && !isTablet && (systemInfo.platform === 'mac' || systemInfo.platform === 'windows' || systemInfo.platform === 'linux')
  
  // 检测屏幕尺寸
  const screenWidth = systemInfo.screenWidth || (typeof window !== 'undefined' ? window.innerWidth : 0)
  const screenHeight = systemInfo.screenHeight || (typeof window !== 'undefined' ? window.innerHeight : 0)
  
  // 根据屏幕尺寸进一步判断
  const isLargeScreen = screenWidth >= 1024
  const isMediumScreen = screenWidth >= 768 && screenWidth < 1024
  const isSmallScreen = screenWidth < 768
  
  // 调试信息
  console.log('设备检测信息:', {
    platform: systemInfo.platform,
    screenWidth,
    screenHeight,
    userAgent,
    isMobile,
    isTablet,
    isDesktop,
    isLargeScreen,
    isMediumScreen,
    isSmallScreen
  })
  
  return {
    isMobile,
    isTablet,
    isDesktop,
    isLargeScreen,
    isMediumScreen,
    isSmallScreen,
    screenWidth,
    screenHeight,
    userAgent,
    platform: systemInfo.platform
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
  const shouldShow = device.isDesktop && device.screenWidth >= 768
  
  console.log('是否显示桌面端:', shouldShow, {
    isDesktop: device.isDesktop,
    screenWidth: device.screenWidth,
    platform: device.platform
  })
  
  return shouldShow
}

// 判断是否应该显示移动端
export function shouldShowMobile() {
  const device = detectDevice()
  
  // 移动设备或屏幕宽度小于768px
  const shouldShow = device.isMobile || device.screenWidth < 768
  
  console.log('是否显示移动端:', shouldShow, {
    isMobile: device.isMobile,
    screenWidth: device.screenWidth,
    platform: device.platform
  })
  
  return shouldShow
}
