// 移动端全局样式变量
export const mobileTheme = {
  // 颜色
  colors: {
    background: '#000',
    surface: '#1a1a1a',
    surfaceVariant: '#222',
    border: '#333',
    borderVariant: '#555',
    text: '#fff',
    textSecondary: '#ccc',
    textTertiary: '#666',
    primary: '#ff0050',
    primaryHover: '#e6004a',
    success: '#28a745',
    warning: '#ffc107',
    error: '#dc3545',
    info: '#17a2b8'
  },
  
  // 字体
  typography: {
    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
    fontSize: {
      xs: '10px',
      sm: '12px',
      base: '14px',
      lg: '16px',
      xl: '18px',
      '2xl': '20px',
      '3xl': '24px'
    },
    fontWeight: {
      normal: '400',
      medium: '500',
      semibold: '600',
      bold: '700'
    }
  },
  
  // 间距
  spacing: {
    xs: '4px',
    sm: '8px',
    base: '12px',
    md: '16px',
    lg: '20px',
    xl: '24px',
    '2xl': '32px',
    '3xl': '40px'
  },
  
  // 圆角
  borderRadius: {
    sm: '4px',
    base: '8px',
    md: '12px',
    lg: '16px',
    full: '50%'
  },
  
  // 阴影
  shadows: {
    sm: '0 1px 2px rgba(0, 0, 0, 0.1)',
    base: '0 2px 4px rgba(0, 0, 0, 0.1)',
    md: '0 4px 8px rgba(0, 0, 0, 0.1)',
    lg: '0 8px 16px rgba(0, 0, 0, 0.1)'
  },
  
  // 过渡
  transitions: {
    fast: '0.15s',
    base: '0.2s',
    slow: '0.3s'
  },
  
  // 断点
  breakpoints: {
    mobile: '480px',
    tablet: '768px',
    desktop: '1024px'
  }
}

// 移动端通用样式类
export const mobileStyles = `
  /* 全局移动端样式 */
  body {
    background: ${mobileTheme.colors.background};
    color: ${mobileTheme.colors.text};
    margin: 0;
    padding: 0;
    font-family: ${mobileTheme.typography.fontFamily};
    font-size: ${mobileTheme.typography.fontSize.base};
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  * {
    box-sizing: border-box;
  }

  /* 滚动条样式 */
  ::-webkit-scrollbar {
    width: 4px;
    height: 4px;
  }

  ::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
  }

  ::-webkit-scrollbar-thumb {
    background: rgba(255, 0, 80, 0.5);
    border-radius: 2px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 0, 80, 0.7);
  }

  /* 移动端按钮样式 */
  .mobile-btn {
    background: ${mobileTheme.colors.primary};
    color: ${mobileTheme.colors.text};
    border: none;
    border-radius: ${mobileTheme.borderRadius.md};
    padding: ${mobileTheme.spacing.md} ${mobileTheme.spacing.lg};
    font-size: ${mobileTheme.typography.fontSize.lg};
    font-weight: ${mobileTheme.typography.fontWeight.semibold};
    cursor: pointer;
    transition: all ${mobileTheme.transitions.base} ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 52px;
    touch-action: manipulation;
  }

  .mobile-btn:hover:not(:disabled) {
    background: ${mobileTheme.colors.primaryHover};
    transform: translateY(-1px);
  }

  .mobile-btn:active:not(:disabled) {
    transform: translateY(0);
  }

  .mobile-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
  }

  /* 移动端输入框样式 */
  .mobile-input {
    background: ${mobileTheme.colors.surface};
    border: 1px solid ${mobileTheme.colors.border};
    border-radius: ${mobileTheme.borderRadius.md};
    padding: ${mobileTheme.spacing.md};
    color: ${mobileTheme.colors.text};
    font-size: ${mobileTheme.typography.fontSize.lg};
    transition: border-color ${mobileTheme.transitions.base} ease;
    width: 100%;
  }

  .mobile-input:focus {
    outline: none;
    border-color: ${mobileTheme.colors.primary};
  }

  .mobile-input::placeholder {
    color: ${mobileTheme.colors.textTertiary};
  }

  /* 移动端卡片样式 */
  .mobile-card {
    background: ${mobileTheme.colors.surface};
    border-radius: ${mobileTheme.borderRadius.md};
    padding: ${mobileTheme.spacing.lg};
    border: 1px solid ${mobileTheme.colors.border};
    transition: all ${mobileTheme.transitions.base} ease;
  }

  .mobile-card:hover {
    background: ${mobileTheme.colors.surfaceVariant};
    transform: translateY(-2px);
  }

  /* 移动端头部样式 */
  .mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: ${mobileTheme.spacing.md} ${mobileTheme.spacing.lg};
    background: ${mobileTheme.colors.background};
    border-bottom: 1px solid ${mobileTheme.colors.border};
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .mobile-header .header-title {
    font-size: ${mobileTheme.typography.fontSize.xl};
    font-weight: ${mobileTheme.typography.fontWeight.semibold};
    margin: 0;
  }

  /* 移动端底部导航样式 */
  .mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: ${mobileTheme.colors.background};
    border-top: 1px solid ${mobileTheme.colors.border};
    z-index: 1000;
    padding: ${mobileTheme.spacing.sm} 0;
    padding-bottom: calc(${mobileTheme.spacing.sm} + env(safe-area-inset-bottom));
  }

  /* 响应式设计 */
  @media (max-width: ${mobileTheme.breakpoints.mobile}) {
    .mobile-header {
      padding: ${mobileTheme.spacing.base} ${mobileTheme.spacing.md};
    }
    
    .mobile-header .header-title {
      font-size: ${mobileTheme.typography.fontSize.lg};
    }
  }

  /* 触摸优化 */
  @media (hover: none) and (pointer: coarse) {
    .mobile-btn:hover {
      transform: none;
    }
    
    .mobile-card:hover {
      transform: none;
    }
  }

  /* 触屏增强优化 */
  @media (max-width: ${mobileTheme.breakpoints.tablet}) {
    body {
      overflow-x: hidden;
      -webkit-tap-highlight-color: transparent;
      -webkit-touch-callout: none;
      -webkit-user-select: none;
      -khtml-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }
    
    input, textarea {
      -webkit-user-select: text;
      -khtml-user-select: text;
      -moz-user-select: text;
      -ms-user-select: text;
      user-select: text;
    }
    
    button, .mobile-btn, .el-button {
      -webkit-tap-highlight-color: transparent;
      touch-action: manipulation;
      min-height: 44px;
      min-width: 44px;
    }
    
    button:active, .mobile-btn:active, .el-button:active {
      transform: scale(0.98);
      transition: transform 0.1s ease;
    }
    
    .mobile-card:active {
      transform: scale(0.98);
      transition: transform 0.1s ease;
    }
    
    img {
      -webkit-user-drag: none;
      -khtml-user-drag: none;
      -moz-user-drag: none;
      -o-user-drag: none;
      user-drag: none;
    }
    
    .smooth-scroll {
      -webkit-overflow-scrolling: touch;
      scroll-behavior: smooth;
    }
    
    .smooth-scroll::-webkit-scrollbar {
      display: none;
    }
    
    .smooth-scroll {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  }

  /* 小屏幕优化 */
  @media (max-width: ${mobileTheme.breakpoints.mobile}) {
    h1 { font-size: 20px; }
    h2 { font-size: 18px; }
    h3 { font-size: 16px; }
    p { font-size: 14px; }
    
    button, .mobile-btn {
      min-height: 48px;
      font-size: 16px;
    }
    
    input, textarea, .mobile-input {
      min-height: 48px;
      font-size: 16px;
    }
  }

  /* 超小屏幕优化 */
  @media (max-width: 360px) {
    h1 { font-size: 18px; }
    h2 { font-size: 16px; }
    h3 { font-size: 14px; }
    p { font-size: 13px; }
    
    button, .mobile-btn {
      min-height: 44px;
      font-size: 14px;
    }
  }

  /* 横屏优化 */
  @media (max-height: 500px) and (orientation: landscape) {
    .mobile-header {
      height: 50px;
    }
    
    .mobile-bottom-nav {
      height: 50px;
    }
  }

  /* 减少动画模式 */
  @media (prefers-reduced-motion: reduce) {
    * {
      animation-duration: 0.01ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: 0.01ms !important;
    }
  }

  /* 高对比度模式 */
  @media (prefers-contrast: high) {
    button, input, .mobile-card {
      border-width: 2px;
      border-color: ${mobileTheme.colors.text};
    }
  }
`

// 应用全局样式
export const applyMobileStyles = () => {
  if (typeof document !== 'undefined') {
    const styleElement = document.createElement('style')
    styleElement.textContent = mobileStyles
    document.head.appendChild(styleElement)
  }
}
